<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_translations');?><?php if(isset($translations_module) && $translations_module):?> : <?php echo ucwords($translations_module);?><?php endif;?></h2>
	<ul class="manage">
		<?php //if($this->user->root):?>
		<li><a href="<?php echo site_url($this->config->item('admin_folder').'/create')?>" id="create_translation"><?php echo $this->lang->line('btn_create');?></a></li>
		<?php //endif;?>
	</ul>
	<?php echo form_open('', array('class' => 'search', 'id' => 'form_translations'));?>
		<fieldset>
			<table class="table_search">
				<tr>
					<td><label for="module"><?php echo $this->lang->line('label_select_page');?></label></td>
					<td>
						<select id="module" name="module" class="input_select">
						<?php if(isset($modules) && $modules):?>
						<?php foreach($modules as $key => $value):?>
						<option value="<?php echo $key;?>"<?php if(isset($translations_module) && ($key == $translations_module)):?>selected="selected"<?php endif;?>><?php echo ucwords($key);?></option>
						<?php endforeach;?>
						<?php endif;?>
						</select>
					</td>
					<td>
						<input type="submit" name="" value="<?php echo $this->lang->line('btn_translate');?>" class="input_submit translate"/>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte notice_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if(isset($rows) && $rows):?>
	<br class="clear"/>
	<?php echo form_open($this->config->item('admin_folder').'/translations/save', array('class' => 'search', 'id' => 'form_translations'));?>
		<div>
			<input type="submit" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/>
		</div>
		<br class="clear"/>
		<div id="ajax_result_translations">
		<?php $this->load->view('translations/partials/index');?>
		</div>
		<input type="submit" name="" value="<?php echo $this->lang->line('btn_save');?>" class="input_submit"/>
		<br class="clear"/>
	</form>
	<?php else :?>
	<?php if($this->session->userdata('translations_module')):?><p class="no_data"><?php echo $this->lang->line('alert_file_translate_not_find');?></p><?php endif;?>
	<?php endif;?>
</div>
<!-- [Main] end -->