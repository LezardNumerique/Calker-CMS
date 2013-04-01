<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->system->site_name?> | <?php echo $this->lang->line('text_administration');?></title>
<script type="text/javascript">
var BASE_URI = '<?php echo site_url();?>';
var ADMIN_FOLDER = '<?php echo $this->config->item('admin_folder');?>';
</script>
<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/css/reset.css')?>" media="all" />
<?php foreach($this->css->get() as $css): ?>
<link rel="stylesheet" href="<?php echo site_url($css)?>" type="text/css" media="screen" charset="utf-8" />
<?php endforeach; ?>
<!--  js -->
<?php foreach($this->javascripts->get() as $javascript): ?>
<script src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/js/'.$javascript)?>" type="text/javascript"></script>
<?php endforeach; ?>
<script type="text/javascript">
var BASE_URI = '<?php echo site_url();?>';
var ADMIN_FOLDER = '<?php echo $this->config->item('admin_folder');?>';
var ADMIN_THEME = '<?php echo $this->config->item('theme_admin');?>';
var DPM_ID = '<?php echo $this->uri->segment(2);?>';
</script>
<!-- MUST BE THE LAST SCRIPT IN <HEAD></HEAD></HEAD> png fix -->
<script src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/js/pngfix.js')?>" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(document).pngFix();
});
</script>
</head>
<body id="login_bg">
<!-- Start: login-holder -->
<div id="login_holder">
	<?php if (isset($alerte) || $alerte = $this->session->flashdata('alerte')):?>
	<p class="alerte alerte_closable" style="display:none;"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if (isset($notification) || $notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none;"><?php echo $notification;?></p>
	<?php endif;?>
	<!-- start sitename -->
	<div id="login_sitemap">
		<?php echo $this->system->site_name;?>
	</div>
	<!-- end sitename -->
	<br class="clear"/>
	<!--  start loginbox -->
	<div id="login_box">
		<!--  start login-inner -->
		<div id="login_inner">
			<form action="<?php echo site_url($this->config->item('admin_folder').'/login')?>" method="post" accept-charset="utf-8">
				<?php if ($redirect = $this->session->flashdata('redirect')):?>
				<input type='hidden' name='redirect' value='<?php echo $redirect;?>' />
				<?php endif;?>
				<label for="username"><?php echo $this->lang->line('label_username');?></label>
				<input type='text' name="username" id="username" class="input_text"/>
				<label for="password"><?php echo $this->lang->line('label_password');?></label>
				<input type="password" name="password" value="" id="password" class="input_text"/>
				<br class="clear" />
				<p><input type="submit" name="submit" class="input_submit" value="<?php echo $this->lang->line('btn_connect');?>"/></p>
			</form>
		</div>
		<!--  end login-inner -->
	</div>
 <!--  end loginbox -->
</div>
<!-- End: login-holder -->
</body>
</html>