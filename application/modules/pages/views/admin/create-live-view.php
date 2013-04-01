<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
	});
</script>
<div id="main">
	<?php echo form_open(($page['id']) ? $this->config->item('admin_folder').'/'.$module.'/editLiveView/'.$page['id'] : $this->config->item('admin_folder').'/'.$module.'/create', array('class' => (!$page['id'] ? 'uri_autocomplete' : ''), 'id' => 'form_live_view'));?>
		<?php if($alerte = validation_errors()):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<p class="ajax_notice notice_closable"></p>
		<p class="ajax_alerte alerte_closable"></p>
		<div id="tabs">
			<ul>
				<li><a href="#one"><?php echo $this->lang->line('menu_content');?></a></li>
				<li><a href="#two"><?php echo $this->lang->line('menu_seo');?></a></li>
				<li><a href="#three"><?php echo $this->lang->line('menu_options');?></a></li>
			</ul>
			<fieldset>
				<div id="one">
					<?php if($page['id']):?><input type="hidden" name="pages_id" id="pages_id" value="<?php echo $page['id'];?>"/><?php endif;?>
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input type="text" id="title" name="title" value="<?php echo ($this->input->post('title')) ? $this->input->post('title') : html_entity_decode($page['title']);?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input type="text" id="uri" name="uri" value="<?php echo ($this->input->post('uri')) ? $this->input->post('uri') : $page['uri'];?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<label for="class"><?php echo $this->lang->line('label_class');?></label>
					<input type="text" id="class" name="class" value="<?php echo ($this->input->post('class')) ? $this->input->post('class') : $page['class'];?>" class="input_text" maxlength="32"/>
					<label for="parent_id"><?php echo $this->lang->line('label_parent');?></label>
					<select name="parent_id" id="parent_id" class="input_select">
					<option value="0"></option>
					<?php
					$follow = null;
					if($parents):
					foreach ($parents as $parent):
					?>
					<?php
					if ($page['id'] == $parent['id'] || $follow == $parent['parent_id'])
					{
						$follow = $page['id'];
						continue;
					}
					else
					{
						$follow = null;
					}
					?>
					<option value="<?php echo $parent['id']?>" <?php echo ($page['parent_id'] == $parent['id'] || (isset($parent_id) && $parent_id == $parent['id'])) ? 'selected="selected"' : '';?>><?php echo ($parent['level'] > 0) ? "|".str_repeat("__", $parent['level']) : '';?> <?php echo (strlen(html_entity_decode($parent['title'])) > 50 ) ? substr(html_entity_decode($parent['title']), 0, 50).'...' : html_entity_decode($parent['title'])?></option>
					<?php
					endforeach;
					endif;
					?>
					</select>
					<label for="active"><?php echo $this->lang->line('label_status');?></label>
					<select name="active" id="active" class="input_select">
						<option value="0"<?php if ($this->input->post('active') == 0 || $page['active'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_desactivate');?></option>
						<option value="1"<?php if ($this->input->post('active') == 1 || $page['active'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_activate');?></option>
					</select>
				</div>
				<div id="two">
					<label for="meta_title"><?php echo $this->lang->line('label_meta_title');?></label>
					<input type="text" id="meta_title" name="meta_title" value="<?php echo ($this->input->post('meta_title')) ? $this->input->post('meta_title') : html_entity_decode($page['meta_title']);?>" class="input_text" maxlength="128"/>
					<label for="meta_keywords"><?php echo $this->lang->line('label_meta_keywords');?></label>
					<input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo ($this->input->post('meta_keywords')) ? $this->input->post('meta_keywords') : $page['meta_keywords'];?>" class="input_text" maxlength="255"/>
					<label for="meta_description"><?php echo $this->lang->line('label_meta_description');?></label>
					<input type="text" id="meta_description" name="meta_description" value="<?php echo ($this->input->post('meta_description')) ? $this->input->post('meta_description') : $page['meta_description'];?>" class="input_text" maxlength="255"/>
				</div>
				<div id="three">
					<label for="show_sub_pages"><?php echo $this->lang->line('label_show_sub_pages');?></label>
					<select name="show_sub_pages" id="show_sub_pages" class="input_select">
					<option value='0'<?php if ($this->input->post('show_sub_pages') == 0 || $page['show_sub_pages'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_no');?></option>
					<option value='1'<?php if ($this->input->post('show_sub_pages') == 1 || $page['show_sub_pages'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_yes');?></option>
					</select>
					<label for="show_navigation"><?php echo $this->lang->line('label_show_navigation');?></label>
					<select name="show_navigation" id="show_navigation" class="input_select">
					<option value="0"<?php if ($this->input->post('show_navigation') == 0 || $page['show_navigation'] == 0) echo ' selected="selected"';?>><?php echo $this->lang->line('option_no');?></option>
					<option value="1"<?php if ($this->input->post('show_navigation') == 1 || $page['show_navigation'] == 1) echo ' selected="selected"';?>><?php echo $this->lang->line('option_yes');?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</form>
</div>
<!-- [Main] end -->