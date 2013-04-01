<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_settings');?></h2>
	<?php echo form_open('admin/'.$module.'/settings');?>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'));?>" class="last"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<fieldset>
			<label for="page_home"><?php echo $this->lang->line('label_page_home');?></label>
			<input type="text" name="settings[page_home]" id="page_home" value="<?php echo isset($this->page->settings['page_home']) ? $this->page->settings['page_home'] : 'index';?>" class="input_text"/>
			<label for="page_publish_feed"><?php echo $this->lang->line('label_publish_feed');?></label>
			<select name="settings[page_publish_feed]" id="page_publish_feed" class="input_select">
				<option value="1" <?php echo ((isset($this->page->settings['page_publish_feed']) && $this->page->settings['page_publish_feed'] == 1) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_yes');?></option>
				<option value="0" <?php echo ((isset($this->page->settings['page_publish_feed']) && $this->page->settings['page_publish_feed'] == 0) ? 'selected="selected"' : '')?>><?php echo $this->lang->line('option_no');?></option>
			</select>
			<label for="per_page"><?php echo $this->lang->line('label_per_page');?></label>
			<input type="text" name="settings[per_page]" id="per_page" value="<?php echo isset($this->page->settings['per_page']) ? $this->page->settings['per_page'] : 10;?>" class="input_text"/>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->