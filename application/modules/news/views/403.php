<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<h1><?php echo $this->lang->line('title_not_allowed');?></h1>	
<p class="unauthorized"><?php echo $this->lang->line('text_page_forbidden')?></p>
<p class="return">
	<a href="javascript:history.go(-1);"><?php echo $this->lang->line('btn_return')?></a></li>
</p>