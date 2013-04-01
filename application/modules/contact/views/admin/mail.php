<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_message');?><?php if(isset($mail['email']) && $mail['email']) echo ' : '.$mail['email'];?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->session->userdata('admin_redirect_uri'))?>"><?php echo $this->lang->line('btn_return');?></a></li>
	</ul>
	<?php if ($notification = $this->session->flashdata('notification')):?>
	<p class="notice notice_closable" style="display:none"><?php echo $notification;?></p>
	<?php endif;?>
	<?php if ($alerte = $this->session->flashdata('alert')):?>
	<p class="alerte alerte_closable" style="display:none"><?php echo $alerte;?></p>
	<?php endif;?>
	<?php if(isset($mail) && $mail):?>
	<div class="fieldset">
		<dl class="detail">
			<dt><span class="name"><?php echo $mail['firstname'];?> <?php echo $mail['lastname'];?></span> <span class="date"><?php echo $this->lang->line('text_the');?> <?php echo date('d/m/Y '.$this->lang->line('date_to').' h:i:s', strtotime($mail['date']));?></span></dt>
			<dd>				
				<?php echo $mail['message'];?>
			</dd>
		</dl>
	</div>
	<?php else: ?>
	<p class="no_data"><?php echo $this->lang->line('text_no_mail');?></p>
	<?php endif;?>
</div>
<!-- [Main] end -->