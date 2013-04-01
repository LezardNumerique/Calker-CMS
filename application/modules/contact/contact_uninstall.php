<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'contact_settings'");
//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'contact'");
//------ TABLE CONTACT
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('contact'));
//------ CLEAR CACHE
if($this->system->cache == 1)
	$this->cache->remove_group('navigation');
$this->system->clear_cache();