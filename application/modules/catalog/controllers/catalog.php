<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Catalog extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->template['module'] = 'catalog';
		$this->config->load('config');
		$this->load->library('catalogue', '', 'catalog');
		$this->load->library('tva');
		$this->load->library('pagination');
		$this->load->helper('catalog');
		$this->load->model('catalog_model', 'model');

		$this->catalog->settings = isset($this->system->catalog_settings) ? unserialize($this->system->catalog_settings) : array();
		if($this->system->modules[$this->template['module']]['active'] != 1) redirect('pages/unauthorized/'.$this->template['module'].'/1');

	}

	public function index()
	{
		if($categorie_home = $this->catalog->get_categories(array('id' => 1)))
		{
			redirect($this->template['module'].'/categories/'.$categorie_home['id'].'/'.$categorie_home['uri'].'/index'.$this->config->item('url_suffix_ext'));
		}
		else
		{
			redirect();
		}
	}

	public function search($uri = '', $start = 0)
	{
		if ($this->system->modules[$this->template['module']]['active'] == 1)
		{
			if($keywords = $this->input->post('search_catalog'))
			{
				$this->template['breadcrumb'][] = 	array(
					'title'	=> 'Recherche',
					'uri'	=> $this->template['module'].'/search/'.format_title($keywords).'/index'
				);
				$products = $this->catalog->search_products(htmlentities($keywords));
				$this->session->set_userdata('search_catalog', ucwords($keywords));
				$this->session->set_userdata('search_catalog_products', $products);
				redirect($this->template['module'].'/search/'.format_title($keywords).'/index'.$this->config->item('url_suffix_ext'));

			}
			if($products = $this->session->userdata('search_catalog_products'))
			{
				$per_page = $this->catalog->settings['per_page'];

				$prods = $this->catalog->list_products(array('select' => '*, '.$this->config->item('table_products').'.id as pID, '.$this->config->item('table_products').'.title as pTITLE, '.$this->config->item('table_products').'.uri as pURI, '.$this->config->item('table_products').'.tva as pTVA, '.$this->config->item('table_specials').'.tva as sTVA, '.$this->config->item('table_specials').'.active as sACTIVE', 'where' => 'products.id IN('.implode(',', $products).')', 'start' => $start, 'limit' => $per_page), true);
				$total_prods = $this->catalog->total_list_products(array('where' => 'products.id IN('.implode(',', $products).')'), true);

				//------------- Requetes images
				if($prods)
				{
					$this->load->library('medias');
					$images = array();
					foreach($products as $product)
					{
						if($rows = $this->medias->list_medias(array('where' => array('src_id' => $product, 'module' => 'products'))))
						{
							if(is_array($rows))
							{
								foreach($rows as $row)
								{
									if($row['options']['cover'] == 1) $images[$product] = $row;
								}
							}
						}
					}
					$this->template['images'] = $images;
				}

				//------------- Pagination
				$config['num_links'] = $this->system->num_links;
				$config['uri_segment'] = 4;
				$config['first_link'] = $this->lang->line('text_begin');
				$config['last_link'] = $this->lang->line('text_end');
				$config['base_url'] = site_url($this->template['module'].'/search/'.$uri);
				$config['total_rows'] = $total_prods;
				$config['per_page'] = $per_page;
				$this->load->library('pagination');

				$this->pagination->initialize($config);

				//------------- Variables
				$this->template['pager'] = $this->pagination->create_links();
				$this->template['start'] = $start;
				$this->template['total'] = $config['total_rows'];
				$this->template['per_page'] = $config['per_page'];
				$this->template['total_rows'] = $config['total_rows'];
				$this->template['products'] = $prods;

			}
			if(!$title = $this->session->userdata('search_catalog'))
			{
				$this->template['breadcrumb'][] = 	array(
					'title'	=> 'Recherche',
					'uri'	=> $this->template['module'].'/search/index'
				);
				$title = $this->lang->line('text_products_not_found');

			}
			else
			{
				$this->template['breadcrumb'][] = 	array(
					'title'	=> 'Recherche',
					'uri'	=> $this->template['module'].'/search/'.format_title($title).'/0/index'
				);

			}
			$this->template['title'] = $title;
			$view = 'search';
		}
		else
		{
			$this->output->set_header("HTTP/1.0 403 Forbidden");
			$this->template['title'] = 'Non autorisé';
			$view = '403';
		}
		$this->javascripts->add(array('jquery', 'colorbox', 'swfobject', 'slider', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->layout->load($this->template, $this->system->theme,  $view);
	}

	public function categories($categories_id = '', $categories_uri = '', $start = 0)
	{
		if ($this->system->modules[$this->template['module']]['active'] == 1)
		{
			//------------- Sort
			if($sort = $this->input->post('sort'))
			{
				$this->session->set_userdata('sort', $sort);
				redirect($this->uri->uri_string());
			}
			if(!$sort = $this->session->userdata('sort'))
			{
				$sort = '';
			}

			$per_page = $this->catalog->settings['per_page'];
			//$per_page = 2;

			//------------- Requetes
			$products = $this->catalog->list_products(array('order_by' => str_replace('|', ' ', $sort), 'select' => '*, '.$this->config->item('table_products').'.id as pID, '.$this->config->item('table_products').'.title as pTITLE, '.$this->config->item('table_products').'.uri as pURI, '.$this->config->item('table_products').'.tva as pTVA, '.$this->config->item('table_specials').'.tva as sTVA, '.$this->config->item('table_specials').'.active as sACTIVE', 'where' => array('categories_id' => $categories_id, $this->config->item('table_products').'.active' => 1), 'start' => $start, 'limit' => $per_page), true);
			$total_products = $this->catalog->total_list_products(array('where' => array('categories_id' => $categories_id, $this->config->item('table_products').'.active' => 1)), true);
			$categorie = $this->catalog->get_categories(array('id' => $categories_id, 'uri' => $categories_uri));

			//------------- Requetes images
			if($products)
			{
				$this->load->library('medias');
				$images = array();
				foreach($products as $product)
				{
					if($rows = $this->medias->list_medias(array('where' => array('src_id' => $product['pID'], 'module' => 'products'))))
					{
						if(is_array($rows))
						{
							foreach($rows as $row)
							{
								if($row['options']['cover'] == 1) $images[$product['pID']] = $row;
							}
						}
					}
				}
				$this->template['images'] = $images;
			}

			//------------- Breadcrumb + Metas
			$this->template['meta_keywords'] = $this->system->meta_keywords;
			$this->template['meta_description'] = $this->system->meta_description;
			$this->template['meta_title'] = html_entity_decode($categorie['title']).((isset($start) && $start != 'index.html') ? ' - Page '.(($start/$per_page)+1) : ' - Page 1');

			/*
			 * Why not ??!!
			$this->template['breadcrumb'][] = 	array(
				'title'	=> 'Catalogue',
				'uri'	=> $this->template['module'].'/categories/'.$categories_id.'/'.$categories_uri.'/index'
			);
			* */

			//------------- Pagination
			$config['num_links'] = $this->system->num_links;
			$config['uri_segment'] = 5;
			$config['first_link'] = $this->lang->line('text_begin');
			$config['last_link'] = $this->lang->line('text_end');
			$config['base_url'] = site_url($this->template['module'].'/categories/'.$categories_id.'/'.$categories_uri);
			$config['total_rows'] = $total_products;
			$config['per_page'] = $per_page;
			$this->load->library('pagination');

			$this->pagination->initialize($config);

			//------------- Variables
			$this->template['pager'] = $this->pagination->create_links();
			$this->template['start'] = $start;
			$this->template['total'] = $config['total_rows'];
			$this->template['per_page'] = $config['per_page'];
			$this->template['total_rows'] = $config['total_rows'];
			$this->template['products'] = $products;
			$this->template['categorie'] = $categorie;
			$this->template['title'] = html_entity_decode($categorie['title']);

			$view = 'categories';
		}
		else
		{
			$this->output->set_header("HTTP/1.0 403 Forbidden");
			$this->template['title'] = 'Non autorisé';
			$view = '403';
		}
		$this->javascripts->add(array('jquery', 'colorbox', 'swfobject', 'slider', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->layout->load($this->template, $this->system->theme,  $view);

		$this->session->set_userdata('redirect_uri_front', $this->uri->uri_string());

	}

	public function specials($start = 0)
	{
		if ($this->system->modules[$this->template['module']]['active'] == 1)
		{
			//------------- Sort
			if($sort = $this->input->post('sort'))
			{
				$this->session->set_userdata('sort', $sort);
				redirect($this->uri->uri_string());
			}
			if(!$sort = $this->session->userdata('sort'))
			{
				$sort = $this->config->item('table_products').'.id';
			}

			$per_page = $this->catalog->settings['per_page'];
			$per_page = 2;

			//------------- Requetes
			$products = $this->catalog->list_specials(array('order_by' => str_replace('|', ' ', $sort), 'where' => array($this->config->item('table_products').'.active' => 1, $this->config->item('table_specials').'.active' => 1), 'select' => '*, '.$this->config->item('table_specials').'.id as sID, '.$this->config->item('table_specials').'.tva as sTVA, '.$this->config->item('table_products').'.id as pID, '.$this->config->item('table_products').'.title as pTITLE, '.$this->config->item('table_products').'.uri as pURI, '.$this->config->item('table_products').'.tva as pTVA', 'start' => $start, 'limit' => $per_page), true, true);
			$total_products =  $this->catalog->total_list_specials(array('where' => array($this->config->item('table_products').'.active' => 1, $this->config->item('table_specials').'.active' => 1)), true, true);

			//------------- Requetes images
			if($products)
			{
				$this->load->library('medias');
				$prods = array();
				$images = array();
				foreach($products as $product)
				{
					if($rows = $this->medias->list_medias(array('where' => array('src_id' => $product['pID'], 'module' => 'products'))))
					{
						if(is_array($rows))
						{
							foreach($rows as $row)
							{
								if($row['options']['cover'] == 1) $images[$product['pID']] = $row;
							}
						}
					}
				}
				$this->template['images'] = $images;
			}

			//------------- Breadcrumb + Metas
			$this->template['meta_keywords'] = $this->system->meta_keywords;
			$this->template['meta_description'] = $this->system->meta_description;
			$this->template['meta_title'] = $this->lang->line('title_specials');
			$this->template['title'] = $this->lang->line('title_specials');


			//------------- Pagination
			$config['num_links'] = $this->system->num_links;
			$config['uri_segment'] = 3;
			$config['first_link'] = $this->lang->line('text_begin');
			$config['last_link'] = $this->lang->line('text_end');
			$config['base_url'] = site_url($this->template['module'].'/specials');
			$config['total_rows'] = $total_products;
			$config['per_page'] = $per_page;
			$this->load->library('pagination');

			$this->pagination->initialize($config);

			//------------- Variables
			$this->template['pager'] = $this->pagination->create_links();
			$this->template['start'] = $start;
			$this->template['total'] = $config['total_rows'];
			$this->template['per_page'] = $config['per_page'];
			$this->template['total_rows'] = $config['total_rows'];
			$this->template['products'] = $products;

			$view = 'specials';
		}
		else
		{
			$this->output->set_header("HTTP/1.0 403 Forbidden");
			$this->template['title'] = 'Non autorisé';
			$view = '403';
		}
		$this->javascripts->add(array('jquery', 'colorbox', 'swfobject', 'slider', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->layout->load($this->template, $this->system->theme,  $view);

		$this->session->set_userdata('redirect_uri_front', $this->uri->uri_string());
	}

	public function products($products_id = '')
	{
		if ($this->system->modules[$this->template['module']]['active'] == 1)
		{
			$this->template['meta_keywords'] = $this->system->meta_keywords;
			$this->template['meta_description'] = $this->system->meta_description;

			if($product = $this->catalog->get_products(array('id' => $products_id)))
			{
				$this->load->library('medias');
				$categorie = $this->catalog->get_categories(array('id' => $product['categories_id_default']));
				$this->template['breadcrumb'][] = 	array(
					'title'	=> html_entity_decode($categorie['title']),
					'uri'	=> $this->template['module'].'/categories/'.$categorie['id'].'/'.$categorie['uri'].'/index'
				);

				$this->template['product'] = $product;
				$this->template['title'] = html_entity_decode($product['title']);

				$products = $this->catalog->list_products_to_products($products_id);

				//------------- Requetes images
				if($products)
				{
					$this->load->library('medias');
					$images = array();
					foreach($products as $product)
					{
						if($rows = $this->medias->list_medias(array('where' => array('src_id' => $product['pID'], 'module' => 'products'))))
						{
							if(is_array($rows))
							{
								foreach($rows as $row)
								{
									if($row['options']['cover'] == 1) $images[$product['pID']] = $row;
								}
							}
						}
					}
					$this->template['images'] = $images;
				}

				$this->template['special'] = $this->catalog->get_specials(array('products_id' => $products_id, 'active' => 1));
				$this->template['images_products'] = $this->medias->list_medias(array('where' => array('src_id' => $products_id, 'module' => 'products'), 'order_by' => 'ordering'));
				$this->template['categorie'] = $categorie;
				$this->template['products'] = $products;

				//------------- Attributs
				$rows_products_attributes_values = array();
				if($products_attributes_values = $this->catalog->list_products_attributes_values(array('select' => 'products_id, attributes_id, attributes_values_id, price, suffix, ordering, color, products_to_attributes_values.id as pavID, '.$this->config->item('table_attributes_lang').'.name as aNAME, '.$this->config->item('table_attributes_values_lang').'.name as avNAME', 'where' => array($this->config->item('table_attributes_lang').'.lang' => $this->user->lang, $this->config->item('table_attributes_values_lang').'.lang' => $this->user->lang, 'products_id' => $products_id))))
				{
					foreach($products_attributes_values as $product_attribute_value)
					{
						$rows_products_attributes_values[$product_attribute_value['aNAME']][] = $product_attribute_value;
					}

				}
				$this->template['products_attributes_values'] = $rows_products_attributes_values;
				$view = 'products';
			}
			else
			{
				$this->output->set_header("HTTP/1.0 404 Not Found");
				$this->template['title'] = 'Non trouvé';
				$view = '404';
			}
		}
		else
		{
			$this->output->set_header("HTTP/1.0 403 Forbidden");
			$this->template['title'] = 'Non autorisé';
			$view = '403';
		}
		$this->javascripts->add(array('jquery', 'colorbox', 'swfobject', 'slider', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->layout->load($this->template, $this->system->theme,  $view);
	}


}
