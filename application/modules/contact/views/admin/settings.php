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
			<label for="per_page_contact"><?php echo $this->lang->line('label_per_page');?></label>
			<input type="text" name="settings[per_page_contact]" id="per_page_contact" value="<?php echo isset($this->settings['per_page_contact']) ? $this->settings['per_page_contact'] : 10;?>" class="input_text"/>
			<label for="active_field_firstname"><?php echo $this->lang->line('label_active_field_firstname');?></label>
			<select id="active_field_firstname" name="settings[active_field_firstname]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_field_firstname']) && $this->settings['active_field_firstname'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_field_firstname']) && $this->settings['active_field_firstname'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="active_field_lastname"><?php echo $this->lang->line('label_active_field_lastname');?></label>
			<select id="active_field_lastname" name="settings[active_field_lastname]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_field_lastname']) && $this->settings['active_field_lastname'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_field_lastname']) && $this->settings['active_field_lastname'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="active_field_phone"><?php echo $this->lang->line('label_active_field_phone');?></label>
			<select id="active_field_phone" name="settings[active_field_phone]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_field_phone']) && $this->settings['active_field_phone'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_field_phone']) && $this->settings['active_field_phone'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="active_field_message"><?php echo $this->lang->line('label_active_field_message');?></label>
			<select id="active_field_message" name="settings[active_field_message]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_field_message']) && $this->settings['active_field_message'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_field_message']) && $this->settings['active_field_message'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="form"><?php echo $this->lang->line('label_form');?></label>
			<select id="form" name="settings[active_form]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_form']) && $this->settings['active_form'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_form']) && $this->settings['active_form'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="map"><?php echo $this->lang->line('label_map');?></label>
			<select id="map" name="settings[active_map]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_map']) && $this->settings['active_map'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_map']) && $this->settings['active_map'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="active_coord"><?php echo $this->lang->line('label_active_coord');?></label>
			<select id="active_coord" name="settings[active_coord]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_coord']) && $this->settings['active_coord'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_coord']) && $this->settings['active_coord'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="active_qrcode"><?php echo $this->lang->line('label_active_qrcode');?></label>
			<select id="active_qrcode" name="settings[active_qrcode]" class="input_select">
				<option value="1"<?php if(isset($this->settings['active_qrcode']) && $this->settings['active_qrcode'] == 1):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0"<?php if(isset($this->settings['active_qrcode']) && $this->settings['active_qrcode'] == 0):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('option_no');?></option>
			</select>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->