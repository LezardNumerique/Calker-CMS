<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CALKER CMS INSTALLATION - <?php echo $title;?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/admin/css/reset.css')?>" media="all" />
	<?php foreach($this->css->get() as $css): ?>
	<link rel="stylesheet" href="<?php echo site_url($css)?>" type="text/css" media="screen" charset="utf-8" />
	<?php endforeach; ?>
	<script type="text/javascript">
	var BASE_URI = '<?php echo site_url();?>';
	var ADMIN_FOLDER = '<?php echo $this->config->item('admin_folder');?>';
	var ADMIN_THEME = '<?php echo $this->config->item('theme_admin');?>';
	var DPM_ID = '<?php echo $this->uri->segment(2);?>';
	var LANG = '<?php echo $this->user->lang;?>';
	var APPPATH = '<?php echo APPPATH;?>';
	</script>
	<?php foreach($this->javascripts->get() as $javascript): ?>
	<script src="<?php echo site_url(APPPATH.'views/install/js/'.$javascript)?>" type="text/javascript"></script>
	<?php endforeach; ?>
	<?php $this->plugin->do_action('header');?>
	<!-- MUST BE THE LAST SCRIPT IN <HEAD></HEAD></HEAD> png fix -->
	<script src="<?php echo site_url(APPPATH.'views/install/js/pngfix.js')?>" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$(document).pngFix();
	});
	</script>
</head>
<body>
<!-- [Global] start -->
<div id="global">
<!-- [Navigation] start -->
<div id="header_install">
	<h1>
		CALKER CMS | Installation
	</h1>
</div>
<!-- [Navigation] end -->
<!-- [Content] start -->
<div id="content">
	<div id="content_inner">