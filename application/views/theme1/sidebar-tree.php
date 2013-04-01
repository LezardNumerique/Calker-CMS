<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<li class="<?php if ($counter == $count || ($counter_tree && $count_tree && $counter_tree == $count_tree)) :?> last<?php endif;?><?php if ($node['children']) :?> children<?php endif;?><?php if($this->user->liveView):?> live_view<?php endif;?>">
	<?php
	$uri = $this->system->get_uri($node['uri']);
	if($uri == site_url('#')) $uri = 'javascript:void(0);';
	?>
	<a href="<?php echo $uri;?>"<?php if(site_url($this->uri->uri_string()) == $uri || $module == $node['module'] || $module == $this->system->get_first_segment_uri($uri) || $this->uri->segment(($this->language->get_uri_language() ? 2 : 1)) == $this->system->get_first_segment_uri($uri)):?> class="active"<?php endif;?>><?php echo $node['title'];?></a>
	<?php if($this->user->liveView):?>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/editLiveView/'.$node['id']);?>" data-title="<?php echo $this->lang->line('btn_edit_btn');?>" class="dialog edit"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/edit.png');?>" alt="<?php echo $this->lang->line('btn_edit_btn');?>" width="16px" height="16px"/></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/deleteLiveView/'.$node['id']);?>" data-title="<?php echo $this->lang->line('btn_delete');?>" class="delete" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/delete.png');?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
	<?php endif;?>
	<?php if ($node['children']):?>
		<ul>
		<?php $counter_tree=1;foreach($node['children'] as $child):?>
		<?php
		$data['node'] = $child;
		$data['counter_tree'] = $counter_tree;
		$data['count_tree'] = count($node['children']);
		?>
		<?php $this->load->view($this->system->theme.'/sidebar-tree', $data);?>
		<?php $counter_tree++;endforeach;?>
		</ul>
	<?php endif;?>
</li>