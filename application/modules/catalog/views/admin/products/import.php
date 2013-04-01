<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_import_products');?></h2>
	<?php echo form_open('', array('enctype' => 'multipart/form-data', 'id' => 'form_import'));?>	
		<ul class="manage">
			<li><input type="submit" name="submit" id="submit" value="<?php echo $this->lang->line('btn_save')?>" class="input_submit"/></li>
			<li><a href="<?php echo site_url($this->config->item('admin_folder').'/'.$module.'/products')?>"><?php echo $this->lang->line('btn_return');?></a></li>
		</ul>
		<?php if ($notification = $this->session->flashdata('notification')):?>
		<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
		<?php endif;?>		
		<?php if (isset($alerte) && $alerte):?>
		<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
		<?php endif;?>
		<fieldset>
			<label for="userfile"><?php echo $this->lang->line('label_file');?></label>
			<input type="file" name="userfile" id="userfile" value="" class="input_file"/>
		</fieldset>
	</form>
	<?php //echo $this->session->flashdata('report');?>
	<script type="text/javascript">
	$(function() {
		$("input.input_file").filestyle({
			image: BASE_URI+APPPATH+"views/"+ADMIN_THEME+"/img/upload_file.gif",
			imageheight : 40,
			imagewidth : 78,
			width : 240
		});
	});
	</script>
</div>
<!-- [Main] end -->
