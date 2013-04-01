<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->set('sidebar_admin_news', 'sidebar_admin_news');

function sidebar_admin_news()
{
	$obj =& get_instance();	
	$module = 'news';	

	$css_settings = '';
	if($obj->uri->segment(2) == $module && $obj->uri->segment(3) == 'settings') $css_settings = 'active';

	echo '
		<ul class="navigation_sub">
			<li class="'.$css_settings.' last"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/settings').'">'.$obj->lang->line('btn_settings').'</a></li>
		</ul>
	';

}

$this->set('box_news', 'box_news');

function box_news($limit = '5')
{
	$module = 'news';
	$obj =& get_instance();
	$obj->load->library($module.'/newss', '', 'news');
	$obj->load->library('medias');
	$obj->config->load($module.'/config');
	$data['news_settings'] = isset($obj->system->news_settings) ? unserialize($obj->system->news_settings) : array();

	if($data['news_settings']['box_news'] == 1)
	{
		$where = array('active' => 1);
		if($obj->user->liveView) $where = array();
		if($data['news'] = $obj->news->list_news(array('where' => $where)))
		{
			if($medias = $obj->medias->list_medias(array('where' => array('module' => $module))))
			{
				$data['images'] = array();
				foreach($medias as $media)
				{
					$data['images'][$media['src_id']] = $media;
				}
			}
			if(is_file(APPPATH.'views/'.$obj->system->theme.'/modules/'.$module.'/views/blocks/box-news.php') && is_readable(APPPATH.'views/'.$obj->system->theme.'/modules/'.$module.'/views/blocks/box-news.php')) return $obj->load->view('../'.$obj->system->theme.'/modules/'.$module.'/views/blocks/box-news', $data);
			return $obj->load->view($module.'/blocks/box-news', $data);
		}
	}

}
