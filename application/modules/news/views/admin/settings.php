<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_settings');?></h2>
	<?php echo form_open($this->config->item('admin_folder').'/'.$module.'/settings');?>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'));?>" class="last"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<fieldset>
			<label for="publish_feed_news"><?php echo $this->lang->line('label_publish_feed');?></label>
			<select name="settings[publish_feed_news]" id="publish_feed_news" class="input_select">
				<option value="1" <?php echo ((isset($this->news->settings['publish_feed_news']) && $this->news->settings['publish_feed_news'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->news->settings['publish_feed_news']) && $this->news->settings['publish_feed_news'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="box_news"><?php echo $this->lang->line('label_box_news');?></label>
			<select name="settings[box_news]" id="box_news" class="input_select">
				<option value="1" <?php echo ((isset($this->news->settings['box_news']) && $this->news->settings['box_news'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->news->settings['box_news']) && $this->news->settings['box_news'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="per_page_news"><?php echo $this->lang->line('label_per_page');?></label>
			<input type="text" name="settings[per_page_news]" id="per_page_news" value="<?php echo isset($this->news->settings['per_page_news']) ? $this->news->settings['per_page_news'] : 20;?>" class="input_text"/>
			<label for="substr_home_news"><?php echo $this->lang->line('label_substr_home_news');?></label>
			<input type="text" name="settings[substr_home_news]" id="substr_home_news" value="<?php echo isset($this->news->settings['substr_home_news']) ? $this->news->settings['substr_home_news'] : 400;?>" class="input_text"/>
			<label for="substr_listing_news"><?php echo $this->lang->line('label_substr_listing_news');?></label>
			<input type="text" name="settings[substr_listing_news]" id="substr_listing_news" value="<?php echo isset($this->news->settings['substr_listing_news']) ? $this->news->settings['substr_listing_news'] : 1000;?>" class="input_text"/>			
			<?php if(isset($medias_types) && $medias_types):?>
			<label for="img_sizes_types_list_news"><?php echo $this->lang->line('label_img_sizes_types_list_news');?></label>
			<select id="img_sizes_types_list_news" name="settings[img_sizes_types_list_news]" class="input_select">
				<?php foreach($medias_types as $media_type):?>
				<option value="<?php echo $media_type['key'];?>" <?php if($this->news->settings['img_sizes_types_list_news'] == $media_type['key']):?>selected="selected"<?php endif;?>><?php echo $media_type['name'];?> <?php echo $media_type['width'];?>x<?php echo $media_type['height'];?>px (<?php echo ucfirst($media_type['module']);?>)</option>
				<?php endforeach;?>
			</select>
			<?php endif;?>
			<?php if(isset($medias_types) && $medias_types):?>
			<label for="img_sizes_types_view_news"><?php echo $this->lang->line('label_img_sizes_types_view_news');?></label>
			<select id="img_sizes_types_view_news" name="settings[img_sizes_types_view_news]" class="input_select">
				<?php foreach($medias_types as $media_type):?>
				<option value="<?php echo $media_type['key'];?>" <?php if($this->news->settings['img_sizes_types_view_news'] == $media_type['key']):?>selected="selected"<?php endif;?>><?php echo $media_type['name'];?> <?php echo $media_type['width'];?>x<?php echo $media_type['height'];?>px (<?php echo ucfirst($media_type['module']);?>)</option>
				<?php endforeach;?>
			</select>
			<?php endif;?>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->