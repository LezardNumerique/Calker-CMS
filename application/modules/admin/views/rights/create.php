<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo ($right['id'])? $this->lang->line('title_edit_rights') : $this->lang->line('title_create_rights');?></h2>
	<?php echo form_open($this->config->item('admin_folder').'/rights/save');?>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/rights')?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<fieldset>
			<label for="group"><?php echo $this->lang->line('label_group');?></label>
			<select name="group" id="group" class="input_select" />
				<?php foreach ($groups as $group) : ?>
				<option value='<?php echo $group['id']?>'<?php if($group['id'] == $right['groups_id']):?>selected="selected"<?php endif;?>><?php echo ucwords($group['title'])?></option>
				<?php endforeach; ?>
			</select>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
			<label for="module"><?php echo $this->lang->line('label_module');?></label>
			<select name="module" id="module" class="input_select" />
				<?php foreach ($this->system->modules as $module) : ?>
				<option value='<?php echo $module['name']?>'<?php if($module['name'] == $right['module']):?>selected="selected"<?php endif;?>><?php echo ucfirst($module['name'])?></option>
				<?php endforeach; ?>
			</select>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
			<label for="level"><?php echo $this->lang->line('label_level');?></label>
			<select name="level" id="level" class="input_select" />
				<?php for ($i = 0; $i <= 4; $i++) : ?>
				<option value='<?php echo $i?>'<?php if($i == $right['level']):?>selected="selected"<?php endif;?>><?php echo $levels[$i];?></option>
				<?php endfor; ?>
			</select>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->