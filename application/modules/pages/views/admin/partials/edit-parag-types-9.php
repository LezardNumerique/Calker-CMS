<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript">
$(document).ready(function() {
	$("input.input_file").filestyle({
		image: BASE_URI+APPPATH+"views/"+ADMIN_THEME+"/img/upload_file.gif",
		imageheight : 40,
		imagewidth : 78,
		width : 240
	});
    $("#box_images_slider").sortable({
      		handle : '.handle',
      		update : function () {
				var order = $('#box_images_slider').sortable('serialize');		
				$.ajax({
					type: "POST",
					url: "<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/ajaxSortOrderMedia');?>",
					processData: false,
					data: order
					   
				});
				$(".ajax_notice").html('<p class="notice">OKI</p>');
			}
    	});
	});
</script>
<div id="tabs">
	<ul>
		<li><a href="#one"><?php echo $this->lang->line('menu_upload');?></a></li>
		<li><a href="#two"><?php echo $this->lang->line('menu_images');?></a></li>		
	</ul>
	<fieldset>
		<div id="one">						
			<input type="file" name="media" id="media" class="input_file"/>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
			<span class="notice"><?php echo $this->lang->line('notice_image_format');?>, Zip</span>			
		</div>
		<div id="two">
			<?php if($medias = $this->paragraph->list_medias(array('order_by' => 'ordering ASC','where' => array('src_id' => $paragraph['id'], 'module' => $module)))) :?>			
			<ul id="box_images_slider">
			<?php foreach($medias as $media):?>
			<?php if(isset($media) && is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file'])) :?>
			<li id="listItem_<?php echo $media['id'];?>">
				<img src="<?php echo site_url($this->config->item('medias_folder').'/images/x100/'.$media['file']);?>" alt="<?php if(isset($media['options']['alt'])) echo $media['options']['alt']?>"/>
				<br /><br />
				<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/deleteParagMedia/'.$paragraph['src_id'].'/'.$paragraph['id'].'/'.$media['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
				<a href="" class="handle">DEPLACER</a>
			</li>
			<?php endif;?>
			<?php endforeach;?>
			</ul>
			<?php endif;?>
		</div>		
	</fieldset>
</div>