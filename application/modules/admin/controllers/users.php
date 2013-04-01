<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Users extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->config->load('config');
			$this->load->library('administration');
			$this->load->library('form_validation');

			$this->template['module'] = 'admin';

			$this->fields_validation = array(
				array(
					'field'   => 	'username',
					'label'   => 	$this->lang->line('validation_username'),
					'rules'   => 	'trim|required|min_length[4]|max_length[12]|xss_clean|callback__verify_username'
				),
				array(
					'field'   => 	'email',
					'label'   => 	$this->lang->line('validation_email'),
					'rules'   => 	'trim|required|valid_email|callback__verify_mail'
				)
			);

		}

		public function index($start = 0)
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);

			$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

			$limit = $this->system->per_page;
			$where = array();
			if($filter = $this->session->userdata('filter_users'))
			{
				$where = array('username' => $filter, 'email' => $filter);
			}
			if ($filter = $this->input->post('filter'))
			{
				$start = 0;
				$where = array('username' => $filter, 'email' => $filter);
				$this->session->set_userdata('filter_users', $filter);
			}
			if(isset($_POST['filter']) && $_POST['filter'] == '')
			{
				$this->session->unset_userdata('filter_users');
				redirect($this->config->item('admin_folder').'/users');
			}

			$users = $this->user->list_users($where, array('select' => '*, users.id as uID', 'order_by' => $this->config->item('table_users').'.id', 'limit' => $limit, 'start' => $start), true);

			$total_users = $this->user->total_list_users($where, true);

			$this->template['users'] = $users;
			$this->load->library('pagination');

			$config['num_links'] = $this->system->num_links;
			$config['uri_segment'] = 4;
			$config['first_link'] = $this->lang->line('text_begin');
			$config['last_link'] = $this->lang->line('text_end');
			$config['base_url'] = site_url($this->config->item('admin_folder').'/users/index');
			$config['total_rows'] = $total_users;
			$config['per_page'] = $limit;

			$this->pagination->initialize($config);

			$this->template['pager'] = $this->pagination->create_links();
			$this->template['total'] = $total_users;
			$this->template['start'] = $start;

			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'tooltip', 'tablesorter', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'users/index');
		}

		public function create()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$this->load->library('group');

			$fields_validation = $this->fields_validation;
			$fields_validation[2] = array(
				'field'   => 	'password',
				'label'   => 	$this->lang->line('validation_password'),
				'rules'   => 	'trim|matches[passconf]|required'
			);
			$fields_validation[3] = array(
				'field'   => 	'passconf',
				'label'   => 	$this->lang->line('validation_password_conf'),
				'rules'   => 	'trim|required|'
			);

			$this->form_validation->set_rules($fields_validation);

			$this->fields['username'] = $this->lang->line('validation_username');
			$this->fields['email'] = $this->lang->line('validation_email');
			$this->fields['password'] = $this->lang->line('validation_password');
			$this->fields['passconf'] = $this->lang->line('validation_password_conf');

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == FALSE)
			{
				$this->css->add(array('admin'));
				$this->javascripts->add(array('jquery', 'sitelib'));
				$this->template['user'] = array('id' => '', 'groups_id' => '', 'username' => '', 'email' => '', 'active' => '');

				if($this->user->root) $where = array();
				else $where = array('where' => array('id !=' => '1'));
				$this->template['groups'] = $this->group->list_groups('', $where);
				$this->layout->load($this->template, $this->config->item('theme_admin'), 'users/create');
			}
			else
			{
				$id = $this->user->register(
					strip_tags($this->input->post('username')),
					strip_tags($this->input->post('password')),
					$this->input->post('email'),
					strip_tags($this->input->post('active')),
					strip_tags($this->input->post('groups_id'))
				);

				$this->session->set_flashdata('notification', $this->lang->line('notification_user_create'));
				redirect($this->session->userdata('redirect_uri'));
			}
		}

		public function edit($users_id)
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);
			if($this->user->root != 1) $this->user->check_level_edit_rights(array('id' => $users_id));

			$this->load->library('group');

			$fields_validation = $this->fields_validation;

			if($this->input->post('password'))
			{
				$fields_validation[2] = array(
					'field'   => 	'password',
					'label'   => 	$this->lang->line('validation_password'),
					'rules'   => 	'trim|matches[passconf]|required'
				);
				$fields_validation[3] = array(
					'field'   => 	'passconf',
					'label'   => 	$this->lang->line('validation_password_conf'),
					'rules'   => 	'trim|required|'
				);
			}

			$this->form_validation->set_rules($fields_validation);

			$this->fields['email'] = $this->lang->line('validation_email');
			$this->fields['username'] = $this->lang->line('validation_username');
			$this->fields['password'] = $this->lang->line('validation_password');
			$this->fields['passconf'] = $this->lang->line('validation_password_conf');

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == FALSE)
			{
				$this->css->add(array('admin'));
				$this->javascripts->add(array('jquery', 'sitelib'));
				$this->template['user'] = $this->user->get_users(array('id' => $users_id));
				if($this->user->root) $where = array();
				else $where = array('where' => array('id !=' => '1'));
				$this->template['groups'] = $this->group->list_groups('', $where);
				$this->layout->load($this->template, $this->config->item('theme_admin'), 'users/create');
			}
			else
			{
				$data = array(
					'username' => strip_tags($this->input->post('username')),
					'email' => $this->input->post('email')
				);

				if(isset($_POST['active'])) $data['active'] = $this->input->post('active');
				if($this->input->post('groups_id')) $data['groups_id'] = $this->input->post('groups_id');

				if($post_password = $this->input->post('password')) $data['password'] = $this->input->post('password');

				$this->user->update($this->input->post('id'), $data);
				$this->session->set_flashdata('notification', $this->lang->line('notification_user_edit_success'));
				redirect($this->session->userdata('redirect_uri'));
			}
		}

		public function delete($users_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);
			if($this->user->root != 1) $this->user->check_level_edit_rights(array('id' => $users_id));

			if (is_null($users_id))
			{
				$this->session->set_flashdata('alert', $this->lang->line('alert_user_name_status_required'));
				redirect($this->config->item('admin_folder').'/user');
			}

			if ($users_id == $this->user->id)
			{
				$this->session->set_flashdata('alert', $this->lang->line('alert_impossible_to_deleted'));
				redirect($this->config->item('admin_folder').'/users');

			}

			$this->db->delete($this->config->item('table_users'), array('id' => $users_id));
			$this->session->set_flashdata('notification', $this->lang->line('notification_user_delete_success'));
			redirect($this->session->userdata('redirect_uri'));

		}

		public function flag($users_id = null, $fromstatus = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			if($this->user->root != 1) $this->user->check_level_edit_rights(array('id' => $users_id));

			if ($users_id == $this->user->id)
			{
				$this->session->set_flashdata('alert', $this->lang->line('alert_impossible_to_flag'));
				redirect($this->config->item('admin_folder').'/users');

			}

			$limit = $this->system->per_page;
			if (is_null($users_id) || is_null($fromstatus))
			{
				$this->session->set_flashdata('alert', $this->lang->line('alert_username_status'));
				redirect($this->config->item('admin_folder').'/users');
			}
			if ($fromstatus == '1')
			{
				$data['active'] = 1;
			}
			else
			{
				$data['active'] = 0;
			}
			$this->user->update($users_id, $data);
			$this->session->set_flashdata('notification', $this->lang->line('notification_user_status_update'));
			redirect($this->session->userdata('redirect_uri'));
		}

		/*
		 *
		 * Callback functions
		 *
		 */

		function _verify_username($data)
		{
			$username = $this->input->post('username');
			$id = $this->input->post('id');

			if ($this->user->exists(array('username' => $username, 'id !=' => $id)))
			{
				$this->form_validation->set_message('_verify_username', $this->lang->line('alert_user_name_already_used'));
				return FALSE;
			}

		}

		function _verify_mail($data)
		{
			$email = $this->input->post('email');
			$id = $this->input->post('id');

			if ($this->user->exists(array('email' => $email, 'id !=' => $id)))
			{
				$this->form_validation->set_message('_verify_mail', $this->lang->line('alert_user_email_already_used'));
				return FALSE;
			}
		}

	}