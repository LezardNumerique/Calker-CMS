<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'catalog_settings'");
//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'catalog'");
//------ TABLE NAVIGATION
$this->db->query("DELETE FROM ".$this->db->dbprefix('navigation')." WHERE module = 'catalog'");
//------ TABLE MEDIAS
$this->db->query("DELETE FROM ".$this->db->dbprefix('medias')." WHERE module = 'categories'");
$this->db->query("DELETE FROM ".$this->db->dbprefix('medias')." WHERE module = 'products'");
//------ TABLE ATTRIBUTES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes'));
//------ TABLE ATTRIBUTES LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes_lang'));
//------ TABLE ATTRIBUTES VALUES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes_values'));
//------ TABLE ATTRIBUTES VALUES LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes_values_lang'));
//------ TABLE CATEGORIES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('categories'));
//------ TABLE PRODUCTS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products'));
//------ TABLE PRODUCTS ATTRIBUTES VALUES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products_to_attributes_values'));
//------ TABLE PRODUCTS TO CATEGORIES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products_to_categories'));
//------ TABLE PRODUCTS TO PRODUCTS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products_to_products'));
//------ TABLE SPECIALS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('specials'));
//------ TABLE TVA
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('tva'));
//------ DELETE IMAGES
$this->load->helper('directory');
if($maps = directory_map('./'.$this->config->item('medias_folder').'/images/'))
{
	foreach($maps as $map)
	{
		$pos = strpos($map, 'categories');
		if ($pos !== false) {
			if(is_readable('./'.$this->config->item('medias_folder').'/images/'.$map)) unlink('./'.$this->config->item('medias_folder').'/images/'.$map);
		}
	}
}
if($maps = directory_map('./'.$this->config->item('medias_folder').'/images/'))
{
	foreach($maps as $map)
	{
		$pos = strpos($map, 'products');
		if ($pos !== false) {
			if(is_readable('./'.$this->config->item('medias_folder').'/images/'.$map)) unlink('./'.$this->config->item('medias_folder').'/images/'.$map);
		}
	}
}
//------ CLEAR CACHE
$this->system->clear_cache();
