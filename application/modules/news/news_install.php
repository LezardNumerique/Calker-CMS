<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

//------ TABLE SETTINGS
$this->db->query("DELETE FROM ".$this->db->dbprefix('settings')." WHERE name = 'news_settings'");
$news_settings = serialize(array(
	'publish_feed_news' 		=> 0,
	'per_page_news' 			=> 20,
	'box_news'					=> 1,
	'substr_home_news'			=> 400,
	'substr_listing_news'		=> 1000,
	'img_sizes_types_list_news'	=> 'listing-news',
	'img_sizes_types_view_news'	=> 'view-news'
));
$this->db->query("INSERT INTO ".$this->db->dbprefix('settings')." VALUES ('', 'news_settings', '".$news_settings."')");

//------ TABLE RIGHTS
$this->db->query("DELETE FROM ".$this->db->dbprefix('rights')." WHERE module = 'news'");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 1,  'news', 4)");
$this->db->query("INSERT INTO ".$this->db->dbprefix('rights')." VALUES ('', 2,  'news', 4)");

//------ TABLE NEWS
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('news'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('news')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) DEFAULT '1',
  `date_added` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

//------ TABLE NEWS LANG
$this->db->query("DROP TABLE IF EXISTS ".$this->db->dbprefix('news_lang'));
$this->db->query("CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('news_lang')." (
  `news_id` int(11) unsigned NOT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `title` varchar(128) DEFAULT NULL,
  `uri` varchar(128) NOT NULL,
  `body` text,
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  UNIQUE KEY `uri` (`uri`,`lang`),
  KEY `news_id` (`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
