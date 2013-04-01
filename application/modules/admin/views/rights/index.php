<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2 class="settings"><?php echo $this->lang->line('title_rights');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/rights/create')?>"><?php echo $this->lang->line('btn_create')?></a></li>
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable"><?php echo $notice;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if(isset($modules) && $modules) : ?>
	<script type="text/javascript">
	$(function() {
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
	<table class="table_list">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="30%"><?php echo $this->lang->line('td_group')?></th>
				<th width="30%"><?php echo $this->lang->line('td_level')?></th>
				<th width="15%" colspan="2" class="center"><?php echo $this->lang->line('td_action')?></th>
			</tr>
		</thead>
		<tbody>
		<?php $i = 1;?>
		<?php foreach ($modules as $module): ?>
		<tr>
			<td colspan="5"><strong><?php echo $this->lang->line('text_module')?> : <?php echo ucfirst($module['name']);?></strong></td>
		</tr>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<?php if($groups = $this->right->get_rights(array('module' => $module['name']))):?>
			<?php foreach($groups as $group):?>
			<tr class="<?php echo $rowClass?>">
				<td class="center"><?php echo $i?></td>
				<td><?php echo ucfirst($group['title']);?></td>
				<td><span class="uppercase"><?php echo $this->template['levels'][$group['level']]?></span></td>
				<td class="center">
					<?php if($group['id'] != 1 && $group['id'] != 2):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/rights/edit/'.$group['rID'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if($group['id'] != 1 && $group['id'] != 2):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/rights/delete/'.$group['rID'])?>" title="<?php echo $this->lang->line('btn_delete')?>" onclick="javascript:return confirmDelete();" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
					<?php endif;?>
				</td>
			</tr>
			<?php endforeach;?>
			<?php endif;?>
			<?php $i++; endforeach;?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<!-- [Main] end -->