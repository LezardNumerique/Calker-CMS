<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->set('sidebar_admin_contact', 'sidebar_admin_contact');

function sidebar_admin_contact()
{
	$obj =& get_instance();
	$module = 'contact';	

	$css_settings = '';
	if($obj->uri->segment(2) == $module && $obj->uri->segment(3) == 'settings') $css_settings = 'active';

	echo '
		<ul class="navigation_sub">
			<li class="'.$css_settings.' last"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/settings').'">'.$obj->lang->line('btn_settings').'</a></li>
		</ul>
	';

}