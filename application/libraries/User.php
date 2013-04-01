<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class User {

		var $id = 0;
		var $groups_id = 0;
		var $username = '';
		var $email = '';
		var $name = '';
		var $logged_in = false;
		var $level = array();
		var $root = false;
		var $liveView = false;

		public function User()
		{
			$this->obj =& get_instance();
			$this->obj->load->library('encrypt');
			$this->_session_to_library();
			if($this->obj->uri->segment(1) == $this->obj->config->item('admin_folder') || $this->obj->uri->segment(2) == $this->obj->config->item('admin_folder'))
				$this->_get_levels();
			if($this->obj->uri->segment(1) == $this->obj->config->item('admin_folder') || $this->obj->uri->segment(2) == $this->obj->config->item('admin_folder'))
				$this->_update_fields();

			//---- Brut force
			if(($this->obj->session->userdata('brut_force') >= 5 || get_cookie('ccmsbf') >= 5) && $this->obj->uri->segment(1) != 'pages' && $this->obj->uri->segment(2) != 'unauthorized')
				redirect('pages/unauthorized');

		}
		public function login($username, $password)
		{
			$this->obj->load->library('group');
			$this->_destroy_session();
			$this->obj->db->where('username', $username);
			$this->obj->db->where('password', $this->_prep_password($password));
			$this->obj->db->where('active', 1);
			$query = $this->obj->db->get($this->obj->config->item('table_users'), 1);

			if ($query->num_rows() == 1)
			{
				$user = $query->row();
				$user->root = 0;
				$user->liveView = false;
				if($is_root = $this->obj->group->is_root(array('id' => $user->id))) $user->root = 1;
				$this->_start_session($user);
				$this->obj->session->set_flashdata('notification', $this->obj->lang->line('notification_login_success'));
				return true;
			}
			else
			{
				$this->_destroy_session();
				$this->obj->system->set_session_brut_force();
				$this->obj->system->set_cookie_brut_force();
				$this->obj->session->set_flashdata('alerte', $this->obj->lang->line('notification_login_failed'));
				return false;
			}
		}
		public function logout()
		{
			$this->update($this->email, array('online' => 0));
			$last_uri = $this->obj->session->userdata('last_uri');
			$this->_destroy_session();
			$this->obj->session->set_userdata(array('last_uri' => $last_uri));
			$this->obj->session->set_flashdata('notification', $this->obj->lang->line('notification_logout_success'));
		}
		public function register($username, $password, $email, $active, $groups_id)
		{
			$data	= 	array(
				'username'		=> strip_tags($username),
				'password'		=> $this->_prep_password($password),
				'email'			=> $email,
				'active'		=> strip_tags($active),
				'registered'	=> mktime(),
				'groups_id'		=> strip_tags($groups_id)
			);

			$query = $this->obj->db->insert($this->obj->config->item('table_users'), $data);

			return $this->obj->db->insert_id();
		}
		public function update($id, $data)
		{
			if (isset($data['password']))
			{
				$data['password'] = $this->_prep_password($data['password']);
			}
			$this->obj->db->where('id', $id);
			$this->obj->db->set($data);
			$this->obj->db->update($this->obj->config->item('table_users'));
		}
		public function check_level($module, $level)
		{
			if (!isset($this->obj->user->level[$module]) || $this->obj->user->level[$module] < $level)
			{
				if ($this->obj->uri->segment(1) == 'admin' || $this->obj->uri->segment(2) == 'admin')
				{
					redirect($this->obj->config->item('theme_admin').'/unauthorized/'. $module . '/' . $level);
				}
			}
		}

		public function get_users($where = '')
		{
			$this->obj->db->select('*');
			if ( is_array($where) )
			{
				foreach ($where as $key => $value)
				{
					$this->obj->db->where($key, $value);
				}
			}
			$query = $this->obj->db->get($this->obj->config->item('table_users'), 1);
			if ( $query->num_rows() == 1 )
			{
				$row = $query->row_array();
				return $row;
			}
			else
			{
				return false;
			}
		}

		public function list_users($or_like = null, $params = array(), $join_groups = false)
		{
			$default_params = array
			(
				'select' 		=> '*',
				'order_by' 		=> 'id',
				'limit' 		=> 5,
				'start' 		=> null,
				'limit' 		=> null
			);

			foreach ($default_params as $key => $value)
			{
				$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
			}

			if (!is_null($params['select']))
			{
				$this->obj->db->select($params['select']);
			}

			$this->obj->db->order_by($params['order_by']);
			$this->obj->db->limit($params['limit'], $params['start']);

			if ( is_array($or_like) )
			{
				foreach ($or_like as $key => $value)
				{
					$this->obj->db->or_like($key, $value);
				}
			}

			$users = array();
			$query = $this->obj->db->from($this->obj->config->item('table_users'));

			if($join_groups) $query = $this->obj->db->join($this->obj->config->item('table_groups'), $this->obj->config->item('table_users').'.groups_id = '.$this->obj->config->item('table_groups').'.id');
			$query = $this->obj->db->get();

			if ($query->num_rows() > 0 )
			{
				foreach ($query->result_array() as $row) {
					$users[] = $row;
				}

				return $users;
			}
			else
			{
				return false;
			}
		}

		public function total_list_users($or_like = null, $join_groups = false)
		{

			$this->obj->db->select($this->obj->config->item('table_users').'.id');

			if ( is_array($or_like) )
			{
				foreach ($or_like as $key => $value)
				{
					$this->obj->db->or_like($key, $value);
				}
			}

			$query = $this->obj->db->from($this->obj->config->item('table_users'));

			if($join_groups) $query = $this->obj->db->join($this->obj->config->item('table_groups'), $this->obj->config->item('table_users').'.groups_id = '.$this->obj->config->item('table_groups').'.id');
			$query = $this->obj->db->get();

			return $query->num_rows();
		}

		public function exists($fields)
		{
			$query = $this->obj->db->get_where($this->obj->config->item('table_users'), $fields, 1, 0);

			if($query->num_rows() == 1)
				return TRUE;
			else
				return FALSE;
		}

		public function check_level_edit_rights($where = '')
		{
			if($user = $this->get_users($where, false))
			{
				if($user['username'] == 'root')
				{
					$this->obj->session->set_flashdata('alert', $this->obj->lang->line('alert_user_level_root_no_access'));
					redirect($this->obj->config->item('theme_admin').'/users');
				}
			}

		}

		/*
		 *
		 * Private functions
		 *
		 */
		function _update_fields()
		{
			if ($this->logged_in)
			{
				$this->update($this->id, array('lastvisit' => mktime(), 'online' => 1));
			}
		}
		function _session_to_library()
		{
			$this->id 				= $this->obj->session->userdata('id');
			$this->groups_id 		= $this->obj->session->userdata('groups_id');
			$this->email			= $this->obj->session->userdata('email');
			$this->name				= $this->obj->session->userdata('name');
			$this->username			= $this->obj->session->userdata('username');
			$this->logged_in 		= $this->obj->session->userdata('logged_in');
			$this->root 			= $this->obj->session->userdata('root');
			$this->lang 			= $this->obj->session->userdata('lang');
			$this->liveView			= $this->obj->session->userdata('liveView');
		}
		function _start_session($user)
		{
			$data = array(
				'id' 			=> $user->id,
				'groups_id' 	=> $user->groups_id,
				'email'			=> $user->email,
				'name'			=> $this->name,
				'username'		=> $user->username,
				'logged_in'		=> true,
				'root'			=> $user->root,
				'liveView'		=> $user->liveView,
			);
			$this->obj->session->set_userdata($data);
		}
		function _destroy_session()
		{
			$data = array(
				'id' 			=> 0,
				'groups_id' 	=> 0,
				'email' 		=> '',
				'name' 			=> '',
				'username' 		=> '',
				'logged_in'		=> false,
				'root'			=> false,
				'liveView'		=> false
			);
			$this->obj->session->set_userdata($data);
			foreach ($data as $key => $value)
			{
				$this->$key = $value;
			}
		}
		function _get_levels()
		{
			$admin = array();
			if ($this->logged_in)
			{
				$this->obj->db->where('groups_id =', $this->groups_id);
				$query = $this->obj->db->get($this->obj->config->item('table_rights'));
				if ($rows = $query->result_array())
				{
					foreach($rows as $val)
					{
						$admin[$val['module']] = $val['level'];
					}
				}
			}

			if (is_array($this->obj->system->modules))
			{
				foreach($this->obj->system->modules as $module)
				{
					if (!isset($admin[$module['name']]))
					{
						$admin[$module['name']] = 0;
					}
				}
			}

			$this->level = $admin;
		}

		function _prep_password($password)
		{
			return $this->obj->encrypt->sha1($password.$this->obj->config->item('encryption_key'));
		}

}