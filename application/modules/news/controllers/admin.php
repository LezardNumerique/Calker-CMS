<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->template['admin'] = true;
		$this->template['module'] = 'news';
		$this->config->load('config');
		$this->load->library('administration');
		$this->load->library('form_validation');
		$this->load->library('newss', '', 'news');
		$this->load->model('news_model', 'model');

		$this->news->settings = isset($this->system->news_settings) ? unserialize($this->system->news_settings) : array();

		$this->fields_validation_news = array(
			array(
				'field'   => 	'id',
				'label'   => 	$this->lang->line('validation_id'),
				'rules'   => 	'trim|numeric'
			),
			array(
				'field'   => 	'active',
				'label'   => 	$this->lang->line('validation_active'),
				'rules'   => 	'trim|numeric|max_length[1]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'title',
				'label'   => 	$this->lang->line('validation_title'),
				'rules'   => 	'trim|required|max_length[128]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'uri',
				'label'   => 	$this->lang->line('validation_uri'),
				'rules'   => 	'trim|max_length[128]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'body',
				'label'   => 	$this->lang->line('validation_body'),
				'rules'   => 	'trim|xss_clean'
			),
			array(
				'field'   => 	'meta_title',
				'label'   => 	$this->lang->line('validation_meta_title'),
				'rules'   => 	'trim|max_length[128]|htmlspecialchars|xss_clean'
			),			
			array(
				'field'   => 	'meta_keywords',
				'label'   => 	$this->lang->line('validation_meta_keywords'),
				'rules'   => 	'trim|max_length[255]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'meta_description',
				'label'   => 	$this->lang->line('validation_meta_description'),
				'rules'   => 	'trim|max_length[255]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'news_redirect',
				'label'   => 	$this->lang->line('validation_redirect'),
				'rules'   => 	'trim|numeric|max_length[1]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'news_tabs',
				'label'   => 	$this->lang->line('validation_tabs'),
				'rules'   => 	'trim|htmlspecialchars|htmlspecialchars|xss_clean'
			)
		);

	}

	//---------------------------------------------- Settings

	public function settings()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);		

		if ($post = $this->input->post('submit'))
		{
			$settings = is_array($this->input->post('settings')) ? serialize($this->input->post('settings')) : '';
			$this->system->set('news_settings', $settings);
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/settings');
		}
		else
		{
			$this->load->library('medias');
			$this->template['medias_types'] = $this->medias->list_medias_types(array('where' => array('theme' => $this->system->theme)));
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/settings');
		}
	}

	//---------------------------------------------- News
	public function index($start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

		$this->load->helper('format');

		$per_page = $this->news->settings['per_page_news'];

		$news = $this->news->list_news(array('select' => '*', 'start' => $start, 'limit' => $per_page));
		$total_news =  $this->news->total_list_news();

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = 4;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/index');
		$config['total_rows'] = $total_news;
		$config['per_page'] = $per_page;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total'] = $total_news;
		$this->template['start'] = $start;

		$this->template['filter'] = TRUE;
		$this->template['news'] = $news;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/index');
	}

	public function newsCreate()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation_news);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$new = array(
				'id'					=> '',
				'active'				=> 0,
				'title'					=> '',
				'uri'					=> '',
				'body'					=> '',
				'meta_title'			=> '',
				'meta_keywords'			=> '',
				'meta_description'		=> ''
			);

			$this->template['new'] = $new;			

			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'tinymce', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/create');
		}
		else
		{
			$news_id = $this->model->save_news();
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			
			if(set_value('news_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/newsEdit/'.$news_id.set_value('news_tabs'));				
		}
	}

	public function newsEdit($news_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation_news);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->library('medias');
			$this->template['new'] = $this->news->get_news(array('id' => $news_id));
			$this->template['media'] = $this->medias->get_medias(array('src_id' => $news_id, 'module' => $this->template['module']));
			
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'tinymce', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/create');
		}
		else
		{
			$news_id = $this->model->save_news();
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			if(set_value('news_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/newsEdit/'.$news_id.set_value('news_tabs'));	
		}
	}

	public function newsDelete($news_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->model->delete_news($news_id);
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri'));
	}

	public function newsFlag($news_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);
		$this->system->flag($news_id, $flag, $this->config->item('table_news'), 'active');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri'));
	}

	public function createLiveView()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation_news);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			if($_POST)
			{
				$this->session->set_flashdata('alerte', validation_errors());
				redirect($this->session->userdata('redirect_admin_live_view'));
			}

			$new = array(
				'id'					=> '',
				'active'				=> 0,
				'title'					=> '',
				'uri'					=> '',
				'body'					=> '',
				'meta_title'			=> '',
				'meta_keywords'			=> '',
				'meta_description'		=> ''
			);

			$this->template['new'] = $new;

			$this->load->view('admin/create-live-view', $this->template);
		}
		else
		{
			$this->model->save_news();
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->session->userdata('redirect_admin_live_view'));
		}
	}

	public function editLiveView($news_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation_news);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			if($_POST)
			{
				$this->session->set_flashdata('alerte', validation_errors());
				redirect($this->session->userdata('redirect_admin_live_view'));
			}

			$this->load->library('medias');
			$this->template['new'] = $this->news->get_news(array('id' => $news_id));
			$this->template['media'] = $this->medias->get_medias(array('src_id' => $news_id, 'module' => $this->template['module']));
			$this->load->view('admin/create-live-view', $this->template);
		}
		else
		{
			$this->model->save_news();
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->session->userdata('redirect_admin_live_view'));
		}
	}

	public function deleteLiveView($news_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->model->delete_news($news_id);
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_admin_live_view'));
	}

	public function flagLiveView($news_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);
		$this->system->flag($news_id, $flag, $this->config->item('table_news'), 'active');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_admin_live_view'));
	}


}
