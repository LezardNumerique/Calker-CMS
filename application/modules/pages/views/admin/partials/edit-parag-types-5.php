<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript">
$(document).ready(function() {
	$("input.input_file").filestyle({
		image: BASE_URI+APPPATH+"views/"+ADMIN_THEME+"/img/upload_file.gif",
		imageheight : 40,
		imagewidth : 78,
		width : 240
	});
});
</script>
<div id="tabs">
	<ul>
		<li><a href="#one"><?php echo $this->lang->line('menu_content');?></a></li>
		<li><a href="#two"><?php echo $this->lang->line('menu_position');?></a></li>
	</ul>
	<fieldset>
		<div id="one">
			<label for="title"><?php echo $this->lang->line('label_title')?></label>
			<input name="title" id="title" class="input_text" value="<?php if($post['title']) echo $post['title'];else echo html_entity_decode($paragraph['title'])?>" maxlength="64"/>
			<label for="class"><?php echo $this->lang->line('label_class')?></label>
			<input name="class" id="class" class="input_text" value="<?php if($post['class']) echo $post['class'];else echo $paragraph['class']?>" maxlength="32"/>
			<label for="swf"><?php echo $this->lang->line('label_swf')?></label>
			<input type="file" name="media" id="media" class="input_file"/>
			<?php if(!$paragraph['id']):?><span class="required"><?php echo $this->lang->line('text_required');?></span><?php endif;?>
			<span class="notice"><?php echo $this->lang->line('notice_swf_format');?></span>
			<?php if(isset($media) && is_file('./'.$this->config->item('medias_folder').'/swf/'.$media['file'])) :?>
			<?php $file = './'.$this->config->item('medias_folder').'/swf/'.$media['file'];?>
			<?php $width = get_media_size($file, 0);$height = get_media_size($file, 1);?>
			<script type="text/javascript">
			swfobject.embedSWF("<?php echo site_url($this->config->item('medias_folder').'/swf/'.$media['file']);?>", "box_swf", "<?php echo $width;?>", "<?php echo $height;?>", "9.0.0", "<?php echo site_url($this->config->item('medias_folder').'/swf/expressInstall.swf');?>");
			</script>
			<br class="clear"/>
			<div id="box_swf">
				<a href="http://www.adobe.com/go/getflashplayer" title="<?php echo $this->lang->line('text_get_adobe_flash_player');?>">
					<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="<?php echo $this->lang->line('text_get_adobe_flash_player');?>"/>
				</a>
			</div>
			<?php endif;?>
		</div>
		<div id="two">
			<label for="position_1"><?php echo $this->lang->line('label_position_left')?></label>
			<input type="radio" name="options[position]" id="position_1" class="input_radio" value="left" <?php if(isset($media['options']['position']) && $media['options']['position'] == 'left') echo 'checked="checked"';if($post['options']['position'] == 'left') echo 'checked="checked"';else echo 'checked="checked"';?>/>
			<label for="position_2"><?php echo $this->lang->line('label_position_center')?></label>
			<input type="radio" name="options[position]" id="position_2" class="input_radio" value="center" <?php if(isset($media['options']['position']) && $media['options']['position'] == 'center') echo 'checked="checked"';if($post['options']['position'] == 'center') echo 'checked="checked"';?>/>
			<label for="position_3"><?php echo $this->lang->line('label_position_right')?></label>
			<input type="radio" name="options[position]" id="position_3" class="input_radio" value="right" <?php if(isset($media['options']['position']) && $media['options']['position'] == 'right') echo 'checked="checked"';if($post['options']['position'] == 'right') echo 'checked="checked"';?>/>
		</div>
	</fieldset>
</div>