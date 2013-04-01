<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_groups');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/groups/create')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable"><?php echo $notice;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if(isset($groups) && $groups):?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers: {2:{sorter: false}},
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
				<th width="70%"><?php echo $this->lang->line('td_title');?></th>
				<th width="25%" colspan="2"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody id="sortable">
			<?php $i = 1;?>
			<?php foreach($groups as $group):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>">
				<td class="center"><?php echo $i;?></td>
				<td><?php echo ucfirst($group['title']);?></td>
				<td class="center">
					<?php if($group['id'] != 1 && $group['id'] != 2):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/groups/edit/'. $group['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if($group['id'] != 1 && $group['id'] != 2):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/groups/delete/'. $group['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" onclick="javascript:return confirmDelete();" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
					<?php endif;?>
				</td>
			</tr>
			<?php $i++;endforeach;?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<!-- [Main] end -->