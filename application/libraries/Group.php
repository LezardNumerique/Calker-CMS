<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

	class Group {

		var $tmplistgroups;
		function Group()
		{
			$this->obj =& get_instance();

		}

		public function get_groups($where = '', $params = array())
		{
			$default_params = array
			(
				'order_by' 	=> 'id',
				'limit' 	=> 5,
				'start' 	=> null,
				'limit' 	=> null
			);

			foreach ($default_params as $key => $value)
			{
				$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
			}

			$this->obj->db->order_by($params['order_by']);
			$this->obj->db->limit($params['limit'], $params['start']);

			if ( is_array($where) )
			{
				foreach ($where as $key => $value)
				{
					$this->obj->db->where($key, $value);
				}
			}
			$this->obj->db->get($this->obj->config->item('table_groups'));
			if ($query->num_rows() > 0 )
			{
				$row = $query->row_array();
				return $row;
			}
			else
			{
				return false;
			}
		}

		public function list_groups($where = '', $params = array())
		{
			$default_params = array
			(
				'order_by' 	=> 'id',
				'start' 	=> null,
				'limit' 	=> null,
				'where'		=> null,
				'where_and'	=> null,
			);

			foreach ($default_params as $key => $value)
			{
				$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
			}
			if (!is_null($params['where']))
			{
				foreach ($params['where'] as $key => $value)
				{
					$this->obj->db->where($key, $value);
				}
			}
			if (!is_null($params['where_and']))
			{
				foreach ($params['where_and'] as $key => $value)
				{
					$this->obj->db->where($key, $value);
				}
			}

			$this->obj->db->order_by($params['order_by']);

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

			$query = $this->obj->db->get($this->obj->config->item('table_groups'));

			if ($query->num_rows() > 0 )
			{
				foreach ($query->result_array() as $row) {
					$this->tmplistgroups[] = $row;
				}

				return $this->tmplistgroups;
			}
			else
			{
				return false;
			}
		}

		public function is_root($where)
		{
			if ( is_array($where) )
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

			$this->obj->db->where(array('id' => 1));
			$query = $this->obj->db->get($this->obj->config->item('table_groups'));

			if ($query->num_rows() > 0 )
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function exists($fields)
		{
			$query = $this->obj->db->get_where($this->obj->config->item('table_groups'), $fields, 1, 0);

			if($query->num_rows() == 1)
				return TRUE;
			else
				return FALSE;
		}
}