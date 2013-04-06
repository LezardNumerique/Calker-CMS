<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'catalog_settings'");
$catalog_settings = serialize(array(
	'publish_feed_catalog' 			=> 0,
	'per_page_catalog' 				=> 20,
	'display_tax_catalog'			=> 1,
	'display_tax_prefix_catalog'	=> 1
));
$this->db->query("INSERT INTO ".$this->db->dbprefix('settings')." VALUES ('', 'catalog_settings', '".$catalog_settings."')");

//------ TABLE MODULES
$this->db->where(array('name' => 'catalog'))->update($this->config->item('table_modules'), array('navigation' => 1));

//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'catalog'");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 1,  'catalog', 4)");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 2,  'catalog', 4)");

//------ TABLE ATTRIBUTES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('attributes')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_color` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_color` (`is_color`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE ATTRIBUTES LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes_lang'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('attributes_lang')." (
  `id` int(11) NOT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

//------ TABLE ATTRIBUTES VALUES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes_values'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('attributes_values')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attributes_id` int(11) NOT NULL,
  `color` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attributes_id` (`attributes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE ATTRIBUTES VALUES LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('attributes_values_lang'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('attributes_values_lang')." (
  `id` int(11) NOT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

//------ TABLE CATEGORIES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('categories'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('categories')." (
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
INSERT INTO ".$this->db->dbprefix('categories')." (`id` ,`parent_id` ,`active` ,`ordering` ,`date_added` ,`date_modified`) VALUES
('1', '0', '1', '0', ".mktime()." , NULL);");

//------ TABLE CATEGORIES LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('categories_lang'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('categories_lang')." (
  `categories_id` int(11) NOT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `title` varchar(64) NOT NULL,
  `body` text,
  `meta_title` varchar(64) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `uri` varchar(64) NOT NULL,
  UNIQUE KEY `uri` (`uri`,`lang`),
  KEY `categories_id` (`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
$this->db->query("
INSERT INTO ".$this->db->dbprefix('categories_lang')." (`categories_id`, `lang`, `title`, `body`, `meta_title`, `meta_keywords`, `meta_description`, `uri`) VALUES
(1, 'fr', 'Catalogue', NULL, NULL, NULL, NULL, 'index'),
(1, 'en', 'Catalog', NULL, NULL, NULL, NULL, 'index');");

//------ TABLE PRODUCTS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('products')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) DEFAULT '1',
  `categories_id_default` int(11) NOT NULL DEFAULT '1',  
  `reference` varchar(32) DEFAULT NULL,  
  `price` decimal(13,4) DEFAULT NULL,
  `price_shopping` decimal(13,4) DEFAULT NULL,
  `tva` decimal(3,1) DEFAULT NULL,  
  `date_added` int(11) NOT NULL,
  `date_modified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`), 
  KEY `active` (`active`),  
  KEY `categories_id_default` (`categories_id_default`),
  KEY `reference` (`reference`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE PRODUCTS LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products_lang'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('products_lang')." (
  `products_id` int(11) unsigned NOT NULL AUTO_INCREMENT,  
  `title` varchar(128) DEFAULT NULL,  
  `uri` varchar(128) DEFAULT NULL, 
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT '',
  `meta_description` text,
  `body` text,
  UNIQUE KEY `uri` (`uri`,`lang`),
  KEY `products_id` (`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE PRODUCTS ATTRIBUTES VALUES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products_to_attributes_values'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('products_to_attributes_values')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color` varchar(32) DEFAULT NULL,
  `products_id` int(11) NOT NULL,
  `attributes_id` int(11) NOT NULL,
  `attributes_values_id` int(11) NOT NULL,
  `price` decimal(13,4) NOT NULL,
  `suffix` varchar(1) NOT NULL,
  `ordering` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE PRODUCTS TO CATEGORIES
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products_to_categories'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('products_to_categories')." (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `categories_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_id`,`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

//------ TABLE PRODUCTS TO PRODUCTS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('products_to_products'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('products_to_products')." (
  `products_id_x` int(11) NOT NULL DEFAULT '0',
  `products_id_y` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_id_x`,`products_id_y`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

//------ TABLE SPECIALS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('specials'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('specials')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `new_price` decimal(15,4) DEFAULT NULL,
  `tva` decimal(3,1) DEFAULT NULL,
  `date_added` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `date_begin` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE TVA
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('tva'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('tva')." (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `rate` decimal(3,1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;");
$this->db->query("INSERT INTO ".$this->db->dbprefix('tva')." (`id`, `title`, `rate`) VALUES
(1, '5,5 %', '5.5'),
(2, '19,6%', '19.6');");
