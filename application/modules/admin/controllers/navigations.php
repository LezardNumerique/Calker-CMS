<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Navigations extends CI_Controller
	{

		public function __construct()
		{
			parent::__construct();

			$this->config->load('config');
			$this->load->library('administration');
			$this->load->model('admin_model', 'model');
			$this->load->library('form_validation');

			$this->template['module'] = 'admin';

			$this->fields_validation = array(
				array(
					'field'   => 	'title',
					'label'   => 	$this->lang->line('validation_title'),
					'rules'   => 	'trim|required|max_length[64]|xss_clean'
				),
				array(
					'field'   => 	'uri',
					'label'   => 	$this->lang->line('validation_uri'),
					'rules'   => 	'trim|max_length[128]|xss_clean'
				)
			);
			
			$this->fields_validation_page = array(
				array(
					'field'   => 	'parent_id',
					'label'   => 	$this->lang->line('validation_parent_id'),
					'rules'   => 	'trim|numeric|xss_clean'
				),
				array(
					'field'   => 	'active',
					'label'   => 	$this->lang->line('validation_active'),
					'rules'   => 	'trim|numeric|exact_length[1]|xss_clean'
				),
				array(
					'field'   => 	'title',
					'label'   => 	$this->lang->line('validation_title'),
					'rules'   => 	'trim|required|max_length[128]|xss_clean'
				),
				array(
					'field'   => 	'uri',
					'label'   => 	$this->lang->line('validation_uri'),
					'rules'   => 	'trim|required|max_length[128]|xss_clean|callback__verify_uri'
				)
			);

		}

		public function index($parent_id = 0)
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);

			$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

			if (!$data = $this->cache->get('list_navigation_'.$this->user->lang.'_'.$parent_id, 'navigation'))
			{
				$data = $this->navigation->list_navigation($parent_id, 0, false);
				if($this->system->cache == 1) $this->cache->save('list_navigation_'.$this->user->lang.'_'.$parent_id, $data, 'navigation', 0);
			}

			$this->template['navigations'] = $data;
			$this->template['parent_id'] = $parent_id;

			$this->template['admin_breadcrumb'] = false;
			$this->template['admin_breadcrumb'] = $this->navigation->get_parent_recursive($parent_id);


			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'navigations/index');
		}

		public function treeview()
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);

			$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

			if (!$data = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
			{
				$data = $this->navigation->list_navigation();
				if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $data, 'navigation', 0);
			}

			$this->template['navigations'] = $data;

			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'tooltip', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'navigations/treeview');
		}

		public function create($parent_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			if (!$data = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
			{
				$data = $this->navigation->list_navigation();
				if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $data, 'navigation', 0);
			}
			$this->template['parent_id'] = $parent_id;
			$this->template['navigations'] = $data;
			$this->template['navigation'] = array(
				'id' 			=> '',
				'lang' 			=> $this->user->lang,
				'parent_id'	 	=> 0,
				'active' 		=> '',
				'module'		=> '',
				'title' 		=> '',
				'uri' 			=> ''
			);

			$mod = array();
			if($modules = $this->model->list_modules(array('navigation' => 1)))
			{
				foreach($modules as $module)
				{
					if($module['name'] != 'admin' && $module['name'] != 'pages') $mod[] = $module;
				}
			}
			$this->template['modules'] = $mod;

			$this->load->library('page');
			$list_pages = $this->page->list_pages_recursive();
			$pages = $this->plugin->apply_filters('list_pages', $list_pages);
			$this->template['pages'] = $pages;

			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'navigations/create');
		}

		public function edit($navigations_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);
			if (!$navigations = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
			{
				$navigations = $this->navigation->list_navigation();
				if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $navigations, 'navigation', 0);
			}
			
			$this->template['navigations'] = $navigations;
			$this->template['navigation'] = $this->navigation->get_navigation(array('id' => $navigations_id));
			$this->template['parent_id'] = $this->template['navigation']['parent_id'];

			$mod = array();
			if($modules = $this->model->list_modules(array('navigation' => 1)))
			{
				foreach($modules as $module)
				{
					if($module['name'] != 'admin' && $module['name'] != 'pages') $mod[] = $module;
				}
			}
			$this->template['modules'] = $mod;

			$this->load->library('page');
			$list_pages = $this->page->list_pages_recursive();
			$pages = $this->plugin->apply_filters('list_pages', $list_pages);
			$this->template['pages'] = $pages;

			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'navigations/create');
		}

		public function saveAjax()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$this->form_validation->set_rules($this->fields_validation);

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == TRUE)
			{
				if($this->input->post('uri') == '#') $uri = '#';
				if ($this->input->post('uri') != '') $uri = format_title($this->input->post('uri'), false);
				else $uri = '';

				$data = array(
					'lang' 			=> $this->user->lang,
					'parent_id' 	=> $this->input->post('parent_id'),
					'active' 		=> $this->input->post('active'),
					'module' 		=> $this->input->post('module'),
					'title' 		=> htmlentities($this->input->post('title')),
					'uri' 			=> $uri
				);

				if($id = $this->input->post('id'))
				{
					$this->db->where('id', $id);
					$this->db->update($this->config->item('table_navigation'), $data);
					$text = $this->lang->line('notification_save');
				}
				else
				{
					$data['ordering'] = 999;
					$this->db->insert($this->config->item('table_navigation'), $data);
					$text = $this->db->insert_id();
				}
				echo json_encode(array('type' => 'notice', 'text' => $text));
			}
			else
			{
				echo json_encode(array('type' => 'alerte', 'text' => validation_errors()));
			}
			if($this->system->cache == 1) $this->cache->remove_group('navigation');

		}

		public function save()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$fields_validation = $this->fields_validation;
			$this->form_validation->set_rules($fields_validation);

			$this->fields['title'] = $this->lang->line('validation_title');

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == TRUE)
			{
				if($this->input->post('uri') == '#') $uri = '#';
				if ($this->input->post('uri') != '') $uri = format_title($this->input->post('uri'), false);
				else $uri = '';

				$data = array(
					'lang' 			=> $this->user->lang,
					'parent_id' 	=> $this->input->post('parent_id'),
					'active' 		=> $this->input->post('active'),
					'module' 		=> $this->input->post('module'),
					'title' 		=> $this->input->post('title'),
					'uri' 			=> $uri
				);

				if($id = $this->input->post('id'))
				{
					$this->db->where('id', $id);
					$this->db->update($this->config->item('table_navigation'), $data);
				}
				else
				{
					$data['ordering'] = 999;
					$this->db->insert($this->config->item('table_navigation'), $data);
				}

				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
				if($this->system->cache == 1) $this->cache->remove_group('navigation');
				redirect($this->session->userdata('redirect_uri'));
			}
			else
			{
				$this->session->set_flashdata('alerte', validation_errors());
				$this->session->set_flashdata('post', $this->input->post());
				redirect($this->input->post('redirect_uri'));
			}
		}
		
		public function createPage($navigations_id = '', $parent_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			
			$this->form_validation->set_rules($this->fields_validation_page);

			$this->form_validation->set_error_delimiters('', '<br />');
	
			if ($this->form_validation->run() == FALSE)
			{
				if(!$_POST)
				{					
					$page = array(				
						'parent_id'				=> '',
						'active'				=> 1,					
						'title'					=> '',
						'uri'					=> ''
					);
		
					$this->template['page'] = $page;
		
					if (!$parents = $this->cache->get('list_pages_recursive'.$this->user->lang, 'pages'))
					{
						$parents = $this->page->list_pages_recursive();
						if($this->system->cache == 1) $this->cache->save('list_pages_recursive'.$this->user->lang, $parents, 'pages', 0);
					}
		
					$this->template['parents'] = $parents;
					$this->template['parent_id'] = $parent_id;
					$this->template['navigations_id'] = $navigations_id;
		
					$this->load->view('navigations/create-page', $this->template);
				}								
			}
			else
			{
				$data = array(
					'ordering'				=> 99999,
					'parent_id'				=> set_value('parent_id'),
					'active'				=> set_value('active'),
					'title'					=> $this->input->post('title'),					
					'lang'					=> $this->user->lang,
					'date_added'			=> mktime()
				);
	
				$parent_uri = '';
				if ($parent_id = set_value('parent_id'))
				{
					$parent = $this->page->get_pages(array('id' => $parent_id));
					if($parent['uri'] != $this->page->settings['page_home']) $parent_uri = $parent['uri'].'/';
				}
				$data['uri'] = $parent_uri.format_title($this->input->post('uri'));
	
				$this->db->insert($this->config->item('table_pages'), $data);
				if($this->system->cache == 1) $this->cache->remove_group('pages');
				
				echo $data['uri'];
							
			}
		}
		
		public function reloadListPages()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			$this->load->library('page');
			$list_pages = $this->page->list_pages_recursive();
			$html = '';
			if($pages = $this->plugin->apply_filters('list_pages', $list_pages))
			{
				foreach($pages as $page)
				{
					$html .= '<option value="'.$page['uri'].'">'.($page['level'] > 0 ? "|".str_repeat("__", $page['level']) : '').character_limiter(html_entity_decode($page['title']), 40).'</option>';
				}
			}
			$data['options'] = $html;
			$data['uri'] = $this->input->post('uri');
			echo json_encode($data);
		}

		public function delete($id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);

			if (!isset($id))
			{
				$this->session->set_flashdata('alerte', $this->lang->line('alerte_select_id'));
				redirect($this->session->userdata('redirect_uri'));
			}
			else
			{
				$navigations = $this->navigation->list_navigation($id, 0, true);
				if(is_array($navigations))
				{
					foreach($navigations as $navigation)
					{
						$this->db->where(array('id' => $navigation['id']))->delete($this->config->item('table_navigation'));
					}
				}
				$this->db->where(array('id' => $id))->delete($this->config->item('table_navigation'));
				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
				if($this->system->cache == 1) $this->cache->remove_group('navigation');
				redirect($this->session->userdata('redirect_uri'));
			}
		}

		public function move($id, $direction)
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			if (!isset($direction) || !isset($id))
			{
				redirect($this->config->item('admin_folder').'/navigations');
			}

			$query = $this->db->get_where($this->config->item('table_navigation'), array('id' => $id));

			if ($row = $query->row())
			{
				$parent_id = $row->parent_id;
			}
			else
			{
				$parent_id = 0;
			}

			$move = ($direction == 'up') ? -1 : 1;
			$this->db->where(array('id' => $id));

			$this->db->set('ordering', 'ordering+'.$move, FALSE);
			$this->db->update($this->config->item('table_navigation'));

			$this->db->where(array('id' => $id));
			$query = $this->db->get($this->config->item('table_navigation'));
			$row = $query->row();
			$new_ordering = $row->ordering;

			if ($move > 0)
			{
				$this->db->set('ordering', 'ordering-1', FALSE);
				$this->db->where(array('ordering <=' => $new_ordering, 'id <>' => $id, 'parent_id' => $parent_id, 'lang' => $this->user->lang));
				$this->db->update($this->config->item('table_navigation'));
			}
			else
			{
				$this->db->set('ordering', 'ordering+1', FALSE);
				$where = array('ordering >=' => $new_ordering, 'id <>' => $id, 'parent_id' => $parent_id, 'lang' => $this->user->lang);

				$this->db->where($where);
				$this->db->update($this->config->item('table_navigation'));
			}

			$i = 0;
			$this->db->order_by('ordering');
			$this->db->where(array('parent_id' => $parent_id, 'lang' => $this->user->lang));

			$query = $this->db->get($this->config->item('table_navigation'));

			if ($rows = $query->result())
			{
				foreach ($rows as $row)
				{
					$this->db->set('ordering', $i);
					$this->db->where('id', $row->id);
					$this->db->update($this->config->item('table_navigation'));
					$i++;
				}
			}

			if($this->system->cache == 1)  $this->cache->remove_group('navigation');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->session->userdata('redirect_uri'));
		}

		public function flag($navigations_id = '', $flag = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$this->system->flag ($navigations_id, $flag, $this->config->item('table_navigation'), 'active');

			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

			if($this->system->cache == 1) $this->cache->remove_group('navigation');

			redirect($this->session->userdata('redirect_uri'));
		}

		public function sortOrder()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			if($items = $this->input->post('items'))
			{
				foreach ($items as $ordering => $id)
				{
					$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_navigation'))." set ordering = ".$ordering." where id = '".(int)$id."'");
				}
			}
			if($this->system->cache == 1)  $this->cache->remove_group('navigation');
		}

		public function createLiveView($parent_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			if (!$data = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
			{
				$data = $this->navigation->list_navigation();
				if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $data, 'navigation', 0);
			}
			$this->template['parent_id'] = $parent_id;
			$this->template['navigations'] = $data;
			$this->template['navigation'] = array(
				'id' 			=> '',
				'lang' 			=> $this->user->lang,
				'parent_id'	 	=> 0,
				'active' 		=> '',
				'module'		=> '',
				'title' 		=> '',
				'uri' 			=> ''
			);

			$mod = array();
			if($modules = $this->model->list_modules(array('navigation' => 1)))
			{
				foreach($modules as $module)
				{
					if($module['name'] != 'admin' && $module['name'] != 'pages') $mod[] = $module;
				}
			}
			$this->template['modules'] = $mod;

			$this->load->library('page');
			$list_pages = $this->page->list_pages_recursive();
			$pages = $this->plugin->apply_filters('list_pages', $list_pages);
			$this->template['pages'] = $pages;

			$this->load->view('navigations/create-live-view', $this->template);

		}

		public function editLiveView($navigations_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);
			if (!$data = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
			{
				$data = $this->navigation->list_navigation();
				if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $data, 'navigation', 0);
			}

			$this->template['navigations'] = $data;
			$this->template['navigation'] = $this->navigation->get_navigation(array('id' => $navigations_id));

			$mod = array();
			if($modules = $this->model->list_modules(array('navigation' => 1)))
			{
				foreach($modules as $module)
				{
					if($module['name'] != 'admin' && $module['name'] != 'pages') $mod[] = $module;
				}
			}
			$this->template['modules'] = $mod;

			$this->load->library('page');
			$list_pages = $this->page->list_pages_recursive();
			$pages = $this->plugin->apply_filters('list_pages', $list_pages);
			$this->template['pages'] = $pages;

			$this->load->view('navigations/create-live-view', $this->template);
		}

		public function saveLiveView()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$fields_validation = $this->fields_validation;
			$this->form_validation->set_rules($fields_validation);

			$this->fields['title'] = $this->lang->line('validation_title');

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == TRUE)
			{
				if($this->input->post('uri') == '#') $uri = '#';
				if ($this->input->post('uri') != '') $uri = format_title($this->input->post('uri'), false);
				else $uri = '';

				$data = array(
					'lang' 			=> $this->user->lang,
					'parent_id' 	=> $this->input->post('parent_id'),
					'active' 		=> $this->input->post('active'),
					'module' 		=> $this->input->post('module'),
					'title' 		=> $this->input->post('title'),
					'uri' 			=> $uri
				);

				if($id = $this->input->post('id'))
				{
					$this->db->where('id', $id);
					$this->db->update($this->config->item('table_navigation'), $data);
				}
				else
				{
					$data['ordering'] = 999;
					$this->db->insert($this->config->item('table_navigation'), $data);
				}

				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
				if($this->system->cache == 1) $this->cache->remove_group('navigation');
				redirect($this->session->userdata('redirect_admin_live_view'));
			}
			else
			{
				$this->session->set_flashdata('alerte', validation_errors());
				$this->session->set_flashdata('post', $this->input->post());
				redirect($this->session->userdata('redirect_admin_live_view'));
			}
		}

		public function deleteLiveView($id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);

			if (!isset($id))
			{
				$this->session->set_flashdata('alerte', $this->lang->line('alerte_select_id'));
				redirect($this->session->userdata('redirect_uri'));
			}
			else
			{
				if($navigations = $this->navigation->list_navigation($id, 0, true))
				{
					foreach($navigations as $navigation)
					{
						$this->db->where(array('id' => $navigation['id']))->delete($this->config->item('table_navigation'));
					}
				}
				$this->db->where(array('id' => $id))->delete($this->config->item('table_navigation'));
				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
				if($this->system->cache == 1) $this->cache->remove_group('navigation');
				redirect($this->session->userdata('redirect_admin_live_view'));
			}
		}
		
		function _verify_uri()
		{
			$uri = $this->input->post('uri');			
	
			if ($this->system->exists(array('lang' => $this->user->lang, 'uri' => $uri), $this->config->item('table_pages')))
			{
				$this->form_validation->set_message('_verify_uri', $this->lang->line('alert_uri_already_used'));
				return FALSE;
			}
	
		}

	}