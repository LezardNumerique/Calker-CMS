<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_languages');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/languages/create')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable"><?php echo $notice;?></p>
	<?php endif;?>
	<p class="ajax_notice notice_closable"></p>
	<?php if(isset($langs) && $langs) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers: {4:{sorter: false}},
		});
		$("#sortable").sortable({
			update : function () {
				$.post("<?php echo site_url($this->config->item('admin_folder').'/languages/sortOrder');?>", $('#sortable').sortable('serialize'), function(data) {
					$('.ajax_notice').fadeTo(0, 200);
					$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
					$(".notice_closable").append('<a href="#" class="notice_close">Fermer</a>');
				});
			}
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
				<th width="30%"><?php echo $this->lang->line('td_title');?></th>
				<th width="30%"><?php echo $this->lang->line('td_code');?></th>
				<th width="10%" class="center"><?php echo $this->lang->line('td_default');?></th>
				<th width="25%" colspan="4" class="center"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody id="sortable">
			<?php $i = 1;$count_lang = count($langs);?>
			<?php foreach ($langs as $lang): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>" id="items_<?php echo $lang['id'];?>">
				<td class="center"><?php echo $i;?></td>
				<td><?php echo $lang['name'];?></td>
				<td><?php echo $lang['code'];?></td>
				<td class="center">
					<?php if ($lang['default'] == 1): echo $this->lang->line('text_yes'); else: echo "<a href='" . site_url($this->config->item('admin_folder').'/languages/setDefault/'. $lang['id']) . "'>".$this->lang->line('text_activate_default')."</a>";endif;?>
				</td>
				<td class="center">
					<?php if($i != '1'):?><a href="<?php echo site_url($this->config->item('admin_folder').'/languages/move/'.$lang['id'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a>&nbsp;<?php else :?>&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>" />
					<?php endif;?>
					<?php if(($count_lang) != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/languages/move/'.$lang['id'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if ($lang['active'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/languages/flag/'.$lang['id'].'/'.$lang['active']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/languages/flag/'.$lang['id'].'/'.$lang['active']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';
					endif;?>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/languages/edit/'. $lang['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/languages/delete/'. $lang['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>"  class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++; endforeach;?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<!-- [Main] end -->