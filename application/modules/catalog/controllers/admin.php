<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->template['admin'] = true;
		$this->template['module'] = 'catalog';
		$this->config->load('config');
		$this->load->library('administration');
		$this->load->library('form_validation');
		$this->load->library('catalogue', '', 'catalog');
		$this->load->library('tva');
		$this->load->model('catalog_model', 'model');

		$this->catalog->settings = isset($this->system->catalog_settings) ? unserialize($this->system->catalog_settings) : array();

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
			$setting = is_array($this->input->post('settings')) ? serialize($this->input->post('settings')) : '';
			$this->system->set('catalog_settings', $setting);
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

	//---------------------------------------------- Categories
	public function categories($categories_id = 1)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		if (!$categories = $this->cache->get('list_categories_'.$categories_id, 'catalog_categories'))
		{
			$categories = $this->catalog->list_categories($categories_id, '', '', 1000, 0, false);
			if($this->system->cache == 1) $this->cache->save('list_categories_'.$categories_id, $categories, 'catalog_categories', 0);
		}

		$this->template['categories'] = $categories;
		$this->template['total_categories'] = count($categories);
		$this->template['products'] = $this->catalog->list_products(array('limit' => 1000, 'where' => array('categories_id' => $categories_id), 'order_by' => $this->config->item('table_products_to_categories').'.ordering'));
		$this->template['total_products'] = count($this->template['products']);
		$this->template['categorie'] = $this->catalog->get_categories(array('id' => $categories_id));
		$this->template['categories_id'] = $categories_id;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/categories/index');

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

	}

	public function categoriesCreate($categories_id = 1)
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if (!$this->input->post('submit'))
		{
			if (!$parents = $this->cache->get('list_categories_parents_'.$categories_id, 'catalog_categories'))
			{
				$parents = $this->catalog->list_categories('','','');
				if($this->system->cache == 1) $this->cache->save('list_categories_parents_'.$categories_id, $parents, 'catalog_categories', 0);
			}
			$this->template['categorie'] = array('id' => '', 'parent_id' => '', 'active' => '', 'ordering' => '', 'title' => '', 'uri' => '', 'body' => '', 'meta_title' => '', 'meta_keywords' => '', 'meta_description' => '');
			$this->template['categories_id'] = $categories_id;
			$this->template['parents'] = $parents;
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/categories/create');
		}
		else
		{
			$categories_id = $this->model->create_categories();
			if($this->system->cache == 1) $this->cache->remove_group('catalog_categories');
			if($this->system->cache == 1) $this->cache->remove_group('navigation');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			
			if($this->input->post('categories_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/categoriesEdit/'.$categories_id.$this->input->post('categories_tabs'));			
		}

	}

	public function categoriesEdit($categories_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->load->library('medias');

		if (!$this->input->post('submit'))
		{			
			if (!$parents = $this->cache->get('list_categories_parents_'.$categories_id, 'catalog_categories'))
			{
				$parents = $this->catalog->list_categories('','','');
				if($this->system->cache == 1) $this->cache->save('list_categories_parents_'.$categories_id, $parents, 'catalog_categories', 0);
			}
			
			$this->template['categorie'] = $this->catalog->get_categories(array('id' => $categories_id));
			$this->template['parents'] = $parents;
			$this->template['image'] = $this->medias->get_medias(array('src_id' => $categories_id, 'module' => 'categories'));
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/categories/create');
		}
		else
		{
			$this->model->create_categories();
			if($this->system->cache == 1) $this->cache->remove_group('catalog_categories');
			if($this->system->cache == 1) $this->cache->remove_group('navigation');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			if($this->input->post('categories_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/categoriesEdit/'.$categories_id.$this->input->post('categories_tabs'));		
		}

	}

	public function categoriesDelete($categories_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		$this->model->delete_categories($categories_id);
		$this->model->delete_categories_to_products($categories_id);

		$this->load->library('medias');

		if($medias = $this->medias->list_medias(array('where' => array('src_id' => $categories_id, 'module' => 'categories'))))
		{
			foreach($medias as $media)
			{
				$this->medias->delete_medias($media['id']);
			}
		}

		if($this->system->cache == 1) $this->cache->remove_group('catalog_categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
		redirect($this->session->userdata('redirect_uri'));
	}

	public function categoriesFlag($categories_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->system->flag($categories_id, $flag, $this->config->item('table_categories'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group('catalog_categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
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
		if($this->system->cache == 1) $this->cache->remove_group('catalog_categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
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
				$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_categories'))." set ordering = ".$ordering." where parent_id = '".(int)$parent_id."' AND id = '".(int)$categories_id."'");
			}
		}
		if($this->system->cache == 1) $this->cache->remove_group('catalog_categories');
		if($this->system->cache == 1) $this->cache->remove_group('navigation');
	}

	public function categoriesDeleteImages($categories_id = '', $images_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->load->library('medias');
		$this->medias->delete_medias($images_id);
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/categoriesEdit/'.$categories_id.'#four');
	}

	//---------------------------------------------- Products

	function productsSearch()
	{
		if($like = $this->input->post('term'))
		{
			$data = array();
			if($products = $this->catalog->list_products(array('where' => array('active' => 1), 'select' => $this->config->item('table_products').'.id as pID, '.$this->config->item('table_products_lang').'.title as pTITLE', 'like' => htmlspecialchars($like)), false))
			{
				foreach($products as $product)
				{
					$data[] = array(
						'label' 	=> html_entity_decode($product['pTITLE']),
						'id' 		=> $product['pID']
					);
				}
			}
			echo json_encode($data);
		}
	}
	
	public function productsImport()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		$config['upload_path'] = './'.$this->config->item('medias_folder').'/tmp/';
		$config['allowed_types'] = 'csv';
		$config['overwrite']	= TRUE;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('userfile'))
		{			
			if($_POST)
			{
				//echo $this->upload->display_errors('', '<br />');;
				$this->template['alerte'] = $this->upload->display_errors('', '<br />');
			}
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/products/import');
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$this->load->library('csvreader');
			$this->load->model('import_model', 'import');
			$file = $data['upload_data']['file_name'];

			if(is_file('./'.$this->config->item('medias_folder').'/tmp/'.$file))
			{
				$rows = $this->csvreader->parse_file('./'.$this->config->item('medias_folder').'/tmp/'.$file);
				$this->import->import_products($rows, $file);
				$this->session->set_flashdata('notification', 'Import effectué avec succès');
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsImport');
			}
			else
			{
				$this->session->set_flashdata('alerte', 'Fichier CSV non trouvé');
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsImport');
			}
		}
	}

	public function products($start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

		$this->load->helper('catalog');

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

		//------------- Filtre categories
		$where = '';
		if ($filter_categories = $this->input->post('filter_categories'))
		{
			if ($filter_categories == '-1')
			{
				$this->session->unset_userdata('filter_categories');
			}
			else
			{
				$this->session->set_userdata('filter_categories', $filter_categories);
			}
		}
		if($filter_categories = $this->session->userdata('filter_categories'))
		{
			$where = array('categories_id' => $filter_categories);
		}

		$per_page = $this->catalog->settings['per_page'];

		$products = $this->catalog->list_products(array('select' => '*, '.$this->config->item('table_products').'.id as pID', 'start' => $start, 'limit' => $per_page, 'where' => $where, $and_or_like => trim(htmlspecialchars($like))));
		$total_products =  $this->catalog->total_list_products(array('where' => $where, $and_or_like => trim(htmlspecialchars($like))));

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = 4;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/products');
		$config['total_rows'] = $total_products;
		$config['per_page'] = $per_page;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total'] = $total_products;
		$this->template['start'] = $start;

		$this->template['filter'] = TRUE;

		if (!$categories = $this->cache->get('list_categories', 'catalog_categories'))
		{
			$categories = $this->catalog->list_categories('');
			if($this->system->cache == 1) $this->cache->save('list_categories', $categories, 'catalog_categories', 0);
		}
		$this->template['categories'] = $categories;
		$this->template['products'] = $products;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/products/index');
	}

	public function productsCreate($categories_id = 1)
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if (!$this->input->post('submit'))
		{
			$this->template['product'] = array('id' => '', 'active' => '', 'categories_id_default' => '', 'manufacturers_id' => '', 'ordering' => '', 'title' => '', 'reference' => '', 'uri' => '', 'price' => '', 'price_shopping' => '', 'tva' => '', 'body' => '', 'meta_title' => '', 'meta_keywords' => '', 'meta_description' => '');
			$this->template['tva'] = $this->tva->list_tva();
			if (!$categories = $this->cache->get('list_categories_recursiv', 'catalog_categories'))
			{
				$categories = $this->catalog->list_categories(0, '', '', 1000, 0, true);
				if($this->system->cache == 1) $this->cache->save('list_categories_recursiv', $categories, 'catalog_categories', 0);
			}
			$this->template['categories'] = $categories;
			$this->template['categories_id'] = $categories_id;			
			$this->template['manufacturers'] = $this->catalog->list_manufacturers();
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'filestyle', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/products/create');
		}
		else
		{
			$products_id = $this->model->create_products();
			if($this->system->cache == 1) $this->cache->remove_group('products');
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));			
				
				
			if($this->input->post('categories_id_default') != '')
				$categories_id = $this->input->post('categories_id_default');
			else
				$categories_id = 1;
				
			if($this->input->post('products_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.$this->input->post('products_tabs'));			
			
		}

	}

	public function productsEdit($categories_id = '', $products_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$this->load->helper('format');

		$this->load->library('medias');

		$fields_validation[] = array(
			'field'   => 	'attributes_id',
			'label'   => 	$this->lang->line('validation_products_attributes'),
			'rules'   => 	'trim|numeric|callback__verify_products_attributes'
		);

		$this->form_validation->set_rules($fields_validation);

		$this->fields['attributes_id'] = $this->lang->line('validation_products_attributes');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['product'] = $this->catalog->get_products(array('id' => $products_id));
			$this->template['tva'] = $this->tva->list_tva();
			if (!$categories = $this->cache->get('list_categories_recursiv', 'catalog_categories'))
			{
				$categories = $this->catalog->list_categories(0, '', '', 1000, 0, true);
				if($this->system->cache == 1) $this->cache->save('list_categories_recursiv', $categories, 'catalog_categories', 0);
			}
			$this->template['categories'] = $categories;
			$this->template['categories_id'] = $categories_id;
			$this->template['products_to_categories'] = $this->catalog->check_products_to_categories(array('products_id' => $products_id), 'categories_id');
			$this->template['images'] = $this->medias->list_medias(array('where' => array('module' => 'products', 'src_id' => $products_id), 'order_by' => 'ordering'));
			$this->template['products_combos'] = $this->catalog->list_products_to_products($products_id);
			$this->template['products_attributes_values'] = $this->catalog->list_products_attributes_values(array('select' => 'products_id, attributes_id, attributes_values_id, price, suffix, ordering, products_to_attributes_values.id as pavID, '.$this->config->item('table_attributes_lang').'.name as aNAME, '.$this->config->item('table_attributes_values_lang').'.name as avNAME', 'where' => array($this->config->item('table_attributes_lang').'.lang' => $this->user->lang, $this->config->item('table_attributes_values_lang').'.lang' => $this->user->lang, 'products_id' => $products_id)));
			$this->template['manufacturers'] = $this->catalog->list_manufacturers();

			if($attributes = $this->catalog->list_attributes(array('where' => array('lang' => $this->user->lang), 'select' => '*', 'start' => 0, 'limit' => 100)))
			{
				$this->template['attributes'] = $attributes;
				$this->template['attributes_values'] = $this->catalog->list_attributes_values(array('where' => array('attributes_id' => $attributes[0]['id'], 'lang' => $this->user->lang), 'select' => '*'));
			}
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'filestyle', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/products/create');
		}
		else
		{
			$this->model->create_products();
			if($this->system->cache == 1) $this->cache->remove_group('products');
			if($this->system->cache == 1) $this->cache->remove_group('medias');
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			
			if($this->input->post('categories_id_default') != '')
				$categories_id = $this->input->post('categories_id_default');
			else
				$categories_id = 1;
			if($this->input->post('products_redirect') == 1)
				redirect($this->session->userdata('redirect_uri'));
			else
				redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.$this->input->post('products_tabs'));
		}

	}

	public function productsDelete($products_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		$this->model->delete_products($products_id);
		$this->model->delete_products_to_categories($products_id);
		$this->model->delete_products_to_products($products_id);

		$this->load->library('medias');

		if($medias = $this->medias->list_medias(array('where' => array('src_id' => $products_id, 'module' => 'products'))))
		{
			foreach($medias as $media)
			{
				$this->medias->delete_medias($media['id']);
			}
		}

		if($this->system->cache == 1) $this->cache->remove_group('products');
		if($this->system->cache == 1) $this->cache->remove_group('medias');

		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

		redirect($this->session->userdata('redirect_uri'));
	}

	public function productsDeleteArray()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if($products = $this->input->post('delete_products'))
		{
			foreach($products as $products_id)
			{
				$this->model->delete_products($products_id);
			}
		}

		if($this->system->cache == 1) $this->cache->remove_group('products');
		if($this->system->cache == 1) $this->cache->remove_group('medias');

		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

		redirect($this->session->userdata('redirect_uri'));
	}

	public function productsDeleteImages($categories_id = '', $products_id = '', $images_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->load->library('medias');
		$this->medias->delete_medias($images_id);
		if($this->system->cache == 1) $this->cache->remove_group('products');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.'#five');
	}

	public function productsCombosDelete($categories_id = '', $products_id_x = '', $products_id_y = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		$this->model->delete_products_combos($products_id_x, $products_id_y);

		if($this->system->cache == 1) $this->cache->remove_group('products');

		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id_x.'#six');
	}

	public function productsFlag($categories_id = '', $flag = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->system->flag($categories_id, $flag, $this->config->item('table_products'), 'active');
		if($this->system->cache == 1) $this->cache->remove_group('products');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri'));
	}

	public function productsMove ($products_id = '', $categories_id = '', $direction = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if (!isset($products_id) || !isset($categories_id) || !isset($direction))
		{
			redirect($this->session->userdata('redirect_uri'));
		}

		$this->model->move_products($products_id, $categories_id, $direction);
		if($this->system->cache == 1) $this->cache->remove_group('categories');
		if($this->system->cache == 1) $this->cache->remove_group('products');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->session->userdata('redirect_uri').'#products');
	}

	public function productsMoveImages($categories_id = '', $products_id = '', $images_id = '', $direction = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$this->load->library('medias');
		if (!isset($categories_id) || !isset($products_id) || !isset($images_id) || !isset($direction))
		{
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.'#five');
		}

		$this->medias->move_medias($products_id, $images_id, $direction, 'products');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.'#five');
	}

	public function productsSortOrder($categories_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		if($items = $this->input->post('items'))
		{
			foreach ($items as $ordering => $products_id)
			{
				$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_products_to_categories'))." set ordering = ".$ordering." where categories_id = '".(int)$categories_id."' AND products_id = '".(int)$products_id."'");
			}
		}
		if($this->system->cache == 1)  $this->cache->remove_group('products');
	}

	public function productsCoverImages($categories_id = '', $products_id = '', $images_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		if (!isset($categories_id) || !isset($products_id) || !isset($images_id))
		{
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.'#five');
		}
		$this->load->library('medias');
		$this->model->cover_products_images($products_id, $images_id, 'products');
		if($this->system->cache == 1) $this->cache->remove_group('medias');
		$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.'#five');
	}

	public function productsAttributesDelete($categories_id = '', $products_id = '', $products_attributes_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->model->delete_products_attributes($products_attributes_id);
		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/productsEdit/'.$categories_id.'/'.$products_id.'#seven');
	}

	public function productsComboSearch($products_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		if($like = $this->input->post('term'))
		{
			$data = array();

			if($products = $this->catalog->list_products(array('where' => array('active' => 1, 'id !=' => $products_id), 'select' => $this->config->item('table_products').'.id as pID, title', 'like' => $like), false))
			{
				foreach($products as $product)
				{
					$data[] = array(
						'label' => html_entity_decode($product['title']),
						'id' => $product['pID']
					);
				}
			}
			echo json_encode($data);
		}
	}

	public function selectProductsCombo($products_id_x = '')
	{
		if($products_id_y = $this->input->post('products_id_y'))
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			if(!$product = $this->catalog->get_products_to_products(array('products_id_x' => $products_id_x, 'products_id_y' => $products_id_y)))
			{
				$this->db->insert($this->config->item('table_products_to_products'), array('products_id_x' => $products_id_x, 'products_id_y' => $products_id_y));
				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
				if($this->system->cache == 1) $this->cache->remove_group('products');
			}
		}
	}

	public function ajaxProductsChangeAttributes()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$data = array();
		if($attributes_values = $this->catalog->list_attributes_values(array('where' => array('lang' => $this->user->lang, 'attributes_id' => $this->input->post('attributes_id')))))
		{
			$data['options'] = '';
			foreach($attributes_values as $attribute_value)
			{
				$data['options'] .= '<option value="'.$attribute_value['id'].'">'.html_entity_decode($attribute_value['name']).'</option>';
			}
			$data['color'] = '';
			if($attribute_value = $this->catalog->get_attributes_values(array('attributes_values.id' => $attributes_values[0]['id'])))
			{
				$data['color'] = $attribute_value['color'];
			}

		}


		echo json_encode($data);
	}

	public function ajaxProductsChangeAttributesValues()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);
		$data['color'] = '';
		if($attribute_value = $this->catalog->get_attributes_values(array('attributes_values.id' =>$this->input->post('attributes_values_id'))))
		{
			$data['color'] = $attribute_value['color'];
		}

		echo json_encode($data);
	}

	//---------------------------------------------- Specials

	public function specials($start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

		$this->load->helper('catalog');

		$per_page = $this->catalog->settings['per_page'];

		$specials = $this->catalog->list_specials(array('select' => '*, '.$this->config->item('table_specials').'.id as sID, '.$this->config->item('table_specials').'.tva as sTVA', 'start' => $start, 'limit' => $per_page));
		$total_specials =  $this->catalog->total_list_specials();

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = 4;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/specials');
		$config['total_rows'] = $total_specials;
		$config['per_page'] = $per_page;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total'] = $total_specials;
		$this->template['start'] = $start;

		$this->template['specials'] = $specials;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/specials/index');
	}

	public function specialsCreate()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$fields_validation[] = array(
			'field'   => 	'products_id',
			'label'   => 	$this->lang->line('validation_products'),
			'rules'   => 	'trim|required|numeric|callback__verify_products'
		);
		$fields_validation[] = array(
			'field'   => 	'new_price',
			'label'   => 	$this->lang->line('validation_new_price'),
			'rules'   => 	'trim|required'
		);

		$this->form_validation->set_rules($fields_validation);

		$this->fields['products_id'] = $this->lang->line('validation_username');
		$this->fields['new_price'] = $this->lang->line('validation_email');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['special'] = array('id' => '', 'products_id' => '', 'new_price' => '', 'tva' => '', 'date_added' => '', 'date_modified' => '', 'date_begin' => '', 'date_end' => '', 'active' => '');
			$this->template['tva'] = $this->tva->list_tva();
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'filestyle', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/specials/create');
		}
		else
		{
			$specials_id = $this->model->create_specials();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/specialsEdit/'.$specials_id);
		}
	}

	public function specialsEdit($specials_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$fields_validation[] = array(
			'field'   => 	'products_id',
			'label'   => 	$this->lang->line('validation_products'),
			'rules'   => 	'trim|required|numeric|callback__verify_products'
		);
		$fields_validation[] = array(
			'field'   => 	'new_price',
			'label'   => 	$this->lang->line('validation_new_price'),
			'rules'   => 	'trim|required'
		);

		$this->form_validation->set_rules($fields_validation);

		$this->fields['products_id'] = $this->lang->line('validation_username');
		$this->fields['new_price'] = $this->lang->line('validation_email');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['special'] = $this->catalog->get_specials(array('id' => $specials_id));
			$this->template['product'] = $this->catalog->get_products(array('id' => $this->template['special']['products_id']));
			$this->template['tva'] = $this->tva->list_tva();
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'filestyle', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/specials/create');
		}
		else
		{
			$specials_id = $this->model->create_specials();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/specialsEdit/'.$specials_id);
		}
	}

	public function specialsDelete($specials_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		$this->model->delete_specials($specials_id);

		redirect($this->session->userdata('redirect_uri'));
	}

	public function specialsDeleteArray()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if($specials = $this->input->post('delete_specials'))
		{
			foreach($specials as $specials_id)
			{
				$this->model->delete_specials($specials_id);
			}
		}
		redirect($this->session->userdata('redirect_uri'));
	}

	//---------------------------------------------- Attributes

	public function attributes ($start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

		$this->load->helper('format');

		$per_page = $this->catalog->settings['per_page'];

		$attributes = $this->catalog->list_attributes(array('where' => array('lang' => $this->user->lang), 'select' => '*', 'start' => $start, 'limit' => $per_page));
		$total_attributes =  $this->catalog->total_list_attributes(array('where' => array('lang' => $this->user->lang)));

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = 4;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/attributes');
		$config['total_rows'] = $total_attributes;
		$config['per_page'] = $per_page;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total'] = $total_attributes;
		$this->template['start'] = $start;

		$this->template['attributes'] = $attributes;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/attributes/index');
	}

	public function attributesCreate()
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$fields_validation[] = array(
			'field'   => 	'is_color',
			'label'   => 	$this->lang->line('validation_is_color'),
			'rules'   => 	'trim|required|numeric'
		);
		$fields_validation[] = array(
			'field'   => 	'name',
			'label'   => 	$this->lang->line('validation_name'),
			'rules'   => 	'trim|required|max_length[64]|xss_clean'
		);

		$this->form_validation->set_rules($fields_validation);

		$this->fields['is_color_group'] = $this->lang->line('validation_is_color');
		$this->fields['name'] = $this->lang->line('validation_name');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['attribute'] = array('id' => '', 'is_color' => '', 'name' => '');
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/attributes/create');
		}
		else
		{
			$attributes_id = $this->model->create_attributes();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/attributesEdit/'.$attributes_id);
		}
	}

	public function attributesEdit($attributes_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$fields_validation[] = array(
			'field'   => 	'is_color',
			'label'   => 	$this->lang->line('validation_is_color'),
			'rules'   => 	'trim|required|numeric'
		);
		$fields_validation[] = array(
			'field'   => 	'name',
			'label'   => 	$this->lang->line('validation_name'),
			'rules'   => 	'trim|required|max_length[64]|xss_clean'
		);

		$this->form_validation->set_rules($fields_validation);

		$this->fields['is_color_group'] = $this->lang->line('validation_is_color');
		$this->fields['name'] = $this->lang->line('validation_name');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['attribute'] = $this->catalog->get_attributes(array($this->config->item('table_attributes').'.id' => $attributes_id, 'lang' => $this->user->lang));
			$this->template['attributes_values'] = $this->catalog->list_attributes_values(array('where' => array($this->config->item('table_attributes_values').'.attributes_id' => $attributes_id, 'lang' => $this->user->lang)));
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/attributes/create');
		}
		else
		{
			$attributes_id = $this->model->create_attributes();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/attributesEdit/'.$attributes_id);
		}
	}

	public function attributesDelete($attributes_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		$this->model->delete_attributes($attributes_id);

		redirect($this->session->userdata('redirect_uri'));

	}

	public function attributesDeleteArray()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if($attributes = $this->input->post('delete_attributes'))
		{
			foreach($attributes as $attributes_id)
			{
				$this->model->delete_attributes($attributes_id);
			}
		}
		redirect($this->session->userdata('redirect_uri'));
	}

	public function attributesValuesCreate($attributes_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		$fields_validation[] = array(
			'field'   => 	'attributes_id',
			'label'   => 	$this->lang->line('validation_attributes_id'),
			'rules'   => 	'trim|required|numeric'
		);
		$fields_validation[] = array(
			'field'   => 	'name',
			'label'   => 	$this->lang->line('validation_name'),
			'rules'   => 	'trim|required|max_length[64]|xss_clean'
		);

		$this->form_validation->set_rules($fields_validation);

		$this->fields['attributes_id'] = $this->lang->line('validation_attributes_id');
		$this->fields['name'] = $this->lang->line('validation_name');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['attribute'] = $this->catalog->get_attributes(array($this->config->item('table_attributes').'.id' => $attributes_id, 'lang' => $this->user->lang));
			$this->template['attribute_value'] = array('id' => '', 'attributes_id' => '', 'name' => '', 'color' => '');
			$this->css->add(array('admin', 'ui', 'colorpicker'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'tinymce', 'colorpicker'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/attributes/create-values');
		}
		else
		{
			$attributes_values_id = $this->model->create_attributes_values();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/attributesValuesEdit/'.$attributes_id.'/'.$attributes_values_id);
		}
	}

	public function attributesValuesEdit($attributes_id = '', $attributes_values_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_EDIT);

		$fields_validation[] = array(
			'field'   => 	'attributes_id',
			'label'   => 	$this->lang->line('validation_attributes_id'),
			'rules'   => 	'trim|required|numeric'
		);
		$fields_validation[] = array(
			'field'   => 	'name',
			'label'   => 	$this->lang->line('validation_name'),
			'rules'   => 	'trim|required|max_length[64]|xss_clean'
		);
		$fields_validation[] = array(
			'field'   => 	'color',
			'label'   => 	$this->lang->line('validation_color'),
			'rules'   => 	'trim|max_length[64]|xss_clean'
		);

		$this->form_validation->set_rules($fields_validation);

		$this->fields['attributes_id'] = $this->lang->line('validation_attributes_id');
		$this->fields['name'] = $this->lang->line('validation_name');

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template['attribute'] = $this->catalog->get_attributes(array($this->config->item('table_attributes').'.id' => $attributes_id, 'lang' => $this->user->lang));
			$this->template['attribute_value'] = $this->catalog->get_attributes_values(array($this->config->item('table_attributes_values').'.id' => $attributes_values_id, 'lang' => $this->user->lang));
			$this->css->add(array('admin', 'ui', 'colorpicker'));
			$this->javascripts->add(array('jquery', 'ui', 'sitelib', 'tinymce', 'colorpicker'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/attributes/create-values');
		}
		else
		{
			$attributes_values_id = $this->model->create_attributes_values();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/attributesValuesEdit/'.$attributes_id.'/'.$attributes_values_id);
		}
	}

	public function attributesValuesDelete($attributes_id = '', $attributes_values_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		$this->model->delete_attributes_values($attributes_values_id);

		redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/attributesEdit/'.$attributes_id.'#two');

	}
	
	//---------------------------------------------- Manufacturers

	public function manufacturers ($start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$this->session->set_userdata('redirect_uri', $this->uri->uri_string());

		$this->load->helper('format');

		$per_page = $this->catalog->settings['per_page'];

		$manufacturers = $this->catalog->list_manufacturers(array('select' => '*', 'start' => $start, 'limit' => $per_page));
		$total_manufacturers =  $this->catalog->total_list_manufacturers();

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = 4;
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->config->item('admin_folder').'/'.$this->template['module'].'/manufacturers');
		$config['total_rows'] = $total_manufacturers;
		$config['per_page'] = $per_page;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total'] = $total_manufacturers;
		$this->template['start'] = $start;

		$this->template['manufacturers'] = $manufacturers;
		$this->template['filter'] = TRUE;

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
		$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/manufacturers/index');
	}

	public function manufacturersCreate($manufacturers_id = 1)
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if (!$this->input->post('submit'))
		{
			$this->template['manufacturer'] = array('id' => '', 'title' => '', 'uri' => '');
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/manufacturers/create');
		}
		else
		{
			$manufacturers_id = $this->model->create_manufacturers();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/manufacturersEdit/'.$manufacturers_id);
		}

	}

	public function manufacturersEdit($manufacturers_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_ADD);

		if (!$this->input->post('submit'))
		{
			$this->template['manufacturer'] = $this->catalog->get_manufacturers(array('id' => $manufacturers_id));
			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib', 'tinymce'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'admin/manufacturers/create');
		}
		else
		{
			$this->model->create_manufacturers();
			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));
			redirect($this->config->item('admin_folder').'/'.$this->template['module'].'/manufacturersEdit/'.$manufacturers_id);
		}

	}

	public function manufacturersDelete($manufacturers_id = '')
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);
		$this->model->delete_manufacturers($manufacturers_id);
		redirect($this->session->userdata('redirect_uri'));
	}

	public function manufacturersDeleteArray()
	{
		$this->user->check_level($this->template['module'], LEVEL_DEL);

		if($manufacturers = $this->input->post('delete_manufacturers'))
		{
			foreach($manufacturers as $manufacturers_id)
			{
				$this->model->delete_manufacturers($manufacturers_id);
			}
		}
		redirect($this->session->userdata('redirect_uri'));
	}


	/*
	*
	* Callback functions
	*
	*/

	function _verify_products($data)
	{
		$products_id = $this->input->post('products_id');
		$id = $this->input->post('id');

		if ($this->model->exists_specials(array('products_id' => $products_id, 'id !=' => $id)))
		{
			$this->form_validation->set_message('_verify_products', $this->lang->line('alert_products_already_used'));
			return FALSE;
		}

	}

	function _verify_products_attributes($data)
	{
		$products_id = $this->input->post('id');
		$attributes_id = $this->input->post('attributes_id');
		$attributes_values_id = $this->input->post('attributes_values_id');

		if ($this->model->exists_products_attributes(array('products_id' => $products_id, 'attributes_id' => $attributes_id, 'attributes_values_id' => $attributes_values_id)))
		{
			$this->form_validation->set_message('_verify_products_attributes', $this->lang->line('alert_attributes_already_used'));
			return FALSE;
		}

	}





}
