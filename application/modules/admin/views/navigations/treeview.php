<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_navigation');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations')?>"><?php echo $this->lang->line('btn_classic_view');?></a></li>
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/create/1')?>"><?php echo $this->lang->line('btn_create');?></a></li>
	</ul>
	<?php if ($notice = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable"><?php echo $notice;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if(isset($navigations) && $navigations) : ?>
	<table class="table_list">
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th width="30%"><?php echo $this->lang->line('td_title');?></th>
				<th width="30%"><?php echo $this->lang->line('td_uri');?></th>
				<th width="35%" colspan="4"><?php echo $this->lang->line('td_action');?></th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;$count_navigation = count($navigations);?>
			<?php foreach ($navigations as $navigation): ?>
			<?php if ($i % 2 != 0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;?>
			<tr class="<?php echo $rowClass?>"  id="items_<?php echo $navigation['id'];?>">
				<td class="center"><?php echo $i?></td>
				<td>
					<?php if ($navigation['level'] == 0) :?>
					<span class="lv0_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv0.gif')?>" alt=""/></span>
					<span class="lv0"><?php echo html_entity_decode($navigation['title']);?></span>
					<?php elseif ($navigation['level'] == 1) :?>
					<span class="lv1_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv1.gif')?>" alt=""/></span>
					<span class="lv1"><?php echo html_entity_decode($navigation['title']);?></span>
					<?php elseif ($navigation['level'] == 2) :?>
					<span class="lv2_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv2.gif')?>" alt=""/></span>
					<span class="lv2"><?php echo html_entity_decode($navigation['title']);?></span>
					<?php elseif ($navigation['level'] == 3) :?>
					<span class="lv3_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv3.gif')?>" alt=""/></span>
					<span class="lv3"><?php echo html_entity_decode($navigation['title']);?></span>
					<?php endif;?>
				</td>
				<td><?php echo $navigation['uri']?></td>
				<td class="center">
					<?php if ($navigation['active'] == 1): echo '<a href="'.site_url($this->config->item('admin_folder').'/navigations/flag/'.$navigation['id'].'/'.$navigation['active']).'" title="'.$this->lang->line('btn_desactivate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate').'"  width="16px" height="16px"/></a>'; else: echo '<a href="'.site_url($this->config->item('admin_folder').'/navigations/flag/'.$navigation['id'].'/'.$navigation['active']).'" title="'.$this->lang->line('btn_activate').'" class="tooltip"><img src="'.site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/status_red.png').'" alt="'.$this->lang->line('btn_activate').'"  width="16px" height="16px"/></a>';endif;?>
				</td>
				<td class="center">
					<?php if($i != '1'):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/move/'. $navigation['id'].'/up')?>" title="<?php echo $this->lang->line('btn_sort_ascending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_ascending.png');?>" width="16px" height="16px" alt="<?php echo $this->lang->line('btn_sort_ascending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4px" height="16px" alt=""/>
					<?php endif;?>
					<?php if(($count_navigation) != $i):?>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/move/'. $navigation['id'].'/down')?>" title="<?php echo $this->lang->line('btn_sort_descending');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/sort_descending.png')?>" width="16" height="16" alt="<?php echo $this->lang->line('btn_sort_descending');?>"/></a><?php else :?>&nbsp;&nbsp;&nbsp;<img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/blank.gif');?>" width="4px" height="16px" alt="" />
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
	<?php endif; ?>
</div>
<!-- [Main] end -->
