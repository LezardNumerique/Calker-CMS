<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo ($paragraph['id'])? $this->lang->line('title_edit_paragraph') : $this->lang->line('title_create_paragraph');?> <?php echo $this->lang->line('title_paragraphs_types');?> : <?php echo $this->lang->line('text_paragraph_type_'.$type['code']);?></h2>
	<?php echo form_open($this->config->item('admin_folder').'/'.$module.'/saveParag/'.$src_id.'/'.$paragraph['id'], array('enctype' => 'multipart/form-data', 'id' => 'form_paragraphs'));?>
		<input type="hidden" name="paragraphs_id" value="<?php echo $paragraph['id'] ?>"/>
		<input type="hidden" name="src_id" value="<?php echo $src_id ?>"/>
		<input type="hidden" name="types_id" value="<?php echo $types_id ?>"/>
		<input type="hidden" name="redirect_uri" value="<?php echo $this->uri->uri_string();?>"/>
		<ul class="manage">
			<li><input type="submit" name="submit" id="submit" value="<?php echo $this->lang->line('btn_save')?>" class="input_submit"/></li>
			<?php if($paragraph['id']): ?><li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/deleteParag/'.$src_id.'/'.$paragraph['id'])?>" onclick="javascript:return confirmDelete();"><?php echo $this->lang->line('btn_delete')?></a></li><?php endif;?>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/edit/'.$src_id)?>"><?php echo $this->lang->line('btn_return')?></a></li>
		</ul>
		<?php $data['post'] = $this->session->flashdata('post');?>
		<?php if($alerte = $this->session->flashdata('alerte')):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>
		<p class="ajax_notice notice_closable"></p>
		<p class="ajax_alerte alerte_closable"></p>
		<?php $this->load->view('admin/partials/edit-parag-types-'.$types_id, $data);?>
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		$("#tabs").tabs();
	});
	</script>
</div>
<!-- [Main] end -->