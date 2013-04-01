<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->system->site_name?> | <?php echo $this->lang->line('text_administration');?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/css/reset.css')?>" media="all" />
	<?php foreach($this->css->get() as $css): ?>
	<link rel="stylesheet" href="<?php echo site_url($css)?>" type="text/css" media="screen" charset="utf-8" />
	<?php endforeach; ?>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/css/custom.css')?>" media="all" />
	<script type="text/javascript">
		var BASE_URI = '<?php echo site_url();?>';
		var ADMIN_FOLDER = '<?php echo $this->config->item('admin_folder');?>';
		var ADMIN_THEME = '<?php echo $this->config->item('theme_admin');?>';
		var DPM_ID = '<?php echo $this->uri->segment(2);?>';
		var LANG = '<?php echo $this->user->lang;?>';
		var APPPATH = '<?php echo APPPATH;?>';
		var CSRF = '<?php echo $this->security->get_csrf_hash();?>';
	</script>
	<script src="<?php echo site_url(APPPATH.'/views/'.$this->config->item('theme_admin').'/js/tinymce/tiny_mce.js')?>" type="text/javascript"></script>	
	<?php foreach($this->javascripts->get() as $javascript): ?>
	<script src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/js/'.$javascript)?>" type="text/javascript"></script>
	<?php endforeach; ?>
	<script type="text/javascript" src="<?php echo site_url(APPPATH.'/views/'.$this->config->item('theme_admin').'/js/ddsmoothmenu.js')?>">
	/***********************************************
	* Smooth Navigational Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
	* This notice MUST stay intact for legal use
	* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
	***********************************************/
	</script>
	<script type="text/javascript">
	ddsmoothmenu.init({
		mainmenuid: "navigation_level", //menu DIV id
		orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
		classname: 'ddsmoothmenu', //class added to menu's outer DIV
		//customtheme: ["#1c5a80", "#18374a"],
		contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
	})
	</script>
	<?php $this->plugin->do_action('header');?>
	<!-- MUST BE THE LAST SCRIPT IN <HEAD></HEAD></HEAD> png fix -->
	<script src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/js/pngfix.js')?>" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$(document).pngFix();
	});
	</script>
</head>
<body <?php if($this->uri->uri_string() == $this->config->item('admin_folder')) echo 'onload="init()"';?>>
<!-- [Global] start -->
<div id="global">
<?php if ($this->system->maintenance == 1):?><div id="bl_maintenance"><?php echo $this->lang->line('text_maintenance');?></div><?php endif;?>
<!-- [Header] start -->
<div id="header">
	<h1>
		<a href="<?php echo site_url('/')?>"><?php echo $this->system->site_name?> | <?php echo $this->lang->line('text_administration');?></a>
	</h1>	
	<div id="box_live_view">
		<?php if($this->user->liveView):?>
		<a href="<?php echo site_url($this->config->item('admin_folder').'/liveView/logout')?>" class="green"><?php echo $this->lang->line('btn_live_view');?></a>
		<?php else:?>
		<a href="<?php echo site_url($this->config->item('admin_folder').'/liveView')?>"><?php echo $this->lang->line('btn_live_view');?></a>
		<?php endif;?>
	</div>
	<div id="box_lang">
		<?php if ($languages = $this->language->list_languages(true)) :?>
		<?php if(count($languages) > 1) :?>
		<ul>
			<?php foreach ($languages as $language): ?>
			<li>
				<a href="<?php echo site_url($this->config->item('theme_admin').'/setLanguages/'.$language['code'].'/'.$this->uri->uri_string()) ?>" title="<?php echo $language['name'];?>" class="tooltip<?php echo ($this->session->userdata('lang') == $language['code']) ? " active" : ""?>">
					<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/flags/'.$language['code'].'.jpg')?>" alt="<?php echo $language['name'];?>" width="16" height="11"/>
				</a>
			</li>
			<?php endforeach;?>
		</ul>
		<?php endif; ?>
		<?php else : ?>
		<span class="no_language"><?php echo $this->lang->line('notification_no_languages');?></span>
		<?php endif;?>
	</div>
