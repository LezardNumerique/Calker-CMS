<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<?php echo form_open($this->config->item('admin_folder').'/navigations/saveLiveView', array('id' => 'form_live_view'));?>
		<input type="hidden" name="id" value="<?php echo $navigation['id']?>"/>
		<?php if($alerte = $this->session->flashdata('alerte')):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<p class="ajax_notice notice_closable"></p>
		<p class="ajax_alerte alerte_closable"></p>
		<?php $post = $this->session->flashdata('post');?>
		<fieldset>
			<label for="title"><?php echo $this->lang->line('label_title');?></label>
			<input name="title" id="title" type="text"  value="<?php if(isset($post['title'])) echo $post['title'];else echo $navigation['title'];?>" class="input_text" autocomplete="off" maxlength="64"/>
			<span class="required"><?php echo $this->lang->line('text_required');?></span>
			<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
			<input name="uri" id="uri" type="text"  value="<?php if(isset($post['uri'])) echo $post['uri'];else echo $navigation['uri'];?>" class="input_text" maxlength="128"/>
			<select id="t_uri" name="t_uri" class="input_select target">
				<option value="0"><?php echo $this->lang->line('option_or_selected');?></option>
				<?php
				if(isset($pages) && $pages):
				foreach ($pages as $page):?>
				<option value="<?php echo $page['uri']?>"><?php echo ($page['level'] > 0) ? "|".str_repeat("__", $page['level']): "";?> <?php echo character_limiter($page['title'], 40);?></option>
				<?php endforeach;endif;?>
			</select>
			<label for="parent_id"><?php echo $this->lang->line('label_parent');?></label>
			<select name="parent_id" class="input_select">
			<option value="0"></option>
			<?php
			$follow = null;
			foreach ($navigations as $parent):?>
			<?php
			if ($navigation['id'] == $parent['id'] || $follow == $parent['parent_id'])
			{
				$follow = $parent['id'];
				continue;
			}
			else
			{
				$follow = null;
			}
			?>
			<option value="<?php echo $parent['id']?>" <?php echo ($navigation['parent_id'] == $parent['id'] || (isset($parent_id) && ($parent_id == $parent['id'])))?'selected="selected"' : '';?>><?php echo ($parent['level'] > 0) ? "|".str_repeat("__", $parent['level']) : '';?> <?php echo character_limiter($parent['title'], 40). $follow ?></option>
			<?php endforeach;?>
			</select>
			<?php if(isset($modules) && $modules):?>
			<label for="module"><?php echo $this->lang->line('label_module');?></label>
			<select id="module" name="module" class="input_select">
				<option value=""></option>
				<?php foreach($modules as $module):?>
				<option value="<?php echo $module['name'];?>"<?php if($module['name'] == $navigation['module']):?> selected="selected"<?php endif;?>><?php echo ucfirst($module['name']);?></option>
				<?php endforeach;?>
			</select>
			<?php endif;?>
			<label for="active"><?php echo $this->lang->line('label_status');?></label>
			<select name="active" class="input_select" id="active">
				<option value='1' <?php if ($navigation['active'] == '1'):?>selected="selected" <?php endif;?>><?php echo $this->lang->line('label_activate');?></option>
				<option value='0' <?php if ($navigation['active'] == '0'):?>selected="selected" <?php endif;?>><?php echo $this->lang->line('label_desactivate');?></option>
			</select>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->