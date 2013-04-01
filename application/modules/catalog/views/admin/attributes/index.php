<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_attributes');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesCreate')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if(isset($attributes) && $attributes) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers:{1:{sorter: false}},
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
	<form action="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesDeleteArray');?>" method="post">
		<table class="table_list" id="table_sort">
			<thead>
				<tr>
					<th width="80%"><?php echo $this->lang->line('td_name')?></th>
					<th width="20%" colspan="2"><?php echo $this->lang->line('td_action')?></th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1;$count_attributes = count($attributes);foreach($attributes as $attribute):?>
				<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
				<tr class="<?php echo $rowClass?>">
					<td>
						<?php echo html_entity_decode($attribute['name']);?>
					</td>
					<td class="center">
						<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesEdit/'.$attribute['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
					</td>
					<td class="center">
						<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesDelete/'.$attribute['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
						<input type="checkbox" name="delete_attributes[]" value="<?php echo $attribute['id'];?>"/>
					</td>
				</tr>
				<?php $i++;endforeach;?>
			</tbody>
		</table>
		<div class="pager">
			<div class="pager_left">
				<?php echo $total;?> <?php echo $this->lang->line('text_total_attributes');?>
			</div>
			<div class="pager_right">
				<?php echo $pager?>
				<input type="submit" id="submit" name="submit" value="Supprimer" class="input_submit red" style="float:right;margin:0 0 0 20px;" onclick="javascript:return confirmDelete();"/>
			</div>
		</div>
	</form>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_attributes');?></p>
	<?php endif;?>
</div>
<!-- [Main] end -->
