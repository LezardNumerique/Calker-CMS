<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Translations extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->config->load('config');
			$this->load->library('administration');
			$this->load->library('translation');

			$this->template['module'] = 'admin';

		}

		public function index()
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);
			$lang = array();
			if($post = $this->input->post())
			{
				$this->session->set_userdata('translations_module', $this->input->post('module'));
				redirect($this->config->item('admin_folder').'/translations');
			}

			if($translations_module = $this->session->userdata('translations_module'))
			{
				if($translations_module == 'default')
				{
					$file = './'.APPPATH.'language/'.$this->user->lang.'/default_lang.php';
					if(is_file($file)) include($file);

				}
				else
				{
					$file = './'.APPPATH.'modules/'.$translations_module.'/language/'.$this->user->lang.'/'.$translations_module.'_lang.php';
					if(is_file($file)) include($file);
				}
				$this->template['translations_module'] = $translations_module;
				ksort($lang);
				$this->template['rows'] = $lang;
			}
			$modules = $this->system->list_modules();
			$modules['default'] = array();
			$this->template['modules'] = $modules;

			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'autosize', 'tablesorter', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'translations/index');
		}

		public function create()
		{
			if($translations_module = $this->session->userdata('translations_module'))
			{
				$lang = $this->input->post();
				$lang['1a'] = '';
				ksort($lang);
				$this->template['rows'] = $lang;
			}
			$this->load->view('translations/partials/index', $this->template);
		}

		public function save()
		{
			$this->translation->save($this->session->userdata('translations_module'), $this->input->post());
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/translations');
		}

		public function delete($delete_key = '')
		{
			if($translations_module = $this->session->userdata('translations_module'))
			{
				foreach($this->input->post() as $key => $value)
				{
					$lang[$key] = $value;
				}
				unset($lang[$delete_key]);
				ksort($lang);
				$this->template['rows'] = $lang;
				$this->translation->save($this->session->userdata('translations_module'), $lang);
				$this->load->view('translations/partials/index', $this->template);
			}
		}

	}