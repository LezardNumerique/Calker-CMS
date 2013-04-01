<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<?php echo form_open($this->config->item('admin_folder').'/'.$module.'/saveParagLiveView/'.$paragraph['id'], array('enctype' => 'multipart/form-data', 'id' => 'form_pages'));?>
		<input type="hidden" name="paragraphs_id" value="<?php echo $paragraph['id'] ?>"/>
		<input type="hidden" name="src_id" value="<?php echo $src_id ?>"/>
		<input type="hidden" name="types_id" value="<?php echo $types_id ?>"/>
		<input type="hidden" name="live_view" value="1"/>
		<input type="hidden" name="redirect_uri" value="<?php echo $this->session->userdata('redirect_admin_live_view');?>"/>
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