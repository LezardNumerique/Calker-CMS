<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div id="main">
	<h2><?php echo $this->lang->line('title_not_allowed');?> : <?php echo $this->lang->line('title_module');?> "<?php echo ucfirst($data['module'])?>"</h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->session->userdata('last_uri'))?>"><?php echo $this->lang->line('btn_return');?></a></li>
	</ul>
	<?php
	switch ($data['level'])
	{
		case 0:
			$levelword = $this->lang->line('level_access');
		break;
		case 1:
			$levelword = $this->lang->line('level_read');
		break;
		case 2:
			$levelword = $this->lang->line('level_add');
		break;
		case 3:
			$levelword = $this->lang->line('level_edit');
		break;
		case 4:
			$levelword = $this->lang->line('level_delete');
		break;
	}
	?>
	<p class="alerte alerte_closable" style="display:none;"><?php echo sprintf($this->lang->line('alert_not_allowed'), $levelword, $data['module'])?></p>
</div>
<!-- [Main] end -->