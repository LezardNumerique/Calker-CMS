<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Page
{
	var $tmppages;
	var $tmppagesrecursive;

	function Page() {
		$this->obj =& get_instance();
		$this->settings = isset($this->obj->system->pages_settings) ? unserialize($this->obj->system->pages_settings) : array();
	}

	public function get_pages($where = '')
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		$query = $this->obj->db->get($this->obj->config->item('table_pages'), 1);
		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}

	}

	public function list_pages($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'ordering ASC, id DESC',
			'limit' 		=> null,
			'start'			=> null,
			'where' 		=> null,
			'like' 			=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		$hash = md5(serialize($params));

		if(!$result = $this->obj->cache->get('list_pages'.$hash, 'pages'))
		{
			if (!is_null($params['like']))
			{
				$this->obj->db->like($params['like']);
			}
			if (!is_null($params['where']))
			{
				$this->obj->db->where($params['where']);
			}

			$this->obj->db->order_by($params['order_by']);

			if(!is_null($params['limit']) && !is_null($params['start']))
			{
				$this->obj->db->limit($params['limit'], $params['start']);
			}

			$this->obj->db->select($params['select']);
			$this->obj->db->from($this->obj->config->item('table_pages'));
			$query = $this->obj->db->get();

			if ($query->num_rows() == 0 )
			{
				$result =  false;
			}
			else
			{
				$results = $query->result_array();
				foreach ($results as $aresult)
				{
					$aresult['children'] = 0;
					$query = $this->obj->db->query("SELECT count('id') cnt FROM " . $this->obj->db->dbprefix($this->obj->config->item('table_pages')) . " WHERE parent_id = '".$aresult['id']."'");

					if($query->num_rows() > 0)
					{
						$row =  $query->row_array();
						$aresult['children'] = $row['cnt'];
					}
					$result[] = $aresult;
				}
			}

			if($this->obj->system->cache == 1) $this->obj->cache->save('list_pages'.$hash, $result, 'pages', 0);
		}

		return $result;

	}

	public function total_list_pages($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'id DESC',
			'limit' 		=> null,
			'start'			=> null,
			'where' 		=> null,
			'like' 			=> null,
			'or_where' 		=> null,
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		$hash = md5(serialize($params));

		if(!$total = $this->obj->cache->get('total_list_pages'.$hash, 'pages'))
		{
			if (!is_null($params['like']))
			{
				$this->obj->db->like($params['like']);
			}
			if (!is_null($params['where']))
			{
				$this->obj->db->where($params['where']);
			}
			if (!is_null($params['or_where']))
			{
				$this->obj->db->where($params['or_where']);
			}

			$this->obj->db->select('id');
			$this->obj->db->from($this->obj->config->item('table_pages'));
			$query = $this->obj->db->get();

			if ($query->num_rows() > 0)
			{
				$total =  $query->num_rows();
			}
			else
			{
				$total = 0;

			}

			if($this->obj->system->cache == 1) $this->obj->cache->save('total_list_pages'.$hash, $total, 'pages', 0);
		}

		return $total;
	}

	public function list_pages_recursive($parent = 0, $level = 0, $where = '')
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		$this->obj->db->where(array('parent_id' => $parent, 'lang' => $this->obj->user->lang));
		$this->obj->db->order_by('parent_id, ordering');
		$query = $this->obj->db->get($this->obj->config->item('table_pages'));
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$row['level'] = $level;
				$row['title'] = 'Pages => '.$row['title'];
				$this->tmppagesrecursive[] = $row;
				$this->list_pages_recursive($row['id'], $level+1, $where);
			}
		}
		return $this->tmppagesrecursive;
	}

}