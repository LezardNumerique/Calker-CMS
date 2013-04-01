<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo ($attribute['id'])? $this->lang->line('title_edit_attributes').' : '.html_entity_decode($attribute['name'])  : $this->lang->line('title_create_attributes');?></h2>
	<form action="<?php echo ($attribute['id']) ? site_url($this->config->item('admin_folder').'/'.$module.'/attributesEdit/'.$attribute['id']) : site_url($this->config->item('admin_folder').'/'.$module.'/attributesCreate');?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo $attribute['id'];?>" />
		<ul class="manage">
			<li><input type="submit" name="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<?php if($attribute['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesDelete/'.$attribute['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->session->userdata('redirect_uri'))?>"><?php echo $this->lang->line('btn_return');?></a></li>
		</ul>
		<?php if ($notice = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none;"><?php echo $notice;?></p>
		<?php endif;?>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<div id="tabs">
			<ul>
				<li><a href="#one"><?php echo $this->lang->line('menu_content');?></a></li>
				<?php if($attribute['id']):?><li><a href="#two"><?php echo $this->lang->line('menu_attributes_values');?></a></li><?php endif;?>
			</ul>
			<fieldset>
				<div id="one">
					<label for="name"><?php echo $this->lang->line('label_name');?></label>
					<input name="name" id="name" type="text" value="<?php if($this->input->post('name')) echo $this->input->post('name');else echo html_entity_decode($attribute['name']);?>" class="input_text" maxlength="64"/><span class="required"><?php echo $this->lang->line('text_fields_required');?></span>
					<label for="is_color"><?php echo $this->lang->line('label_is_color');?></label>
					<select name="is_color" id="is_color" class="input_select">
						<option value="0" <?php echo ($attribute['is_color'] == '0') ? "selected" : "";?>><?php echo $this->lang->line('option_no')?></option>
						<option value="1" <?php echo ($attribute['is_color'] == '1') ? "selected" : "";?>><?php echo $this->lang->line('option_yes')?></option>
					</select>
				</div>
				<?php if($attribute['id']):?>
				<div id="two">
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesValuesCreate/'.$attribute['id'])?>"><?php echo $this->lang->line('btn_create_attributes_values');?></a>
					<?php if(isset($attributes_values) && $attributes_values) : ?>
					<table class="table_list">
						<thead>
							<tr>
								<th width="80%"><?php echo $this->lang->line('td_values')?></th>
								<th width="20%" colspan="2"><?php echo $this->lang->line('td_action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;$count_attributes_values = count($attributes_values);foreach($attributes_values as $attribute_value):?>
							<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
							<tr class="<?php echo $rowClass?>">
								<td>
									<?php echo html_entity_decode($attribute_value['name']);?>
								</td>
								<td class="center">
									<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesValuesEdit/'.$attribute['id'].'/'.$attribute_value['id'])?>" title="<?php echo $this->lang->line('btn_edit')?>" class="tooltip"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/edit.png')?>" alt="<?php echo $this->lang->line('btn_edit')?>" width="16px" height="16px"/></a>
								</td>
								<td class="center">
									<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/attributesValuesDelete/'.$attribute['id'].'/'.$attribute_value['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
								</td>
							</tr>
							<?php $i++;endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</div>
				<?php endif;?>
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