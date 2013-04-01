<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->set('sidebar_admin_catalog', 'sidebar_admin_catalog');

function sidebar_admin_catalog()
{
	$obj =& get_instance();
	$module = 'catalog';

	$css_products = '';
	if($obj->uri->segment(3) == 'products' || $obj->uri->segment(3) == 'productsCreate' || $obj->uri->segment(3) == 'productsEdit') $css_products = 'active';
	
	$css_manufacturers = '';
	if($obj->uri->segment(3) == 'manufacturers' || $obj->uri->segment(3) == 'manufacturersCreate' || $obj->uri->segment(3) == 'manufacturersEdit') $css_manufacturers = 'active';

	$css_specials = '';
	if($obj->uri->segment(3) == 'specials' || $obj->uri->segment(3) == 'specialsCreate' || $obj->uri->segment(3) == 'specialsEdit') $css_specials = 'active';

	$css_attributes = '';
	if($obj->uri->segment(3) == 'attributes' || $obj->uri->segment(3) == 'attributesCreate' || $obj->uri->segment(3) == 'attributesEdit' || $obj->uri->segment(3) == 'attributesValuesCreate' || $obj->uri->segment(3) == 'attributesValuesEdit') $css_attributes = 'active';
	
	$css_settings = '';
	if($obj->uri->segment(2) == $module && $obj->uri->segment(3) == 'settings') $css_settings = 'active';

	$css_import = '';
	if($obj->uri->segment(2) == $module && $obj->uri->segment(3) == 'productsImport') $css_import = 'active';	

	echo '
		<ul class="navigation_sub">
			<li class="'.$css_products.'"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/products').'">'.$obj->lang->line('btn_products').'</a></li>
			<li class="'.$css_manufacturers.'"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/manufacturers').'">'.$obj->lang->line('btn_manufacturers').'</a></li>
			<li class="'.$css_specials.'"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/specials').'">'.$obj->lang->line('btn_specials').'</a></li>
			<!--<li class="'.$css_attributes.'"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/attributes').'">'.$obj->lang->line('btn_attributes').'</a></li>-->
			<li class="'.$css_import.'"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/productsImport').'">'.$obj->lang->line('btn_import').'</a></li>
			<li class="'.$css_settings.' last"><a href="'.site_url($obj->config->item('admin_folder').'/'.$module.'/settings').'">'.$obj->lang->line('btn_settings').'</a></li>
		</ul>
	';

}

$this->set('menu_front_catalog', 'menu_front_catalog');

function menu_front_catalog($treeview = true)
{
	$obj =& get_instance();

	$html = get(array('parent_id' => 0, 'active' => 1, 'lang' => $obj->user->lang), $treeview);

	return $html;

}

function get($where = null, $treeview = true)
{
	$obj =& get_instance();

	$obj->nav_catalog_html = '';

	$hash = $obj->user->lang;

	$obj->db->where($where);
	$query = $obj->db->get('categories');
	if ($query->num_rows() > 0 )
	{
		$row = $query->row_array();
		$parent_id = $row['id'];
	}
	else
	{
		$parent_id = 0;
	}

	if(isset($row) && $row && isset($parent_id) && $parent_id) _get($row, $parent_id, 0, $treeview);

	return $obj->nav_catalog_html;
}

function _get($row = array(), $parent = 0, $level = 0, $treeview = true)
{
	$obj =& get_instance();

	$obj->db->where(array('parent_id' => $parent, 'lang' => $obj->user->lang, 'active' => 1));
	$obj->db->order_by('parent_id, ordering');
	$query = $obj->db->get('categories');
	if ($query->num_rows() > 0 )
	{
		//if($level > 0) $obj->nav_catalog_html .= '<ul>';
		$obj->nav_catalog_html .= '<ul>';
		$i = 1;
		foreach ($query->result_array() as $row)
		{
			$css = '';
			if($i == 1 && $level == 0) $css = 'begin';
			$obj->nav_catalog_html .= '<li class="'.$css.'">';
			$obj->nav_catalog_html .= '<a href="'.site_url('catalog/categories/'.$row['id'].'/'.$row['uri'].'/index'.$obj->config->item('url_suffix_ext')).'">'.html_entity_decode($row['title']).'</a>';
			if($treeview) _get($row, $row['id'], $level+1, $treeview);
			$obj->nav_catalog_html .= '</li>';
			$i++;

		}
		//if($level > 0) $obj->nav_catalog_html .= '</ul>';
		 $obj->nav_catalog_html .= '</ul>';
	}
}

$this->set('box_search_catalog', 'box_search_catalog');

function box_search_catalog()
{
	$obj =& get_instance();
	return $obj->load->view('../../catalog/views/blocks/search.php');

}

$this->set('box_featured_categories', 'box_featured_categories');

function box_featured_categories()
{
	$obj =& get_instance();

	$data['categories'] = array();

	$obj->db->where(array('parent_id' => 1, 'lang' => $obj->user->lang, 'active' => 1, 'is_home' => 1, 'module' => 'categories'));
	$obj->db->order_by('categories.ordering');
	$obj->db->from('categories');
	$obj->db->join('medias', 'categories.id = medias.src_id');
	$query = $obj->db->get();

	if ($query->num_rows() > 0 )
	{
		foreach ($query->result_array() as $row)
		{
			$data['categories'][] = $row;

		}
	}

	return $obj->load->view('../../catalog/views/blocks/featured-categories.php', $data);


}
