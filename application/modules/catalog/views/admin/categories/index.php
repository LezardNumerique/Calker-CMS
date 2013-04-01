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
	<?php if (isset($categories) && $categories) : ?>
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
				order = [];
				$('tbody').children('tr').each(function(idx, elm) {
				  order.push(elm.id.split('_')[1])
				});
				$.post("<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesSortOrder/'.$categories_id);?>", {tokencsrf: CSRF, items: order}, function(data) {
					$('.ajax_notice').fadeTo(0, 200);
					$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
					$(".notice_closable").append('<a href="#" class="notice_close"><?php echo $this->lang->line('btn_delete');?></a>');					
				});
			}
		});
	});
	</script>
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
					<?php if($i != '1'):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesMove/'.$categorie['id'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
					<?php if(($count_categories) != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesMove/'.$categorie['id'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
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
	<h2 id="products"><?php echo $this->lang->line('title_products');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsCreate/'.$categories_id)?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if (isset($products) && $products) : ?>
	<script type="text/javascript">
	$(function() {
		$("#table_sort_products").tablesorter({
			headers: {3:{sorter: false}},
		});
		$("#sortable_products").sortable({
			update : function () {
				order = [];
				$('tbody').children('tr').each(function(idx, elm) {
				  order.push(elm.id.split('_')[1])
				});
				$.post("<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsSortOrder/'.$categories_id);?>", {tokencsrf: CSRF, items: order}, function(data) {
					$('.ajax_notice').fadeTo(0, 200);
					$('.ajax_notice').html('<?php echo $this->lang->line('notification_save');?>');
					$(".notice_closable").append('<a href="#" class="notice_close">Fermer</a>');					
				});
			}
		});
	});
	</script>
	<table class="table_list" id="table_sort_products">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="35%"><?php echo $this->lang->line('td_products');?></th>
				<th width="35%"><?php echo $this->lang->line('td_uri');?></th>
				<th width="25%" colspan="4" class="last"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody id="sortable_products">
			<?php $i=1;$count_products = count($products);foreach($products as $product):?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>" id="items_<?php echo $product['id'];?>">
				<td class="center"><?php echo $i?></td>
				<td><?php echo html_entity_decode($product['title']);?></td>
				<td><?php echo $product['uri'];?></td>
				<td class="center">
					<?php if ($product['active'] == '1'): echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/productsFlag/'.$product['id'].'/'.$product['active']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('admin_folder').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/'.$module.'/productsFlag/'.$product['id'].'/'.$product['active']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/admin/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"/></a>';endif;?>
				</td>
				<td class="center">
					<?php if($i != '1'):?><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsMove/'.$product['id'].'/'.$categories_id.'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
					<?php if($count_products != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsMove/'.$product['id'].'/'.$categories_id.'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/>
					<?php endif;?>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsEdit/'.$categories_id.'/'.$product['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
				</td>
				<td class="center">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/productsDelete/'.$product['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				</td>
			</tr>
			<?php $i++;endforeach;?>
		</tbody>
	</table>
	<div class="pager">
		<div class="pager_left">
			<?php echo $total_products;?> <?php echo $this->lang->line('text_total_products');?>
		</div>
	</div>
	<?php else :?>
	<p class="no_data"><?php echo $this->lang->line('text_no_products');?></p>
	<?php endif;?>
</div>
<!-- [Main] end -->