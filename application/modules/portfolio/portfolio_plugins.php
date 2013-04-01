<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->add_action('list_pages', 'portfolio_list_pages');

function portfolio_list_pages($data)
{
	$obj =& get_instance();
	$module = 'portfolio';
	$obj->load->library($module.'/portfolios', '', 'portfolio');
	$obj->config->load($module.'/config');
	$portfolio_settings = isset($obj->system->portfolio_settings) ? unserialize($obj->system->portfolio_settings) : array();
	$list_pages[] = array('id' => 0, 'title' => $obj->lang->line('title_portfolio'), 'uri' => $module, 'lang' => $obj->user->lang, 'level' => 0, 'parent_id' => 0, 'module' => $module);
	if($rows = $obj->portfolio->list_categories(1, '', '', '', '', false))
	{
		foreach ($rows as $key => $row)
		{
			$list_pages[$key+1]['id'] = $row['id'];
			$list_pages[$key+1]['title'] = $module.' => '.$row['title'];
			$list_pages[$key+1]['uri'] = $module.'/'.$row['uri'].'/'.$row['id'];
			$list_pages[$key+1]['lang'] = $obj->user->lang;
			$list_pages[$key+1]['level'] = 0;
			$list_pages[$key+1]['parent_id'] = 0;
			$list_pages[$key+1]['module'] = $module;
		}
		if($data) return array_merge($data, $list_pages);
	}
	if($data) return array_merge($data, $list_pages);

}
