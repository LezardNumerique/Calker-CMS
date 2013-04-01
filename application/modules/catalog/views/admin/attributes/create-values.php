<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<script type="text/javascript">

$(document).ready(function() {
	$('#color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val('#'+hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
});

</script>
<div id="colorSelector"><div style="background-color: #0000ff"></div></div>
<div id="main">
	<h2><?php echo ($attribute_value['id']) ? $this->lang->line('title_edit_attributes_values').' : '.html_entity_decode($attribute['name'])  : $this->lang->line('title_create_attributes_values').' : '.html_entity_decode($attribute['name']);?></h2>
	<form action="<?php echo ($attribute_value['id']) ? site_url($this->config->item('admin_folder').'/'.$module.'/attributesValuesEdit/'.$attribute['id'].'/'.$attribute_value['id']) : site_url($this->config->item('admin_folder').'/'.$module.'/attributesValuesCreate/'.$attribute['id']);?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo $attribute_value['id'];?>" />
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<?php if($attribute_value['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesValuesDelete/'.$attribute['id'].'/'.$attribute_value['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesEdit/'.$attribute['id'].'#two')?>"><?php echo $this->lang->line('btn_return');?></a></li>
		</ul>
		<?php if ($notice = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
		<?php endif;?>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<div>
			<fieldset>
				<div id="one">
					<input type="hidden" name="attributes_id" value="<?php echo $attribute['id'];?>" />
					<label for="name"><?php echo $this->lang->line('label_name');?></label>
					<input name="name" id="name" type="text" value="<?php if($this->input->post('name')) echo $this->input->post('name');else echo html_entity_decode($attribute_value['name']);?>" class="input_text" maxlength="64"/><span class="required"><?php echo $this->lang->line('text_fields_required');?></span>
					<?php if($attribute['is_color'] == 1):?>
					<label for="color"><?php echo $this->lang->line('label_color');?></label>
					<input name="color" id="color" type="text" value="<?php if($this->input->post('color')) echo $this->input->post('color');else echo $attribute_value['color'];?>" class="input_text" maxlength="64"/>
					<?php endif;?>
				</div>
			</fieldset>
		</div>
	</form>
</div>
<!-- [Main] end -->