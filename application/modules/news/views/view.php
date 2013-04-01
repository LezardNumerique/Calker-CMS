<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if($this->user->liveView):?>
<script src="<?php echo site_url(APPPATH.'views/assets/js/live_view.js?v='.mktime());?>" type="text/javascript"></script>
<?php endif;?>
<div id="dialog"></div>
<div id="new">
	<?php if (isset($new) && $new) :?>
	<?php $data['new'] = $new;?>
	<h1><?php echo $new['title'];?></h1>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<?php if($alerte = $this->session->flashdata('alerte')):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php $this->load->view('partials/live-view', $data);?>
	<div class="parag">
		<div class="img">
			<?php if(isset($media) && $media && is_readable('./'.$this->config->item('medias_folder').'/images/'.$media['file']) && is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file'])):?>
			<img src="<?php echo site_url($this->config->item('medias_folder').'/images/'.$images_sizes['width'].'x'.$images_sizes['height'].'/'.$media['file']);?>" alt="<?php echo $new['title'];?>" width="<?php echo $images_sizes['width'];?>" height="<?php echo $images_sizes['height'];?>"/>
			<?php else :?>
			<img src="<?php echo site_url($this->config->item('medias_folder').'/images/'.$images_sizes['width'].'x'.$images_sizes['height'].'/default.jpg');?>" alt="<?php echo $new['title'];?>" width="<?php echo $images_sizes['width'];?>" height="<?php echo $images_sizes['height'];?>"/>
			<?php endif;?>
		</div>
		<div class="body">
			<h2 class="date"><?php echo date ('l d F Y', $new['date_added']);?></h2>
			<?php echo $new['body'];?>
			<p class="return"><a href="<?php echo site_url($this->session->userdata('redirect_uri_front'));?>"><?php echo $this->lang->line('btn_return');?></a></p>
		</div>
	</div>
	<?php else : ?>
	<p class="no_data"><?php echo $this->lang->line('new_not_found');?></p>
	<?php endif; ?>
</div>
