<?php if(!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php if ($notification = $this->session->flashdata('notification')):?>
<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
<?php endif;?>
<?php if($alerte = $this->session->flashdata('alerte')):?>
<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
<?php endif;?>
<div id="pages">
	<?php $this->load->view('partials/live-view');?>
	<h1><?php echo html_entity_decode($page['title']);?></h1>
	<div <?php if ((isset($this->settings['page_home']) && $this->settings['page_home'] && $this->uri->uri_string() == $this->settings['page_home'].$this->config->item('url_suffix_ext')) || $this->uri->uri_string() == '') :?>class="parag_home"<?php endif;?>>
	<?php echo $paragraph;?>
	</div>
	<script type="text/javascript">
	$(document).ready(function() {
		$(".colorbox").colorbox({rel:'paragraphs', onComplete : function() {$(this).colorbox.resize();}});
	});
	</script>
	<?php if ((isset($this->settings['page_home']) && $this->settings['page_home'] && $this->uri->uri_string() == $this->settings['page_home'].$this->config->item('url_suffix_ext')) || $this->uri->uri_string() == '') :?>
	<?php echo $this->block->get('box_portfolio');?>
	<?php echo $this->block->get('box_news');?>
	<?php endif;?>
	<br class="clear"/>
	<?php if(isset($page['show_sub_pages']) && $page['show_sub_pages'] == 1) :?>
	<?php if($sub_pages = $this->model->get_sub_pages($page['id'])):?>
	<div class="sub_pages">
		<ul>
		<?php foreach($sub_pages as $sub_page) : ?>
		<li><a href="<?php echo site_url($sub_page['uri'].$this->config->item('url_suffix_ext'))?>"><?php echo html_entity_decode($sub_page['title'])?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<?php endif;?>
	<?php endif;?>
	<?php if(isset($page['show_navigation']) && $page['show_navigation'] == 1) :?>
	<?php $this->model->get_next_page($page) ?>
	<div class="nav_pages">
		<?php if (isset($page['previous_page'])) :?>
		<div class="previous_page">
		<a href="<?php echo site_url($page['previous_page']['uri'].$this->config->item('url_suffix_ext'))?>"><span><<</span> <?php echo html_entity_decode($page['previous_page']['title'])?></a>
		</div>
		<?php endif; ?>
		<?php if (isset($page['next_page'])) : ?>
		<div class="next_page">
		<a href="<?php echo site_url($page['next_page']['uri'].$this->config->item('url_suffix_ext'))?>"><?php echo html_entity_decode($page['next_page']['title'])?> <span>>></span></a>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>
