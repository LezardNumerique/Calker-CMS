<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2 class="navigation"><?php echo $this->lang->line('title_navigation');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/treeview')?>"><?php echo $this->lang->line('btn_treeview');?></a></li>
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/create/'.$parent_id)?>"><?php echo $this->lang->line('btn_create');?></a></li>		
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable"><?php echo $notice;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable"><?php echo $alerte;?></p>
	<?php endif;?>
	<p class="ajax_notice notice_closable"></p>	
	<script type="text/javascript">
	$(function() {
		$("#table_sort").tablesorter({
			headers: {3:{sorter: false}},
		});
		$("#sortable").sortable({
			update : function () {
				order = [];
				$('tbody').children('tr').each(function(idx, elm) {
				  order.push(elm.id.split('_')[1])
				});
				$.post("<?php echo site_url($this->config->item('admin_folder').'/navigations/sortOrder');?>", {tokencsrf: CSRF, items: order}, function(data) {
					$('.ajax_notice').fadeTo(0, 200);
					$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
					$(".notice_closable").append('<a href="#" class="notice_close"><?php echo $this->lang->line('btn_accueil');?></a>');
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
	<?php if(isset($admin_breadcrumb) && $admin_breadcrumb):?>
	<div class="pathway">
		<?php echo anchor($this->config->item('admin_folder').'/navigations/index', $this->lang->line('btn_accueil'));?>
		<?php if (isset($admin_breadcrumb) && $admin_breadcrumb): $count = count($admin_breadcrumb);?>
		<?php for($i = $count-1; $i >=0; $i--):?>
		<?php if($i == 0):?>
		&gt; <?php echo $admin_breadcrumb[$i]['title'];?>
		<?php else :?>
		&gt; <?php echo anchor($this->config->item('admin_folder').'/navigations/index/'.$admin_breadcrumb[$i]['id'], $admin_breadcrumb[$i]['title']);?>
		<?php endif;?>
		<?php endfor;?>
		<?php endif;?>
	</div>
	<?php endif;?>
	<?php if(isset($navigations) && $navigations && is_array($navigations)) : ?>
	<table class="table_list" id="table_sort">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="40%"><?php echo $this->lang->line('td_title');?></th>
				<th width="35%"><?php echo $this->lang->line('td_uri');?></th>
				<th width="20%" colspan="4"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody id="sortable">
			<?php $i = 1;$count_navigation = count($navigations);?>
			<?php foreach ($navigations as $navigation): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>"  id="items_<?php echo $navigation['id'];?>">
				<td class="center"><?php echo $i?></td>
				<td>
					<?php if ($navigation['children'] == 1) :?>
					<span class="lw0_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv0.gif')?>" alt=""/></span>
					<span class="lw0"><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/index/'.$navigation['id']);?>"><?php echo html_entity_decode($navigation['title']);?></a></span>
					<?php else :?>
					<span class="lw1_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv1.gif')?>" alt=""/></span>
					<span class="lw1"><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/index/'.$navigation['id']);?>"><?php echo html_entity_decode($navigation['title']);?></a></span>
					<?php endif;?>
				</td>
				<td><?php echo $navigation['uri']?></td>
				<td class="center">
					<?php if ($navigation['active'] == 1): echo '<a href="'.site_url($this->config->item('admin_folder').'/navigations/flag/'.$navigation['id'].'/'.$navigation['active']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'" width="16px" height="16px"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/navigations/flag/'.$navigation['id'].'/'.$navigation['active']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'" width="16px" height="16px"/></a>';endif;?>
				</td>
				<td class="center">
					<?php if($i != '1'):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/move/'.$navigation['id'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16px" height="16px" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'/views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4px" height="16px" alt=""/>
					<?php endif;?>
					<?php if(($count_navigation) != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/move/'.$navigation['id'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4px" height="16px" alt=""/>
					<?php endif;?>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/edit/'.$navigation['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/delete/'.$navigation['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++;endforeach;?>
		</tbody>
	</table>
	<?php else :?>
	<p class="no_data"><?php echo $this->lang->line('text_no_navigation');?></p>
	<?php endif; ?>
</div>
<!-- [Main] end -->