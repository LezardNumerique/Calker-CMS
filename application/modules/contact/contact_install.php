<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'contact_settings'");
$contact_settings = serialize(array(
	'per_page_contact' 			=> 20,
	'active_field_firstname' 	=> 1,
	'active_field_lastname'		=> 1,
	'active_field_phone'		=> 1,
	'active_field_message'		=> 1,
	'active_form'				=> 1,
	'active_map'				=> 1,
	'active_coord'				=> 1,
	'active_qrcode'				=> 1
));
$this->system->set('contact_settings', $contact_settings);

//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'contact'");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 1,  'contact', 4)");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 2,  'contact', 4)");

//------ TABLE CONTACT
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('contact'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('contact')."(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(64) DEFAULT NULL,
  `lastname` varchar(64) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `message` text NOT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `date` datetime NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `trash` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `phone` (`phone`),
  KEY `email` (`email`),
  KEY `lang` (`lang`),
  KEY `date` (`date`),
  KEY `ip_address` (`ip_address`),
  KEY `trash` (`trash`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
//------ CLEAR CACHE
if($this->system->cache == 1)
	$this->cache->remove_group('navigation');
$this->system->clear_cache();