<?php if(!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php if($this->user->liveView):?>
<div class="box_live_view">
	<span><a href="<?php echo site_url($module.'/view/'.$new['id'].'/'.$new['uri'].$this->config->item('url_suffix_ext'));?>"><?php echo $new['title'];?></a></span>
	<ul>
		<li>
			<?php if ($new['active'] == 1) :?>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/flagLiveView/'.$new['id'].'/'.$new['active']);?>" title="<?php echo $this->lang->line('btn_desactivate_news');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/status_green.png').'" alt="'.$this->lang->line('btn_desactivate');?>"/></a>
			<?php else :?>
			<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/flagLiveView/'.$new['id'].'/'.$new['active']);?>" title="<?php echo $this->lang->line('btn_activate_news');?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/status_red.png');?>" alt="<?php echo $this->lang->line('btn_activate');?>"/></a>
			<?php endif;?>
		</li>
		<li>
			<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/createLiveView');?>" title="<?php echo $this->lang->line('btn_create_news');?>" data-title="<?php echo $this->lang->line('btn_create_news');?>" class="dialog tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/create.png');?>" alt="<?php echo $this->lang->line('btn_create');?>" width="16px" height="16px"/></a>
		</li>
		<li>
			<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/editLiveView/'.$new['id']);?>" title="<?php echo $this->lang->line('btn_edit_news');?>" data-title="<?php echo $this->lang->line('btn_edit_news');?>" class="dialog tooltip"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/edit.png');?>" alt="<?php echo $this->lang->line('btn_edit');?>" width="16px" height="16px"/></a></li>
		<li>
			<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/deleteLiveView/'.$new['id']);?>" title="<?php echo $this->lang->line('btn_delete_news');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/assets/img/icons/delete.png');?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
		</li>
	</ul>
</div>
<?php endif;?>