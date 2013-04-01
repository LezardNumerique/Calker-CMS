<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_select_paragraph');?></h2>
	<?php echo form_open('', array('id' => 'form_paragraphs'));?>
		<input type="hidden" name="src_id" value="<?php echo $src_id;?>"/>
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/edit/'.$src_id)?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<fieldset>
			<label for="types_id"><?php echo $this->lang->line('label_select_paragraph');?></label>
			<select name="types_id" id="types_id" class="input_select">
				<?php if(isset($paragraphs_types) && $paragraphs_types) :?>
				<?php foreach ($paragraphs_types as $paragraph_type) :?>
				<option value="<?php echo $paragraph_type['id'];?>"<?php if($this->session->userdata('types_id') == $paragraph_type['id']):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('text_paragraph_type_'.$paragraph_type['code']);?></option>
				<?php endforeach;?>
				<?php endif;?>
			</select>
		</fieldset>
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		$("#tabs").tabs();
	});
	</script>
</div>
<!-- [Main] end -->