<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->set('sidebar_admin_portfolio', 'sidebar_admin_portfolio');

function sidebar_admin_portfolio()
{
	$obj =& get_instance();
	$module = 'portfolio';

	$css_medias = '';
	if($obj->uri->segment(3) == 'medias' || $obj->uri->segment(3) == 'mediasCreate' || $obj->uri->segment(3) == 'mediasEdit') $css_medias = ' class="active"';

	$css_settings = '';
	if($obj->uri->segment(2) == $module && $obj->uri->segment(3) == 'settings') $css_settings = 'active';

	echo '
		<ul class="navigation_sub">
			<li class="'.$css_medias.'"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/medias').'">'.$obj->lang->line('btn_medias').'</a></li>
			<li class="'.$css_settings.' last"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/settings').'">'.$obj->lang->line('btn_settings').'</a></li>
		</ul>
	';

}

$this->set('sidebar_front_portfolio', 'sidebar_front_portfolio');

function sidebar_front_portfolio($parent_id = 0)
{
	$obj =& get_instance();
	$module = 'portfolio';
	$obj->config->load($module.'/config');
	$portfolio_settings = isset($obj->system->portfolio_settings) ? unserialize($obj->system->portfolio_settings) : array();

	if (!$rows = $obj->cache->get('getTree_'.$module.'_'.$parent_id.'_'.$obj->user->lang, 'navigation'))
	{
		$rows = _sidebar_front_portfolio(1, 1, $parent_id);
		if($obj->system->cache == 1) $obj->cache->save('getTree_'.$module.'_'.$parent_id.'_'.$obj->user->lang, $rows, 'navigation', 0);
	}

	return $rows;
}

function _sidebar_front_portfolio($parent = 1, $level = 1, $parent_id = 0)
{
	$obj =& get_instance();
	$module = 'portfolio';
	$obj->config->load($module.'/config');
	$portfolio_settings = isset($obj->system->portfolio_settings) ? unserialize($obj->system->portfolio_settings) : array();

	$obj->db->where(array('active' => 1, 'parent_id' => $parent, 'lang' => $obj->user->lang));
	$obj->db->order_by('parent_id, ordering');
	$obj->db->from($obj->config->item('table_portfolio_categories'));
	$obj->db->join($obj->config->item('table_portfolio_categories_lang'), $obj->config->item('table_portfolio_categories_lang').'.categories_id = '.$obj->config->item('table_portfolio_categories').'.id');
	$query = $obj->db->get();
	if ($query->num_rows() > 0 )
	{
		foreach ($query->result_array() as $row)
		{
			$obj->nav_portfolio_html[] = array(
				'level' 		=> $level,
				'children' 		=> '',
				'title' 		=> html_entity_decode($row['title']),
				'module' 		=> 'portfolio',
				'active' 		=> $row['active'],
				'parent_id' 	=> ($parent_id ? $parent_id : $module.'-'._get_parent_id($row['parent_id'])),
				'id' 			=> $module.'-'.$row['id'],
				'uri' 			=> $module.'/'.$row['uri'].'/'.$row['id'].'/'
			);
			_sidebar_front_portfolio($row['id'], $level+1);
		}

		if(isset($obj->nav_portfolio_html)) return $obj->nav_portfolio_html;
	}
}

function _get_parent_id ($id = '')
{
	$obj =& get_instance();
	$obj->db->select('id');
	$obj->db->where(array('id' => $id));
	$query = $obj->db->get('portfolio_categories', 1);
	if ($query->num_rows() > 0)
	{
		$row = $query->row_array();
		return $row['id'];
	}
}

$this->set('box_portfolio', 'box_portfolio');

function box_portfolio($limit = '3')
{
	$data['module'] = 'portfolio';
	$obj =& get_instance();
	$obj->load->library($data['module'].'/portfolios', '', 'portfolio');
	$obj->config->load($data['module'].'/config');
	$portfolio_settings = isset($obj->system->portfolio_settings) ? unserialize($obj->system->portfolio_settings) : array();

	if($portfolio_settings['box_portfolio'] == 1)
	{
		if($data['medias'] = $obj->portfolio->list_medias(array('select' => 'categories_id_default, file, '.$obj->config->item('table_portfolio_medias_lang').'.title, '.$obj->config->item('table_portfolio_categories_lang').'.uri as cURI', 'order_by' => 'rand()', 'limit' => $limit, 'where' => array($obj->config->item('table_portfolio_categories_lang').'.categories_id !=' => 1, $obj->config->item('table_portfolio_medias').'.active' => 1, 'is_box' => 1))))
		{
			//pre_affiche($data['medias']);
			if(is_file(APPPATH.'views/'.$obj->system->theme.'/modules/'.$data['module'].'/views/blocks/box-portfolio.php') && is_readable(APPPATH.'views/'.$obj->system->theme.'/modules/'.$data['module'].'/views/blocks/box-portfolio.php')) return $obj->load->view('../'.$obj->system->theme.'/modules/'.$data['module'].'/views/blocks/box-portfolio', $data);
			else return $obj->load->view($data['module'].'/blocks/box-portfolio', $data);
		}
	}
}
