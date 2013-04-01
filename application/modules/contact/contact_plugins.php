<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->add_action('list_pages', 'contact_list_pages');

function contact_list_pages($data)
{
	$obj =& get_instance();
	$module = 'contact';
	$obj->config->load($module.'/config');
	$contact_settings = isset($obj->system->contact_settings) ? unserialize($obj->system->contact_settings) : array();
	$list_pages[] = array('id' => 0, 'title' => $obj->lang->line('title_contact'), 'uri' => $module, 'lang' => $obj->user->lang, 'level' => 0, 'parent_id' => 0, 'module' => $module);
	if($data) return array_merge($data, $list_pages);
}
