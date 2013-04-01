<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_news');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/newsCreate')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
	<?php endif;?>
	<?php if (isset($news) && $news) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort_news").tablesorter({
			headers: {3:{sorter: false}},
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
	<table class="table_list" id="table_sort_news">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="35%"><?php echo $this->lang->line('td_title');?></th>
				<th width="35%"><?php echo $this->lang->line('td_uri');?></th>
				<th width="25%" colspan="3" class="last"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody id="sortable_news">
			<?php if (isset($news) && $news) : ?>
			<?php $i = 1;$count_news = count($news);foreach($news as $new): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>" id="items_<?php echo $new['id'];?>">
				<td class="center"><?php echo $i?></td>
				<td><?php echo html_entity_decode($new['title'])?></td>
				<td><?php echo $new['uri']?></td>
				<td class="center">
					<?php if ($new['active'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/newsFlag/'.$new['id'].'/'.$new['active']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/newsFlag/'.$new['id'].'/'.$new['active']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';
					endif;?>
				</td>				
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/newsEdit/'.$new['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/newsDelete/'.$new['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++; endforeach;?>
			<?php endif; ?>
		</tbody>
	</table>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total;?> <?php echo $this->lang->line('text_total_news');?>
		</div>
		<?php if(isset($pager) && $pager):?>
		<div class="pager_right">
			<?php echo $pager?>
		</div>
		<?php endif;?>
	</div>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_news');?></p>
	<?php endif;?>
</div>
<!-- [Main] end -->