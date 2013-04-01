<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Language {

		var $codes;
		var $default;

		function Language ()
		{
			$this->obj =& get_instance();

			$module = str_replace('../modules/', '', $this->obj->router->directory);
			$module = explode('/', $module);

			if (!$this->default = $this->obj->cache->get('get_default_language', 'languages'))
			{
				$this->default = $this->get_default();
				if($this->obj->system->cache == 1) $this->obj->cache->save('get_default_language', $this->default, 'languages', 0);
			}			

			if (!$this->obj->session->userdata('lang')) {
				$this->obj->session->set_userdata('lang', $this->default);
			}			

			if (!$this->codes = $this->obj->cache->get('list_codes', 'languages'))
			{
				$this->codes = $this->list_codes();
				if($this->obj->system->cache == 1) $this->obj->cache->save('list_codes', $this->codes, 'languages', 0);
			}
			
			if($this->obj->uri->segment(1) != $this->obj->config->item('admin_folder'))
				$this->get_uri_language();

			if (!in_array($module[0], $this->codes))
			{
				$this->load($module[0], $this->obj->session->userdata('lang'));
			}
					

		}
		
		public function get_uri_language()
		{
			$lang = $this->obj->uri->segment(1);		
			
			if(!$lang)
				return false;
				
			if(strlen($lang) != 2)
				return false;			
				
			if(in_array($lang, $this->codes))
			{
				$this->obj->session->set_userdata('lang', $lang);				
				return $lang.'/';
			}
			else
			{
				return false;
			}		
		}	

		public function load($module = '', $code = 'fr')
		{
			$this->obj->lang->load('default', $code);
			$this->obj->lang->load($module, $code);
		}

		public function list_codes()
		{
			$this->obj->db->select('code');
			$this->obj->db->where('active', 1);
			$this->obj->db->order_by('ordering');
			$query = $this->obj->db->get($this->obj->config->item('table_languages'));
			$codes = array();

			if ( $query->num_rows() > 0 )
			{
				foreach ( $query->result() as $row )
				{
					$codes[] = $row->code;
				}
			}
			return $codes;
		}

		public function list_languages($cache = true)
		{
			if($cache)
			{
				if (!$data = $this->obj->cache->get('list_language', 'languages') )
				{
					$data = $this->_list_languages(array('active' => 1));
					if($this->obj->system->cache == 1) $this->obj->cache->save('list_language', $data, 'languages', 0);
				}
			}
			else
			{
				$data = $this->_list_languages('');
			}

			return $data;

		}

		private function _list_languages($where = '')
		{
			$this->obj->db->select('*');

			if ( is_array($where) )
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

			$this->obj->db->order_by('ordering');
			$query = $this->obj->db->get($this->obj->config->item('table_languages'));
			$this->tmplistlanguages = '';

			if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row) {
					$this->tmplistlanguages[] = $row;
				}

				return $this->tmplistlanguages;
			}
			else
			{
				return false;
			}
		}

		public function get_languages($where = '')
		{
			$this->obj->db->select('*');

			if ( is_array($where) )
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

			$query = $this->obj->db->get($this->obj->config->item('table_languages'), 1);

			if ( $query->num_rows() == 1 )
			{
				$row = $query->row_array();
				return $row;
			}
			else
			{
				return false;
			}

		}

		public function get_default()
		{
			$this->obj->db->select('code');
			$this->obj->db->where('default', 1);
			$this->obj->db->limit(1);
			$query = $this->obj->db->get($this->obj->config->item('table_languages'));

			if ($query->num_rows() == 1)
			{
				$row = $query->row();
				return $row->code ;
			}
			else
			{
				return 'fr';
			}

		}

		public function exists($fields)
		{
			$query = $this->obj->db->get_where($this->obj->config->item('table_languages'), $fields, 1, 0);

			if($query->num_rows() == 1)
				return TRUE;
			else
				return FALSE;
		}
	}