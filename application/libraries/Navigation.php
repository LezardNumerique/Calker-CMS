<?php

class Navigation {

	var $nav_html;

	function Navigation()
	{
		$this->obj =& get_instance();
	}

	public function get_navigation($where = '')
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		$query = $this->obj->db->get($this->obj->config->item('table_navigation'));
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	public function list_navigation($parent = 0, $level = 0, $treeview = true, $is_active = false, $where = false)
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			$this->obj->db->where(array('parent_id' => $parent));
		}
		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		if($is_active) $this->obj->db->where(array('active' => 1));
		$this->obj->db->order_by('parent_id, ordering');
		$query = $this->obj->db->get($this->obj->config->item('table_navigation'));

		foreach ($query->result_array() as $row)
		{
			$this->nav[] = array(
				'level' 		=> $level,
				'children' 		=> $this->as_children(array('parent_id' => $row['id'])),
				'title' 		=> $row['title'],
				'module' 		=> $row['module'],
				'active' 		=> $row['active'],
				'parent_id' 	=> $row['parent_id'],
				'id' 			=> $row['id'],
				'uri' 			=> $row['uri']
			);

			if($treeview) $this->list_navigation($row['id'], $level+1, $treeview, $is_active);
		}

		if(isset($this->nav)) return $this->nav;
		else return ' ';

	}

	public function as_children($where)
	{
		$this->obj->db->select('*');

		if(is_array($where))
		{
			foreach($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		$this->obj->db->where('lang', $this->obj->user->lang);

		$query = $this->obj->db->get($this->obj->config->item('table_navigation'));

		if ( $query->num_rows() > 0 )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function _get_parent($id)
	{
		return $navigation = $this->get_navigation(array('id' => $id));
	}

	public function get_parent_recursive($id)
	{
		$bc = false;

		if($parent = $this->_get_parent($id))
		{
			$bc[] = array(
				'title'	=> html_entity_decode($parent['title']),
				'id'	=> $parent['id']
			);

			while($parent = $this->_get_parent($parent['parent_id']))
			{
				$bc[] = array(
					'title'	=> html_entity_decode($parent['title']),
					'id'	=> $parent['id']
				);
			}
		}

		return $bc;
	}

	public function getTree($id_selected = '')
	{
		if (!$rows = $this->obj->cache->get('getTree_'.$id_selected.'_'.$this->obj->user->lang, 'navigation'))
		{
			$rows = $this->list_navigation(0, 0, true, true, array('id' => $id_selected));
			if($this->obj->system->cache == 1) $this->obj->cache->save('getTree_'.$id_selected.'_'.$this->obj->user->lang, $rows, 'navigation', 0);
		}

		$modules = array();
		if($rows && is_array($rows))
			foreach($rows as $result)
				if($result['module'])
					if ($modules = $this->obj->block->get('sidebar_front_'.$result['module'], $result['id']))
						foreach($modules as $module)
							$rows[] = $module;

		if(sizeof($rows) > 0 && is_array($rows))
		{
			$resultParents = array();
			foreach($rows as $row)
			{
				$resultParents[$row['parent_id']][] = $row;
				$resultIds[$row['id']] = $row;
			}
			return $this->_getTree($id_selected, $resultParents, $resultIds, 3);
		}
	}

	private function _getTree($id_selected, $resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
	{
		//------------ Prestashop 2007-2012 PrestaShop modules/blockcategories.php
		if (is_null($id_category))
			$id_category = $id_selected;

		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				if(!$children[] = $this->_getTree($id_selected, $resultParents, $resultIds, $maxDepth, $subcat['id'], $currentDepth + 1))

		if (!isset($resultIds[$id_category]))
			return false;
		if(isset($resultIds[$id_category])) $return = array('id' => $id_category, 'uri' => $resultIds[$id_category]['uri'], 'active' => $resultIds[$id_category]['active'],
					 'title' => $resultIds[$id_category]['title'],
					 'children' => $children, 'module' => $resultIds[$id_category]['module']);
		else $return = false;
		return $return;
	}

}
