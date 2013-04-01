<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo ($user['id'])? $this->lang->line('title_edit_user') : $this->lang->line('title_create_user');?></h2>
	<?php echo form_open(($user['id'])? $this->config->item('admin_folder').'/users/edit/'.$user['id'] : $this->config->item('admin_folder').'/users/create', array('id' => 'form_users'));?>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save')?>" class="input_submit" /></li>
			<?php if($user['id'] && $this->user->id != $user['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/users/delete/'.$user['id']);?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'));?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<fieldset>
			<?php if($user['id']):?><input type="hidden" name="id" id="id" value="<?php echo $user['id'];?>" /><?php endif;?>
			<label for="username"><?php echo $this->lang->line('label_username');?></label>
			<input id="username" name="username" type='text' value='<?php echo ($this->input->post('username')) ? $this->input->post('username') : $user['username'];?>' class="input_text" <?php if($user['username'] == 'admin'):?>DISABLED<?php endif;?> maxlength="64"/>
			<?php if($user['username'] == 'admin'):?><input type="hidden" name="username" id="username" value="<?php echo $user['username'];?>"/><?php endif;?>
			<span class="required"><?php echo $this->lang->line('text_fields_required');?></span>
			<label for="password"><?php echo $this->lang->line('label_password')?></label>
			<input type="password" name="password" value="" id="password" class="input_text" maxlength="12"/>
			<?php if(!$user['id']):?><span class="required"><?php echo $this->lang->line('text_fields_required');?></span><?php endif;?>
			<label for="passconf"><?php echo $this->lang->line('label_password_confirm')?></label>
			<input type="password" name="passconf" value="" id="" class="input_text" maxlength="12"/>
			<?php if(!$user['id']):?><span class="required"><?php echo $this->lang->line('text_fields_required');?></span><?php endif;?>
			<label for="email"><?php echo $this->lang->line('label_email')?></label>
			<input type="text" name="email" value="<?php echo ($this->input->post('email')) ? $this->input->post('email') : $user['email'];?>" id="email" class="input_text" maxlength="128"/>
			<span class="required"><?php echo $this->lang->line('text_fields_required');?></span>
			<?php if($this->user->id != $user['id']):?>
			<label for="active"><?php echo $this->lang->line('label_status')?></label>
			<select name="active" class="input_select" id="active">
				<option value="0"<?php if ($this->input->post('active') == '0' || $user['active'] == '0') echo ' selected="selected"';?>><?php echo $this->lang->line('option_desactivate')?></option>
				<option value="1"<?php if ($this->input->post('active') == '1' || $user['active'] == '1') echo ' selected="selected"';?>><?php echo $this->lang->line('option_activate')?></option>
			</select>
			<?php endif;?>
			<?php if($this->user->id != $user['id']):?>
			<label for="groups_id"><?php echo $this->lang->line('label_group')?></label>
			<select name="groups_id" class="input_select" id="groups_id">
			<?php if(isset($groups) && $groups):?>
			<?php foreach($groups as $group):?>
			<option value="<?php echo $group['id'];?>"<?php if ($group['id'] == $this->input->post('groups_id') || $group['id'] == $user['groups_id']):?> selected="selected"<?php endif;?>><?php echo ucfirst($group['title']);?></option>
			<?php endforeach;?>
			<?php endif;?>
			</select>
			<?php endif;?>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->
