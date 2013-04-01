<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Portfolios {

	var $categories;

	function Portfolios ()
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
		$this->obj->db->from($this->obj->config->item('table_portfolio_categories'));
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories_lang'), $this->obj->config->item('table_portfolio_categories').'.id = '.$this->obj->config->item('table_portfolio_categories_lang').'.categories_id');
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

	public function get_categories_uri_parent()
	{
		$this->obj->db->select('uri');
		$this->obj->db->where(array('lang' => $this->obj->user->lang, 'categories_id' => 1));
		$this->obj->db->from($this->obj->config->item('table_portfolio_categories_lang'));
		$query = $this->obj->db->get('', 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row['uri'];
		}
		else
		{
			return 'accueil';
		}
	}

	public function list_categories($parent = 0, $level = 0, $where = '', $limit = 1000, $start = 0, $recursiv = true)
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
		if($limit && $start) $this->obj->db->limit($limit, $start);
		$this->obj->db->from($this->obj->config->item('table_portfolio_categories'));
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories_lang'), $this->obj->config->item('table_portfolio_categories').'.id = '.$this->obj->config->item('table_portfolio_categories_lang').'.categories_id');
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

	public function total_categories($parent = 0, $level = 0, $where)
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
		$this->obj->db->from($this->obj->config->item('table_portfolio_categories'));
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories_lang'), $this->obj->config->item('table_portfolio_categories').'.id = '.$this->obj->config->item('table_portfolio_categories_lang').'.categories_id');

		return $this->obj->db->count_all_results();
	}

	//-------------------------------- Medias --------------------------------//

	public function get_medias($where = '')
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
		$this->obj->db->where('lang', $this->obj->user->lang);
		$this->obj->db->from($this->obj->config->item('table_portfolio_medias'));
		$this->obj->db->join($this->obj->config->item('table_portfolio_medias_lang'), $this->obj->config->item('table_portfolio_medias').'.id = '.$this->obj->config->item('table_portfolio_medias_lang').'.medias_id');
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

	public function list_medias($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'start' 		=> 0,
			'limit' 		=> 20,
			'order_by' 		=> 'id DESC',
			'where' 		=> null,
			'like'			=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']))
		{
			$this->obj->db->where($params['where']);
		}

		if (!is_null($params['like']) && $params['like'] != '')
		{
			if(is_numeric($params['like']))
			{
				$this->obj->db->where(array($this->obj->config->item('table_portfolio_medias').'.id' => $params['like']));
			}
			else
			{
				$strings = explode(' ', $params['like']);
				foreach($strings as $string)
				{
					$this->obj->db->like(array($this->obj->config->item('table_portfolio_categories_lang').'.title' => $string));
					$this->obj->db->or_like(array($this->obj->config->item('table_portfolio_medias_lang').'.title' => $string));
				}
			}

		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->select($params['select']);
		$this->obj->db->where(array($this->obj->config->item('table_portfolio_categories_lang').'.lang' => $this->obj->user->lang));
		$this->obj->db->where(array($this->obj->config->item('table_portfolio_medias_lang').'.lang' => $this->obj->user->lang));
		$this->obj->db->limit($params['limit'], $params['start']);

		$this->obj->db->from($this->obj->config->item('table_portfolio_medias'));
		$this->obj->db->join($this->obj->config->item('table_portfolio_medias_lang'), $this->obj->config->item('table_portfolio_medias').'.id = '.$this->obj->config->item('table_portfolio_medias_lang').'.medias_id');
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories_to_medias'), $this->obj->config->item('table_portfolio_medias').'.id = '.$this->obj->config->item('table_portfolio_categories_to_medias').'.medias_id');
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories'), $this->obj->config->item('table_portfolio_categories').'.id = '.$this->obj->config->item('table_portfolio_categories_to_medias').'.categories_id');
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories_lang'), $this->obj->config->item('table_portfolio_categories').'.id = '.$this->obj->config->item('table_portfolio_categories_lang').'.categories_id');
		$this->obj->db->group_by($this->obj->config->item('table_portfolio_medias').'.id');
		$query = $this->obj->db->get();

		$medias = array();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$medias[] = $row;
			}
			return $medias;
		}
		else
		{
			return false;
		}

	}

	public function total_list_medias($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']))
		{
			$this->obj->db->where($params['where']);
		}

		$this->obj->db->select($params['select']);
		$this->obj->db->where(array($this->obj->config->item('table_portfolio_categories_lang').'.lang' => $this->obj->user->lang));
		$this->obj->db->where(array($this->obj->config->item('table_portfolio_medias_lang').'.lang' => $this->obj->user->lang));
		$this->obj->db->from($this->obj->config->item('table_portfolio_medias'));
		$this->obj->db->join($this->obj->config->item('table_portfolio_medias_lang'), $this->obj->config->item('table_portfolio_medias').'.id = '.$this->obj->config->item('table_portfolio_medias_lang').'.medias_id');
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories_to_medias'), $this->obj->config->item('table_portfolio_medias').'.id = '.$this->obj->config->item('table_portfolio_categories_to_medias').'.medias_id');
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories'), $this->obj->config->item('table_portfolio_categories').'.id = '.$this->obj->config->item('table_portfolio_categories_to_medias').'.categories_id');
		$this->obj->db->join($this->obj->config->item('table_portfolio_categories_lang'), $this->obj->config->item('table_portfolio_categories').'.id = '.$this->obj->config->item('table_portfolio_categories_lang').'.categories_id');
		$this->obj->db->group_by($this->obj->config->item('table_portfolio_medias').'.id');
		$query = $this->obj->db->get();
		return $query->num_rows();
	}

	public function list_categories_to_medias($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'ordering ASC, medias_id DESC',
			'where' 		=> null,
			'where_in'		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']))
		{
			$this->obj->db->where($params['where']);
		}

		if (!is_null($params['where_in']))
		{
			$this->obj->db->where_in('categories_id', $params['where_in']);
		}

		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->select($params['select']);

		$this->obj->db->from($this->obj->config->item('table_portfolio_categories_to_medias'));
		$query = $this->obj->db->get();

		//echo $this->obj->db->last_query();

		$categories_to_medias = array();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$categories_to_medias[$row['categories_id']] = $row;
			}
			return $categories_to_medias;
		}
		else
		{
			return false;
		}

	}

	public function delete_medias_files($medias_id = '')
	{
		if($media = $this->get_medias(array('id' => $medias_id)))
		{
			if($media['file'] && is_readable('./'.$this->obj->config->item('medias_folder').'/images/'.$media['file']))
			{
				unlink('./'.$this->obj->config->item('medias_folder').'/images/'.$media['file']);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
