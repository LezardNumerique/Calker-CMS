<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->system->site_name?> | <?php echo $this->lang->line('text_administration');?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/css/reset.css')?>" media="all" />
	<?php foreach($this->css->get() as $css): ?>
	<link rel="stylesheet" href="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/css/'.$css)?>" type="text/css" media="screen" charset="utf-8" />
	<?php endforeach; ?>
	<?php foreach($this->javascripts->get() as $javascript): ?>
	<script src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/js/'.$javascript)?>" type="text/javascript"></script>
	<?php endforeach; ?>
	<?php $this->plugin->do_action('header');?>
</head>
<body>
<!-- [Global] start -->
<div id="global">
<?php if ($this->system->maintenance == 1):?><div id="bl_maintenance"><?php echo $this->lang->line('text_maintenance');?></div><?php endif;?>
<!-- [Header] start -->
<div id="header">
	<h1>
		<a href="<?php echo site_url('admin')?>"><?php echo $this->system->site_name?></a>
	</h1>
</div>
<!-- [Header] end -->
