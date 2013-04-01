<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->template['admin'] = true;
		$this->template['module'] = 'pages';
		$this->config->load('config');
		$this->load->library('administration');
		$this->load->library('form_validation');
		$this->load->library('paragraph');
		$this->load->model('pages_model', 'model');

		$this->fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->lang->line('validation_title'),
				'rules'   => 	'trim|required|max_length[128]|xss_clean'
			),
			array(
				'field'   => 	'uri',
				'label'   => 	$this->lang->line('validation_uri'),
				'rules'   => 	'trim|required|max_length[128]|xss_clean|callback__verify_uri'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'meta_title',
				'label'   => 	$this->lang->line('validation_meta_title'),
				'rules'   => 	'trim|max_length[255]|xss_clean'
			),
			array(
				'field'   => 	'meta_keywords',
				'label'   => 	$this->lang->line('validation_meta_keywords'),
				'rules'   => 	'trim|max_length[255]|xss_clean'
			),
			array(
				'field'   => 	'meta_description',
				'label'   => 	$this->lang->line('validation_meta_description'),
				'rules'   => 	'trim|xss_clean'
			)
		);
		
		$this->fields_validation_navigation = array(
			array(
				'field'   => 	'parent_id',
				'label'   => 	$this->lang->line('validation_parent_id'),
				'rules'   => 	'trim|numeric|xss_clean'
			),
			array(
				'field'   => 	'page_parents_id',
				'label'   => 	$this->lang->line('validation_page_parents_id'),
				'rules'   => 	'trim|numeric|xss_clean'
			),
			array(
				'field'   => 	'page_parents_uri',
				'label'   => 	$this->lang->line('validation_page_parents_uri'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'page_uri',
				'label'   => 	$this->lang->line('validation_page_uri'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
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

		$this->page->settings = isset($this->system->pages_settings) ? unserialize($this->system->pages_settings) : array();

	}

	public function settings()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if ($post = $this->input->post('submit'))
		{
			$setting = is_array($this->input->post('settings')) ? serialize($this->input->post('settings')) : '';
			$this->system->set('pages_settings', $setting);
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

	//---------------------------------------------- Pages

	public function index($parent_id = 0, $start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

		$limit = $this->page->settings['per_page'];

		$where = array();

		$params['where'] = array('parent_id' => $parent_id, 'lang' => $this->user->lang);
		$params['limit'] = $limit;
		$params['start'] = $start;
		$params['order_by'] = 'ordering';
		$this->template['admin_breadcrumb'] = false;
		if(isset($params['where']['parent_id']))
		{
			$parent_id = $params['where']['parent_id'];
			$this->template['admin_breadcrumb'] = $this->model->list_parent_recursive($parent_id);
		}
		$this->template['parent_id'] = $parent_id;

		$pages = $this->page->list_pages($params);
		$total_pages = $this->page->total_list_pages($params);

		$this->template['pages'] = $pages;
		$this->load->library('pagination');

		$config['uri_segment'] = 5;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/index/'.$parent_id);
		$config['total_rows'] = $total_pages;
		$config['per_page'] = $limit;
		$config['num_links'] = $this->system->num_links;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total'] = $total_pages;
		$this->template['start'] = $start;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/index');
	}

	public function create($parent_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$page = array(
				'id'					=> 0,
				'parent_id'				=> '',
				'active'				=> 0,
				'title'					=> '',
				'uri'					=> '',
				'class'					=> '',
				'meta_title'			=> '',
				'meta_keywords'			=> '',
				'meta_description'		=> '',
				'show_sub_pages'		=> 0,
				'show_navigation'		=> 0
			);

			$this->template['page'] = $page;

			if (!$parents = $this->cache->get('list_pages_recursive'.$this->user->lang, 'pages'))
			{
				$parents = $this->page->list_pages_recursive();
				if($this->system->cache == 1) $this->cache->save('list_pages_recursive'.$this->user->lang, $parents, 'pages', 0);				
			}
			$this->template['parents'] = $parents;			
			$this->template['parent_id'] = $parent_id;			
			
			if($parents)								
				$parents_uri = array();
				foreach($parents as $parent)				
					$parents_uri[$parent['uri']] = $parent['uri'];				
				$this->template['parents_uri'] = $parents_uri;
			
			if (!$navigations = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
			{
				$navigations = $this->navigation->list_navigation();
				if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $navigations, 'navigation', 0);
			}
			$this->template['navigations'] = $navigations;
			
			$parent = $this->page->get_pages(array('id' => $page['parent_id']));
			$parent_uri = '';
			if($parent['uri'] != $this->page->settings['page_home'])
			{
				$parent_uri = $parent['uri'].'/';				
			}
			$this->template['parent_uri'] = $parent_uri;

			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'selectbox', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/create');
		}
		else
		{
			$data = array(
				'ordering'				=> 99999,
				'parent_id'				=> strip_tags($this->input->post('parent_id')),
				'active'				=> strip_tags($this->input->post('active')),
				'title'					=> htmlentities($this->input->post('title')),
				'class'					=> strip_tags($this->input->post('class')),
				'lang'					=> $this->user->lang,
				'meta_title'			=> htmlentities($this->input->post('meta_title')),
				'meta_keywords'			=> strip_tags($this->input->post('meta_keywords')),
				'meta_description'		=> strip_tags($this->input->post('meta_description')),
				'show_sub_pages'		=> strip_tags($this->input->post('show_sub_pages')),
				'show_navigation'		=> strip_tags($this->input->post('show_navigation')),
				'date_added'			=> mktime()
			);

			$parent_uri = '';
			if ($parent_id = $this->input->post('parent_id'))
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home']) $parent_uri = $parent['uri'].'/';
			}
			$data['uri'] = $parent_uri.format_title($this->input->post('uri'));

			$this->model->save('', $data);
			if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->session->userdata('redirect_uri'));
		}
	}

	public function edit($pages_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$this->form_validation->set_rules($this->fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$page = $this->page->get_pages(array('id' => $pages_id));
			$parent_uri = '';
			if ($parent_id = $page['parent_id'])
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home'])
				{
					$parent_uri = $parent['uri'].'/';
					$page['uri'] = str_replace($parent_uri, '', $page['uri']);
				}

			}			

			$this->template['parent_uri'] = $parent_uri;
			$this->template['page'] = $page;

			if (!$parents = $this->cache->get('list_pages_recursive'.$this->user->lang, 'pages'))
			{
				$parents = $this->page->list_pages_recursive();
				if($this->system->cache == 1) $this->cache->save('list_pages_recursive'.$this->user->lang, $parents, 'pages', 0);
			}
			$this->template['parents'] = $parents;
			$this->template['parent_id'] = $parent_id;
			
			if($parents)								
				$parents_uri = array();
				foreach($parents as $parent)				
					$parents_uri[$parent['uri']] = $parent['uri'];				
				$this->template['parents_uri'] = $parents_uri;
				
			if (!$navigations = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
			{
				$navigations = $this->navigation->list_navigation();
				if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $navigations, 'navigation', 0);
			}
			$this->template['navigations'] = $navigations;
			
			$this->template['paragraphs'] = $this->paragraph->list_paragraphs(array('order_by' => 'ordering ASC', 'select' => '*, '.$this->config->item('table_paragraphs').'.id as pID, '.$this->config->item('table_paragraphs').'.active as pACTIVE', 'where' => array('src_id' => $pages_id), 'ordering' => 'ordering'));

			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/create');
		}
		else
		{
			$data = array(
				'parent_id'				=> strip_tags($this->input->post('parent_id')),
				'active'				=> strip_tags($this->input->post('active')),
				'title'					=> htmlentities($this->input->post('title')),
				'class'					=> strip_tags($this->input->post('class')),
				'lang'					=> $this->user->lang,
				'meta_title'			=> htmlentities($this->input->post('meta_title')),
				'meta_keywords'			=> strip_tags($this->input->post('meta_keywords')),
				'meta_description'		=> strip_tags($this->input->post('meta_description')),
				'show_sub_pages'		=> strip_tags($this->input->post('show_sub_pages')),
				'show_navigation'		=> strip_tags($this->input->post('show_navigation')),
				'date_modified'			=> mktime()
			);

			$parent_uri = '';
			if ($parent_id = $this->input->post('parent_id'))
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home']) $parent_uri = $parent['uri'].'/';
			}
			$data['uri'] = $parent_uri.format_title($this->input->post('uri'));

			$this->model->save($this->input->post('pages_id'), $data);
			if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->session->userdata('redirect_uri'));
		}
	}

	public function createAjax()
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$this->form_validation->set_rules($this->fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == TRUE)
		{
			$data = array(
				'ordering'				=> 99999,
				'parent_id'				=> strip_tags($this->input->post('parent_id')),
				'active'				=> strip_tags($this->input->post('active')),
				'title'					=> htmlentities($this->input->post('title')),
				'class'					=> strip_tags($this->input->post('class')),
				'lang'					=> $this->user->lang,
				'meta_title'			=> htmlentities($this->input->post('meta_title')),
				'meta_keywords'			=> strip_tags($this->input->post('meta_keywords')),
				'meta_description'		=> strip_tags($this->input->post('meta_description')),
				'show_sub_pages'		=> strip_tags($this->input->post('show_sub_pages')),
				'show_navigation'		=> strip_tags($this->input->post('show_navigation')),
				'date_modified'			=> mktime()
			);

			$parent_uri = '';
			if ($parent_id = $this->input->post('parent_id'))
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home']) $parent_uri = $parent['uri'].'/';
			}
			$data['uri'] = $parent_uri.format_title($this->input->post('uri'));

			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
			echo json_encode(array('type' => 'notice', 'text' => $this->model->save('', $data)));
		}
		else
		{
			echo json_encode(array('type' => 'alerte', 'text' => validation_errors()));
		}

	}

	public function editAjax($pages_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$this->form_validation->set_rules($this->fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == TRUE)
		{
			$data = array(
				'parent_id'				=> strip_tags($this->input->post('parent_id')),
				'active'				=> strip_tags($this->input->post('active')),
				'title'					=> htmlentities($this->input->post('title')),
				'uri'					=> format_title($this->input->post('uri')),
				'class'					=> strip_tags($this->input->post('class')),
				'lang'					=> $this->user->lang,
				'meta_title'			=> htmlentities($this->input->post('meta_title')),
				'meta_keywords'			=> strip_tags($this->input->post('meta_keywords')),
				'meta_description'		=> strip_tags($this->input->post('meta_description')),
				'show_sub_pages'		=> strip_tags($this->input->post('show_sub_pages')),
				'show_navigation'		=> strip_tags($this->input->post('show_navigation')),
				'date_modified'			=> mktime()
			);

			$parent_uri = '';
			if ($parent_id = $this->input->post('parent_id'))
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home']) $parent_uri = $parent['uri'].'/';
			}
			$data['uri'] = $parent_uri.format_title($this->input->post('uri'));

			$this->model->save($this->input->post('pages_id'), $data);
			if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
			echo json_encode(array('type' => 'notice', 'text' => $this->lang->line('notification_save')));
		}
		else
		{
			echo json_encode(array('type' => 'alerte', 'text' => validation_errors()));
		}

	}

	public function flag($src_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->system->flag ($src_id, $flag, $this->config->item('table_pages'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri'));
	}

	public function move($pages_id = '', $direction = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if(!isset($direction) || !isset($pages_id))
		{
			redirect($this->session->userdata('redirect_uri'));
		}

		$this->model->move($pages_id, $direction);
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri'));
	}

	public function delete($pages_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->model->delete($pages_id);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		redirect($this->session->userdata('redirect_uri'));
	}

	public function sortOrder()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if($items = $this->input->post('items'))
			foreach ($items as $ordering => $id)
				$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_pages'))." set ordering = ".(int)$ordering." where id = '".(int)$id."'");

		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
	}

	public function tinyPageList()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$list_pages = $this->page->list_pages(array('where' => array('active' => 1, 'lang' => $this->user->lang)));
		$rows = $this->plugin->apply_filters('list_pages', $list_pages);

		$pages = array();
		foreach ($rows as $row)
		{
			$pages[] = "[\"".stripslashes(html_entity_decode($row['title']))."\", \"".site_url($row['uri'].$this->config->item('url_suffix_ext')) . "\"]" ;
		}
		echo "var tinyMCELinkList = new Array(";
		echo join(", ", $pages);
		echo ");";
	}

	public function createLiveView($parent_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			if($_POST)
			{
				$this->session->set_flashdata('alerte', validation_errors());
				redirect($this->session->userdata('redirect_admin_live_view'));
			}

			$page = array(
				'id'					=> '',
				'parent_id'				=> '',
				'active'				=> 0,
				'title'					=> '',
				'uri'					=> '',
				'class'					=> '',
				'meta_title'			=> '',
				'meta_keywords'			=> '',
				'meta_description'		=> '',
				'show_sub_pages'		=> 0,
				'show_navigation'		=> 0
			);

			$this->template['page'] = $page;

			if (!$parents = $this->cache->get('list_pages_recursive'.$this->user->lang, 'pages'))
			{
				$parents = $this->page->list_pages_recursive();
				if($this->system->cache == 1) $this->cache->save('list_pages_recursive'.$this->user->lang, $parents, 'pages', 0);
			}

			$this->template['parents'] = $parents;
			$this->template['parent_id'] = $parent_id;

			$this->load->view('admin/create-live-view', $this->template);
		}
		else
		{
			$data = array(
				'ordering'				=> 999,
				'parent_id'				=> strip_tags($this->input->post('parent_id')),
				'active'				=> strip_tags($this->input->post('active')),
				'title'					=> htmlentities($this->input->post('title')),
				'class'					=> strip_tags($this->input->post('class')),
				'lang'					=> $this->user->lang,
				'meta_title'			=> htmlentities($this->input->post('meta_title')),
				'meta_keywords'			=> strip_tags($this->input->post('meta_keywords')),
				'meta_description'		=> strip_tags($this->input->post('meta_description')),
				'show_sub_pages'		=> strip_tags($this->input->post('show_sub_pages')),
				'show_navigation'		=> strip_tags($this->input->post('show_navigation')),
				'date_added'			=> mktime()
			);

			$parent_uri = '';
			if ($parent_id = $this->input->post('parent_id'))
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home']) $parent_uri = $parent['uri'].'/';
			}
			$data['uri'] = $parent_uri.format_title($this->input->post('uri'));

			$this->model->save('', $data);
			if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($data['uri'].$this->config->item('url_suffix_ext'));
		}
	}

	public function editLiveView($pages_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$this->form_validation->set_rules($this->fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			if($_POST)
			{
				$this->session->set_flashdata('alerte', validation_errors());
				redirect($this->session->userdata('redirect_admin_live_view'));
			}

			$page = $this->page->get_pages(array('id' => $pages_id));
			if ($parent_id = $page['parent_id'])
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home'])
				{
					$parent_uri = $parent['uri'].'/';
					$page['uri'] = str_replace($parent_uri, '', $page['uri']);
				}

			}

			$this->template['page'] = $page;

			if (!$parents = $this->cache->get('list_pages_recursive'.$this->user->lang, 'pages'))
			{
				$parents = $this->page->list_pages_recursive();
				if($this->system->cache == 1) $this->cache->save('list_pages_recursive'.$this->user->lang, $parents, 'pages', 0);
			}

			$this->template['parents'] = $parents;
			$this->template['paragraphs'] = $this->paragraph->list_paragraphs(array('order_by' => 'ordering ASC', 'select' => '*, '.$this->config->item('table_paragraphs').'.id as pID, '.$this->config->item('table_paragraphs').'.active as pACTIVE', 'where' => array('src_id' => $pages_id), 'ordering' => 'ordering'));

			$this->load->view('admin/create-live-view', $this->template);
		}
		else
		{
			$data = array(
				'ordering'				=> 999,
				'parent_id'				=> strip_tags($this->input->post('parent_id')),
				'active'				=> strip_tags($this->input->post('active')),
				'title'					=> htmlentities($this->input->post('title')),
				'class'					=> strip_tags($this->input->post('class')),
				'lang'					=> $this->user->lang,
				'meta_title'			=> htmlentities($this->input->post('meta_title')),
				'meta_keywords'			=> strip_tags($this->input->post('meta_keywords')),
				'meta_description'		=> strip_tags($this->input->post('meta_description')),
				'show_sub_pages'		=> strip_tags($this->input->post('show_sub_pages')),
				'show_navigation'		=> strip_tags($this->input->post('show_navigation')),
				'date_modified'			=> mktime()
			);

			$parent_uri = '';
			if ($parent_id = $this->input->post('parent_id'))
			{
				$parent = $this->page->get_pages(array('id' => $parent_id));
				if($parent['uri'] != $this->page->settings['page_home']) $parent_uri = $parent['uri'].'/';
			}
			$data['uri'] = $parent_uri.format_title($this->input->post('uri'));

			$this->model->save($this->input->post('pages_id'), $data);
			if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($data['uri'].$this->config->item('url_suffix_ext'));
		}
	}
	
	public function createNavigation($pages_id = '', $parents_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
			
		$this->form_validation->set_rules($this->fields_validation_navigation);

		$this->form_validation->set_error_delimiters('', '<br />');
	
		if ($this->form_validation->run() == FALSE)
		{
			if(!$_POST)
			{		
				$navigation = array(		
					'id'					=> '',		
					'parent_id'				=> '',
					'active'				=> 1,					
					'title'					=> '',
					'uri'					=> ''
				);		
				$this->template['navigation'] = $navigation;
				$this->template['page'] = $this->page->get_pages(array('id' => $pages_id));
				$this->template['page_parent'] = $this->page->get_pages(array('id' => $parents_id));
					
				if (!$navigations = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
				{
					$navigations = $this->navigation->list_navigation();
					if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $navigations, 'navigation', 0);
				}			
				$this->template['navigations'] = $navigations;
		
				$this->load->view('admin/create-navigation', $this->template);
			}								
		}
		else
		{
			$data = array(
				'ordering'				=> 99999,
				'parent_id'				=> set_value('parent_id'),
				'active'				=> set_value('active'),
				'title'					=> $this->input->post('title'),					
				'lang'					=> $this->user->lang
			);
	
			$page_parent_uri = '';
			if ($page_parents_id = set_value('page_parents_id'))
			{
				$page_parent = $this->page->get_pages(array('id' => $page_parents_id));
				if($page_parent['uri'] != $this->page->settings['page_home']) $page_parent_uri = $page_parent['uri'].'/';
			}
			$data['uri'] = $page_parent_uri.format_title($this->input->post('uri'));
			
			$this->db->insert($this->config->item('table_navigation'), $data);
			if($this->system->cache == 1) $this->cache->remove_group('navigation');
			
			$data['page_parents_uri'] = set_value('page_parents_uri');
			$data['page_uri'] = set_value('page_uri');
			echo json_encode($data);
							
		}
	}
		
	public function reloadListNavigations($pages_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->load->library('page');		
		
		if (!$parents = $this->cache->get('list_pages_recursive'.$this->user->lang, 'pages'))
		{
			$parents = $this->page->list_pages_recursive();
			if($this->system->cache == 1) $this->cache->save('list_pages_recursive'.$this->user->lang, $parents, 'pages', 0);				
		}			
		if($parents)								
			$parents_uri = array();
			foreach($parents as $parent)				
				$parents_uri[$parent['uri']] = $parent['uri'];
				
		if (!$navigations = $this->cache->get('treeview_navigation_'.$this->user->lang, 'navigation'))
		{
			$navigations = $this->navigation->list_navigation();
			if($this->system->cache == 1) $this->cache->save('treeview_navigation_'.$this->user->lang, $navigations, 'navigation', 0);
		}
						
		$html = '';
		if($navigations)
		{
			foreach($navigations as $navigation)
			{
				$html .= '<option value="'.end(explode('/', $navigation['uri'])).'" '.($navigation['parent_id'] == 0 || in_array($navigation['uri'], $parents_uri) && $navigation['uri'] != $this->input->post('page_parents_uri') && end(explode('/', $navigation['uri'])) != $this->input->post('page_uri') ? ' disabled="disabled"' : '').'>'.($navigation['level'] > 0 ? "|".str_repeat("__", $navigation['level']) : '').character_limiter(html_entity_decode($navigation['title']), 40).'</option>';
			}
		}
		$data['options'] = $html;
		$data['uri'] = end(explode('/', $this->input->post('uri')));		
		echo json_encode($data);
	}

	public function deleteLiveView($pages_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->model->delete($pages_id);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		redirect($this->session->userdata('redirect_admin_live_view'));
	}

	public function flagLiveView($src_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->system->flag ($src_id, $flag, $this->config->item('table_pages'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_admin_live_view'));
	}


	//---------------------------------------------- Paragraph

	public function selectParag($src_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		if($submit = $this->input->post('submit'))
		{
			$this->session->set_userdata('types_id', $this->input->post('types_id'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/createParag/'.$src_id);
		}
		else
		{
			$this->template['paragraphs_types'] = $this->paragraph->list_paragraphs_types(array('active' => 1));
			$this->template['src_id'] = $src_id;
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'tablesorter', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/select-paragraph');
		}
	}

	public function createParag($src_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if($types_id = $this->session->userdata('types_id'))
		{
			$this->template['paragraph'] = array('id' => '', 'title' => '', 'title_2' => '', 'title_3' => '', 'body' => '', 'body_2' => '', 'body_3' => '', 'class' => '', 'class_2' => '', 'class_3' => '');
			$this->template['src_id'] = $src_id;
			$this->template['types_id'] = $types_id;
			$this->template['type'] = $this->paragraph->get_paragraphs_types(array('id' => $types_id));

			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'tablesorter', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/edit-paragraph-types');
		}

	}

	public function editParag($src_id = '', $paragraphs_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$view = 'admin/404';

		if($paragraph = $this->paragraph->get_paragraphs(array('id' => $paragraphs_id)))
		{
			$this->template['paragraph'] = $paragraph;
			$this->template['media'] = $this->paragraph->get_medias(array('src_id' => $paragraphs_id, 'module' => $this->template['module']));
			$this->template['src_id'] = $src_id;
			$this->template['types_id'] = $this->template['paragraph']['types_id'];
			$this->template['type'] = $this->paragraph->get_paragraphs_types(array('id' => $this->template['paragraph']['types_id']));
			$view = 'admin/edit-paragraph-types';
		}

		$this->css->add(array('admin', 'ui'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'tablesorter', 'sitelib', 'tinymce', 'swfobject'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), $view);
	}

	public function saveParag($src_id = '', $paragraphs_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if($types_id = $this->input->post('types_id'))
		{
			$types_id = 'traitement_parag_type_'.$types_id;
			$last_id = $this->paragraph->$types_id($this->template['module']);
		}

		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		if($this->input->post('paragraphs_id'))
		{
			redirect($this->input->post('redirect_uri'));
		}
		else
		{
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/editParag/'.$src_id.'/'.$last_id);
		}
	}

	public function deleteParag($src_id = '', $paragraphs_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->paragraph->delete($paragraphs_id, $this->template['module']);
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/edit/'.$src_id.'#paragraphe');
	}

	public function deleteParagMedia ($src_id = '', $paragraphs_id = '', $medias_id = '')
	{
		$this->paragraph->delete_parag_media($medias_id);
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/editParag/'.$src_id.'/'.$paragraphs_id.'#two');
	}

	function ajaxSortOrderMedia ()
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);
		$listItem = $_POST['listItem'];
		for ($i=0, $n=sizeof($listItem); $i<$n;)
		{
			$this->db->update($this->config->item('table_medias'), array('ordering ' => $i), "id = '".$listItem[$i]."' and module = '".$this->template['module']."' ");
			$i++;
		}
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
	}

	public function flagParag($src_id = '', $paragraphs_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->system->flag ($paragraphs_id, $flag, $this->config->item('table_paragraphs'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/edit/'.$src_id.'#paragraphe');
	}

	public function moveParag($src_id = '', $paragraphs_id = '', $direction = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if(!isset($src_id) || !isset($paragraphs_id) || !isset($direction))
		{
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/edit/'.$src_id);
		}

		$this->paragraph->move_paragraphs($src_id, $paragraphs_id, $direction, $this->template['module']);
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/edit/'.$src_id.'#paragraphe');
	}

	public function sortOrderParag($src_id = '')
	{
		if($items = $this->input->post('items'))
		{
			foreach ($items as $ordering => $id)
			{
				$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_paragraphs'))." set ordering = ".$ordering." where id = '".(int)$id."' AND src_id='".(int)$src_id."' AND module = '".$this->template['module']."'");
			}
			if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		}
	}

	public function selectParagLiveView($paragraphs_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		if($types_id = $this->input->post('types_id'))
		{
			$this->session->set_userdata('types_id', $types_id);
		}
		else
		{
			$this->template['paragraphs_types'] = $this->paragraph->list_paragraphs_types(array('active' => 1));
			$this->template['src_id'] = $paragraphs_id;
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'filestyle', 'tablesorter', 'sitelib'));
			$this->load->view('admin/select-paragraph-live-view', $this->template);
		}
	}

	public function createParagLiveView($src_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if($types_id = $this->session->userdata('types_id'))
		{
			$this->template['paragraph'] = array('id' => '', 'title' => '', 'title_2' => '', 'title_3' => '', 'body' => '', 'body_2' => '', 'body_3' => '', 'class' => '', 'class_2' => '', 'class_3' => '');
			$this->template['src_id'] = $src_id;
			$this->template['types_id'] = $types_id;
			$this->template['type'] = $this->paragraph->get_paragraphs_types(array('id' => $types_id));

			$this->load->view('admin/edit-paragraph-types-live-view', $this->template);
		}
	}

	public function editParagLiveView($paragraphs_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$view = 'admin/404';

		if($paragraph = $this->paragraph->get_paragraphs(array('id' => $paragraphs_id)))
		{
			$this->template['paragraph'] = $paragraph;
			$this->template['media'] = $this->paragraph->get_medias(array('src_id' => $paragraphs_id, 'module' => $this->template['module']));
			$this->template['src_id'] = $paragraph['src_id'];
			$this->template['types_id'] = $this->template['paragraph']['types_id'];
			$this->template['type'] = $this->paragraph->get_paragraphs_types(array('id' => $this->template['paragraph']['types_id']));
			$view = 'admin/edit-paragraph-types-live-view';
		}
		$this->load->view($view, $this->template);
	}

	public function saveParagLiveView($paragraphs_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if($types_id = $this->input->post('types_id'))
		{
			$types_id = 'traitement_parag_type_'.$types_id;
			$last_id = $this->paragraph->$types_id($this->template['module']);
		}

		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_admin_live_view'));
	}

	public function flagParagLiveView($paragraphs_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->system->flag ($paragraphs_id, $flag, $this->config->item('table_paragraphs'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_admin_live_view'));
	}

	public function moveParagLiveView($paragraphs_id = '', $direction = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if(!isset($paragraphs_id) || !isset($direction))
		{
			redirect($this->session->userdata('redirect_admin_live_view'));
		}

		$this->paragraph->move_paragraphs($src_id, $paragraphs_id, $direction, $this->template['module']);
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_admin_live_view'));
	}

	public function deleteParagLiveView($paragraphs_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->paragraph->delete($paragraphs_id, $this->template['module']);
		if($this->system->cache == 1) $this->cache->remove_group($this->template['module']);
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_admin_live_view'));
	}

	/*
	*
	* Callback functions
	*
	*/

	function _verify_uri()
	{
		$uri = $this->input->post('uri');
		$id = $this->input->post('pages_id');

		if ($this->system->exists(array('lang' => $this->user->lang, 'uri' => $uri, 'id !=' => $id), $this->config->item('table_pages')) && $uri != 'index')
		{
			$this->form_validation->set_message('_verify_uri', $this->lang->line('alert_uri_already_used'));
			return FALSE;
		}

	}
}
