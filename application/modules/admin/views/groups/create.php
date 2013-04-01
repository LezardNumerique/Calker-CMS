<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo ($group['id'])? $this->lang->line('title_edit_groups') : $this->lang->line('title_create_groups');?></h2>
	<?php echo form_open($this->config->item('admin_folder').'/groups/save');?>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/groups');?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if($alerte = $this->session->flashdata('alerte')):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<fieldset>
			<?php $post = $this->session->flashdata('post');?>
			<input type="hidden" name="redirect_uri_error" value="<?php echo $this->uri->uri_string();?>"/>
			<?php if($group['id']):?><input type="hidden" name="id" id="id" value="<?php echo $group['id'];?>"/><?php endif;?>
			<label for="title"><?php echo $this->lang->line('label_title');?></label>
			<input name="title" type="text" value="<?php if(isset($post['title'])) echo $post['title'];else echo $group['title'];?>" class="input_text" maxlength="64"/>
			<span class="required"><?php echo $this->lang->line('text_fields_required');?></span>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->