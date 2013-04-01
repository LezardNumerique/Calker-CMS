<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->add_action('list_pages', 'news_list_pages');

function news_list_pages($data)
{
	$obj =& get_instance();
	$module = 'news';
	$obj->config->load($module.'/config');
	$news_settings = isset($obj->system->news_settings) ? unserialize($obj->system->news_settings) : array();
	$list_pages[] = array('id' => 0, 'title' => $obj->lang->line('title_news'), 'uri' => $module, 'lang' => $obj->user->lang, 'level' => 0, 'parent_id' => 0, 'module' => $module);
	if($data) return array_merge($data, $list_pages);
}
