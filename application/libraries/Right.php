<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

	class Right
	{
		function Right()
		{
			$this->obj =& get_instance();

		}
		public function get_rights($where = '', $join_right = true)
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

			$this->obj->db->from($this->obj->config->item('table_rights'));

			if($join_right)
			{
				$this->obj->db->select('*, '.$this->obj->config->item('table_rights').'.id as rID');
				$this->obj->db->order_by('title');
				$this->obj->db->join($this->obj->config->item('table_groups'), $this->obj->config->item('table_rights').'.groups_id = '.$this->obj->config->item('table_groups').'.id');
			}

			$query = $this->obj->db->get();

			if($rows = $query->result_array())
			{
				$rights = array();
				foreach ( $rows as $right )
				{
					$result[] = $right;
				}
				return $result;
			}
		}
		public function list_rights()
		{
			$this->obj->db->order_by('module ASC, level DESC');
			$query = $this->obj->db->get($this->obj->config->item('table_rights'));
			return $query->result_array();
		}

		public function check_level_edit_rights($id)
		{
			if($right = $this->get_rights(array('id' => $id), false))
			{
				if($right['username'] != 'root' && $right['username'] != 'admin')
				{
					$this->obj->session->set_flashdata('notification', $this->obj->lang->line('notification_user_level_root_no_access'));
					redirect($this->obj->config->item('theme_admin').'/rights');
				}
			}

		}
	}