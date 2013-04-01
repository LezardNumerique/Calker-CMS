<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->template['admin'] = true;
		$this->template['module'] = 'portfolio';
		$this->config->load('config');
		$this->load->library('administration');
		$this->load->library('form_validation');
		$this->load->library('portfolios', '', 'portfolio');
		$this->load->model('portfolio_model', 'model');

		$this->portfolio->settings = isset($this->system->portfolio_settings) ? unserialize($this->system->portfolio_settings) : array();
		
		$this->fields_validation_categories = array(
			array(
				'field'   => 	'id',
				'label'   => 	$this->lang->line('validation_id'),
				'rules'   => 	'trim|numeric'
			),
			array(
				'field'   => 	'parent_id',
				'label'   => 	$this->lang->line('validation_parent_id'),
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
				'rules'   => 	'trim|required|max_length[64]|htmlspecialchars|xss_clean'
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
				'rules'   => 	'trim|max_length[64]|htmlspecialchars|xss_clean'
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
				'field'   => 	'categories_redirect',
				'label'   => 	$this->lang->line('validation_redirect'),
				'rules'   => 	'trim|numeric|max_length[1]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'categories_tabs',
				'label'   => 	$this->lang->line('validation_tabs'),
				'rules'   => 	'trim|htmlspecialchars|htmlspecialchars|xss_clean'
			)
		);

		$this->fields_validation_medias = array(
			array(
				'field'   => 	'id',
				'label'   => 	$this->lang->line('validation_id'),
				'rules'   => 	'trim|numeric'
			),
			array(
				'field'   => 	'is_box',
				'label'   => 	$this->lang->line('validation_parent_id'),
				'rules'   => 	'trim|numeric|max_length[1]'
			),			
			array(
				'field'   => 	'active',
				'label'   => 	$this->lang->line('validation_active'),
				'rules'   => 	'trim|numeric|max_length[1]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'title',
				'label'   => 	$this->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'uri',
				'label'   => 	$this->lang->line('validation_uri'),
				'rules'   => 	'trim|max_length[64]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'alt',
				'label'   => 	$this->lang->line('validation_alt'),
				'rules'   => 	'trim|max_length[128]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'legend',
				'label'   => 	$this->lang->line('validation_legend'),
				'rules'   => 	'trim|max_length[128]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'body',
				'label'   => 	$this->lang->line('validation_body'),
				'rules'   => 	'trim|xss_clean'
			),
			array(
				'field'   => 	'categories_id_default',
				'label'   => 	$this->lang->line('validation_categories_id_default'),
				'rules'   => 	'trim|required|numeric|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'medias_redirect',
				'label'   => 	$this->lang->line('medias_redirect'),
				'rules'   => 	'trim|numeric|max_length[1]|htmlspecialchars|xss_clean'
			),
			array(
				'field'   => 	'medias_tabs',
				'label'   => 	$this->lang->line('medias_tabs'),
				'rules'   => 	'trim|htmlspecialchars|htmlspecialchars|xss_clean'
			)
		);

	}

	public function index()
	{
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/categories');
	}

	//---------------------------------------------- Settings

	public function settings()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if ($post = $this->input->post('submit'))
		{
			$settings = is_array($this->input->post('settings')) ? serialize($this->input->post('settings')) : '';
			$this->system->set('portfolio_settings', $settings);
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

	//---------------------------------------------- Categories
	public function categories($categories_id = 1)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		if (!$categories = $this->cache->get('list_categories_'.$categories_id, 'categories'))
		{
			$categories = $this->portfolio->list_categories($categories_id, '', '', 1000, 0, false);
			if($this->system->cache == 1) $this->cache->save('list_categories_'.$categories_id, $categories, 'categories', 0);
		}

		$this->template['categories'] = $categories;
		$this->template['total_categories'] = count($categories);
		$this->template['medias'] = $this->portfolio->list_medias(array('select' => '*, '.$this->config->item('table_portfolio_medias').'.active as mACTIVE', 'where' => array($this->config->item('table_portfolio_categories_to_medias').'.categories_id' => $categories_id), 'order_by' => $this->config->item('table_portfolio_categories_to_medias').'.ordering'));
		$this->template['total_medias'] = $this->portfolio->total_list_medias(array('where' => array($this->config->item('table_portfolio_categories_to_medias').'.categories_id' => $categories_id)));
		$this->template['categorie'] = $this->portfolio->get_categories(array('id' => $categories_id));
		$this->template['categories_id'] = $categories_id;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/categories/index');

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

	}

	public function categoriesCreate($categories_id = 1)
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		
		$this->form_validation->set_rules($this->fields_validation_categories);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['categorie'] = array('id' => '', 'parent_id' => '', 'active' => '', 'ordering' => '', 'title' => '', 'uri' => '', 'body' => '', 'meta_title' => '', 'meta_keywords' => '', 'meta_description' => '');
			$this->template['categories_id'] = $categories_id;
			$this->template['parents'] = $this->portfolio->list_categories('','','');
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/categories/create');
		}
		else
		{
			$this->load->library('medias');
			$categories_id = $this->model->create_categories();
			if($this->system->cache == 1) $this->cache->remove_group('categories');
			if($this->system->cache == 1) $this->cache->remove_group('navigation');
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));			
			
			if(set_value('categories_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/categoriesEdit/'.$categories_id.set_value('categories_tabs'));
		}

	}

	public function categoriesEdit($categories_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->load->library('medias');
		
		$this->form_validation->set_rules($this->fields_validation_categories);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['categorie'] = $this->portfolio->get_categories(array('id' => $categories_id));
			$this->template['parents'] = $this->portfolio->list_categories(0, 0, array('id !=' => $categories_id));
			$this->template['image'] = $this->medias->get_medias(array('src_id' => $categories_id, 'module' => 'portfolio_categories'));
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/categories/create');
		}
		else
		{
			$this->model->create_categories();
			if($this->system->cache == 1) $this->cache->remove_group('categories');
			if($this->system->cache == 1) $this->cache->remove_group('navigation');
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			
			if(set_value('categories_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/categoriesEdit/'.$categories_id.set_value('categories_tabs'));
		}

	}

	public function categoriesDelete($categories_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if($categories_id == 1) redirect($this->session->userdata('redirect_uri'));

		$this->model->delete_categories($categories_id);
		$this->model->delete_categories_to_medias($categories_id);

		$this->load->library('medias');

		if($medias = $this->medias->list_medias(array('where' => array('src_id' => $categories_id, 'module' => 'portfolio_categories'))))
		{
			foreach($medias as $media)
			{
				$this->medias->delete_medias($media['id']);
			}
		}

		if($this->system->cache == 1) $this->cache->remove_group('categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		redirect($this->session->userdata('redirect_uri'));
	}

	public function categoriesFlag($categories_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->system->flag($categories_id, $flag, $this->config->item('table_portfolio_categories'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group('categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri'));
	}

	public function categoriesMove ($id = '', $direction = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if (!isset($direction) || !isset($id))
		{
			redirect($this->session->userdata('redirect_uri'));
		}

		$this->model->move_categories($id, $direction);
		if($this->system->cache == 1) $this->cache->remove_group('categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri'));
	}

	public function categoriesSortOrder($parent_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		if($items = $this->input->post('items'))
		{
			foreach ($items as $ordering => $categories_id)
			{
				$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_portfolio_categories'))." set ordering = ".$ordering." where parent_id = '".(int)$parent_id."' AND id = '".(int)$categories_id."'");
			}
		}
		if($this->system->cache == 1) $this->cache->remove_group('categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
	}

	public function categoriesDeleteImages($categories_id = '', $images_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->load->library('medias');
		$this->medias->delete_medias($images_id);
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/categoriesEdit/'.$categories_id.'#four');
	}

	//---------------------------------------------- medias

	public function medias($start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$per_page = $this->portfolio->settings['per_portfolio_medias'];

		//------------- Filtre search
		$like = '';
		if ($filter_search = $this->input->post('filter_search'))
		{
			$like = $filter_search;
			$this->session->set_userdata('filter_search', $filter_search);
		}
		if($_POST && !$_POST['filter_search'])
		{
			$like = '';
			$this->session->unset_userdata('filter_search');
		}
		if($filter_search = $this->session->userdata('filter_search'))
		{
			$like = $filter_search;
		}

		$and_or_like = 'like';
		if($this->input->post('filter_or'))
		{
			$and_or_like = 'or_like';
			$this->session->set_userdata('filter_or', $and_or_like);
		}
		else
		{
			$this->session->set_userdata('filter_or', 'like');
		}

		$medias = $this->portfolio->list_medias(array('select' => '*, '.$this->config->item('table_portfolio_medias').'.id as mID, '.$this->config->item('table_portfolio_medias').'.active as mACTIVE, '.$this->config->item('table_portfolio_medias_lang').'.uri as mURI, '.$this->config->item('table_portfolio_medias_lang').'.title as mTITLE', 'like' => trim($like), 'start' => $start, 'limit' => $per_page, 'order_by' => $this->config->item('table_portfolio_categories_to_medias').'.ordering'));
		$total_medias = $this->portfolio->total_list_medias(array('like' => trim($like)));

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = 4;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/medias');
		$config['total_rows'] = $total_medias;
		$config['per_page'] = $per_page;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total_medias'] = $total_medias;
		$this->template['start'] = $start;

		$this->template['medias'] = $medias;
		$this->template['filter'] = true;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/medias/index');

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

	}

	public function mediasCreate($categories_id = 1)
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation_medias);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['media'] = array('id' => '', 'active' => '', 'title' => '', 'uri' => '', 'alt' => '', 'legend' => '', 'body' => '', 'categories_id_default' => '', 'is_box' => '');
			if (!$categories = $this->cache->get('list_categories_recursiv', 'categories'))
			{
				$categories = $this->portfolio->list_categories(0, '', '', 1000, 0, true);
				if($this->system->cache == 1) $this->cache->save('list_categories_recursiv', $categories, 'categories', 0);
			}
			$this->template['categories'] = $categories;
			$this->template['categories_id'] = $categories_id;
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'filestyle', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/medias/create');
		}
		else
		{
			$this->load->library('medias');
			$medias_id = $this->model->create_medias();
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			if($this->system->cache == 1) $this->cache->remove_group('navigation');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			
			if(set_value('medias_redirect') == 1)
				redirect($this->session->userdata('redirect_uri').'#medias');
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/mediasEdit/'.$categories_id.'/'.$medias_id.set_value('medias_tabs'));
		}

	}

	public function mediasEdit($categories_id = 1, $medias_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$this->form_validation->set_rules($this->fields_validation_medias);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['media'] = $this->portfolio->get_medias(array('id' => $medias_id));
			if (!$categories = $this->cache->get('list_categories_recursiv', 'categories'))
			{
				$categories = $this->portfolio->list_categories(0, '', '', 1000, 0, true);
				if($this->system->cache == 1) $this->cache->save('list_categories_recursiv', $categories, 'categories', 0);
			}
			if($check_categories = $this->portfolio->list_categories_to_medias(array('where' => array('medias_id' => $medias_id))))
			{
				foreach($check_categories as $key => $value)
				{
					$medias_to_categories[$key] = $key;
				}
				$this->template['medias_to_categories'] = $medias_to_categories;
			}

			$this->template['categories'] = $categories;
			$this->template['categories_id'] = $categories_id;
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'filestyle', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/medias/create');
		}
		else
		{
			$medias_id = $this->model->create_medias();
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			if($this->system->cache == 1) $this->cache->remove_group('navigation');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			
			if(set_value('medias_redirect') == 1)
				redirect($this->session->userdata('redirect_uri').'#medias');
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/mediasEdit/'.$categories_id.'/'.$medias_id.set_value('medias_tabs'));
		}

	}

	public function mediasDelete($medias_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->model->delete_medias($medias_id);
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri').'#medias');
	}

	public function mediasFlag($medias_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->system->flag($medias_id, $flag, $this->config->item('table_portfolio_medias'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri').'#medias');
	}

	function ajaxSortOrderMedia ($categories_id = '')
	{
		$listItem = $_POST['listItem'];
		for ($i=0, $n=sizeof($listItem); $i<$n;)
		{
			$this->db->update($this->config->item('table_portfolio_categories_to_medias'), array('ordering ' => $i), "medias_id = '".$listItem[$i]."' and categories_id = '".$categories_id."' ");
			$i++;
		}
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
	}

}
