<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_categories');?><?php if($categorie['id'] != 1) echo ' : '.html_entity_decode($categorie['title']);?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesCreate/'.$categories_id)?>"><?php echo $this->lang->line('btn_create');?></a></li>
		<?php if($categories_id != 1):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categories/'.$categorie['parent_id'])?>"><?php echo $this->lang->line('btn_return');?></a></li><?php endif;?>
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
	<?php endif;?>
	<p class="ajax_notice notice_closable"></p>
	<script type="text/javascript">
	$(function() {
		$("#table_sort_categories").tablesorter({
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
		$("#sortable_categories").sortable({
			update : function () {
				$.post("<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesSortOrder/'.$categories_id);?>", $('#sortable_categories').sortable('serialize'), function(data) {
					$('.ajax_notice').fadeTo(0, 200);
					$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
					$(".notice_closable").append('<a href="#" class="notice_close">Fermer</a>');
					return robotAjax = setInterval(automateCloseAjax, 5000);
				});
			}
		});
		$("#sortable_medias").sortable({
      		handle : '.handle',
      		update : function () {
				var order = $('#sortable_medias').sortable('serialize');
				$.ajax({
					type: "POST",
					url: "<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/ajaxSortOrderMedia/'.$categories_id);?>",
					processData: false,
					data: order

				});
				$('.ajax_notice').fadeTo(0, 200);
				$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
				$(".notice_closable").append('<a href="#" class="notice_close"><?php echo $this->lang->line('btn_close');?></a>');
				return robotAjax = setInterval(automateCloseAjax, 5000);
			}
    	});
	});
	</script>
	<?php if (isset($categories) && $categories) : ?>
	<table class="table_list" id="table_sort_categories">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="35%"><?php echo $this->lang->line('td_categories');?></th>
				<th width="35%"><?php echo $this->lang->line('td_uri');?></th>
				<th width="25%" colspan="4" class="last"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody id="sortable_categories">
			<?php if (isset($categories) && $categories) : ?>
			<?php $i = 1;$count_categories = count($categories);foreach($categories as $categorie): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>" id="items_<?php echo $categorie['id'];?>">
				<td class="center"><?php echo $i?></td>
				<td>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categories/'.$categorie['id']);?>">
						<?php if ($categorie['level'] == 0) :?>
						<span class="lv0_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv0.gif')?>" alt=""/></span>
						<span class="lv0"><?php echo html_entity_decode($categorie['title'])?></span>
						<?php elseif ($categorie['level'] == 1) :?>
						<span class="lv2_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv2.gif')?>" alt=""/></span>
						<span class="lv2"><?php echo html_entity_decode($categorie['title'])?></span>
						<?php elseif ($categorie['level'] == 2) :?>
						<span class="lv1_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv1.gif')?>" alt=""/></span>
						<span class="lv1"><?php echo html_entity_decode($categorie['title'])?></span>
						<?php endif;?>
					</a>
				</td>
				<td><?php echo $categorie['uri']?></td>
				<td class="center">
					<?php if ($categorie['active'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/categoriesFlag/'.$categorie['id'].'/'.$categorie['active']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/categoriesFlag/'.$categorie['id'].'/'.$categorie['active']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';
					endif;?>
				</td>
				<td class="center">
					<?php if($i != '1'):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesMove/'.$categorie['id'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
					<?php if(($count_categories) != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesMove/'.$categorie['id'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesEdit/'.$categorie['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesDelete/'.$categorie['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++; endforeach;?>
			<?php endif; ?>
		</tbody>
	</table>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total_categories;?> <?php echo $this->lang->line('text_total_categories');?>
		</div>
		<?php if(isset($pager) && $pager):?>
		<div class="pager_right">
			<?php echo $pager?>
		</div>
		<?php endif;?>
	</div>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_categories');?></p>
	<?php endif;?>
	<br class="clear"/>
	<h2 id="medias"><?php echo $this->lang->line('title_medias');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mediasCreate/'.$categories_id)?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php //pre_affiche($medias);?>
	<?php if (isset($medias) && $medias) : ?>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
	<?php endif;?>
	<p class="ajax_notice notice_closable"></p>
	<ul id="sortable_medias">
		<?php foreach($medias as $media):?>
		<li id="listItem_<?php echo $media['medias_id'];?>">
			<div class="img" style="background:url('<?php if($media['file'] && is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file']) && is_readable('./'.$this->config->item('medias_folder').'/images/'.$media['file'])) echo site_url($this->config->item('medias_folder').'/images/x240/'.$media['file']);else echo site_url($this->config->item('medias_folder').'/images/x240/default.jpg');?>') no-repeat center center">
			</div>
			<div class="button">
				<a href="#" class="tooltip handle" title="<?php echo $this->lang->line('btn_move')?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/move.png')?>" alt="<?php echo $this->lang->line('btn_move')?>" width="32px" height="32px"/></a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mediasEdit/'.$categories_id.'/'.$media['medias_id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mediasDelete/'.$media['medias_id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				<?php if ($media['mACTIVE'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/mediasFlag/'.$media['medias_id'].'/'.$media['mACTIVE']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/mediasFlag/'.$media['medias_id'].'/'.$media['mACTIVE']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';endif;?>
			</div>
		</li>
		<?php endforeach;?>
	</ul>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total_medias;?> <?php echo $this->lang->line('text_total_medias');?>
		</div>
	</div>
	<?php else :?>
	<p class="no_data"><?php echo $this->lang->line('text_no_medias');?></p>
	<?php endif;?>
</div>
<!-- [Main] end -->
