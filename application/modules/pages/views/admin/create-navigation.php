<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">		
	<?php echo form_open($this->config->item('admin_folder').'/'.$module.'/createNavigation', array('class' => 'uri_autocomplete', 'id' => 'form_navigations'));?>		
		<div>			
			<fieldset>
				<div id="one">		
					<input type="hidden" name="page_parents_id" value="<?php echo $page_parent['id'];?>"/>
					<input type="hidden" name="page_parents_uri" value="<?php echo $page_parent['uri'];?>"/>
					<input type="hidden" name="page_uri" value="<?php echo str_replace($page_parent['uri'].'/', '', $page['uri']);?>"/>		
					<label for="title"><?php echo $this->lang->line('label_title');?></label>
					<input type="text" id="title" name="title" value="<?php echo ($this->input->post('title')) ? $this->input->post('title') : html_entity_decode($navigation['title']);?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>
					<label for="uri"><?php echo $this->lang->line('label_uri');?></label>
					<input type="text" id="uri" name="uri" value="<?php echo ($this->input->post('uri')) ? $this->input->post('uri') : $navigation['uri'];?>" class="input_text" maxlength="128"/>
					<span class="required"><?php echo $this->lang->line('text_required');?></span>					
					<label for="parent_id"><?php echo $this->lang->line('label_parent');?></label>
					<select name="parent_id" class="input_select">
						<option value="0"></option>
						<?php
						$follow = null;
						foreach ($navigations as $parent):?>
						<?php
						if ($navigation['id'] == $parent['id'] || $follow == $parent['parent_id'])
						{
							$follow = $parent['id'];
							continue;
						}
						else
						{
							$follow = null;
						}
						?>
						<option value="<?php echo $parent['id']?>" <?php echo ($parent['uri'] == $page_parent['uri'] || $navigation['parent_id'] == $parent['id'] || (isset($parent_id) && ($parent_id == $parent['id']))) ? 'selected="selected"' : '';?>><?php echo ($parent['level'] > 0) ? "|".str_repeat("__", $parent['level']) : '';?> <?php echo html_entity_decode($parent['title']).$follow;?></option>
			<?php endforeach;?>
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