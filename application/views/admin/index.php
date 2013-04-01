<?php $this->load->view('./'.$this->config->item('theme_admin').'/header'); ?>
<?php $this->load->view('../modules/' . $module . '/views/' . $view); ?>
<?php $this->load->view('./'.$this->config->item('theme_admin').'/footer'); ?>