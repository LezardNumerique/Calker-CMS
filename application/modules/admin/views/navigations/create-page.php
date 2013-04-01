<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">	
	<?php echo form_open($this->config->item('admin_folder').'/createPage/navigations/'.$navigations_id, array('class' => 'uri_autocomplete', 'id' => 'form_pages'));?>		
		<div>			
			<fieldset>
				<div id="one">					
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input type="text" id="title" name="title" value="<?php echo ($this->input->post('title')) ? $this->input->post('title') : html_entity_decode($page['title']);?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input type="text" id="uri" name="uri" value="<?php echo ($this->input->post('uri')) ? $this->input->post('uri') : $page['uri'];?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>					
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
					<option value="<?php echo $parent['id']?>" <?php echo ($page['parent_id'] == $parent['id'] || (isset($parent_id) && $parent_id == $parent['id'])) ? 'selected="selected"' : '';?>><?php echo ($parent['level'] > 0) ? "|".str_repeat("__", $parent['level']): '';?> <?php echo (strlen(html_entity_decode($parent['title'])) > 50 ) ? substr(html_entity_decode($parent['title']), 0, 50) . '...' : html_entity_decode($parent['title'])?></option>
					<?php
					endforeach;
					endif;
					?>
					</select>
					<label for="active"><?php echo $this->lang->line('label_status');?></label>
					<select name="active" id="active" class="input_select">
						<option value="0"><?php echo $this->lang->line('option_desactivate');?></option>
						<option value="1"><?php echo $this->lang->line('option_activate');?></option>
					</select>
				</div>				
			</fieldset>
		</div>
	</form>
</div>
<!-- [Main] end -->