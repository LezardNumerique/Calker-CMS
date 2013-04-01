<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Medias
{

	function Medias() {
		$this->obj =& get_instance();
	}

	public function get_medias($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$query = $this->obj->db->get($this->obj->config->item('table_medias'), 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row_array();
			$row['options'] = unserialize($row['options']);
			return $row;
		}
		else
		{
			return false;
		}

	}

	public function list_medias($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'id DESC',
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		$hash = md5(serialize($params));
		if(!$medias = $this->obj->cache->get('list_medias'.$hash, 'medias'))
		{
			if (!is_null($params['where']))
			{
				$this->obj->db->where($params['where']);
			}

			$this->obj->db->order_by($params['order_by']);

			$this->obj->db->select($params['select']);
			$this->obj->db->from($this->obj->config->item('table_medias'));
			$query = $this->obj->db->get();
			$medias = false;
			foreach ($query->result_array() as $row)
			{
				$row['options'] = unserialize($row['options']);
				$medias[] = $row;
			}
			$this->obj->cache->save('list_medias'.$hash, $medias, 'medias', 0);
		}
		return $medias;
	}

	public function delete_medias($medias_id = '')
	{
		if($media = $this->get_medias(array('id' => $medias_id)))
		{
			if(is_file('./'.$this->obj->config->item('medias_folder').'/images/'.$media['file']))
			{
				@unlink('./'.$this->obj->config->item('medias_folder').'/images/'.$media['file']);
			}
			$this->obj->db->delete($this->obj->config->item('table_medias'), array('id' => $medias_id));
		}
	}

	public function move_medias($src_id = '', $id = '', $direction = '', $module = '')
	{
		$query = $this->obj->db->get_where($this->obj->config->item('table_paragraphs'), array('id' => $id));

		$move = ($direction == 'up') ? -1 : 1;
		$this->obj->db->where(array('id' => $id));

		$this->obj->db->set('ordering', 'ordering+'.$move, FALSE);
		$this->obj->db->update($this->obj->config->item('table_medias'));

		$this->obj->db->where(array('id' => $id));
		$query = $this->obj->db->get($this->obj->config->item('table_medias'));
		$row = $query->row();
		$new_ordering = $row->ordering;

		if ($move > 0)
		{
			$this->obj->db->set('ordering', 'ordering-1', FALSE);
			$this->obj->db->where(array('ordering <=' => $new_ordering, 'src_id' => $src_id, 'id <>' => $id, 'module' => $module));
			$this->obj->db->update($this->obj->config->item('table_medias'));
		}
		else
		{
			$this->obj->db->set('ordering', 'ordering+1', FALSE);
			$where = array('ordering >=' => $new_ordering, 'src_id' => $src_id, 'id <>' => $id, 'module' => $module);

			$this->obj->db->where($where);
			$this->obj->db->update($this->obj->config->item('table_medias'));
		}

		//Reordinate
		$i = 0;
		$this->obj->db->order_by('ordering');
		$this->obj->db->where(array('src_id' => $src_id, 'module' => $module));
		$query = $this->obj->db->get($this->obj->config->item('table_medias'));
		if ($rows = $query->result())
		{
			foreach ($rows as $row)
			{
				$this->obj->db->set('ordering', $i);
				$this->obj->db->where('id', $row->id);
				$this->obj->db->update($this->obj->config->item('table_medias'));
				$i++;
			}
		}
	}
	
	//----- Medias types
	
	public function get_medias_types($where = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->obj->db->where($where);
		}

		$query = $this->obj->db->get($this->obj->config->item('table_medias_types'), 1);

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
	
	public function list_medias_types($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> $this->obj->config->item('table_medias_types_sizes').'.theme ASC, '.$this->obj->config->item('table_medias_types').'.medias_types_id ASC',
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		$hash = md5(serialize($params));
		if(!$medias_types = $this->obj->cache->get('list_medias_types_'.$hash, 'medias'))
		{
			if (!is_null($params['where']))
			{
				if($params['where'] != '') $this->obj->db->where($params['where']);
			}

			$this->obj->db->order_by($params['order_by']);

			$this->obj->db->select($params['select']);
			$this->obj->db->from($this->obj->config->item('table_medias_types'));
			$this->obj->db->join($this->obj->config->item('table_medias_types_sizes'), $this->obj->config->item('table_medias_types').'.medias_types_id = '.$this->obj->config->item('table_medias_types_sizes').'.medias_types_id');
			$query = $this->obj->db->get();
			$medias_types = array();
			foreach ($query->result_array() as $row)
			{
				$medias_types[] = $row;
			}
			$this->obj->cache->save('list_medias_types_'.$hash, $medias_types, 'medias', 0);
		}
		return $medias_types;
	}
	
	public function exists_types($fields)
	{
		$query = $this->obj->db->get_where($this->obj->config->item('table_medias_types'), $fields, 1, 0);

		if($query->num_rows() == 1)
			return TRUE;
		else
			return FALSE;
	}
	
	public function list_medias_types_sizes($params = array(), $group_theme = false)
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'medias_types_sizes_id DESC',
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		$hash = md5(serialize($params).$group_theme);
		if(!$medias_types_sizes = $this->obj->cache->get('list_medias_types_sizes_'.$hash, 'medias'))
		{
			if (!is_null($params['where']))
			{
				$this->obj->db->where($params['where']);
			}

			$this->obj->db->order_by($params['order_by']);

			$this->obj->db->select($params['select']);
			$this->obj->db->from($this->obj->config->item('table_medias_types_sizes'));
			$this->obj->db->join($this->obj->config->item('table_medias_types'), $this->obj->config->item('table_medias_types').'.medias_types_id = '.$this->obj->config->item('table_medias_types_sizes').'.medias_types_id');
			$query = $this->obj->db->get();
			$medias_types_sizes = array();
			foreach ($query->result_array() as $row)
			{
				if($group_theme)
					$medias_types_sizes[$row['theme']] = $row;
				else
					$medias_types_sizes[] = $row;
			}
			$this->obj->cache->save('list_medias_types_sizes_'.$hash, $medias_types_sizes, 'medias', 0);
		}
		return $medias_types_sizes;
	}
	
	public function get_medias_types_sizes($key = '')
	{
		$hash = md5(serialize($key));
		if(!$medias_types_sizes = $this->obj->cache->get('get_medias_types_sizes_'.$hash, 'medias'))
		{		
			$this->obj->db->select('*');
			$this->obj->db->where(array('theme' => $this->obj->system->theme, 'key' => $key));
			$this->obj->db->from($this->obj->config->item('table_medias_types'));
			$this->obj->db->join($this->obj->config->item('table_medias_types_sizes'), $this->obj->config->item('table_medias_types').'.medias_types_id = '.$this->obj->config->item('table_medias_types_sizes').'.medias_types_id');
			$query = $this->obj->db->get('', 1);
			$medias_types_sizes = array();
			if ($query->num_rows() == 1)			
				$medias_types_sizes = $query->row_array();			
			$this->obj->cache->save('get_medias_types_sizes_'.$hash, $medias_types_sizes, 'medias', 0);
		}
		return $medias_types_sizes;
	}
	
	
}