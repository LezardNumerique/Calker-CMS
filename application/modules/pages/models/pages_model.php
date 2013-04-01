<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class pages_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function list_parent_recursive($id)
	{
		$bc = false;

		if($parent = $this->list_parent($id))
		{
			$bc[] = array(
				'title'	=> html_entity_decode($parent['title']),
				'id'	=> $parent['id']
			);

			while($parent = $this->list_parent($parent['parent_id']))
			{
				$bc[] = array(
					'title'	=> html_entity_decode($parent['title']),
					'id'	=> $parent['id']
				);
			}
		}
		return $bc;
	}

	public function list_parent($id)
	{
		$page = $this->page->get_pages(array('id' => $id));
		return $page;
	}

	public function save($pages_id = '', $data = array())
	{
		if($pages_id && $pages_id != '')
		{
			$this->db->where(array('id' => $pages_id))->update($this->config->item('table_pages'), $data);
		}
		else
		{
			$this->db->insert($this->config->item('table_pages'), $data);
			$pages_id = $this->db->insert_id();
		}
		if($this->system->cache == 1) $this->cache->remove_group('pages');

		return $pages_id;
	}

	public function move($pages_id = '', $direction = '')
	{
		$query = $this->db->get_where($this->config->item('table_pages'), array('id' => $pages_id));

		if ($row = $query->row())
		{
			$parent_id = $row->parent_id;
		}
		else
		{
			$parent_id = 0;
		}

		$move = ($direction == 'up') ? -1 : 1;
		$this->db->where(array('id' => $pages_id));

		$this->db->set('ordering', 'ordering+'.$move, FALSE);
		$this->db->update($this->config->item('table_pages'));

		$this->db->where(array('id' => $pages_id));
		$query = $this->db->get($this->config->item('table_pages'));
		$row = $query->row();
		$new_ordering = $row->ordering;

		if ($move > 0)
		{
			$this->db->set('ordering', 'ordering-1', FALSE);
			$this->db->where(array('ordering <=' => $new_ordering, 'id <>' => $pages_id, 'parent_id' => $parent_id, 'lang' => $this->user->lang));
			$this->db->update($this->config->item('table_pages'));
		}
		else
		{
			$this->db->set('ordering', 'ordering+1', FALSE);
			$where = array('ordering >=' => $new_ordering, 'id <>' => $pages_id, 'parent_id' => $parent_id, 'lang' => $this->user->lang);

			$this->db->where($where);
			$this->db->update($this->config->item('table_pages'));
		}
		//Reordinate
		$i = 0;
		$this->db->order_by('ordering');
		$this->db->where(array('parent_id' => $parent_id, 'lang' => $this->user->lang));
		$query = $this->db->get($this->config->item('table_pages'));
		if ($rows = $query->result())
		{
			foreach ($rows as $row)
			{
				$this->db->set('ordering', $i);
				$this->db->where('id', $row->id);
				$this->db->update($this->config->item('table_pages'));
				$i++;
			}
		}
	}

	public function delete($pages_id = '')
	{
		if($pages_childrens = $this->page->list_pages_recursive($pages_id))
		{
			foreach($pages_childrens as $page_children)
			{
				$this->paragraph->delete_recursive($page_children['id'], $this->template['module']);
				$this->db->delete($this->config->item('table_pages'), array('id' => $page_children['id']));
			}
		}

		$this->paragraph->delete_recursive($pages_id, $this->template['module']);
		$this->db->delete($this->config->item('table_pages'), array('id' => $pages_id));
	}

	function get_sub_pages($id, $limit = null)
	{
		$this->db->order_by('ordering');
		$this->db->where('parent_id', $id);
		$this->db->where('active', 1);
		$this->db->where('lang', $this->user->lang);
		$query = $this->db->get($this->config->item('table_pages'), $limit);
		return $query->result_array();
	}

	function get_next_page(&$page)
	{
		$this->db->where('active', 1);
		$this->db->where('parent_id', $page['parent_id']);
		$this->db->where('parent_id <> ', '0');
		$this->db->where('lang', $this->user->lang);
		$this->db->order_by('ordering');

		$query = $this->db->get($this->config->item('table_pages'));

		if($familypages = $query->result_array())
		{
			foreach($familypages as $key=>$val) {
				if($val['id'] == $page['id']) {
					$id = $key;
				}
			}
			if(($id - 1) >= 0) {
				$page['previous_page'] = $familypages[$id - 1];
			}
			if (($id + 1) < count( $familypages )) {
				$page['next_page'] = $familypages[$id + 1];
			}
		}
	}

}
