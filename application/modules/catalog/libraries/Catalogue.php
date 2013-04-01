<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Catalogue {

	var $categories = '';
	var $list_products = '';

	var $catalog;

	function Catalogue ()
	{
		$this->obj =& get_instance();
	}

	//-------------------------------- Categories --------------------------------//

	public function get_categories($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->from($this->obj->config->item('table_categories'));
		$this->obj->db->join($this->obj->config->item('table_categories_lang'), $this->obj->config->item('table_categories').'.id = '.$this->obj->config->item('table_categories_lang').'.categories_id');
		$query = $this->obj->db->get('', 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	function list_categories($parent = 0, $level = 0, $where = '', $limit = 1000, $start = 0, $recursiv = true)
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		$this->obj->db->select('*');
		$this->obj->db->where(array('parent_id' => $parent, 'lang' => $this->obj->user->lang));
		$this->obj->db->order_by('parent_id, ordering');
		$this->obj->db->limit($limit, $start);
		$this->obj->db->from($this->obj->config->item('table_categories'));
		$this->obj->db->join($this->obj->config->item('table_categories_lang'), $this->obj->config->item('table_categories').'.id = '.$this->obj->config->item('table_categories_lang').'.categories_id', 'left');
		$query = $this->obj->db->get();

		if ( $query->num_rows() > 0 )
		{
			foreach ($query->result_array() as $row) {
				$this->categories[] = array(
					'level' 		=> $level,
					'title' 		=> $row['title'],
					'body' 			=> $row['body'],
					'active' 		=> $row['active'],
					'parent_id' 	=> $row['parent_id'],
					'id' 			=> $row['id'],
					'uri' 			=> $row['uri']
				);

				if($recursiv) $this->list_categories($row['id'], $level+1, $where);
			}
			return $this->categories;
		}
	}

	function total_categories($parent = 0, $level = 0, $where)
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		$this->obj->db->where(array('parent_id' => $parent, 'lang' => $this->obj->user->lang));

		$this->obj->db->select('count(id)');
		$this->obj->db->from($this->obj->config->item('table_categories'));

		return $this->obj->db->count_all_results();
	}

	//-------------------------------- Products --------------------------------//

	public function search_products($keywords = '')
	{
		if($keywords)
		{
			$keywords = explode(' ', trim($keywords));
			foreach($keywords as $keyword)
			{
				if($keyword != '')
				{
					$this->obj->db->or_like($this->obj->config->item('table_products_lang').'.title', $keyword);
					$this->obj->db->or_like($this->obj->config->item('table_products_lang').'.body', $keyword);
					$this->obj->db->or_like($this->obj->config->item('table_products').'.reference', $keyword);
				}
			}

			$this->obj->db->select('*, '.$this->obj->config->item('table_products').'.id as pID, '.$this->obj->config->item('table_products').'.title as pTITLE, '.$this->obj->config->item('table_products').'.uri as pURI, '.$this->obj->config->item('table_categories').'.id as cID, '.$this->obj->config->item('table_categories').'.title as cTITLE, '.$this->obj->config->item('table_categories').'.uri as cURI');
			
			$this->obj->db->where(array($this->obj->config->item('table_products').'.active' => 1));
			$this->obj->db->from($this->obj->config->item('table_categories'));
		$this->obj->db->join($this->obj->config->item('table_categories_lang'), $this->obj->config->item('table_categories').'.id = '.$this->obj->config->item('table_categories_lang').'.categories_id', 'left');
			$query = $this->obj->db->get();
			$products = array();

			if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row) {
					$products[$row['pID']] = $row['pID'];
				}

				return $products;
			}
			else
			{
				return false;
			}

		}

	}

	public function get_products($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->from($this->obj->config->item('table_products'));
		$this->obj->db->join($this->obj->config->item('table_products_lang'), $this->obj->config->item('table_products').'.id = '.$this->obj->config->item('table_products_lang').'.products_id');
		$query = $this->obj->db->get('', 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function list_products($params = array(), $join_specials = false)
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> $this->obj->config->item('table_products_lang').'.title',
			'start' 	=> 0,
			'limit' 	=> 20,
			'where'		=> null,
			'like'		=> null,
			'or_like'	=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			if(is_array($params['where']))
			{
				foreach ($params['where'] as $key => $value)
				{
					$this->obj->db->where($key, $value);
				}
			}
			else
			{
				if($params['where'] != '') $this->obj->db->where($params['where']);
			}
		}

		if (!is_null($params['like']) && $params['like'] != '')
		{
			$strings = explode(' ', $params['like']);
			foreach($strings as $string)
			{
				$this->obj->db->like(array($this->obj->config->item('table_products_lang').'.title' => $string));
			}

		}

		if (!is_null($params['or_like']) && $params['or_like'] != '')
		{
			$strings = explode(' ', $params['or_like']);
			foreach($strings as $string)
			{
				$this->obj->db->or_like(array($this->obj->config->item('table_products_lang').'.title' => $string));
			}

		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}

		$this->obj->db->limit($params['limit'], $params['start']);

		if(!is_null($params['order_by']) && $params['order_by'] != '')
		{
			$this->obj->db->order_by($params['order_by']);
		}
		$this->obj->db->group_by($this->obj->config->item('table_products').'.id');
		
		$this->obj->db->where(array('lang' => $this->obj->user->lang));

		$this->obj->db->from($this->obj->config->item('table_products'));
		$this->obj->db->join($this->obj->config->item('table_products_lang'), $this->obj->config->item('table_products').'.id = '.$this->obj->config->item('table_products_lang').'.products_id', 'left');
		$this->obj->db->join($this->obj->config->item('table_products_to_categories'), $this->obj->config->item('table_products_to_categories').'.products_id = '.$this->obj->config->item('table_products').'.id', 'left');
		if($join_specials) $this->obj->db->join($this->obj->config->item('table_specials'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products').'.id', 'left');
		$query = $this->obj->db->get();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$this->list_products[] = $row;
			}

			return $this->list_products;
		}
		else
		{
			return false;
		}
	}

	public function total_list_products($params = array(), $join_specials = false)
	{
		$default_params = array
		(
			'order_by' 	=> $this->obj->config->item('table_products_lang').'.title',
			'where'		=> null,
			'like'		=> null,
			'or_like'	=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			if(is_array($params['where']))
			{
				foreach ($params['where'] as $key => $value)
				{
					$this->obj->db->where($key, $value);
				}
			}
			else
			{
				if($params['where'] != '') $this->obj->db->where($params['where']);
			}
		}

		if (!is_null($params['like']) && $params['like'] != '')
		{
			$strings = explode(' ', $params['like']);
			foreach($strings as $string)
			{
				$this->obj->db->like(array($this->obj->config->item('table_products_lang').'.title' => $string));
			}
		}

		if (!is_null($params['or_like']) && $params['or_like'] != '')
		{
			$strings = explode(' ', $params['or_like']);
			foreach($strings as $string)
			{
				$this->obj->db->or_like(array($this->obj->config->item('table_products_lang').'.title' => $string));
			}

		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->group_by($this->obj->config->item('table_products').'.id');
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->from($this->obj->config->item('table_products'));
		$this->obj->db->join($this->obj->config->item('table_products_lang'), $this->obj->config->item('table_products').'.id = '.$this->obj->config->item('table_products_lang').'.products_id', 'left');
		$this->obj->db->join($this->obj->config->item('table_products_to_categories'), $this->obj->config->item('table_products_to_categories').'.products_id = '.$this->obj->config->item('table_products').'.id', 'left');
		if($join_specials) $this->obj->db->join($this->obj->config->item('table_specials'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products').'.id', 'left');
		$query = $this->obj->db->get();

		return $query->num_rows();
	}

	public function get_products_to_categories($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$query = $this->obj->db->get($this->obj->config->item('table_products_to_categories'), 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function check_products_to_categories($where = '', $group_by = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$query = $this->obj->db->get($this->obj->config->item('table_products_to_categories'));
		$check_products_to_categories = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$check_products_to_categories[$row[$group_by]] = $row[$group_by];
			}

			return $check_products_to_categories;
		}
		else
		{
			return false;
		}
	}

	public function get_products_to_products($where)
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$query = $this->obj->db->get($this->obj->config->item('table_products_to_products'), 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function list_products_to_products($products_id = '', $limit = 5)
	{
		$hash = md5($products_id.$limit);
		if(!$products_to_products = $this->obj->cache->get('list_products_to_products'.$hash, 'products'))
		{
			$this->obj->db->select('*, '.$this->obj->config->item('table_products').'.id as pID, '.$this->obj->config->item('table_products_lang').'.title as pTITLE, '.$this->obj->config->item('table_products_lang').'.uri as pURI, '.$this->obj->config->item('table_products').'.tva as pTVA, '.$this->obj->config->item('table_specials').'.active as sACTIVE, '.$this->obj->config->item('table_specials').'.tva as sTVA');
			$this->obj->db->limit($limit);
			$this->obj->db->where(array('products.active' => 1));
			$this->obj->db->where(array('products_id_x' => $products_id));
			$this->obj->db->or_where(array('products_id_y' => $products_id));
			$this->obj->db->from($this->obj->config->item('table_products_to_products'));
			$this->obj->db->join($this->obj->config->item('table_products'), $this->obj->config->item('table_products').'.id = '.$this->obj->config->item('table_products_to_products').'.products_id_y', 'left');
			$this->obj->db->join($this->obj->config->item('table_products_lang'), $this->obj->config->item('table_products_lang').'.products_id = '.$this->obj->config->item('table_products_to_products').'.products_id_y', 'left');
			$this->obj->db->join($this->obj->config->item('table_specials'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products').'.id', 'left');
			$query = $this->obj->db->get();

			$products_to_products = array();
			foreach ($query->result_array() as $row) {
				if($row['pID'] != $products_id) $products_to_products[] = $row;
			}

			$this->obj->db->select('*, '.$this->obj->config->item('table_products').'.id as pID, '.$this->obj->config->item('table_products_lang').'.title as pTITLE, '.$this->obj->config->item('table_products_lang').'.uri as pURI, '.$this->obj->config->item('table_products').'.tva as pTVA, '.$this->obj->config->item('table_specials').'.active as sACTIVE, '.$this->obj->config->item('table_specials').'.tva as sTVA');
			$this->obj->db->limit(5);
			$this->obj->db->where(array('products.active' => 1));
			$this->obj->db->where(array('products_id_x' => $products_id));
			$this->obj->db->or_where(array('products_id_y' => $products_id));
			$this->obj->db->from($this->obj->config->item('table_products_to_products'));
			$this->obj->db->join($this->obj->config->item('table_products'), $this->obj->config->item('table_products').'.id = '.$this->obj->config->item('table_products_to_products').'.products_id_x', 'left');
			$this->obj->db->join($this->obj->config->item('table_products_lang'), $this->obj->config->item('table_products_lang').'.products_id = '.$this->obj->config->item('table_products_to_products').'.products_id_y', 'left');
			$this->obj->db->join($this->obj->config->item('table_specials'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products').'.id', 'left');
			$query = $this->obj->db->get();
			//echo $this->obj->db->last_query();

			foreach ($query->result_array() as $row) {
				if($row['pID'] != $products_id) $products_to_products[] = $row;
			}

			$this->obj->cache->save('list_products_to_products'.$hash, $products_to_products, 'products', 0);
		}
		return $products_to_products;

	}

	public function check_products_to_products($products_id = '')
	{
		$this->obj->db->select('*');

		$this->obj->db->where('products_id_x = '.$products_id.' OR products_id_y = '.$products_id);
		$query = $this->obj->db->get($this->obj->config->item('table_products_to_products'));
		$check_products_to_products = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$check_products_to_products[$row['products_id_y']] = $row['products_id_y'];
				$check_products_to_products[$row['products_id_x']] = $row['products_id_x'];
			}

			return $check_products_to_products;
		}
		else
		{
			return false;
		}
	}

	//-------------------------------- Specials --------------------------------//

	public function get_specials($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$query = $this->obj->db->get($this->obj->config->item('table_specials'), 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function list_specials($params = array())
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> $this->obj->config->item('table_products_lang').'.title',
			'where'		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_specials'));
		$this->obj->db->join($this->obj->config->item('table_products'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products').'.id');
		$this->obj->db->join($this->obj->config->item('table_products_lang'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products_lang').'.products_id');
		$query = $this->obj->db->get();

		if ($query->num_rows() > 0)
		{
			$specials = array();
			foreach ($query->result_array() as $row) {
				$specials[] = $row;
			}
			return $specials;
		}
	}

	public function total_list_specials($params = array())
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> $this->obj->config->item('table_products_lang').'.title',
			'where'		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_specials'));
		$this->obj->db->join($this->obj->config->item('table_products'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products').'.id');
		$this->obj->db->join($this->obj->config->item('table_products_lang'), $this->obj->config->item('table_specials').'.products_id = '.$this->obj->config->item('table_products_lang').'.products_id');
		$query = $this->obj->db->get();

		return $query->num_rows();
	}

	//-------------------------------- Attributes --------------------------------//

	public function get_attributes($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$this->obj->db->from($this->obj->config->item('table_attributes'));
		$this->obj->db->join($this->obj->config->item('table_attributes_lang'), $this->obj->config->item('table_attributes').'.id = '.$this->obj->config->item('table_attributes_lang').'.id');

		$query = $this->obj->db->get('', 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function list_attributes($params = array())
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> 'name',
			'where'		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_attributes'));
		$this->obj->db->join($this->obj->config->item('table_attributes_lang'), $this->obj->config->item('table_attributes').'.id = '.$this->obj->config->item('table_attributes_lang').'.id');
		$query = $this->obj->db->get();

		if ($query->num_rows() > 0)
		{
			$attributes = array();
			foreach ($query->result_array() as $row) {
				$attributes[] = $row;
			}
			return $attributes;
		}
	}

	public function total_list_attributes($params = array())
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> 'name',
			'where'		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_attributes'));
		$this->obj->db->join($this->obj->config->item('table_attributes_lang'), $this->obj->config->item('table_attributes').'.id = '.$this->obj->config->item('table_attributes_lang').'.id');
		$query = $this->obj->db->get();

		return $query->num_rows();
	}

	public function get_attributes_values($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$this->obj->db->from($this->obj->config->item('table_attributes_values'));
		$this->obj->db->join($this->obj->config->item('table_attributes_values_lang'), $this->obj->config->item('table_attributes_values').'.id = '.$this->obj->config->item('table_attributes_values_lang').'.id');

		$query = $this->obj->db->get('', 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function list_attributes_values($params = array())
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> 'name',
			'where'		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_attributes_values'));
		$this->obj->db->join($this->obj->config->item('table_attributes_values_lang'), $this->obj->config->item('table_attributes_values').'.id = '.$this->obj->config->item('table_attributes_values_lang').'.id');
		$query = $this->obj->db->get();

		if ($query->num_rows() > 0)
		{
			$attributes_values = array();
			foreach ($query->result_array() as $row) {
				$attributes_values[] = $row;
			}
			return $attributes_values;
		}
	}

	public function list_products_attributes_values($params = array())
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> 'aNAME, avNAME',
			'where'		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_products_to_attributes_values'));
		//$this->obj->db->join($this->obj->config->item('table_attributes'), $this->obj->config->item('table_products_to_attributes_values').'.attributes_id = '.$this->obj->config->item('table_attributes').'.id');
		$this->obj->db->join($this->obj->config->item('table_attributes_lang'), $this->obj->config->item('table_products_to_attributes_values').'.attributes_id = '.$this->obj->config->item('table_attributes_lang').'.id');
		$this->obj->db->join($this->obj->config->item('table_attributes_values_lang'), $this->obj->config->item('table_products_to_attributes_values').'.attributes_values_id = '.$this->obj->config->item('table_attributes_values_lang').'.id');
		$query = $this->obj->db->get();

		if ($query->num_rows() > 0)
		{
			$products_attributes_values = array();
			foreach ($query->result_array() as $row) {
				$products_attributes_values[] = $row;
			}
			return $products_attributes_values;
		}
	}
	
	//-------------------------------- Manufacturers --------------------------------//

	public function get_manufacturers($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$query = $this->obj->db->get($this->obj->config->item('table_manufacturers'), 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function list_manufacturers($params = array(), $join_specials = false)
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> 'title asc',
			'start' 	=> 0,
			'limit' 	=> 20,
			'where'		=> null,
			'like'		=> null,
			'or_like'	=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if (!is_null($params['like']) && $params['like'] != '')
		{
			$strings = explode(' ', $params['like']);
			foreach($strings as $string)
			{
				$this->obj->db->like(array($this->obj->config->item('table_manufacturers').'.name' => $string));
			}

		}

		if (!is_null($params['or_like']) && $params['or_like'] != '')
		{
			$strings = explode(' ', $params['or_like']);
			foreach($strings as $string)
			{
				$this->obj->db->or_like(array($this->obj->config->item('table_manufacturers').'.title' => $string));
			}

		}

		if(!is_null($params['select']) && $params['select'] != '')
		{
			$this->obj->db->select($params['select']);
		}

		$this->obj->db->limit($params['limit'], $params['start']);

		if(!is_null($params['order_by']) && $params['order_by'] != '')
		{
			$this->obj->db->order_by($params['order_by']);
		}

		$this->obj->db->from($this->obj->config->item('table_manufacturers'));
		$query = $this->obj->db->get();
		$manufacturers = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$manufacturers[] = $row;
			}

			return $manufacturers;
		}
		else
		{
			return false;
		}
	}

	public function total_list_manufacturers($params = array(), $join_specials = false)
	{
		$default_params = array
		(
			'order_by' 	=> 'title asc',
			'where'		=> null,
			'like'		=> null,
			'or_like'	=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']) && $params['where'] != '')
		{
			foreach ($params['where'] as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		if (!is_null($params['like']) && $params['like'] != '')
		{
			$strings = explode(' ', $params['like']);
			foreach($strings as $string)
			{
				$this->obj->db->like(array($this->obj->config->item('table_manufacturers').'.name' => $string));
			}

		}

		if (!is_null($params['or_like']) && $params['or_like'] != '')
		{
			$strings = explode(' ', $params['or_like']);
			foreach($strings as $string)
			{
				$this->obj->db->or_like(array($this->obj->config->item('table_manufacturers').'.name' => $string));
			}

		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_manufacturers'));
		$query = $this->obj->db->get();

		return $query->num_rows();
	}



}
