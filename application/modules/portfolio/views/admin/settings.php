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
			<label for="publish_feed_portfolio"><?php echo $this->lang->line('label_publish_feed');?></label>
			<select name="settings[publish_feed_portfolio]" id="publish_feed_portfolio" class="input_select">
				<option value="1" <?php echo ((isset($this->portfolio->settings['publish_feed_portfolio']) && $this->portfolio->settings['publish_feed_portfolio'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->portfolio->settings['publish_feed_portfolio']) && $this->portfolio->settings['publish_feed_portfolio'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="box_portfolio"><?php echo $this->lang->line('label_box_portfolio');?></label>
			<select name="settings[box_portfolio]" id="box_portfolio" class="input_select">
				<option value="1" <?php echo ((isset($this->portfolio->settings['box_portfolio']) && $this->portfolio->settings['box_portfolio'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->portfolio->settings['box_portfolio']) && $this->portfolio->settings['box_portfolio'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="per_portfolio_categories"><?php echo $this->lang->line('label_per_portfolio_categories');?></label>
			<input type="text" name="settings[per_portfolio_categories]" id="per_portfolio" value="<?php echo isset($this->portfolio->settings['per_portfolio_categories']) ? $this->portfolio->settings['per_portfolio_categories'] : 20;?>" class="input_text"/>
			<label for="per_portfolio_medias"><?php echo $this->lang->line('label_per_portfolio_medias');?></label>
			<input type="text" name="settings[per_portfolio_medias]" id="per_portfolio_medias" value="<?php echo isset($this->portfolio->settings['per_portfolio_medias']) ? $this->portfolio->settings['per_portfolio_medias'] : 20;?>" class="input_text"/>
			<label for="substr_body_portfolio"><?php echo $this->lang->line('label_substr_body_portfolio');?></label>
			<input type="text" name="settings[substr_body_portfolio]" id="substr_body_portfolio" value="<?php echo isset($this->portfolio->settings['substr_body_portfolio']) ? $this->portfolio->settings['substr_body_portfolio'] : 100;?>" class="input_text"/>
			<?php if(isset($medias_types) && $medias_types):?>
			<label for="img_sizes_types_big_portfolio"><?php echo $this->lang->line('label_img_sizes_types_big_portfolio');?></label>
			<select id="img_sizes_types_big_portfolio" name="settings[img_sizes_types_big_portfolio]" class="input_select">
				<?php foreach($medias_types as $media_type):?>
				<option value="<?php echo $media_type['key'];?>" <?php if($this->portfolio->settings['img_sizes_types_big_portfolio'] == $media_type['key']):?>selected="selected"<?php endif;?>><?php echo $media_type['name'];?> <?php echo $media_type['width'];?>x<?php echo $media_type['height'];?>px (<?php echo ucfirst($media_type['module']);?>)</option>
				<?php endforeach;?>
			</select>
			<?php endif;?>
			<?php if(isset($medias_types) && $medias_types):?>
			<label for="img_sizes_types_little_portfolio"><?php echo $this->lang->line('label_img_sizes_types_little_portfolio');?></label>
			<select id="img_sizes_types_little_portfolio" name="settings[img_sizes_types_little_portfolio]" class="input_select">
				<?php foreach($medias_types as $media_type):?>
				<option value="<?php echo $media_type['key'];?>" <?php if($this->portfolio->settings['img_sizes_types_little_portfolio'] == $media_type['key']):?>selected="selected"<?php endif;?>><?php echo $media_type['name'];?> <?php echo $media_type['width'];?>x<?php echo $media_type['height'];?>px (<?php echo ucfirst($media_type['module']);?>)</option>
				<?php endforeach;?>
			</select>
			<?php endif;?>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->