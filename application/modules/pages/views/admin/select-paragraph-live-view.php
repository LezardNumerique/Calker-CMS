<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div>	
	<?php echo form_open('', array('id' => 'form_paragraphs'));?>
		<input type="hidden" name="src_id" value="<?php echo $src_id;?>"/>
		<input type="hidden" id="select_parag" value="1"/>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<fieldset>
			<label for="types_id"><?php echo $this->lang->line('label_select_paragraph');?></label>
			<select name="types_id" id="types_id" class="input_select">
				<?php if(isset($paragraphs_types) && $paragraphs_types) :?>
				<?php foreach ($paragraphs_types as $paragraph_type) :?>
				<option value="<?php echo $paragraph_type['id'];?>"<?php if($this->session->userdata('types_id') == $paragraph_type['id']):?>selected="selected"<?php endif;?>><?php echo $this->lang->line('text_paragraph_type_'.$paragraph_type['code']);?></option>
				<?php endforeach;?>
				<?php endif;?>
			</select>
		</fieldset>
	</form>
</div>
<!-- [Main] end -->