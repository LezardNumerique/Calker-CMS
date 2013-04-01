<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'portfolio_settings'");
//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'portfolio'");
//------ TABLE NAVIGATION
$this->db->query("DELETE FROM ".$this->db->dbprefix('navigation')." WHERE module = 'portfolio'");
//------ TABLE MEDIAS
$this->db->query("DELETE FROM ".$this->db->dbprefix('medias')." WHERE module = 'portfolio_categories'");
//------ TABLE CATEGORIES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_categories'));
//------ TABLE CATEGORIES LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_categories_lang'));
//------ TABLE CATEGORIES TO MEDIAS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_categories_to_medias'));
//------ TABLE MEDIAS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_medias'));
//------ TABLE MEDIAS LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_medias_lang'));
//------ DELETE IMAGES
$this->load->helper('directory');
if($maps = directory_map('./'.$this->config->item('medias_folder').'/images/'))
{
	foreach($maps as $map)
	{
		$pos = strpos($map, 'portfolio');
		if ($pos !== false) {
			if(is_readable('./'.$this->config->item('medias_folder').'/images/'.$map)) unlink('./'.$this->config->item('medias_folder').'/images/'.$map);
		}
	}
}
//------ CLEAR CACHE
$this->system->clear_cache();
