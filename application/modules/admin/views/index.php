<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<div id="dashboard">
		<h2><?php echo $this->lang->line('title_dashboard');?></h2>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<div class="dashboard_left first">
			<h3><?php echo $this->lang->line('title_system_infos');?></h3>
			<dl class="fieldset">
				<dt><?php echo $this->lang->line('text_version');?></dt>
				<dd><strong>CALKER <?php echo  $this->system->version;?></strong></dd>
				<dt><?php echo $this->lang->line('text_site_name');?></dt>
				<dd><?php echo $this->system->site_name;?></dd>
				<dt><?php echo $this->lang->line('text_site_team');?></dt>
				<dd><?php echo $this->administration->no_active_users();?></dd>
				<dt><?php echo $this->lang->line('text_bdd_size');?></dt>
				<dd><?php echo formatfilesize($this->administration->db_size());?></dd>
				<dt><?php echo $this->lang->line('text_cache_size');?></dt>
				<dd><?php echo $this->system->cache_size();?></dd>
			</dl>
		</div>
		<div class="dashboard_left">
			<h3><?php echo $this->lang->line('title_tools_box');?></h3>
			<ul class="fieldset">
				<?php if($this->user->root) :?><li><a href="<?php echo $this->config->item('admin_folder');?>/chmod"><?php echo $this->lang->line('menu_chmod');?></a></li><?php endif;?>
				<li><a href="<?php echo $this->config->item('admin_folder');?>/clearCache"><?php echo $this->lang->line('menu_reload_cache');?></a></li>
				<li><a href="<?php echo $this->config->item('admin_folder');?>/backupBdd"><?php echo $this->lang->line('menu_bdd_backup');?></a></li>
				<li><a href="<?php echo $this->config->item('admin_folder');?>/purge"><?php echo $this->lang->line('menu_purge');?></a></li>
				<?php if($this->user->root) :?><li><a href="<?php echo $this->config->item('admin_folder');?>/phpInfo"><?php echo $this->lang->line('menu_phpinfo');?></a></li><?php endif;?>
				<?php if($this->user->root) :?><li><a href="<?php echo $this->config->item('admin_folder');?>/Utf8Tables"><?php echo $this->lang->line('menu_utf8_tables');?></a></li><?php endif;?>
				<li><a href="<?php echo $this->config->item('admin_folder');?>/optimizeTables"><?php echo $this->lang->line('menu_optimise_tables');?></a></li>
				<?php if($this->user->root) :?><li><a href="<?php echo $this->config->item('admin_folder');?>/repairTables"><?php echo $this->lang->line('menu_repair_tables');?></a></li><?php endif;?>
			</ul>
		</div>
		<div class="dashboard_left last">
			<?php $this->load->view('admin/partials/map');?>
		</div>
		<?php if(isset($visits) && $visits):?>
		<div class="dashboard_left chart">
			<?php $this->load->view('admin/partials/visits');?>
		</div>
		<br class="clear"/>
		<?php $this->load->view('admin/partials/analytics');?>
		<?php endif;?>
		<br class="clear"/>
	</div>
</div>
<script type="text/javascript">
	var margin = 36;
	var width = ($('#main').width()/3)-margin;
	$('.dashboard_left').css({width: width});
	$(window).resize(function() {
		var width = ($('#main').width()/3)-margin;
		$('.dashboard_left').css({width: width});
	});
	var width = $('#dashboard .first').width()-42;
	$('#dashboard .first dl').css({width: width});
	$(window).resize(function() {
		var width = $('#dashboard .first').width()-42;
		$('#dashboard .first dl').css({width: width});
	});
	var width = ($('#main').width());
	$('#dashboard .chart').css({width: width-25});
	$(window).resize(function() {
		var width = ($('#main').width());
		$('#dashboard .chart').css({width: width-25});
	});
	var width = ($('#main').width()/2)-33;
	$('#dashboard .analytics').css({width: width});
	$(window).resize(function() {
		var width = ($('#main').width()/2)-33;
		$('#dashboard .analytics').css({width: width});
	});
</script>
<!-- [Main] end -->