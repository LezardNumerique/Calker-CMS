<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Portfolio extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->template['module'] = 'portfolio';
		$this->config->load('config');
		$this->load->library('portfolios', '', 'portfolio');
		$this->load->library('pagination');
		$this->load->helper('portfolio');
		$this->load->model('portfolio_model', 'model');

		$this->portfolio->settings = isset($this->system->portfolio_settings) ? unserialize($this->system->portfolio_settings) : array();
		if($this->system->modules[$this->template['module']]['active'] != 1) redirect('pages/unauthorized/'.$this->template['module'].'/1');
	}

	public function index($uri = 'index', $categories_id = '1', $start = 0)
	{
		$this->user->check_level($this->template['module'], LEVEL_VIEW);

		$per_page = $this->portfolio->settings['per_portfolio_medias'];
		$per_page = 2;

		$medias = $this->portfolio->list_medias(array('select' => '*, '.$this->config->item('table_portfolio_medias_lang').'.title as mTITLE, '.$this->config->item('table_portfolio_medias_lang').'.body as mBODY, '.$this->config->item('table_portfolio_categories_lang').'.title as cTITLE', 'where' => array($this->config->item('table_portfolio_categories_to_medias').'.categories_id' => $categories_id, $this->config->item('table_portfolio_categories_lang').'.uri' => $uri, $this->config->item('table_portfolio_medias').'.active' => 1), 'start' => $start, 'limit' => $per_page, 'order_by' => $this->config->item('table_portfolio_categories_to_medias').'.ordering'));
		$total_medias = $this->portfolio->total_list_medias(array('where' => array($this->config->item('table_portfolio_categories_to_medias').'.categories_id' => $categories_id, $this->config->item('table_portfolio_categories_lang').'.uri' => $uri, $this->config->item('table_portfolio_medias').'.active' => 1)));

		if($categorie = $this->portfolio->get_categories(array('id' => $categories_id, $this->config->item('table_portfolio_categories_lang').'.uri' => $uri)))
		{
			if($categories_id != 1)
			{
				$this->template['breadcrumb'][] = 	array(
					'title'	=> $this->lang->line('title_portfolio'),
					'uri'	=> $this->template['module'].'/'.$this->portfolio->get_categories_uri_parent().'/1'
				);
			}
			if($parent = $this->portfolio->get_categories(array('id' => $categorie['parent_id'])))
			{
				if($parent['id'] != 1)
				{
					$this->template['breadcrumb'][] = 	array(
						'title'	=> html_entity_decode($parent['title']),
						'uri'	=> $this->template['module'].'/'.$parent['uri'].'/'.$parent['id']
					);
					$categorie['title'] = $categorie['title'].' - '.$parent['title'];
				}

			}
			$this->template['categories_id'] = $categories_id;
			$this->template['title'] = html_entity_decode(str_replace(' - '.$parent['title'], '', $categorie['title']));
			$this->template['meta_title'] = ($categorie['meta_title'] ? $categorie['meta_title'] : html_entity_decode($categorie['title'])).($start != 0 ? ' - '.$this->lang->line('meta_title_page').' '.($start+1) : '').($categories_id != 1 && !$categorie['meta_title'] ? ' - '.$this->lang->line('title_portfolio').(isset($this->system->meta_more) ? ' - '.$this->system->meta_more : '') : '');
			$this->template['meta_keywords'] = $categorie['meta_keywords'];
			$this->template['meta_description'] = $categorie['meta_description'];
			
			$this->load->library('medias');
			$images_sizes = $this->medias->get_medias_types_sizes('little-portfolio');
			$images_sizes = array(
				'width' 	=> $images_sizes['width'],
				'height' 	=> $images_sizes['height'] 
			);
			$this->template['images_sizes'] = $images_sizes;

			$view = 'index';
		}
		else
		{
			$this->output->set_header('HTTP/1.0 404 Not Found');
			$this->template['title'] = $this->lang->line('title_categories_not_found');
			$view = '404';
		}

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = count($this->uri->segments);
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->language->get_uri_language('/').$this->template['module'].'/'.($categories_id == 1 ? '' : $uri.'/'.$categories_id));
		$config['total_rows'] = $total_medias;
		$config['per_page'] = $per_page;

		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total_medias'] = $total_medias;
		$this->template['start'] = $start;

		$this->template['medias'] = $medias;

		$this->javascripts->add(array('jquery', 'colorbox', 'swfobject', 'slider', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->layout->load($this->template, $this->system->theme,  $view);


		$this->session->set_userdata('redirect_uri_front', $this->uri->uri_string());
	}
}