</div>
<!-- [Header] end -->
<!-- [Navigation] start -->
<div id="navigation_level_repeat">
	<div id="navigation_level">
		<?php if($this->user->logged_in):?>		
		<ul class="navigation">
			<li class="dp_navigation_sub<?php if ($module == 'admin' && $view == 'index' || ($this->uri->segment(2) == 'settings' || $this->uri->segment(2) == 'module' || $this->uri->segment(2) == 'rights' || $this->uri->segment(2) == 'groups' || $this->uri->segment(2) == 'users' || $this->uri->segment(2) == 'phpInfo' || $this->uri->segment(2) == 'languages' || $this->uri->segment(2) == 'translations' || $this->uri->segment(2) == 'medias')):?> current<?php endif;?>" style="padding-left:0;"><a href="<?php echo site_url('admin')?>"><?php echo $this->lang->line('menu_dashboard');?></a><span></span></li>
			<li class="dp_navigation_sub<?php if ($this->uri->segment(2) == 'navigations'):?> current<?php endif;?>"><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations');?>"><?php echo $this->lang->line('menu_navigation');?></a><span></span></li>
			<?php if (isset($this->system->modules)) : ?>
			<?php foreach ($this->system->modules as $admin_module): ?>			
			<?php if ($admin_module['admin'] == 1 && $admin_module['active'] == 1 && isset($this->user->level[$admin_module['name']])) :?>
			<li class="dp_navigation_sub<?php if ($module == $admin_module['name']):?> current<?php endif;?>"><a href="<?php echo site_url('admin/'.$admin_module['name'])?>"><?php echo $this->lang->line('menu_'.$admin_module['name']);?></a><?php if($ss_menu = $this->block->get('sidebar_admin_'.$admin_module['name'])) echo $this->block->get('sidebar_admin_'.$admin_module['name']);?><span></span></li>
			<?php endif; ?>
			<?php endforeach;?>
			<?php endif; ?>
		</ul>
		<?php endif;?>
		<!-- start nav-right -->
		<div id="nav_right">			
			<div class="nav_divider">&nbsp;</div>
			<div class="showhide_settings"></div>			
			<div class="nav_divider">&nbsp;</div>
			<a href="<?php echo site_url('admin/logout');?>" id="logout"><?php echo $this->lang->line('btn_logout');?></a>
			<div class="clear">&nbsp;</div>			
			<!--  start account-content -->
			<div class="account_content">
				<div class="settings_drop_inner">
					<?php if($this->user->groups_id == 1 || $this->user->groups_id == 2):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/settings')?>" id="acc_settings"><?php echo $this->lang->line('text_config_general');?></a>
					<div class="clear">&nbsp;</div>
					<div class="acc_line">&nbsp;</div>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/module')?>" id="acc_modules"><?php echo $this->lang->line('text_config_modules');?></a>
					<div class="clear">&nbsp;</div>
					<div class="acc_line">&nbsp;</div>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/users')?>" id="acc_users"><?php echo $this->lang->line('text_config_users');?></a>
					<div class="clear">&nbsp;</div>
					<div class="acc_line">&nbsp;</div>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/groups')?>" id="acc_groups"><?php echo $this->lang->line('text_config_groups');?></a>
					<div class="clear">&nbsp;</div>
					<div class="acc_line">&nbsp;</div>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/rights')?>" id="acc_rights"><?php echo $this->lang->line('text_config_rights');?></a>
					<div class="clear">&nbsp;</div>
					<div class="acc_line">&nbsp;</div>
					<?php if($this->user->root) :?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/languages')?>" id="acc_languages"><?php echo $this->lang->line('text_config_languages');?></a>
					<div class="clear">&nbsp;</div>
					<div class="acc_line">&nbsp;</div>
					<?php endif;?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/translations')?>" id="acc_translations"><?php echo $this->lang->line('text_config_translations');?></a>
					<div class="clear">&nbsp;</div>
					<div class="acc_line">&nbsp;</div>
					<?php endif;?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/medias')?>" id="acc_medias"><?php echo $this->lang->line('text_config_medias');?></a>
				</div>
			</div>
			<!--  end account-content -->
			
		</div>
		<!-- end nav-right -->

	</div>
</div>
<!-- [Navigation] end -->
<!-- [Content] start -->
<div id="content">
	<div id="content_inner">
