<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Admin extends CI_Controller
	{

		public function __construct()
		{
			parent::__construct();

			$this->config->load('config');
			$this->load->library('administration');
			$this->load->library('medias');
			$this->load->library('form_validation');

			$this->template['module'] = 'medias';

			$this->fields_validation = array(
				array(
					'field'   => 	'id',
					'label'   => 	$this->lang->line('validation_id'),
					'rules'   => 	'trim|numeric'
				),
				array(
					'field'   => 	'medias_redirect',
					'label'   => 	$this->lang->line('validation_medias_redirect'),
					'rules'   => 	'trim|numeric|xss_clean|htmlspecialchars'
				),
				array(
					'field'   => 	'medias_tabs',
					'label'   => 	$this->lang->line('validation_medias_tabs'),
					'rules'   => 	'trim|xss_clean|htmlspecialchars'
				),
				array(
					'field'   => 	'name',
					'label'   => 	$this->lang->line('validation_name'),
					'rules'   => 	'trim|required|max_length[32]|xss_clean|htmlspecialchars|callback__verify_name'
				),
				array(
					'field'   => 	'module',
					'label'   => 	$this->lang->line('validation_module'),
					'rules'   => 	'trim|required|max_length[16]|xss_clean|htmlspecialchars'
				),
				array(
					'field'   => 	'key',
					'label'   => 	$this->lang->line('validation_key'),
					'rules'   => 	'trim|required|max_length[164]|xss_clean|htmlspecialchars|callback__verify_key'
				)
			);
			
			if($themes = $this->layout->list_themes())
			{
				foreach($themes as $key => $theme)
				{
					$this->fields_validation[] = array(
						'field'   => 	'theme['.$theme.']',
						'label'   => 	$this->lang->line('validation_theme_').$theme,
						'rules'   => 	'required|max_length[16]|xss_clean|htmlspecialchars'
					);
					$this->fields_validation[] = array(
						'field'   => 	'width['.$theme.']',
						'label'   => 	$this->lang->line('validation_width_').$theme,
						'rules'   => 	'trim|required|numeric|max_length[5]|xss_clean|htmlspecialchars'
					);
					$this->fields_validation[] = array(
						'field'   => 	'height['.$theme.']',
						'label'   => 	$this->lang->line('validation_height_').$theme,
						'rules'   => 	'trim|required|numeric|max_length[5]|xss_clean|htmlspecialchars'
					);
				}
			}

		}

		public function index()
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);
			
			//pre_affiche($this->medias->get_medias_types_sizes('aa', 'height'));
			
			//------------- Filtre theme
			$where = '';
			if ($filter_theme = $this->input->post('filter_theme'))
			{
				if ($filter_theme == '-1')
				{
					$this->session->unset_userdata('filter_theme');
				}
				else
				{
					$this->session->set_userdata('filter_theme', $filter_theme);
				}				
				redirect($this->config->item('admin_folder').'/'.$this->template['module']);
			}
			if($filter_theme = $this->session->userdata('filter_theme'))
			{
				$where = array('theme' => $filter_theme);
			}

			$this->template['medias_types'] = $this->medias->list_medias_types(array('where' => $where));
			$this->template['themes'] = $this->layout->list_themes();

			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/index');
		}

		public function create()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$this->form_validation->set_rules($this->fields_validation);

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == FALSE)
			{
				$this->template['media_types'] = array('medias_types_id' => '', 'module' => '', 'name' => '', 'key' => '');
				if($themes = $this->layout->list_themes())
				{
					foreach($themes as $key => $theme)
					{
						$this->template['media_types_sizes'][$theme]['width'] = '';
						$this->template['media_types_sizes'][$theme]['height'] = '';
					}
				}
				$this->template['themes'] = $themes;

				$this->css->add(array('admin', 'ui'));
				$this->javascripts->add(array('jquery', 'ui', 'sitelib'));
				$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/create');
			}
			else
			{				
				if($themes = $this->layout->list_themes())
				{
					$data_types = array(
						'name'		=> $this->input->post('name'),
						'key'		=> format_title($this->input->post('key')),
						'module'	=> set_value('module')
					);
					
					$this->db->insert($this->config->item('table_medias_types'), $data_types);
					$medias_types_id = $this->db->insert_id();
					
					foreach($themes as $key => $theme)
					{						
						$data_types_sizes = array(	
							'theme'		=> $theme,									
							'width'		=> set_value('width['.$theme.']'),
							'height'	=> set_value('height['.$theme.']')
						);					
						
						$data_types_sizes['medias_types_id'] = $medias_types_id;
						$this->db->insert($this->config->item('table_medias_types_sizes'), $data_types_sizes);
					}
				}

				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

				if($this->system->cache == 1) $this->cache->remove_group('medias');

				if(set_value('medias_redirect') == 1)
					redirect($this->config->item('admin_folder').'/'.$this->template['module']);
				else
					redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/edit/'.$medias_types_id.set_value('medias_tabs'));	
				
			}

		}

		public function edit($medias_types_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			$this->form_validation->set_rules($this->fields_validation);

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == FALSE)
			{				
				$this->template['media_types'] = $this->medias->get_medias_types(array('medias_types_id' => $medias_types_id));
				$this->template['media_types_sizes'] = $this->medias->list_medias_types_sizes(array('where' => array($this->config->item('table_medias_types').'.medias_types_id' => $medias_types_id)), true);				
				$this->template['themes'] = $this->layout->list_themes();

				$this->css->add(array('admin', 'ui'));
				$this->javascripts->add(array('jquery', 'ui', 'sitelib'));
				$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/create');
			}
			else
			{
				if($themes = $this->layout->list_themes())
				{
					$data_types = array(
						'name'		=> $this->input->post('name'),
						'key'		=> format_title($this->input->post('key')),
						'module'	=> set_value('module')
					);
					
					$this->db->where(array('medias_types_id' => $medias_types_id))->update($this->config->item('table_medias_types'), $data_types);		
					
					foreach($themes as $key => $theme)
					{						
						$data_types_sizes = array(	
							'theme'		=> $theme,										
							'width'		=> set_value('width['.$theme.']'),
							'height'	=> set_value('height['.$theme.']')
						);					
						
						$this->db->where(array('medias_types_id' => $medias_types_id, 'theme' => $theme))->update($this->config->item('table_medias_types_sizes'), $data_types_sizes);
					}
				}		
				

				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

				if($this->system->cache == 1) $this->cache->remove_group('medias');

				if(set_value('medias_redirect') == 1)
					redirect($this->config->item('admin_folder').'/'.$this->template['module']);
				else
					redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/edit/'.$medias_types_id.set_value('medias_tabs'));
			}
		}

		public function delete($medias_types_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);

			$this->db->delete($this->config->item('table_medias_types'), array('medias_types_id' => $medias_types_id));
			$this->db->delete($this->config->item('table_medias_types_sizes'), array('medias_types_id' => $medias_types_id));

			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

			if($this->system->cache == 1) $this->cache->remove_group('medias');

			redirect($this->config->item('admin_folder').'/'.$this->template['module']);
		}
		

		/*
		 *
		 * Callback functions
		 *
		 */

		function _verify_name($data)
		{
			$id = (int)$this->input->post('id');			
			if ($this->medias->exists_types(array('name' => $data, 'medias_types_id !=' => $id)))
			{
				$this->form_validation->set_message('_verify_name', $this->lang->line('alert_name_already_used'));
				return FALSE;
			}
			else return $data;

		}

		function _verify_key($data)
		{
			$id = (int)$this->input->post('id');
			if ($this->medias->exists_types(array('key' => $data, 'medias_types_id !=' => $id)))
			{
				$this->form_validation->set_message('_verify_key', $this->lang->line('alert_key_already_used'));
				return FALSE;
			}
			else return $data;

		}


	}
