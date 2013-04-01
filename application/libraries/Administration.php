<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

	class Administration {

		function Administration()
		{
			$this->obj =& get_instance();
			$this->obj->load->helper('dashboard');

			if (!$this->obj->user->logged_in && $this->obj->uri->segment(2) != 'login')
			{
				$this->obj->session->set_flashdata('redirect', $this->obj->uri->uri_string());
				redirect($this->obj->config->item('admin_folder').'/login');
			}

			if ($this->obj->user->logged_in && count($this->obj->user->level) == 0 )
			{
				$this->obj->session->set_flashdata('notification', $this->obj->lang->line('notification_not_admin'));
				$this->obj->user->logout();
				redirect($this->obj->config->item('admin_folder').'/login');
			}
		}

		public function no_active_users()
		{
			$this->obj->db->where('active', 1);
			$query = $this->obj->db->get($this->obj->config->item('table_users'));
			return $query->num_rows();
		}

		public function db_size()
		{
			$sql = 'SHOW TABLE STATUS';

			$query = $this->obj->db->query($sql);
			$result = $query->result_array();

			foreach ($result as $row)
			{
				$db_size = $row['Data_length'] + $row['Index_length'];
			}

			return $db_size;

		}
	}