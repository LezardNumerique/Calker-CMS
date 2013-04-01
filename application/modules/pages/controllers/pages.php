<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('config');
		$this->template['module'] = 'pages';
		$this->load->library('paragraph');
		$this->load->model('pages_model', 'model');
		$this->settings = isset($this->system->pages_settings) ? unserialize($this->system->pages_settings) : array();
		if($this->system->modules[$this->template['module']]['active'] != 1) redirect($this->template['module'].'/unauthorized/'.$this->template['module'].'/1');
		$this->session->set_userdata('redirect_admin_live_view', $this->uri->uri_string());
	}

	public function unauthorized($module = '', $level = '')
	{
		$this->javascripts->add(array('jquery', 'colorbox', 'swfobject', 'sitelib'));
		$this->css->add($this->template['module']);
		$this->template['title'] = $this->lang->line('title_unauthorized');
		$this->template['data']  = array('module' => $module, 'level' => $level);
		$this->layout->load($this->template, $this->system->theme, '403');
	}	

	public function index()
	{	
		if($this->language->get_uri_language())
			$count_segment = 2;
		else
			$count_segment = 1;
			
		$alias = $this->uri->segment($count_segment);
		if ($alias)
		{
			$num = $count_segment;
			if($alias == 'ajax') $num = $count_segment+1;

			$built_uri = '';

			while ($segment = $this->uri->segment($num))
			{
				$built_uri .= $segment.'/';
				$num++;
			}

			$new_length = strlen($built_uri) - 1;
			$built_uri = substr($built_uri, 0, $new_length);			
		}
		else
		{
			$built_uri = $this->page->settings['page_home'];
		}

		$hash = md5($built_uri);
		if (!$page = $this->cache->get('page_'.$hash.'_'.$this->user->lang, $this->template['module']))
		{
			$page = $this->page->get_pages(array('uri' => $built_uri, 'lang' => $this->user->lang));
			if($this->system->cache == 1) $this->cache->save('page_'.$hash.'_'.$this->user->lang, $page, $this->template['module'], 0);
		}

		if ($page)
		{
			if ($page['active'] == 0 && !$this->user->liveView)
			{
				$this->output->set_header('HTTP/1.0 403 Forbidden');
				$this->template['title'] = $this->lang->line('title_page_forbidden');
				$view = '403';
			}
			else
			{
				$this->template['page'] = $page;
				$view = 'index';

				if (!$parent = $this->cache->get('page_parent_'.$hash.'_'.$this->user->lang, $this->template['module']))
				{
					if($page['parent_id'] != 0)
					{
						if(!$parent = $this->page->get_pages(array('id' => $page['parent_id'])))
							$parent = ' ';//Know bug here??!!Cache need write
					}
					if($this->system->cache == 1) $this->cache->save('page_parent_'.$hash.'_'.$this->user->lang, $parent, $this->template['module'], 0);
				}
				if($parent)
				{
					$this->_breadcrumb($parent['id'], $hash);
					if(isset($this->template['breadcrumb'])) $this->template['breadcrumb'] = array_reverse($this->template['breadcrumb']);
				}

				$where = array('active' => 1, 'src_id' => $page['id'], 'module' => $this->template['module']);
				if($this->user->logged_in) $where = array('src_id' => $page['id'], 'module' => $this->template['module']);
				$hash = md5(serialize($where));
				if (!$paragraph = $this->cache->get('print_paragraph_'.$page['id'].'_'.$this->template['module'].'_'.$this->user->lang.'_'.$hash, $this->template['module']))
				{
					if(!$paragraph = $this->paragraph->print_paragraph($where, $this->template['module'], ''))
						$paragraph = ' ';//Know bug here??!!Cache need write
					if($this->system->cache == 1) $this->cache->save('print_paragraph_'.$page['id'].'_'.$this->template['module'].'_'.$this->user->lang.'_'.$hash, $paragraph, $this->template['module'], 0);
				}

				$this->template['title'] = html_entity_decode($page['title']);
				$this->template['meta_title'] = $this->template['page']['meta_title'];
				$this->template['meta_keywords'] = $this->template['page']['meta_keywords'];
				$this->template['meta_description'] = $this->template['page']['meta_description'];
				$this->template['paragraph'] = $paragraph;
			}
		}
		else
		{
			$this->output->set_header('HTTP/1.0 404 Not Found');
			$this->template['title'] = $this->lang->line('title_page_not_found');
			$view = '404';
		}
		if ($this->uri->segment(1) == 'ajax')
		{
			$this->layout->load($this->template, 'ajax', $view);
		}
		else
		{
			$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'colorbox', 'swfobject', 'slider', 'sitelib'));
			$this->css->add($this->template['module']);
			$this->layout->load($this->template, $this->system->theme, $view);
		}

	}

	private function _breadcrumb($parent_id = '', $hash = '')
	{
		if (!$parent = $this->cache->get('page_breadcrumb_'.$parent_id.'_'.$hash, $this->template['module']))
		{
			if($parent_id != 0)
			{
				if(!$parent = $this->page->get_pages(array('id' => $parent_id)))
					$parent = ' ';//Know bug here??!!Cache need write
			}
			if($this->system->cache == 1) $this->cache->save('page_breadcrumb_'.$parent_id.'_'.$hash, $parent, $this->template['module'], 0);
		}
		if($parent)
		{
			$this->template['breadcrumb'][] = 	array(
				'title'	=> html_entity_decode($parent['title']),
				'uri'	=> $parent['uri']
			);

			$this->_breadcrumb($parent['parent_id'], $hash);
		}
		else
		{
			return false;
		}
	}

}
