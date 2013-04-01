<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<script type="text/javascript">
$(document).ready(function() {
	$('.button_submit').click(function() {		
		$('#medias_redirect').val(1);
		$('#submit_medias').click();
	});
	
	form = $('.uri_autocomplete');
	$('input[name="name"]', form).keyup(function(){
		$.post(BASE_URI + ADMIN_FOLDER + '/urlTitle', { tokencsrf: CSRF, title : $(this).val() }, function(slug){
			$('input[name="key"]', form).val( slug );
		});
	});
});
</script>
<div id="main">
	<?php if(isset($media_types) && $media_types):?>
	<h2><?php echo ($media_types['medias_types_id'])? $this->lang->line('title_edit_media').' : '.$media_types['name'] : $this->lang->line('title_create_media');?></h2>	
	<?php echo form_open(($media_types['medias_types_id']) ? $this->config->item('admin_folder').'/'.$module.'/edit/'.$media_types['medias_types_id'] : $this->config->item('admin_folder').'/'.$module.'/create', (!$media_types['medias_types_id'] ? array('class' => 'uri_autocomplete') : ''));?>		
		<ul class="manage">
			<li><input type="submit" name="submit" id="submit_medias" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><input type="button" name="" value="<?php echo $this->lang->line('btn_save_quit');?>" class="input_submit button_submit"/></li>
			<?php if($media_types['medias_types_id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/delete/'.$media_types['medias_types_id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module)?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<?php if ($notice = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable"><?php echo $notice;?></p>
		<?php endif;?>
		
		<div id="tabs">
			<ul>
				<li><a href="#one"><?php echo $this->lang->line('menu_name');?></a></li>
				<li><a href="#two"><?php echo $this->lang->line('menu_sizes');?></a></li>
			</ul>	
			<fieldset>
				<div id="one">
					<?php if($media_types['medias_types_id']):?><input type="hidden" name="id" id="id" value="<?php echo $media_types['medias_types_id'];?>"/><?php endif;?>
					<input type="hidden" name="medias_redirect" id="medias_redirect" value="0" />
					<input type="hidden" name="medias_tabs" id="medias_tabs" value="#one" />
					<label for="name"><?php echo $this->lang->line('label_name')?></label>
					<input type="text" name="name" id="name" value="<?php if(set_value('name')) echo set_value('name');else echo $media_types['name'];?>" class="input_text" maxlenght="32"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<label for="key"><?php echo $this->lang->line('label_key')?></label>
					<input type="text" name="key" id="key" value="<?php if(set_value('key')) echo set_value('key');else echo $media_types['key'];?>" class="input_text" maxlenght="16"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>					
					<?php if($modules = $this->system->list_modules()):?>					
					<label for="module"><?php echo $this->lang->line('label_module')?></label>
					<select id="module" name="module" class="input_select">					
						<?php foreach($modules as $module):?>
						<option value="<?php echo $module['name'];?>" <?php if($media_types['module'] == $module['name']):?>selected="selected"<?php endif;?>><?php echo ucfirst($module['name']);?></option>
						<?php endforeach;?>
					</select>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<?php endif;?>													
				</div>
				<div id="two">
					<?php if(isset($themes) && $themes):?>					
					<?php foreach($themes as $key => $theme):?>
					<h2><?php echo $this->lang->line('title_theme');?> : <?php echo $theme;?></h2>
					<input type="hidden" name="theme[<?php echo $theme;?>]" value="<?php echo $theme;?>"/>				
					<label for="width_<?php echo $theme;?>"><?php echo $this->lang->line('label_width')?></label>
					<input type="text" name="width[<?php echo $theme;?>]" id="width_<?php echo $theme;?>" value="<?php if(set_value('width['.$theme.']')) echo set_value('width['.$theme.']');else echo $media_types_sizes[$theme]['width'];?>" class="input_text"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<span class="notice">px</span>
					<label for="height_<?php echo $theme;?>"><?php echo $this->lang->line('label_height')?></label>
					<input type="text" name="height[<?php echo $theme;?>]" id="height_<?php echo $theme;?>" value="<?php if(set_value('height['.$theme.']')) echo set_value('height['.$theme.']');else echo $media_types_sizes[$theme]['height'];?>" class="input_text"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<span class="notice">px</span>
					<?php endforeach;?>
					<?php endif;?>
				</div>
			</fieldset>
		</div>
		<script type="text/javascript">
		$(function() {
			$("#tabs").tabs({
				show: function(e, ui) {
					$('#medias_tabs').val('#'+ui.panel.id);
				}
			 });
		});
		</script>		
	</form>
	<?php endif;?>
</div>
<!-- [Main] end -->