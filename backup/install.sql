-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 08, 2012 at 07:44 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.2-1ubuntu4.18

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `calker`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_captcha`
--

DROP TABLE IF EXISTS `ci_captcha`;
CREATE TABLE IF NOT EXISTS `ci_captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `captcha_time` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `word` varchar(20) NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ci_captcha`
--


-- --------------------------------------------------------

--
-- Table structure for table `ci_contact`
--

DROP TABLE IF EXISTS `ci_contact`;
CREATE TABLE IF NOT EXISTS `ci_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(64) DEFAULT NULL,
  `lastname` varchar(64) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `date` int(11) NOT NULL,
  `trash` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `phone` (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ci_contact`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_groups`
--

DROP TABLE IF EXISTS `ci_groups`;
CREATE TABLE IF NOT EXISTS `ci_groups` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ci_groups`
--

INSERT INTO `ci_groups` (`id`, `title`) VALUES
(1, 'root'),
(2, 'admin'),
(3, 'utilisateur');

-- --------------------------------------------------------

--
-- Table structure for table `ci_languages`
--

DROP TABLE IF EXISTS `ci_languages`;
CREATE TABLE IF NOT EXISTS `ci_languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(5) NOT NULL,
  `name` varchar(64) NOT NULL,
  `ordering` int(5) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `default` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `name` (`name`),
  KEY `active` (`active`),
  KEY `default` (`default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ci_languages`
--

INSERT INTO `ci_languages` (`id`, `code`, `name`, `ordering`, `active`, `default`) VALUES
(1, 'en', 'English', 1, 1, 0),
(2, 'fr', 'Français', 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ci_medias`
--

DROP TABLE IF EXISTS `ci_medias`;
CREATE TABLE IF NOT EXISTS `ci_medias` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `types_id` int(11) NOT NULL,
  `module` varchar(128) NOT NULL DEFAULT '',
  `file` varchar(128) NOT NULL DEFAULT '',
  `src_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(3) NOT NULL DEFAULT '0',
  `options` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `types_id` (`types_id`),
  KEY `module` (`module`),
  KEY `src_id` (`src_id`),
  KEY `ordering` (`ordering`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ci_medias`
--

-- --------------------------------------------------------

--
-- Structure de la table `ci_medias_types_sizes`
--

DROP TABLE IF EXISTS `ci_medias_types_sizes`;
CREATE TABLE IF NOT EXISTS `ci_medias_types_sizes` (
  `medias_types_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(16) NOT NULL,
  `name` varchar(16) NOT NULL,
  `key` varchar(16) NOT NULL,
  PRIMARY KEY (`medias_types_id`),
  UNIQUE KEY `medias_types_name` (`name`),
  UNIQUE KEY `medias_types_key` (`key`),
  KEY `medias_types_module` (`module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_modules`
--

DROP TABLE IF EXISTS `ci_modules`;
CREATE TABLE IF NOT EXISTS `ci_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0',
  `navigation` tinyint(1) DEFAULT '0',
  `version` varchar(5) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '0',
  `ordering` int(3) DEFAULT '0',
  `info` text,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `admin` (`admin`),
  KEY `active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ci_modules`
--

INSERT INTO `ci_modules` (`id`, `name`, `admin`, `navigation`, `version`, `active`, `ordering`, `info`, `description`) VALUES
(1, 'admin', 0, 0, '1.0.0', 1, 1, NULL, 'Admin core module'),
(2, 'pages', 1, 0, '1.0.0', 1, 2, NULL, 'Pages core module'),
(3, 'medias', 0, 0, '1.0.0', 1, 3, NULL, 'Medias core module'),
(4, 'contact', 0, 0, '1.0.0', 1, 1001, NULL, 'Contact module');

-- --------------------------------------------------------

--
-- Table structure for table `ci_navigation`
--

DROP TABLE IF EXISTS `ci_navigation`;
CREATE TABLE IF NOT EXISTS `ci_navigation` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `ordering` int(3) DEFAULT '0',
  `module` varchar(64) DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `uri` varchar(128) DEFAULT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `active` (`active`),
  KEY `ordering` (`ordering`),
  KEY `module` (`module`),
  KEY `uri` (`uri`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ci_navigation`
--

INSERT INTO `ci_navigation` (`id`, `parent_id`, `active`, `ordering`, `module`, `title`, `uri`, `lang`) VALUES
(1, 0, 1, 0, NULL, 'Menu Haut', NULL, 'fr'),
(2, 0, 1, 0, NULL, 'Menu Bas', NULL, 'fr');

-- --------------------------------------------------------

--
-- Table structure for table `ci_pages`
--

DROP TABLE IF EXISTS `ci_pages`;
CREATE TABLE IF NOT EXISTS `ci_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `ordering` int(3) DEFAULT '0',
  `title` varchar(128) DEFAULT NULL,
  `class` varchar(32) DEFAULT NULL,
  `uri` varchar(128) DEFAULT NULL,
  `lang` char(5) DEFAULT 'fr',
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `show_sub_pages` tinyint(1) NOT NULL DEFAULT '0',
  `show_navigation` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `date_modified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `active` (`active`),
  KEY `ordering` (`ordering`),
  KEY `uri` (`uri`,`lang`),
  KEY `show_sub_pages` (`show_sub_pages`),
  KEY `show_navigation` (`show_navigation`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ci_pages`
--

INSERT INTO `ci_pages` (`id`, `parent_id`, `active`, `ordering`, `title`, `class`, `uri`, `lang`, `meta_title`, `meta_keywords`, `meta_description`, `show_sub_pages`, `show_navigation`, `date_added`, `date_modified`) VALUES
(1, 0, 1, 0, 'Bienvenue', '', 'index', 'fr', '', '', '', 0, 0, 0, 1349267350);

-- --------------------------------------------------------

--
-- Table structure for table `ci_paragraphs`
--

DROP TABLE IF EXISTS `ci_paragraphs`;
CREATE TABLE IF NOT EXISTS `ci_paragraphs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(32) DEFAULT NULL,
  `class_2` varchar(32) DEFAULT NULL,
  `class_3` varchar(32) DEFAULT NULL,
  `class_4` varchar(32) DEFAULT NULL,
  `src_id` int(11) NOT NULL,
  `types_id` int(11) DEFAULT NULL,
  `module` varchar(64) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `ordering` int(3) DEFAULT '0',
  `title` varchar(128) DEFAULT NULL,
  `title_2` varchar(128) DEFAULT NULL,
  `title_3` varchar(128) DEFAULT NULL,
  `title_4` varchar(32) DEFAULT NULL,
  `lang` char(5) NOT NULL DEFAULT 'fr',
  `body` longtext,
  `body_2` longtext,
  `body_3` longtext,
  `body_4` varchar(32) DEFAULT NULL,
  `date_added` int(11) NOT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `width` tinyint(5) DEFAULT NULL,
  `height` tinyint(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `src_id` (`src_id`),
  KEY `types_id` (`types_id`),
  KEY `module` (`module`),
  KEY `active` (`active`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ci_paragraphs`
--


-- --------------------------------------------------------

--
-- Table structure for table `ci_paragraphs_types`
--

DROP TABLE IF EXISTS `ci_paragraphs_types`;
CREATE TABLE IF NOT EXISTS `ci_paragraphs_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `module` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `ci_paragraphs_types`
--

INSERT INTO `ci_paragraphs_types` (`id`, `code`, `module`, `active`) VALUES
(1, 'text', 'pages', 1),
(2, 'image', 'pages', 1),
(3, 'text_image', 'pages', 1),
(4, 'galery', 'pages', 0),
(5, 'flash', 'pages', 1),
(6, 'videos', 'pages', 1),
(7, 'text_2_cols', 'pages', 1),
(8, 'text_3_cols', 'pages', 1),
(9, 'slider_img', 'pages', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ci_rights`
--

DROP TABLE IF EXISTS `ci_rights`;
CREATE TABLE IF NOT EXISTS `ci_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groups_id` int(3) NOT NULL,
  `module` varchar(64) NOT NULL DEFAULT '',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `groups_id` (`groups_id`),
  KEY `module` (`module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `ci_rights`
--

INSERT INTO `ci_rights` (`id`, `groups_id`, `module`, `level`) VALUES
(1, 1, 'admin', 4),
(2, 2, 'admin', 4),
(3, 1, 'pages', 4),
(4, 2, 'pages', 4),
(5, 1, 'medias', 4),
(6, 2, 'medias', 4);

-- --------------------------------------------------------

--
-- Table structure for table `ci_settings`
--

DROP TABLE IF EXISTS `ci_settings`;
CREATE TABLE IF NOT EXISTS `ci_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT '0',
  `value` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=141 ;

--
-- Dumping data for table `ci_settings`
--

INSERT INTO `ci_settings` (`id`, `name`, `value`) VALUES
(1, 'site_email', 'contact@lezard-numerique.net'),
(6, 'cache', '1'),
(9, 'debug', '1'),
(12, 'google_analytic_ga_id', 'ga:51831747'),
(15, 'logo', 'logo.png'),
(18, 'maintenance', '0'),
(20, 'meta_more', 'Gestionnaire de contenu Web'),
(21, 'meta_keywords', 'cms,content,management,system,modulaire,wysiwyg,codeigniter,ci-cms,site,internet,videos,photos,galery,gallerie,galerie,forms,formulaires,catalog,catalogue,shop,ecommerce,newsletters,bannières,vitrine,gestionnaire,contenu'),
(22, 'meta_description', 'Calker CMS - Gestionnaire de contenu Web 2.0 - Made with CODEIGNITER'),
(31, 'site_adress', '7 Allée des phalènes'),
(32, 'site_city', 'ANTIBES'),
(34, 'site_phone', '0643528831'),
(35, 'site_post_code', '06600'),
(36, 'site_name', 'Calker CMS'),
(39, 'stylesheet', 'style.css'),
(42, 'tiny_config', 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyfull,|,bullist,numlist,|,link,unlink,|,fontsizeselect,formatselect,|,undo,redo,image|,cleanup,removeformat,code,filemanager'),
(45, 'version', '2.1'),
(48, 'theme', 'theme1'),
(49, 'site_country', 'FRANCE'),
(51, 'per_page', '20'),
(52, 'ip_allow', '0'),
(53, 'cache_css', '0'),
(54, 'cache_time', '0'),
(56, 'per_captcha', '4'),
(57, 'google_analytic_ua_id', 'UA-19531658-1'),
(58, 'num_links', '5'),
(60, 'quality_img', '100'),
(62, 'pages_settings', ''),
(68, 'site_adress_next', 'Résidence les balcons du port'),
(74, 'google_analytics_email', 'contact@lezard-numerique.net'),
(75, 'google_analytics_password', ''),
(76, 'google_analytic_domain', 'http://www.domain.com'),
(79, 'smtp_host', '91.212.26.180'),
(80, 'smtp_username', 'contact@domain.com'),
(81, 'smtp_password', ''),
(82, 'smtp_port', '2525'),
(83, 'smtp_is', '0'),
(138, 'google_analytic_code', ''),
(136, 'site_schedule', 'Ouvert du Lundi au Vendredi\nDe 9h30 à 18h30'),
(139, 'google_analytic_stats', '0'),
(140, 'google_analytic_visits', '0');

-- --------------------------------------------------------

--
-- Table structure for table `ci_users`
--

DROP TABLE IF EXISTS `ci_users`;
CREATE TABLE IF NOT EXISTS `ci_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groups_id` int(3) NOT NULL,
  `username` varchar(12) DEFAULT NULL,
  `password` varchar(50) DEFAULT '',
  `email` varchar(128) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `lastvisit` int(11) DEFAULT '0',
  `registered` int(11) DEFAULT '0',
  `online` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `groups_id` (`groups_id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `online` (`online`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ci_users`
--

INSERT INTO `ci_users` (`id`, `groups_id`, `username`, `password`, `email`, `active`, `lastvisit`, `registered`, `online`) VALUES
(1, 1, 'root', '60c00a67f838ec77fc6ac3d7df57e18115c12709', 'contact@lezard-numerique.net', 1, 1349716789, 1303587380, 1),
(2, 2, 'admin', '2bc5d99d425304c30e9c5af79a1482b5b6114cbf', 'admin@lezard-numerique.net', 1, 1349305336, 1315319295, 1),
(3, 3, 'john', '2bc5d99d425304c30e9c5af79a1482b5b6114cbf', 'johndoe@test.fr', 1, 1349305667, 1315326074, 1);
