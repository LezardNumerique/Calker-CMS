<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!-- [Main] start -->
<div class="main">
	<h2><?php echo $this->lang->line('title_phpinfo');?></h2>
	<ul class="manage">
		<li><a href="<?php echo site_url($this->config->item('admin_folder'))?>"><?php echo $this->lang->line('btn_return');?></a></li>
	</ul>
	<style type="text/css">
		#phpinfo {margin:0;}
		#phpinfo pre {}
		#phpinfo a:link {}
		#phpinfo a:hover {}
		#phpinfo table {
			border-collapse: collapse;
			width: 99.9%;
			margin: 10px 0 20px 1px;
			padding: 0;
		}
		#phpinfo .center {}
		#phpinfo .center table {}
		#phpinfo .center th {}
		#phpinfo td, th {}
		#phpinfo h1 {}
		#phpinfo h2 {}
		#phpinfo .p {}
		#phpinfo .e {padding:4px;background:#e8e8e8;border-bottom:1px dashed #dcdcdc}
		#phpinfo .h {background: #f5f5f5;border-bottom:1px solid #ccc;padding:4px;}
		#phpinfo .v {padding:4px;background:#eee;border-bottom:1px dashed #dcdcdc}
		#phpinfo .vr {}
		#phpinfo img {}
		#phpinfo hr {}
	</style>
	<div id="phpinfo">
		<?php
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean () ;
		echo (str_replace("module_Zend Optimizer", "module_Zend_Optimizer", preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo )));
		?>
	</div>
</div>
<!-- [Main] end -->