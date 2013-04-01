<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo ($language['id'])? $this->lang->line('title_edit_languages') : $this->lang->line('title_create_languages');?></h2>
	<?php echo form_open(($language['id']) ? $this->config->item('admin_folder').'/languages/edit/'.$language['id'] : $this->config->item('admin_folder').'/languages/create');?>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/languages')?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<fieldset>
			<?php if($language['id']):?><input type="hidden" name="id" id="id" value="<?php echo $language['id'];?>"/><?php endif;?>
			<label for="name"><?php echo $this->lang->line('label_name')?></label>
			<input type="text" name="name" id="name" value="<?php if($this->input->post('name')) echo $this->input->post('name');else echo $language['name'];?>" class="input_text"/>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
			<label for="code"><?php echo $this->lang->line('label_code')?></label>
			<input type="text" name="code" id="code" value="<?php if($this->input->post('code')) echo $this->input->post('code');else echo $language['code'];?>" class="input_text"/>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
			<label for="status"><?php echo $this->lang->line('label_active')?></label>
			<select name="active" id="active" class="input_select">
				<option value="1"<?php if($this->input->post('code')) echo $this->input->post('code');if ($language['active'] == 1):?> selected="selected"<?php endif;?>><?php echo $this->lang->line('text_yes')?></option>
				<option value="0"<?php if($this->input->post('code')) echo $this->input->post('code');if ($language['active'] == 0):?> selected="selected"<?php endif;?>><?php echo $this->lang->line('text_no')?></option>
			</select>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->