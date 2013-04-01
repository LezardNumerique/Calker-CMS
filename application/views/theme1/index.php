<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<?php $this->load->view($this->system->theme.'/header'); ?>
<?php
if (is_file(APPPATH.'views/'.$this->system->theme.'/modules/'.$module.'/views/'.$view.'.php') && is_readable(APPPATH.'views/'.$this->system->theme.'/modules/'.$module.'/views/'.$view.'.php')) $this->load->view($this->system->theme.'/modules/'.$module.'/views/'.$view);
else $this->load->view('../modules/'.$module.'/views/'.$view);
?>
<?php $this->load->view($this->system->theme.'/footer');?>
