<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php if(isset($meta_title) && $meta_title):?><?php echo $meta_title?> - <?php echo $this->system->site_name;?><?php else:?><?php if(isset($title)) echo $title.' - '?><?php if(isset($this->system->meta_more)) echo $this->system->meta_more.' - '?><?php if(isset($this->system->site_name)) echo $this->system->site_name?><?php endif;?></title>
	<meta name="keywords" content="<?php if (isset($meta_keywords) && $meta_keywords != ''):?><?php echo $meta_keywords?><?php else: ?><?php echo $this->system->meta_keywords;?><?php endif; ?>"/>
	<meta name="description" content="<?php if (isset($meta_description) && $meta_description):?><?php echo $meta_description?><?php else: ?><?php echo $this->system->meta_description;?><?php endif;?>"/>
	<meta name="robots" content="index,follow" />
	<link rel="shortcut icon" href="<?php echo base_url()?>application/views/<?php echo $this->system->theme ?>/images/favicon.ico" type="image/x-icon" />
	<?php foreach($this->css->get() as $css): ?>
	<link rel="stylesheet" href="<?php echo site_url($css)?>?v=<?php echo mktime();?>" type="text/css" media="screen"/>
	<?php endforeach; ?>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/css/'.$this->system->stylesheet)?>?v=<?php echo mktime();?>" media="all"/>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/css/ui.css');?>?v=<?php echo mktime();?>" media="all"/>
	<!--[if IE]>
	<link rel="stylesheet" href="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/css/ie/ie.css')?>?v=<?php echo mktime();?>" type="text/css" media="screen"/>
	<![endif]-->
	<?php if($this->user->liveView):?>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/css/liveview.css');?>?v=<?php echo mktime();?>" media="all"/>
	<?php endif;?>
	<script type="text/javascript">
	var BASE_URI = '<?php echo site_url();?>';
	var LANG = '<?php echo $this->user->lang;?>';
	var APPPATH = '<?php echo APPPATH;?>';
	var THEME = '<?php echo $this->system->theme;?>';
	var LANG = '<?php echo $this->user->lang;?>';
	<?php if($this->user->liveView):?>var ADMIN_THEME = '<?php echo $this->config->item('theme_admin');?>';<?php endif;?>
	<?php if($this->user->liveView):?>var ADMIN_FOLDER = '<?php echo $this->config->item('admin_folder');?>';<?php endif;?>
	</script>
	<?php if($this->user->liveView):?><script src="<?php echo site_url(APPPATH.'/views/'.$this->config->item('theme_admin').'/js/tinymce/tiny_mce.js')?>" type="text/javascript"></script><?php endif;?>
	<?php foreach($this->javascripts->get() as $javascript): ?>
	<script src="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/js/'.$javascript)?>?v=<?php echo mktime();?>" type="text/javascript"></script>
	<?php endforeach; ?>
	<script src="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/js/flexslider.js')?>?v=<?php echo mktime();?>" type="text/javascript"></script>
	<script src="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/js/ddsmoothmenu.js')?>?v=<?php echo mktime();?>" type="text/javascript">
	//Smooth Navigational Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
	//This notice MUST stay intact for legal use
	//Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
	</script>
	<script type="text/javascript">
	ddsmoothmenu.init({
		mainmenuid: "sidebartop",
		orientation: 'h',
		contentsource: "markup"
	});
	</script>
	<?php $this->plugin->do_action('header');?>
	<?php if($this->system->google_analytic_visits == 1) echo '<script type="text/javascript">'.$this->system->google_analytic_code.'</script>';?>
	<script src="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/js/pngfix.js')?>" type="text/javascript"></script>
	<!-- MUST BE THE LAST SCRIPT IN <HEAD></HEAD></HEAD> png fix -->
	<script type="text/javascript">
	$(document).ready(function(){
		$(document).pngFix();
	});
	</script>
</head>
<body>
	<?php if($this->user->logged_in):?>
	<div id="live_view_alert">
		<a href="<?php echo site_url($this->config->item('admin_folder'));?>"><?php echo $this->lang->line('btn_return_backoffice');?></a>
		<?php if($this->user->liveView):?>
		<a href="<?php echo site_url($this->config->item('admin_folder').'/liveView/logout')?>" class="green"><?php echo $this->lang->line('btn_live_view');?></a>
		<?php else:?>
		<a href="<?php echo site_url($this->config->item('admin_folder').'/liveView')?>" class="red"><?php echo $this->lang->line('btn_live_view');?></a>
		<?php endif;?>
	</div>
	<?php endif;?>
	<div id="header_content">
		<div id="header">
			<a href="<?php echo $this->system->get_uri();?>" id="logo"><img src="<?php echo site_url(APPPATH.'views/'.$this->system->theme.'/img/'.$this->system->logo);?>" alt="<?php echo $this->system->site_name;?>" <?php echo get_media_size(APPPATH.'views/'.$this->system->theme.'/img/'.$this->system->logo, 3);?>/>
			</a>
			<?php if(count($this->language->list_languages()) > 1):?>
			<div class="box_lang">
				<?php if ($languages = $this->language->list_languages()) :?>
				<ul>
					<?php foreach ($languages as $language): ?>
					<li>
						<a href="<?php echo site_url($language['code'], '', true)?>"<?php echo ($this->session->userdata('lang') == $language['code']) ? " class='active'" : "";?>><img src="<?php echo site_url(APPPATH.'views/assets/img/flags/'.$language['code'].'.jpg')?>" alt="<?php echo $language['name'];?>" width="16" height="11"/></a>
					</li>
					<?php endforeach;?>
				</ul>
				<?php endif; ?>
			</div>
			<?php endif;?>
		</div>
		<div id="sidebartop">
			<?php if ($data['blockCategTree'] = $this->navigation->getTree(1)) :?>
			<?php $this->load->view($this->system->theme.'/sidebar', $data);?>
			<?php endif;?>
		</div>
	</div>
	<div id="container">
		<div id="content">
			<div id="pathway">
				<?php if($this->uri->uri_string() != '' && $this->uri->uri_string() != $this->page->settings['page_home']) echo anchor('', $this->lang->line('text_home'), false, $this->language->get_uri_language('/')); ?>
				<?php foreach($breadcrumb as $b): ?>
				<small>&gt;</small> <?php echo anchor($b['uri'], $b['title'], false, $this->language->get_uri_language('/')) ?>
				<?php endforeach; ?>
				<?php if ($this->uri->uri_string() != '' && $this->uri->uri_string() != $this->page->settings['page_home']) :?><small>&gt;</small> <?php echo $title;?><?php endif;?>
			</div>