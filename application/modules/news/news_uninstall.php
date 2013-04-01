<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'news_settings'");
//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'news'");
//------ TABLE NAVIGATION
$this->db->query("DELETE FROM ".$this->db->dbprefix('navigation')." WHERE module = 'news'");
//------ TABLE MEDIAS
$this->db->query("DELETE FROM ".$this->db->dbprefix('medias')." WHERE module = 'news'");
//------ TABLE NEWS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('news'));
//------ TABLE NEWS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('news_lang'));
//------ DELETE IMAGES
$this->load->helper('directory');
if($maps = directory_map('./'.$this->config->item('medias_folder').'/images/'))
{
	foreach($maps as $map)
	{
		$pos = strpos($map, 'news');
		if ($pos !== false) {
			if(is_readable('./'.$this->config->item('medias_folder').'/images/'.$map)) unlink('./'.$this->config->item('medias_folder').'/images/'.$map);
		}
	}
}
//------ CLEAR CACHE
$this->system->clear_cache();
