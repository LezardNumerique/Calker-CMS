<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->template['module'] = 'news';				
		$this->config->load('config');
		$this->load->library('newss', '', 'news');
		$this->load->library('pagination');
		$this->load->library('medias');
		$this->load->helper('news');
		$this->load->model('news_model', 'model');		

		$this->news->settings = isset($this->system->news_settings) ? unserialize($this->system->news_settings) : array();
		if($this->system->modules[$this->template['module']]['active'] != 1) redirect('pages/unauthorized/'.$this->template['module'].'/1');
		$this->session->set_userdata('redirect_admin_live_view', $this->uri->uri_string());
	}

	function index()
	{		
		$start = end($this->uri->segments);
	
		$this->template['title'] = $this->lang->line('title_news');
		$this->template['meta_title'] = $this->lang->line('title_news').($start != 0 ? ' - '.$this->lang->line('meta_title_page').' '.($start+1).(isset($this->system->meta_more) ? ' - '.$this->system->meta_more : '') : '');

		$per_page = $this->news->settings['per_page_news'];
		$where = array($this->config->item('table_news').'.active' => 1, 'lang' => $this->user->lang, 'lang' => $this->user->lang);
		if($this->user->liveView) $where = array();

		
		$news = $this->news->list_news(array('where' => $where, 'select' => '*', 'start' => $start, 'limit' => $per_page));
		$total_news = $this->news->total_list_news(array('where' => $where));

		$this->template['images'] = array();
		if($medias = $this->medias->list_medias(array('where' => array('module' => $this->template['module']))))
		{
			foreach($medias as $media)
			{
				$this->template['images'][$media['src_id']] = $media;
			}
		}
			
		$images_sizes = $this->medias->get_medias_types_sizes('listing-news');
		$images_sizes = array(
			'width' 	=> $images_sizes['width'],
			'height' 	=> $images_sizes['height'] 
		);
		$this->template['images_sizes'] = $images_sizes;

		$this->load->library('pagination');

		$config['num_links'] = $this->system->num_links;
		$config['uri_segment'] = count($this->uri->segments);
		$config['first_link'] = $this->lang->line('text_begin');
		$config['last_link'] = $this->lang->line('text_end');
		$config['base_url'] = site_url($this->language->get_uri_language('/').$this->template['module'].'/');
		$config['total_rows'] = $total_news;
		$config['per_page'] = $per_page;
		
		$this->pagination->initialize($config);

		$this->template['pager'] = $this->pagination->create_links();
		$this->template['total_news'] = $total_news;
		$this->template['start'] = $start;

		$this->template['news'] = $news;		

		$this->javascripts->add(array('jquery', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->layout->load($this->template, $this->system->theme,  'index');

		$this->session->set_userdata('redirect_uri_front', $this->uri->uri_string());
	}

	function view($uri = '', $news_id = '')
	{
		if($new = $this->news->get_news(array('id' => $news_id, 'uri' => $uri, 'lang' => $this->user->lang)))
		{
			$this->template['new'] = $new;
			$this->template['title'] = $new['title'];
			$this->template['meta_title'] = ($new['meta_title'] ? $new['meta_title'] : $new['title'].' - '.$this->lang->line('title_news').(isset($this->system->meta_more) ? ' - '.$this->system->meta_more : ''));
			$this->template['meta_keywords'] = $new['meta_keywords'];
			$this->template['meta_description'] = $new['meta_description'];
			$this->template['breadcrumb'][] = 	array(
				'title'	=> $this->lang->line('title_news'),
				'uri'	=> $this->template['module']
			);
			
			$this->template['media'] = $this->medias->get_medias(array('src_id' => $news_id, 'module' => $this->template['module']));
			$images_sizes = $this->medias->get_medias_types_sizes('view-news');
			$images_sizes = array(
				'width' 	=> $images_sizes['width'],
				'height' 	=> $images_sizes['height'] 
			);
			$this->template['images_sizes'] = $images_sizes;
			
			$view = 'view';
			if($new['active'] == 0 && !$this->user->liveView)
			{
				$this->output->set_header('HTTP/1.0 403 Forbidden');
				$this->template['title'] = $this->lang->line('title_page_forbidden');
				$view = '403';
			}
		}
		else
		{
			$this->output->set_header('HTTP/1.0 404 Not Found');
			$this->template['title'] = $this->lang->line('title_new_not_found');
			$view = '404';
		}

		$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'colorbox', 'swfobject', 'slider', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->layout->load($this->template, $this->system->theme,  $view);
	}

}