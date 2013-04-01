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
		<li><a href="#three"><?php echo $this->lang->line('menu_ratio');?></a></li>
		<li><a href="#four"><?php echo $this->lang->line('menu_options');?></a></li>
	</ul>
	<fieldset>
		<div id="one">
			<label for="title"><?php echo $this->lang->line('label_title')?></label>
			<input name="title" id="title" class="input_text" value="<?php if($post['title']) echo $post['title'];else echo html_entity_decode($paragraph['title'])?>" maxlength="64"/>
			<label for="class"><?php echo $this->lang->line('label_class')?></label>
			<input name="class" id="class" class="input_text" value="<?php if($post['class']) echo $post['class'];else echo $paragraph['class']?>" maxlength="32"/>
			<label for="media"><?php echo $this->lang->line('label_videos')?></label>
			<input type="file" name="media" id="media" class="input_file"/>
			<?php if(!$paragraph['id']):?><span class="required"><?php echo $this->lang->line('text_required');?></span><?php endif;?>
			<span class="notice"><?php echo $this->lang->line('notice_videos_format');?> | <?php echo $this->lang->line('notice_post_max_size');?> <?php echo ini_get('upload_max_filesize');?></span>
			<?php if(isset($media) && is_file('./'.$this->config->item('medias_folder').'/videos/'.$media['file'])) :?>
			<br class="clear"/>
			<a href="<?php echo site_url($this->config->item('medias_folder').'/videos/'.$media['file']);?>" class="popin_video"><?php echo $this->lang->line('text_play_video');?></a>
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
		<div id="three">
			<label for="ratio_1"><?php echo $this->lang->line('label_100')?></label>
			<input type="radio" name="options[ratio]" id="ratio_1" class="input_radio" value="1" <?php if(isset($media['options']['ratio']) && $media['options']['ratio'] == 1 ) echo 'checked="checked"';if($post['options']['ratio'] == 1) echo 'checked="checked"';else echo 'checked="checked"';?>/>
			<label for="ratio_2"><?php echo $this->lang->line('label_1/2')?></label>
			<input type="radio" name="options[ratio]" id="ratio_2" class="input_radio" value="0.50" <?php if(isset($media['options']['ratio']) && $media['options']['ratio'] == 0.50 ) echo 'checked="checked"';if($post['options']['ratio'] == 0.50) echo 'checked="checked"';?>/>
			<label for="ratio_3"><?php echo $this->lang->line('label_1/4');?></label>
			<input type="radio" name="options[ratio]" id="ratio_3" class="input_radio" value="0.25" <?php if(isset($media['options']['ratio']) && $media['options']['ratio'] == 0.25 ) echo 'checked="checked"';if($post['options']['ratio'] == 0.25) echo 'checked="checked"';?>/>
		</div>
		<div id="four">
		<label for="autostart"><?php echo $this->lang->line('label_autostart')?></label>
		<select name="options[autostart]" id="autostart" class="input_select">
			<option value="0"<?php if(isset($media['options']['autostart']) && $media['options']['autostart'] == 0) echo ' selected="selected"';if(isset($post['options']['autostart']) && $post['options']['autostart'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_no')?></option>
			<option value="1"<?php if(isset($media['options']['autostart']) && $media['options']['autostart'] == 1) echo ' selected="selected"';if(isset($post['options']['autostart']) && $post['options']['autostart'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_yes')?></option>
		</select>
		<label for="fullscreen"><?php echo $this->lang->line('label_fullscreen')?></label>
		<select name="options[fullscreen]" id="fullscreen" class="input_select">
			<option value="1"<?php if(isset($media['options']['fullscreen']) && $media['options']['fullscreen'] == 1) echo ' selected="selected"';if(isset($post['options']['fullscreen'])  && $post['options']['fullscreen'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_yes')?></option>
			<option value="0"<?php if(isset($media['options']['fullscreen']) && $media['options']['fullscreen'] == 0) echo ' selected="selected"';if(isset($post['options']['fullscreen']) && $post['options']['fullscreen'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_no')?></option>
		</select>
	</div>
	</fieldset>
</div>
