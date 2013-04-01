<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_settings');?></h2>
	<?php echo form_open(site_url($this->config->item('admin_folder').'/'.$module.'/settings'));?>	
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'));?>" class="last"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<fieldset>
			<label for="page_publish_feed"><?php echo $this->lang->line('label_publish_feed');?></label>
			<select name="settings[page_publish_feed]" id="page_publish_feed" class="input_select">
				<option value="1" <?php echo ((isset($this->catalog->settings['page_publish_feed']) && $this->catalog->settings['page_publish_feed'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->catalog->settings['page_publish_feed']) && $this->catalog->settings['page_publish_feed'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="per_page"><?php echo $this->lang->line('label_per_page');?></label>
			<input type="text" name="settings[per_page]" id="per_page" value="<?php echo isset($this->catalog->settings['per_page']) ? $this->catalog->settings['per_page'] : 10;?>" class="input_text"/>
			<label for="display_tax"><?php echo $this->lang->line('label_display_tax');?></label>
			<select name="settings[display_tax]" id="display_tax" class="input_select">
				<option value="1" <?php echo ((isset($this->catalog->settings['display_tax']) && $this->catalog->settings['display_tax'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->catalog->settings['display_tax']) && $this->catalog->settings['display_tax'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="display_tax_prefix"><?php echo $this->lang->line('label_display_tax_prefix');?></label>
			<select name="settings[display_tax_prefix]" id="display_tax_prefix" class="input_select">
				<option value="1" <?php echo ((isset($this->catalog->settings['display_tax_prefix']) && $this->catalog->settings['display_tax_prefix'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->catalog->settings['display_tax_prefix']) && $this->catalog->settings['display_tax_prefix'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->