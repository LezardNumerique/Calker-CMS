<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="contact">
	<h1><?php echo $this->lang->line('title_contact');?></h1>
	<p class="notification closable"><?php if(isset($this->settings['notification_success_send_message'])) echo $this->settings['notification_success_send_message'];?></p>
</div>