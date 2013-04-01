<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_medias');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mediasCreate/1')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
	<?php endif;?>
	<p class="ajax_notice notice_closable"></p>
	<?php $this->load->view('admin/partials/filter-medias');?>
	<?php if (isset($medias) && $medias) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort_medias").tablesorter({
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
	<?php //pre_affiche($medias);?>
	<table class="table_list" id="table_sort_medias">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="10%" class="center"><?php echo $this->lang->line('td_medias');?></th>
				<th width="35%"><?php echo $this->lang->line('td_title');?></th>
				<th width="35%"><?php echo $this->lang->line('td_uri');?></th>
				<th width="15%" colspan="3" class="last"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody id="sortable_medias">
			<?php if (isset($medias) && $medias) : ?>
			<?php $i = 1;$count_medias = count($medias);foreach($medias as $media): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>" id="items_<?php echo $media['id'];?>">
				<td class="center"><?php echo $media['mID'];?></td>
				<td class="center" style="padding:5px 0;"><?php if(is_file('./medias/images/'.$media['file']) && is_readable('./medias/images/'.$media['file'])):?><img src="<?php echo site_url($this->config->item('medias_folder').'/images/x50/'.$media['file']);?>" alt="<?php echo $media['file'];?>" class="img"/><?php endif;?></td>
				<td><?php echo $media['mTITLE']?></td>
				<td><?php echo $media['mURI']?></td>
				<td class="center">
					<?php if ($media['mACTIVE'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/mediasFlag/'.$media['medias_id'].'/'.$media['mACTIVE']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/mediasFlag/'.$media['medias_id'].'/'.$media['mACTIVE']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';
					endif;?>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mediasEdit/'.$media['categories_id_default'].'/'.$media['medias_id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mediasDelete/'.$media['medias_id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++; endforeach;?>
			<?php endif; ?>
		</tbody>
	</table>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total_medias;?> <?php echo $this->lang->line('text_total_medias');?>
		</div>
		<?php if(isset($pager) && $pager):?>
		<div class="pager_right">
			<?php echo $pager?>
		</div>
		<?php endif;?>
	</div>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_medias');?></p>
	<?php endif;?>
</div>
<!-- [Main] end -->
