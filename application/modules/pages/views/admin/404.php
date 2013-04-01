<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_page_not_found')?></h2>
	<ul class="manage">
		<li><a href="javascript:history.go(-1);"><?php echo $this->lang->line('btn_return')?></a></li>
	</ul>
	<p class="not_found"><?php echo $this->lang->line('text_page_not_found')?></p>
</div>
<!-- [Main] end -->
