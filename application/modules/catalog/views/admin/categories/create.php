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
		$('#categories_redirect').val(1);
		$('#submit_categories').click();
	});
});
</script>
<div id="main">
	<h2><?php echo ($categorie['id'])? $this->lang->line('title_edit_categories').' : '.html_entity_decode($categorie['title']) : $this->lang->line('title_create_categories');?></h2>	
	<?php echo form_open(($categorie['id']) ? site_url($this->config->item('admin_folder').'/'.$module.'/categoriesEdit/'.$categorie['id']) : site_url($this->config->item('admin_folder').'/'.$module.'/categoriesCreate'), array('enctype' => 'multipart/form-data', 'class' => (!$categorie['id'] ? 'uri_autocomplete' : ''), 'id' => 'form_categories'));?>	
		<input type="hidden" name="id" value="<?php echo $categorie['id'];?>" />
		<input type="hidden" name="categories_redirect" id="categories_redirect" value="0" />
		<input type="hidden" name="categories_tabs" id="categories_tabs" value="#one" />
		<ul class="manage">
			<li><input type="submit" name="submit" id="submit_categories" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/></li>
			<li><input type="button" name="" value="<?php echo $this->lang->line('btn_save_quit');?>" class="input_submit button_submit"/></li>
			<?php if($categorie['id']):?><li><a href="<?php echo site_url('admin/'.$module.'/categoriesDelete/'.$categorie['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete');?></a></li><?php endif;?>
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
				<?php if($categorie['id']):?><li><a href="#four"><?php echo $this->lang->line('menu_images');?></a></li><?php endif;?>
			</ul>
			<fieldset>
				<div id="one">
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input name="title" id="title" type="text" value="<?php if($this->input->post('title')) echo $this->input->post('title');else echo html_entity_decode($categorie['title']);?>" class="input_text" maxlength="64"/>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input name="uri" id="uri" type="text" value="<?php if($this->input->post('uri')) echo $this->input->post('uri');else echo $categorie['uri'];?>" class="input_text" maxlength="64"/>
					<label for="parent_id"><?php echo $this->lang->line('label_parent');?></label>
					<select name="parent_id" id="parent_id" class="input_select" />
						<?php if (isset($parents) && $parents): ?>
						<?php foreach ($parents as $parent):?>
						<option value="<?php echo $parent['id']?>" <?php echo ($categorie['parent_id'] == $parent['id'] || (isset($categories_id) && $categories_id == $parent['id'])) ? "selected" : "";?>><?php echo ($parent['level'] > 0) ? "|".str_repeat("__", $parent['level']): ""?> <?php echo html_entity_decode($parent['title']);?></option>
						<?php endforeach;?>
						<?php endif;?>
					</select>
					<label for="active"><?php echo $this->lang->line('label_status');?></label>
					<select name="active" class="input_select" id="active">
						<option value="1" <?php echo ($categorie['active'] == '1') ? "selected" : "";?>><?php echo $this->lang->line('option_activate')?></option>
						<option value="0" <?php echo ($categorie['active'] == '0') ? "selected" : "";?>><?php echo $this->lang->line('option_desactivate')?></option>
					</select>
				</div>
				<div id="two">
					<textarea name="body" id="body" class="input_textarea"><?php if($this->input->post('body')) echo $this->input->post('body');else echo $categorie['body'];?></textarea>
				</div>
				<div id="three">
					<label for="meta_title"><?php echo $this->lang->line('label_meta_title');?></label>
					<input type="text" id="meta_title" name="meta_title" value="<?php if($this->input->post('meta_title')) echo $this->input->post('meta_title');else echo $categorie['meta_title'];?>" class="input_text" maxlength="64"/>
					<label for="meta_keywords"><?php echo $this->lang->line('label_meta_keywords');?></label>
					<input type="text" id="meta_keywords" name="meta_keywords" value="<?php if($this->input->post('meta_keywords')) echo $this->input->post('meta_keywords');else echo $categorie['meta_keywords'];?>" class="input_text" maxlength="255"/>
					<label for="meta_description"><?php echo $this->lang->line('label_meta_description');?></label>
					<input type="text" id="meta_description" name="meta_description" value="<?php if($this->input->post('meta_description')) echo $this->input->post('meta_description');else echo $categorie['meta_description'];?>" class="input_text"/>
				</div>
				<?php if($categorie['id']):?>
				<div id="four">
					<label for="image"><?php echo $this->lang->line('label_image');?></label>
					<input type="file" name="image" id="image" value="" class="file input_file"/>
					<label for="legend"><?php echo $this->lang->line('label_legend');?></label>
					<input type="text" name="legend" id="legend" value="" class="input_text" maxlength="64"/>
					<br class="clear"/>
					<br />
					<?php if(isset($image) && $image):?>
					<?php if(is_file('./'.$this->config->item('medias_folder').'/images/'.$image['file'])):?>
					<img src="<?php echo site_url($this->config->item('medias_folder').'/images/120x80/'.$image['file']);?>" alt="<?php echo $image['options']['legend'];?>" width="120" height="80"/>
					<?php else :?>
					<img src="<?php echo site_url($this->config->item('medias_folder').'/images/120x80/default.jpg');?>" alt="<?php echo html_entity_decode($categorie['title']);?>" width="120" height="80"/>
					<?php endif;?>
					<br />
					<br />
					<a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/categoriesDeleteImages/'.$categorie['id'].'/'.$image['id'])?>" title="<?php echo $this->lang->line('btn_delete');?>" class="tooltip" onclick="javascript:return confirmDelete();"><img src="<?php echo site_url(APPPATH.'views/'.$this->config->item('theme_admin').'/img/icons/delete.png')?>" alt="<?php echo $this->lang->line('btn_delete');?>" width="16px" height="16px"/></a>
					<?php endif;?>
				</div>
				<?php endif;?>
			</fieldset>
		</div>
	</form>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs({
			show: function(e, ui) {
				$('#categories_tabs').val('#'+ui.panel.id);
			}
		 });
	});
	</script>
</div>
<!-- [Main] end -->
