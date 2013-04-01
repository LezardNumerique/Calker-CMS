<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Rights extends CI_Controller
	{
		var $levels;

		public function __construct()
		{
			parent::__construct();
			$this->config->load('config');
			$this->load->library('administration');
			$this->load->model('admin_model', 'model');
			$this->template['module'] = 'admin';
			$this->template['levels'] = array(
				0 => $this->lang->line('text_level_no_access'),
				1 => $this->lang->line('text_level_can_view'),
				2 => $this->lang->line('text_level_can_create'),
				3 => $this->lang->line('text_level_can_edit'),
				4 => $this->lang->line('text_level_can_delete')
			);
			$this->user->check_level($this->template['module'], LEVEL_EDIT);
		}

		public function index()
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);
			$this->load->library('right');
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'tooltip', 'sitelib'));
			if (!$modules = $this->cache->get('list_modules', 'modules'))
			{
				if (!$modules = $this->model->list_modules()) $modules = array();
				if($this->system->cache == 1) $this->cache->save('list_modules', $modules, 'modules', 0);
			}
			$this->template['modules'] = $modules;
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'rights/index');
		}

		public function create()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			$this->load->library('group');

			$this->template['right'] = array('id' => '', 'groups_id' => '', 'username' => '', 'level' => '', 'module' => '');
			$where = array('where' => array('id !=' => '1'));
			if($this->user->root == 1) $where = array('where' => array());
			$this->template['groups'] = $this->group->list_groups('', $where);

			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'rights/create');
		}

		public function edit($id)
		{
			$this->load->library('group');
			$this->load->library('right');

			$this->user->check_level($this->template['module'], LEVEL_EDIT);
			if($this->user->root != 1) $this->right->check_level_edit_rights($id);

			$this->db->where('id', $id);
			$query = $this->db->get($this->config->item('table_rights'));

			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->template['right'] = $query->row_array();
			$where = array('where' => array('id !=' => '1'));
			if($this->user->root == 1) $where = array();
			$this->template['groups'] = $this->group->list_groups('', array('where' => $where));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'rights/create');
		}

		public function save($id = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			if ($this->input->post('group') < $this->user->groups_id)
			{
				$this->session->set_flashdata('alert', $this->lang->line('alert_operation_not_supported'));
				redirect($this->config->item('admin_folder').'/rights');
			}
			if ($this->input->post('submit'))
			{
				$this->db->where(array(
					'groups_id' => $this->input->post('group'),
					'module' => $this->input->post('module'))
				);
				$query = $this->db->get($this->config->item('table_rights'));
				$data = array(
					'groups_id' => strip_tags($this->input->post('group')),
					'module' => strip_tags($this->input->post('module')),
					'level' => strip_tags($this->input->post('level'))
				);
				if ($query->num_rows() > 0)
				{
					$this->db->where(array(
						'groups_id' => $this->input->post('group'),
						'module' => $this->input->post('module'))
					);
					$this->db->update($this->config->item('table_rights'), $data);
					$this->session->set_flashdata('notification', $this->lang->line('notification_user_level_update_success'));
				}
				else
				{
					$this->db->insert($this->config->item('table_rights'), $data);
					$this->session->set_flashdata('notification', $this->lang->line('notification_user_level_create_success'));
				}
			}

			redirect($this->config->item('admin_folder').'/rights');
		}

		public function delete($id)
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);
			if($this->user->root != 1) $this->right->check_level_edit_rights($id);

			$this->db->where('id', $id);
			$query = $this->db->get($this->config->item('table_rights'));
			if ($query->num_rows == 1)
			{
				$row = $query->row_array();

				if ($row['groups_id'] == 1 || $row['groups_id'] == 2)
				{
					$this->session->set_flashdata('alert', $this->lang->line('alert_user_delete_impossible'));
				}
				else
				{
					$this->db->where('id', $id);
					$this->db->delete($this->config->item('table_rights'));
					$this->session->set_flashdata('notification', $this->lang->line('notification_user_level_delete_success'));
				}
			}
			else
			{
				$this->session->set_flashdata('alert', $this->lang->line('alert_user_not_found'));
			}
			redirect($this->config->item('admin_folder').'/rights');
		}
	}