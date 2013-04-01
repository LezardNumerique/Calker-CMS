<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Groups extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->config->load('config');
		$this->load->library('administration');
		$this->load->library('form_validation');
		$this->template['module'] = 'admin';

		$this->fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->lang->line('validation_title'),
				'rules'   => 	'trim|required|min_length[4]|max_length[64]|xss_clean|callback__verify_title'
			)
		);
	}

	function index($start = 0, $id = null)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);
		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());
		$this->load->library('group');
		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'tooltip', 'tablesorter', 'sitelib'));
		if (!$groups = $this->cache->get('groups', 'groups'))
		{
			if (!$groups = $this->group->list_groups('', array())) $admins = array();
			if($this->system->cache == 1) $this->cache->save('groups', $groups, 'groups', 0);
		}
		$this->template['groups'] = $groups;
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'groups/index');

	}

	public function create()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'sitelib'));
		$this->template['group'] = array('id' => '', 'title' => '', 'level' => '');
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'groups/create');
	}

	public function edit($id)
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);
		$this->db->where('id', $id);
		$query = $this->db->get($this->config->item('table_groups'));
		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'sitelib'));
		$this->template['group'] = $query->row_array();
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'groups/create');
	}

	public function save($id = null)
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$this->load->library('group');

		if ($id == '1' || $id == '2')
		{
			$this->session->set_flashdata('alert', $this->lang->line('alert_operation_not_supported'));
			redirect($this->config->item('admin_folder').'/groups');
		}

		if ($this->input->post('title') == 'root' || $this->input->post('title') == 'admin')
		{
			$this->session->set_flashdata('alert', $this->lang->line('alert_operation_not_supported'));
			redirect($this->config->item('admin_folder').'/groups');
		}

		$fields_validation = $this->fields_validation;
		$this->form_validation->set_rules($fields_validation);

		$this->fields['title'] = $this->lang->line('validation_title');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == TRUE)
		{
			$this->db->where(array(
				'title' => $this->input->post('title')
				)
			);
			$query = $this->db->get($this->config->item('table_groups'));
			$data = array(
				'title' => strtolower(strip_tags($this->input->post('title')))
			);
			if ($query->num_rows() > 0)
			{
				$this->db->where(array(
					'title' => $this->input->post('title')
					)
				);
				$this->db->update($this->config->item('table_groups'), $data);
				$this->cache->remove_group('groups');
				$this->session->set_flashdata('notification', $this->lang->line('notification_group_update_success'));
			}
			else
			{
				$this->db->insert($this->config->item('table_groups'), $data);
				$this->cache->remove_group('groups');
				$this->session->set_flashdata('notification', $this->lang->line('notification_group_create_success'));
			}
			redirect($this->session->userdata('redirect_uri'));
		}
		else
		{
			$this->session->set_flashdata('alerte', validation_errors());
			$this->session->set_flashdata('post', $this->input->post());
			redirect($this->input->post('redirect_uri_error'));
		}

	}

	public function delete($id)
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		if ($id == '1' || $id == '2')
		{
			$this->session->set_flashdata('alert', $this->lang->line('alert_operation_not_supported'));
			redirect($this->config->item('admin_folder').'/groups');
		}
		$this->db->where('id', $id);
		$query = $this->db->get($this->config->item('table_groups'));
		if ($query->num_rows == 1)
		{
			$row = $query->row_array();

			$this->db->where('id', $id);
			$this->db->delete($this->config->item('table_groups'));
			$this->cache->remove_group('groups');
			$this->session->set_flashdata('notification', $this->lang->line('notification_group_delete_success'));
		}
		else
		{
			$this->session->set_flashdata('alert', $this->lang->line('alert_group_not_found'));
		}
		redirect($this->session->userdata('redirect_uri'));
	}

	/*
	*
	* Callback functions
	*
	*/

	function _verify_title($data)
	{
		$title = $this->input->post('title');
		$id = $this->input->post('id');

		if ($this->group->exists(array('title' => $title, 'id !=' => $id)))
		{
			$this->form_validation->set_message('_verify_title', $this->lang->line('alert_language_title_already_used'));
			return FALSE;
		}

	}
}