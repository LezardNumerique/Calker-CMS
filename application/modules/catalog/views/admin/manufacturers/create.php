<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start --> 
<div id="main">
	<h2><?php echo ($manufacturer['id'])? $this->lang->line('title_edit_manufacturers').' : '.$manufacturer['title']  : $this->lang->line('title_create_manufacturers');?></h2>	
	<?php echo form_open(($manufacturer['id']) ? site_url($this->config->item('admin_folder').'/'.$module.'/manufacturersEdit/'.$manufacturer['id']) : site_url($this->config->item('admin_folder').'/'.$module.'/manufacturersCreate'), array('enctype' => 'multipart/form-data', 'class' => (!$manufacturer['id'] ? 'uri_autocomplete' : ''), 'id' => 'form_manufacturers'));?>
		<input type="hidden" name="id" value="<?php echo $manufacturer['id'];?>" />
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<?php if($manufacturer['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/manufacturersDelete/'.$manufacturer['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'))?>"><?php echo $this->lang->line('btn_return');?></a></li>
		</ul>
		<?php if ($notice = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
		<?php endif;?>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<div>
			<fieldset>
				<div>
					<label for="title"><?php echo $this->lang->line('label_name');?></label>
					<input name="title" id="title" type="text" value="<?php if($this->input->post('title')) echo $this->input->post('title');else echo html_entity_decode($manufacturer['title']);?>" class="input_text" maxlength="64"/>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input name="uri" id="uri" type="text" value="<?php if($this->input->post('uri')) echo $this->input->post('uri');else echo $manufacturer['uri'];?>" class="input_text" maxlength="64"/>
				</div>
			</fieldset>
		</div>
	</form>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
	});
	</script>
</div>
<!-- [Main] end -->