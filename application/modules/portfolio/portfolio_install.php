<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'portfolio_settings'");
$portfolio_settings = serialize(array(
	'publish_feed_portfolio' 			=> 0,
	'per_portfolio_categories' 			=> 20,
	'per_portfolio_medias' 				=> 20,
	'box_portfolio'						=> 1,
	'substr_body_portfolio'				=> 100,
	'img_sizes_types_big_portfolio'		=> 'big-portfolio',
	'img_sizes_types_little_portfolio'	=> 'little-portfolio'
));
$this->db->query("INSERT INTO ".$this->db->dbprefix('settings')." VALUES ('', 'portfolio_settings', '".$portfolio_settings."')");

//------ TABLE MODULES
$this->db->where(array('name' => 'portfolio'))->update($this->config->item('table_modules'), array('navigation' => 1));

//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'portfolio'");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 1,  'portfolio', 4)");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 2,  'portfolio', 4)");

//------ TABLE CATEGORIES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_categories'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('portfolio_categories')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `ordering` int(3) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `date_modified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `parent_id` (`parent_id`),
  KEY `ordering` (`ordering`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->db->query("
INSERT INTO ".$this->db->dbprefix('portfolio_categories')." (`id` ,`parent_id` ,`active` ,`ordering` ,`date_added` ,`date_modified`) VALUES
('1', '0', '1', '0', ".mktime()." , NULL);");

//------ TABLE CATEGORIES LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_categories_lang'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('portfolio_categories_lang')." (
  `categories_id` int(11) NOT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `title` varchar(64) NOT NULL,
  `body` text,
  `meta_title` varchar(64) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `uri` varchar(64) NOT NULL,
  UNIQUE KEY `categories_id` (`categories_id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$this->db->query("
INSERT INTO ".$this->db->dbprefix('portfolio_categories_lang')." (`categories_id`, `lang`, `title`, `body`, `meta_title`, `meta_keywords`, `meta_description`, `uri`) VALUES
(1, 'fr', 'Portfolio', NULL, NULL, NULL, NULL, 'index'),
(1, 'en', 'Portfolio', NULL, NULL, NULL, NULL, 'index');");

//------ TABLE CATEGORIES TO MEDIAS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_categories_to_medias'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('portfolio_categories_to_medias')." (
  `medias_id` int(11) NOT NULL DEFAULT '0',
  `categories_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`medias_id`,`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

//------ TABLE MEDIAS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_medias'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('portfolio_medias')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) DEFAULT '1',
  `is_box` tinyint(1) DEFAULT '1',
  `categories_id_default` int(11) NOT NULL DEFAULT '1',
  `file` varchar(128) DEFAULT NULL,
  `date_added` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `is_box` (`is_box`),
  KEY `categories_id_default` (`categories_id_default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE MEDIAS LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('portfolio_medias_lang'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('portfolio_medias_lang')." (
  `medias_id` int(11) unsigned NOT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `title` varchar(64) NOT NULL,
  `body` text,
  `uri` varchar(64) NOT NULL,
  `legend` varchar(128) DEFAULT NULL,
  `alt` varchar(128) DEFAULT NULL,
  UNIQUE KEY `medias_id` (`medias_id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

//------ RESET CACHE
if($this->system->cache == 1) $this->cache->remove_group('categories');
if($this->system->cache == 1) $this->cache->remove_group('navigation');

