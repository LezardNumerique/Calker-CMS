<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<script type="text/javascript">
$(document).ready(function() {
	$("input.input_file").filestyle({
			image: BASE_URI+APPPATH+"views/"+ADMIN_THEME+"/img/upload_file.gif",
			imageheight : 40,
			imagewidth : 78,
			width : 240
	});
	$('.button_submit').click(function() {
		$('#news_redirect').val(1);
		$('#submit_news').click();
	});
	$('.dialog').click(function() {
		var uri = $(this).attr('href');
		var title = $(this).attr('data-title');
		$("#dialog").load(uri, function() {
			$(this).dialog({
				title:title,
				width:'400px',
				modal: true,
				resizable: false,
				draggable: false,
				position: 'center',
				overlay: {
					backgroundColor: '#000',
					opacity: 0.5
				},
				buttons: {					
					Fermer: function() {
						$("#dialog").dialog('close');
					}
				}
			});
		});
		return false;
	});
});
</script>
<div id="dialog"></div>
<div id="main">
	<h2><?php echo ($new['id'])? $this->lang->line('title_edit_news').' : '.$new['title'] : $this->lang->line('title_create_news');?></h2>
	<?php echo form_open(($new['id']) ? $this->config->item('admin_folder').'/'.$module.'/newsEdit/'.$new['id'] : $this->config->item('admin_folder').'/'.$module.'/newsCreate', array('enctype' => 'multipart/form-data', 'class' => (!$new['id'] ? 'uri_autocomplete' : '')));?>
		<input type="hidden" name="id" value="<?php echo $new['id'];?>" />
		<input type="hidden" name="news_redirect" id="news_redirect" value="0" />
		<input type="hidden" name="news_tabs" id="news_tabs" value="#one" />
		<ul class="manage">
			<li><input type="submit" name="submit" id="submit_news" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><input type="button" name="" value="<?php echo $this->lang->line('btn_save_quit');?>" class="input_submit button_submit"/></li>
			<?php if($new['id']):?><li><a href="<?php echo site_url('admin/'.$module.'/newsDelete/'.$new['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
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
				<li><a href="#two"><?php echo $this->lang->line('menu_body');?></a></li>
				<li><a href="#three"><?php echo $this->lang->line('menu_seo');?></a></li>
			</ul>
			<fieldset>
				<div id="one">
					<?php if(isset($media) && $media && is_file('./'.$this->config->item('medias_folder').'/images/'.$media['file']) && is_readable('./'.$this->config->item('medias_folder').'/images/'.$media['file'])):?>
					<div id="box_images">
						<img src="<?php echo site_url($this->config->item('medias_folder').'/images/x100/'.$media['file']);?>" alt="" />
					</div>
					<?php endif;?>
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input name="title" id="title" type="text" value="<?php if(set_value('title')) echo set_value('title');else echo $new['title'];?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input name="uri" id="uri" type="text" value="<?php if(set_value('uri')) echo set_value('uri');else echo $new['uri'];?>" class="input_text" maxlength="128"/>
					<label for="image"><?php echo $this->lang->line('label_image');?></label>
					<input type="file" name="image" id="image" value="" class="file input_file"/>
					<span class="notice"><?php echo $this->lang->line('notice_image_format');?> | <?php echo $this->lang->line('notice_post_max_size');?> <?php echo ini_get('upload_max_filesize');?></span>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/listMediasTypesSizes/'.$module);?>" class="dialog" data-title="<?php echo $this->lang->line('btn_medias_types_sizes');?> <?php echo $module;?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/medias.png');?>" alt="<?php echo $this->lang->line('btn_medias_types_sizes');?> <?php echo $module;?>" width="16" height="16"/></a>					
					<label for="active"><?php echo $this->lang->line('label_status');?></label>
					<select name="active" class="input_select" id="active">
						<option value="1" <?php echo ($new['active'] == '1') ? "selected" : "";?>><?php echo $this->lang->line('option_activate')?></option>
						<option value="0" <?php echo ($new['active'] == '0') ? "selected" : "";?>><?php echo $this->lang->line('option_desactivate')?></option>
					</select>
				</div>
				<div id="two">
					<textarea name="body" id="body" class="input_textarea"><?php if(set_value('body')) echo set_value('body');else echo $new['body'];?></textarea>
				</div>
				<div id="three">
					<label for="meta_title"><?php echo $this->lang->line('label_meta_title');?></label>
					<input type="text" id="meta_title" name="meta_title" value="<?php if(set_value('meta_title')) echo set_value('meta_title');else echo $new['meta_title'];?>" class="input_text" maxlength="128"/>
					<label for="meta_keywords"><?php echo $this->lang->line('label_meta_keywords');?></label>
					<input type="text" id="meta_keywords" name="meta_keywords" value="<?php if(set_value('meta_keywords')) echo set_value('meta_keywords');else echo $new['meta_keywords'];?>" class="input_text" maxlength="255"/>
					<label for="meta_description"><?php echo $this->lang->line('label_meta_description');?></label>
					<input type="text" id="meta_description" name="meta_description" value="<?php if(set_value('meta_description')) echo set_value('meta_description');else echo $new['meta_description'];?>" class="input_text" maxlength="255"/>
				</div>
			</fieldset>
		</div>
	</form>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs({
			show: function(e, ui) {
				$('#news_tabs').val('#'+ui.panel.id);
			}
		 });
	});
	</script>
</div>
<!-- [Main] end -->