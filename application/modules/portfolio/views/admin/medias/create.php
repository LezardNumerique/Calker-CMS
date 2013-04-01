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
		$('#medias_redirect').val(1);
		$('#submit_medias').click();
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
	<h2><?php echo ($media['id'])? $this->lang->line('title_edit_medias').($media['title'] ? ' : '.$media['title'] : '')  : $this->lang->line('title_create_medias');?></h2>
	<?php echo form_open(($media['id']) ? $this->config->item('admin_folder').'/'.$module.'/mediasEdit/'.$categories_id.'/'.$media['id'] : $this->config->item('admin_folder').'/'.$module.'/mediasCreate', array('enctype' => 'multipart/form-data', 'class' => (!$media['id'] ? 'uri_autocomplete' : '')));?>
		<input type="hidden" name="id" value="<?php echo $media['id'];?>" />
		<input type="hidden" name="medias_redirect" id="medias_redirect" value="0" />
		<input type="hidden" name="medias_tabs" id="medias_tabs" value="#one" />
		<ul class="manage">
			<li><input type="submit" name="submit" id="submit_medias" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><input type="button" name="" value="<?php echo $this->lang->line('btn_save_quit');?>" class="input_submit button_submit"/></li>
			<?php if($media['id']):?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/mediasDelete/'.$categories_id.'/'.$media['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
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
				<li><a href="#one"><?php echo $this->lang->line('menu_medias');?></a></li>
				<li><a href="#two"><?php echo $this->lang->line('menu_body');?></a></li>
				<li><a href="#three"><?php echo $this->lang->line('menu_categories');?></a></li>
			</ul>
			<fieldset>
				<div id="one">
					<?php if(isset($media['file']) && $media['file'] && is_file('./medias/images/'.$media['file']) && is_readable('./medias/images/'.$media['file'])):?>
					<div id="box_images">
						<img src="<?php echo site_url($this->config->item('medias_folder').'/images/x100/'.$media['file']);?>" alt="<?php ($media['alt'] ? $media['alt'] : $media['medias_id']);?>" />
					</div>
					<?php endif;?>
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input name="title" id="title" type="text" value="<?php if($this->input->post('title')) echo $this->input->post('title');else echo $media['title'];?>" class="input_text" maxlength="64"/>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input name="uri" id="uri" type="text" value="<?php if($this->input->post('uri')) echo $this->input->post('uri');else echo $media['uri'];?>" class="input_text" maxlength="64"/>
					<label for="active"><?php echo $this->lang->line('label_status');?></label>
					<select name="active" class="input_select" id="active">
						<option value="0" <?php echo ($media['active'] == '0') ? 'selected="selected"' : '';?>><?php echo $this->lang->line('option_desactivate')?></option>
						<option value="1" <?php echo ($media['active'] == '1') ? 'selected="selected"' : '';?>><?php echo $this->lang->line('option_activate')?></option>
					</select>
					<label for="is_box"><?php echo $this->lang->line('label_is_box');?></label>
					<select name="is_box" class="input_select" id="active">
						<option value="0" <?php echo ($media['is_box'] == '0') ? 'selected="selected"' : '';?>><?php echo $this->lang->line('option_desactivate')?></option>
						<option value="1" <?php echo ($media['is_box'] == '1') ? 'selected="selected"' : '';?>><?php echo $this->lang->line('option_activate')?></option>
					</select>
					<label for="image"><?php echo $this->lang->line('label_image');?></label>
					<input type="file" name="image" id="image" value="" class="file input_file"/>
					<span class="notice"><?php echo $this->lang->line('notice_image_format');?> | <?php echo $this->lang->line('notice_post_max_size');?> <?php echo ini_get('upload_max_filesize');?></span>
					<a href="<?php echo site_url($this->config->item('admin_folder').'/listMediasTypesSizes/'.$module);?>" class="dialog" data-title="<?php echo $this->lang->line('btn_medias_types_sizes');?> <?php echo $module;?>"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/medias.png');?>" alt="<?php echo $this->lang->line('btn_medias_types_sizes');?> <?php echo $module;?>" width="16" height="16"/></a>	
					<label for="legend"><?php echo $this->lang->line('label_legend');?></label>
					<input type="text" name="legend" id="legend" value="<?php if($this->input->post('legend')) echo $this->input->post('legend');else echo $media['legend'];?>" class="input_text" maxlength="128"/>
					<label for="alt"><?php echo $this->lang->line('label_alt');?></label>
					<input type="text" name="alt" id="alt" value="<?php if($this->input->post('alt')) echo $this->input->post('alt');else echo $media['alt'];?>" class="input_text" maxlength="128"/>
				</div>
				<div id="two">
					<textarea name="body" id="body" class="input_textarea"><?php if($this->input->post('body')) echo $this->input->post('body');else echo $media['body'];?></textarea>
				</div>
				<div id="three">
					<?php if(isset($categories) && $categories):?>
					<table class="table_list">
						<thead>
							<tr>
								<th width="70%"><?php echo $this->lang->line('td_categories');?></th>
								<th width="10%" class="center"><?php echo $this->lang->line('td_action');?></th>
								<th width="10%" class="center"><?php echo $this->lang->line('td_default');?></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;$count_categories = count($categories);foreach($categories as $categorie):?>
							<?php if ($i % 2 != 0): $rowClass = 'odd';else: $rowClass = 'even';endif;?>
							<tr class="<?php echo $rowClass?>">
								<td>
									<label for="categories_<?php echo $categorie['id'];?>" style="width:100%;float:left;">
									<?php if ($categorie['level'] == 0) :?>
									<span class="lv0_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv0.gif')?>" alt=""/></span>
									<span class="lv0"><?php echo $categorie['title']?></span>
									<?php elseif ($categorie['level'] == 1) :?>
									<span class="lv1_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv1.gif')?>" alt=""/></span>
									<span class="lv1"><?php echo $categorie['title']?></span>
									<?php elseif ($categorie['level'] == 2) :?>
									<span class="lv2_img"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/lv2.gif')?>" alt=""/></span>
									<span class="lv2"><?php echo $categorie['title']?></span>
									<?php endif;?>
									</label>
								</td>
								<td class="center">
									<input type="checkbox" id="categories_<?php echo $categorie['id'];?>" name="categories[<?php echo $categorie['id'];?>]" value="<?php echo $categorie['id'];?>" <?php if(isset($medias_to_categories) && in_array($categorie['id'], $medias_to_categories) || $categories_id == $categorie['id']):?>checked="checked"<?php endif;?>/>
								</td>
								<td class="center">
									<input type="radio" name="categories_id_default" value="<?php echo $categorie['id'];?>" <?php if($categorie['id'] == $media['categories_id_default'] || $categories_id == $categorie['id']):?>checked="checked"<?php endif;?>/>
								</td>
							</tr>
							<?php $i++;endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</div>
			</fieldset>
		</div>
	</form>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs({
			show: function(e, ui) {
				$('#medias_tabs').val('#'+ui.panel.id);
			}
		 });
	});
	</script>
</div>
<!-- [Main] end -->