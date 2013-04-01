<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<h2><?php echo $this->lang->line('title_modules');?></h2>
<?php if ($notice = $this->session->flashdata('notification')):?>
<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
<?php endif;?>
<?php if ($alerte = $this->session->flashdata('alert')):?>
<p class="alerte alerte_closable" style="display:none;"><?php echo $alerte;?></p>
<?php endif;?>
<script type="text/javascript">
$(function() {
		$("#table_sort").tablesorter({
			headers: {4:{sorter: false}},
		});
		$('a.tooltip').tooltip({
			track: true,
			delay: 0,
			fixPNG: true,
			showURL: false,
			showBody: " - ",
			top: -35,
			left: 5
		});
	});
</script>
<table class="table_list" id="table_sort">
	<thead>
		<tr>
			<th width="5%" class="center">#</th>
			<th width="30%"><?php echo $this->lang->line('td_modules');?></th>
			<th width="35%"><?php echo $this->lang->line('td_description');?></th>
			<th width="10%"><?php echo $this->lang->line('td_version');?></th>
			<th width="20%" colspan="3"><?php echo $this->lang->line('btn_action');?></th>
		</tr>
	</thead>
		<tbody>
		<?php $i = 1;foreach ($modules as $module): ?>
		<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
		<tr class="<?php echo $rowClass?>">
			<td class="center"><?php echo $i;?></td>
			<td><?php echo ucfirst($module['name'])?></td>
			<td><?php echo $module['description']?></td>
			<td><?php echo $module['version']?></td>
			<td class="center">
				<?php if ($module['active'] == 1 && $module['ordering'] >= 100): ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/module/move/up/'. $module['name'])?>" title="<?php echo $this->lang->line('btn_move_up');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png')?>" width="16px" height="16px" alt="<?php echo $this->lang->line('btn_move_up');?>"/></a>&nbsp;
				<a href="<?php echo site_url($this->config->item('admin_folder').'/module/move/down/'. $module['name'])?>" title="<?php echo $this->lang->line('btn_move_down');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16px" height="16px" alt="<?php echo $this->lang->line('btn_move_down');?>"/></a>
				<?php endif;?>
			</td>
			<td class="center">
				<?php if ($module['active'] == 1 && $module['ordering'] >= 100): ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/module/desactivate/'. $module['name'])?>" class="tooltip" title="<?php echo $this->lang->line('btn_desactivate');?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/status_green.png');?>" alt="<?php echo $this->lang->line('btn_desactivate');?>" width="16px" height="16px"/></a>
				<?php elseif ($module['active'] == 0) : ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/module/activate/'. $module['name'])?>" class="tooltip" title="<?php echo $this->lang->line('btn_activate');?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/status_red.png');?>" alt="<?php echo $this->lang->line('btn_activate');?>" width="16px" height="16px"/></a>
				<?php endif;?>
			</td>
			<td class="center">
				<?php if ($module['active'] == 1  && $module['ordering'] >= 100): ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/module/uninstall/'. $module['name'])?>" title="<?php echo $this->lang->line('btn_uninstall_module');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/uninstall.gif');?>" alt="<?php echo $this->lang->line('btn_uninstall_module');?>" width="16px" height="16px"/></a>
				<?php elseif ($module['active'] == -1): ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/module/install/'. $module['name'])?>" title="<?php echo $this->lang->line('btn_install_module');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/install.gif');?>" alt="<?php echo $this->lang->line('btn_install_module');?>" width="16px" height="16px"/></a>
				<?php else: ?>
				<?php if (isset($module['nversion']) && $module['nversion'] > $module['version']) : ?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/update/'. $module['name'])?>"><span style='color: #FF0000'><?php echo $this->lang->line('btn_update');?></span></a>
				<?php endif;?>
				<?php endif;?>
			</td>
		</tr>
		<?php $i++;endforeach;?>
	</tbody>
</table>
<!-- [Main] end -->