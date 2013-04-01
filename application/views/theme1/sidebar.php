<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<ul>
	<?php $counter=1;foreach ($blockCategTree['children'] as $child) :?>
	<?php
	$data['node'] = $child;
	$data['counter'] = $counter;
	$data['count'] = count($blockCategTree['children']);
	$data['counter_tree'] = false;
	$data['count_tree'] = false;
	?>
	<?php $this->load->view($this->system->theme.'/sidebar-tree', $data);?>
	<?php $counter++;endforeach;?>
	<?php if($this->user->liveView):?>
	<li class="create"><a href="<?php echo site_url($this->config->item('admin_folder').'/navigations/createLiveView/create/0');?>" title="<?php echo $this->lang->line('btn_create_btn');?>" data-title="<?php echo $this->lang->line('btn_create_btn');?>" class="dialog tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/create.png');?>" alt="<?php echo $this->lang->line('btn_create_btn');?>" width="16px" height="16px"/></a></li>
	<?php endif;?>
</ul>