<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->template['admin'] = true;
		$this->template['module'] = 'contact';
		$this->config->load('config');
		$this->load->library('administration');
		$this->load->model('contact_model', 'model');

        $this->fields_validation = array(
			array(
				'field'   => 	'contact_firstname',
				'label'   => 	$this->lang->line('validation_firstname'),
				'rules'   => 	'trim|required|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'contact_lastname',
				'label'   => 	$this->lang->line('validation_lastname'),
				'rules'   => 	'trim|required|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'contact_email',
				'label'   => 	$this->lang->line('validation_email'),
				'rules'   => 	'trim|required|max_length[128]|xss_clean|valid_email|callback__verify_email'
			),
			array(
				'field'   => 	'contact_phone',
				'label'   => 	$this->lang->line('validation_phone'),
				'rules'   => 	'trim|numeric|max_length[10]|xss_clean'
			)
		);

		$this->settings = isset($this->system->contact_settings) ? unserialize($this->system->contact_settings) : array();

		if (!$this->system->is_module_installed($this->template['module'])) redirect($this->config->item('admin_folder').'/unauthorized/'.$this->template['module']);
		if (!$this->system->is_module_actived($this->template['module'])) redirect($this->config->item('admin_folder').'/unauthorized/'.$this->template['module']);

	}

	public function settings()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if ($post = $this->input->post('submit'))
		{
			$setting = is_array($this->input->post('settings')) ? serialize($this->input->post('settings')) : '';
			$this->system->set('contact_settings', $setting);
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/settings');
		}
		else
		{
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/settings');
		}
	}

	public function index($start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$this->session->set_userdata('admin_redirect_uri', $this->uri->uri_string());

		$this->load->helper('format');

		$limit = $this->settings['per_page_contact'];
		$where = array();
		if ($filter = $this->input->post('filter'))
		{
			$where = array('firstname' => $filter, 'lastname' => $filter, 'email' => $filter);
		}

		$contacts = $this->model->list_contacts($where, array('limit' => $limit, 'start' => $start, 'where' => array('trash' => 0)));

		$total_contacts = $this->model->total_list_contacts($where, array('where' => array('trash' => 0)));

		$this->template['contacts'] = $contacts;
		$this->load->library('pagination');

		$config['uri_segment'] = 4;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/index');
		$config['total_rows'] = $total_contacts;
		$config['per_page'] = $limit;
		$config['num_links'] = $this->system->num_links;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total'] = $total_contacts;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/index');
	}

	public function mail($contacts_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'sitelib'));
		$this->template['mail'] = $this->model->get_contacts(array('id' => $contacts_id, 'trash' => 0));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/mail');
	}

	public function delete($contacts_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if (is_null($contacts_id))
		{
			redirect($this->session->userdata('redirect_uri'));
		}

		$this->db->where(array('id' => $contacts_id))->update($this->config->item('table_contact'), array('trash' => 1));
		$this->session->set_flashdata('notification', $this->lang->line('notification_contact_delete_success'));
		redirect($this->session->userdata('redirect_uri'));

	}

	public function export()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if($contacts = $this->model->list_contacts('', array('where' => array('trash' => 0))))
		{
			$csv = $this->lang->line('csv_firstname').";".$this->lang->line('csv_lastname').";".$this->lang->line('csv_email').";".$this->lang->line('csv_phone')."\n";
			foreach($contacts as $contact)
			{
				$csv .= $contact['firstname'].";".$contact['lastname'].";".$contact['email'].";".$contact['phone']."\n";
			}
			$this->load->helper('download');
			force_download(format_title($this->system->site_name).'-export-contact'.date("Y-m-d-h:i:s").'.csv', $csv);

		}
		else
		{
			redirect($this->config->item('admin_folder').'/contact');
		}
	}

	/*
	*
	* Callback functions
	*
	*/

	function _verify_email()
	{
		$id = $this->input->post('id');
		$email = $this->input->post('contact_email');
		//Check trash
		if($contact = $this->model->get_contacts(array('email' => $email, 'trash' => 1)))
		{
			$this->form_validation->set_message('_verify_email', $this->lang->line('alert_email_already_used_update').' <a href="'.site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/flag/'.$contact['id'].'/1').'">'.$this->lang->line('alert_email_already_used_activated').'</a> ?');
			return FALSE;
		}
		else
		{
			if ($this->model->exists(array('email' => $email, 'id !=' => $id)))
			{
				$this->form_validation->set_message('_verify_email', $this->lang->line('alert_email_already_used'));
				return FALSE;
			}
		}

	}

}
