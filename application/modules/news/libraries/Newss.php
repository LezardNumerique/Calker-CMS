<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Newss {

	function Newss ()
	{
		$this->obj =& get_instance();
	}

	//-------------------------------- News --------------------------------//

	public function get_news($where = '')
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
		$this->obj->db->from($this->obj->config->item('table_news'));
		$this->obj->db->join($this->obj->config->item('table_news_lang'), $this->obj->config->item('table_news').'.id = '.$this->obj->config->item('table_news_lang').'.news_id');
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

	public function list_news($params = array())
	{
		$default_params = array
		(
			'select' 	=> '*',
			'order_by' 	=> 'id desc, date_added desc',
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
				$this->obj->db->like(array($this->obj->config->item('table_news').'.title' => $string));
			}

		}

		if (!is_null($params['or_like']) && $params['or_like'] != '')
		{
			$strings = explode(' ', $params['or_like']);
			foreach($strings as $string)
			{
				$this->obj->db->or_like(array($this->obj->config->item('table_news').'.title' => $string));
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
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->from($this->obj->config->item('table_news'));
		$this->obj->db->join($this->obj->config->item('table_news_lang'), $this->obj->config->item('table_news').'.id = '.$this->obj->config->item('table_news_lang').'.news_id');
		$query = $this->obj->db->get();
		$list_products = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$list_products[] = $row;
			}

			return $list_products;
		}
		else
		{
			return false;
		}
	}

	public function total_list_news($params = array())
	{
		$default_params = array
		(
			'order_by' 	=> 'id desc, date_added desc',
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
				$this->obj->db->like(array($this->obj->config->item('table_news').'.title' => $string));
			}
		}

		if (!is_null($params['or_like']) && $params['or_like'] != '')
		{
			$strings = explode(' ', $params['or_like']);
			foreach($strings as $string)
			{
				$this->obj->db->or_like(array($this->obj->config->item('table_news').'.title' => $string));
			}

		}
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->order_by($params['order_by']);
		$this->obj->db->from($this->obj->config->item('table_news'));
		$this->obj->db->join($this->obj->config->item('table_news_lang'), $this->obj->config->item('table_news').'.id = '.$this->obj->config->item('table_news_lang').'.news_id');
		$query = $this->obj->db->get();

		return $query->num_rows();
	}

}
