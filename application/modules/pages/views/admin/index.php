<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_pages');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/settings')?>"><?php echo $this->lang->line('btn_settings');?></a></li>
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/create/'.$parent_id)?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if($admin_breadcrumb):?>
	<div class="pathway">
		<?php echo anchor($this->config->item('admin_folder').'/'.$module.'/index', $this->lang->line('text_home'));?>
		<?php if ($admin_breadcrumb): $count =  count($admin_breadcrumb); ?>
		<?php for($i = $count-1; $i >= 0; $i--) : ?>
		<?php if($i == 0):?>
		&gt; <?php echo $admin_breadcrumb[$i]['title'];?>
		<?php else :?>
		&gt; <?php echo anchor($this->config->item('admin_folder').'/'.$module.'/index/'.$admin_breadcrumb[$i]['id'], $admin_breadcrumb[$i]['title']);?>
		<?php endif;?>
		<?php endfor;?>
		<?php endif;?>
	</div>
	<?php endif;?>
	<?php if(isset($pages) && $pages) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers:{3:{sorter: false}},
		});
		$("#sortable").sortable({
			update : function () {
				order = [];
				$('tbody').children('tr').each(function(idx, elm) {
				  order.push(elm.id.split('_')[1])
				});
				$.post("<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/sortOrder');?>", {tokencsrf: CSRF, items: order}, function(data) {
					$('.ajax_notice').fadeTo(0, 200);
					$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
					$(".notice_closable").append('<a href="#" class="notice_close"><?php echo $this->lang->line('btn_close')?></a>');
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
	<p class="ajax_notice notice_closable"></p>
	<table class="table_list" id="table_sort">
		<thead>
			<tr>
				<th width="3%" class="center">#</th>
				<th width="30%"><?php echo $this->lang->line('td_title')?></th>
				<th width="20%"><?php echo $this->lang->line('td_uri')?></th>
				<th width="25%" colspan="5"><?php echo $this->lang->line('td_action')?></th>
			</tr>
		</thead>
		<tbody id="sortable">
			<?php $i = 1;$count_pages = count($pages);foreach($pages as $page):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
			<tr class="<?php echo $rowClass?>" id="items_<?php echo $page['id'];?>">
				<td class="center"><?php echo ($i*$start)+$i?></td>
				<td>
					<?php if($page['children'] > 0): ?>
					<span class="lw0_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv0.gif')?>" alt=""/></span>
					<span class="lw0"><?php echo anchor($this->config->item('admin_folder').'/'.$module.'/index/'.$page['id'].'/0', html_entity_decode($page['title']));?> [<?php echo anchor($this->config->item('admin_folder').'/'.$module.'/index/'.$page['id'].'/0', "+".$page['children']) ?>]
					<?php else: ?></span>
					<span class="lw1_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv1.gif')?>" alt=""/></span>
					<span class="lw1"><?php echo html_entity_decode($page['title']);?></span>
					<?php endif;?>
				</td>
				<td><?php echo $page['uri']?></td>
				<td class="center">
					<?php if($i != '1'):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/move/'.$page['id'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
					<?php if(($count_pages) != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/move/'.$page['id'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if ($page['active'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/flag/'.$page['id'].'/'.$page['active']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/flag/'.$page['id'].'/'.$page['active']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';
					endif;?>
				</td>
				<td class="center">
					<a href="<?php echo site_url($page['uri'].$this->config->item('url_suffix_ext'))?>" title="<?php echo $this->lang->line('btn_fullscreen');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/fullscreen.png')?>" alt="<?php echo $this->lang->line('btn_fullscreen');?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/edit/'.$page['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/delete/'.$page['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++;endforeach;?>
		</tbody>
	</table>
	<?php if(isset($pager) && $pager):?>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total;?> <?php echo $this->lang->line('text_total_pages');?>
		</div>
		<div class="pager_right">
			<?php echo $pager?>
		</div>
	</div>
	<?php endif;?>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_pages');?></p>
	<?php endif;?>
</div>
<!-- [Main] end -->