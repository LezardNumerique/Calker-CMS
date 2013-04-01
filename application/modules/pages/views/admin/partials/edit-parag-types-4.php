<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript">
$(document).ready(function() {
	<?php if(!isset($media['options']['link'])):?>$("div#target_block").hide();<?php endif;?>
	$("input#link").click(function() {
		$("input#popup").attr('checked', false);
		$("div#target_block").show();
	});
	$("input#popup").click(function() {
		$("input#link").val('');
		$("div#target_block").hide();
	});
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
	<?php if(isset($media) && is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file'])) :?>
	<div id="media">
		<img src="<?php echo site_url('media/images/x100/'.$media['file']);?>" alt="<?php if(isset($media['options']['alt'])) echo $media['options']['alt']?>"/>
	</div>
	<?php endif;?>
	<div id="one">
		<label for="title"><?php echo $this->lang->line('label_title')?></label>
		<input name="title" id="title" class="input_text" value="<?php echo $paragraph['title']?>"/>
		<label for="image"><?php echo $this->lang->line('label_image')?></label>
		<input type="file" name="image" id="image" class="input_file"/>
		<span class="notice"><?php echo $this->lang->line('notice_image_format');?></span>
		<label for="class"><?php echo $this->lang->line('label_class')?></label>
		<input name="class" id="class" class="input_text" value="<?php echo $paragraph['class']?>"/>
	</div>
	<div id="two">
		<label for="position_1"><?php echo $this->lang->line('label_position_left')?></label>
		<input type="radio" name="options[position]" id="position_1" class="input_radio" value="left" <?php if(isset($media['options']['position']) && $media['options']['position'] == 'left' ) echo 'checked="checked"';else echo 'checked="checked"';?>/>
		<label for="position_2"><?php echo $this->lang->line('label_position_right')?></label>
		<input type="radio" name="options[position]" id="position_2" class="input_radio" value="right" <?php if(isset($media['options']['position']) && $media['options']['position'] == 'right' ) echo 'checked="checked"';?>/>
		<label for="position_3"><?php echo $this->lang->line('label_position_center')?></label>
		<input type="radio" name="options[position]" id="position_3" class="input_radio" value="right" <?php if(isset($media['options']['position']) && $media['options']['position'] == 'center' ) echo 'checked="checked"';?>/>
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
		<label for="legend"><?php echo $this->lang->line('label_legend')?></label>
		<input type="text" name="options[legend]" id="legend" class="input_text" value="<?php if(isset($media['options']['legend'])) echo $media['options']['legend'];if($post['options']['legend']) echo $media['options']['legend'];?>"/>
		<label for="alt"><?php echo $this->lang->line('label_text_alt')?></label>
		<input type="text" name="options[alt]" id="alt" class="input_text" value="<?php if(isset($media['options']['alt'])) echo $media['options']['alt'];if($post['options']['alt']) echo $media['options']['alt'];?>"/>
		<hr/>
		<label for="popup"><?php echo $this->lang->line('label_popup')?></label>
		<input type="checkbox" name="options[popup]" id="popup" class="input_checkbox" value="1" <?php if(isset($media['options']['popup']) && $media['options']['popup'] == 1) echo 'checked="checked"';?>/>
		<hr/>
		<label for="link"><?php echo $this->lang->line('label_link')?></label>
		<input type="text" name="options[link]" id="link" class="input_text" value="<?php if(isset($media['options']['link'])) echo $media['options']['link'];if($post['options']['link']) echo $media['options']['link'];?>"/>
		<div id="target_block">
			<label for="target"><?php echo $this->lang->line('label_target')?></label>
			<select name="options[target]" id="target" class="input_select">
				<option value="0"<?php if(isset($media['options']['target']) && $media['options']['target'] == 0) echo ' selected="selected"';if($post['options']['target'] == 0) echo ' selected="selected';?>><?php echo $this->lang->line('option_same_page')?></option>
				<option value="1"<?php if(isset($media['options']['target']) && $media['options']['target'] == 1) echo ' selected="selected"';if($post['options']['target'] == 1) echo ' selected="selected';?>><?php echo $this->lang->line('option_new_page')?></option>
			</select>
		</div>
	</div>
</fieldset>
</div>