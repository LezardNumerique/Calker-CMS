<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

	class System {

		var $version ;
		var $revision;
		var $modules;
		var $obj;

		public function System()
		{
			$this->obj =& get_instance();
			$this->obj->load->helper('text');
			$this->obj->load->library('cache', array('dir' => './'.$this->obj->config->item('cache_folder').'/'));
			if ($this->obj->uri->segment(1) != 'install')
			{
				if(is_dir('./'.APPPATH.'modules/install'))
					redirect('install');
				$this->list_settings();
				$this->find_modules();
			}
			if($this->obj->uri->segment(1) == 'install' && !is_dir('./'.APPPATH.'modules/install'))
				redirect('');

		}

		public function find_modules($return = false)
		{
			if (!$modules = $this->obj->cache->get('list_modules', 'system'))
			{
				$this->obj->db->where('active', 1);
				$this->obj->db->order_by('ordering');
				$query = $this->obj->db->get($this->obj->config->item('table_modules'));
				foreach ($query->result_array() as $row)
				{
					$modules[$row['name']] = $row;
				}
				$this->obj->cache->save('list_modules', $modules, 'system', 0);
			}
			$this->modules = $modules;
			if($return) return $this->modules;
		}

		public function list_modules()
		{
			$this->obj->load->helper('file');

			if($modules = $this->find_modules(true))
			{
				//$modules['default'] = array();
				return $modules;
			}

		}

		public function list_settings()
		{
			if(!$settings = $this->obj->cache->get($this->obj->config->item('table_settings'), 'settings'))
			{
				$query = $this->obj->db->get($this->obj->config->item('table_settings'));
				$settings = $query->result();
				$this->obj->cache->save('settings', $settings, 'settings', 0);
			}
			if (!empty($settings))
			{
			   foreach ($settings as $row)
			   {
			      $this->{$row->name} = $row->value;
			   }
			}

		}

		public function set_settings($name, $value)
		{
			if (!isset($this->$name)) {
				$this->$name = $value;
				$this->obj->db->insert($this->obj->config->item('table_settings'), array('name' => $name, 'value' => $value));
				$this->obj->cache->remove('settings', 'settings');
			}
			elseif ($this->$name != $value)
			{
				$this->$name = $value;
				$this->obj->db->update($this->obj->config->item('table_settings'), array('value' => $value), "name = '".$name."'");
				$this->obj->cache->remove('settings', 'settings');
			}
		}

		public function is_module_installed($module)
		{
			$query = $this->obj->db->get_where($this->obj->config->item('table_modules'), array('name' => $module), 1);
			if ($query->num_rows() > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function is_module_actived($module)
		{
			$query = $this->obj->db->get_where('modules', array('name' => $module, 'active' => 1), 1);
			if ($query->num_rows() > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function set($name, $value)
		{
			if (!isset($this->$name)) {
				$this->$name = $value;
				$this->obj->db->insert('settings', array('name' => $name, 'value' => $value));
				$this->obj->cache->remove('settings', 'settings');
			}
			elseif ($this->$name != $value)
			{
				$this->$name = $value;
				$this->obj->db->update('settings', array('value' => $value), 'name = "'.$name.'"');
				$this->obj->cache->remove('settings', 'settings');
			}
		}

		public function flag ($id, $flag, $table, $field)
		{
			if ($flag == '1') {
				return $query = $this->obj->db->query("update ".$this->obj->db->dbprefix($table)." set ".$field." = '0' where id = '".(int)$id."'");
			}
			elseif ($flag == '0') {
				return $query = $this->obj->db->query("update ".$this->obj->db->dbprefix($table)." set ".$field." = '1' where id = '".(int)$id."'");
			}
			else {
				return -1;
			}

		}

		public function exists($fields, $table)
		{
			$query = $this->obj->db->get_where($table, $fields, 1, 0);

			if($query->num_rows() == 1)
				return TRUE;
			else
				return FALSE;
		}

		public function clear_cache()
		{
			$this->obj->load->helper('file');
			$dir = './'.$this->obj->config->item('cache_folder');
			if ($handle = opendir($dir))
			{
				delete_files($dir, TRUE, $level = 0)	;
			}

			//Css
			$this->obj->load->helper('file');

			//Delete image cache
			$dir = APPPATH.'views/'.$this->obj->config->item('theme_admin').'/css/.'.$this->obj->config->item('cache_folder');

			$handle = opendir($dir);

			if ($handle)
			{
				while ( false !== ($css_file = readdir($handle)) )
				{
					if (($css_file != '.') && ($css_file != '..'))
					{
						if(is_file($dir.'/'.$css_file) && is_readable($dir.'/'.$css_file)) unlink($dir.'/'.$css_file);
					}
				}
			}

		}

		public function utf8()
		{
			$tables = $this->obj->db->list_tables();
            		foreach ($tables as $table)
			{
			    $this->obj->db->query('ALTER TABLE '.$table.' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
			}
		}

		public function optimize()
		{
			$this->load->dbutil();
			$tables = $this->db->list_tables();
            foreach ($tables as $table)
			{
			   	$this->dbutil->optimize_table($table);
			}
		}

		public function repair()
		{
			$this->load->dbutil();
			$tables = $this->db->list_tables();
            foreach ($tables as $table)
			{
			   	$this->dbutil->repair_table($table);
			}
		}

		public function purge()
		{
			$this->obj->load->helper('file');

			//---- On purge les images
			$dir = './'.$this->obj->config->item('medias_folder').'/images';
			$dir_cache = '/'.$this->obj->config->item('cache_folder');
			$handle = opendir($dir);
			if ($handle)
			{
				while (false !== ($image_file = readdir($handle)))
				{

					if (($image_file != '.') && ($image_file != '..') && ($image_file != '.'.$this->obj->config->item('cache_folder')) && ($image_file != 'index.html') && ($image_file != 'default.jpg'))
					{
						$this->obj->db->select('file');
						$this->obj->db->where('file', $image_file);
						$query = $this->obj->db->get($this->obj->config->item('table_medias'), 1);

						if ($query->num_rows() == 0)
							if(is_file($dir.'/'.$image_file)) unlink($dir.'/'.$image_file);

					}
				}
			}
			$this->obj->cache->remove_group('images_list');
			if (is_dir($dir.$dir_cache))
				$this->rmdir_recursive($dir.$dir_cache);

			//---- On purge les images du captcha
			$dir = './'.$this->obj->config->item('medias_folder').'/captcha';
			$handle = opendir($dir);
			if ($handle)
			{
				while (false !== ($image_file = readdir($handle)))
				{

					if (($image_file != '.') && ($image_file != '..') && ($image_file != 'index.html'))
					{
						if(is_file($dir.'/'.$image_file)) unlink($dir.'/'.$image_file);
					}
				}
			}
			$this->obj->db->query('TRUNCATE TABLE '.$this->obj->db->dbprefix($this->obj->config->item('table_captcha')));

		}

		public function chmod()
		{
			$this->recursiveChmod('./'.$this->obj->config->item('medias_folder'), 0755, 0777);
			$this->recursiveChmod('./'.$this->obj->config->item('cache_folder'), 0644, 0777);
			@chmod('./'.$this->obj->config->item('backup_folder'), 0777);
			@chmod(APPPATH.'logs', 0777);
			$this->recursiveChmod(APPPATH.'language', 0777, 0777);
			if($modules = $this->list_modules())
			{
				foreach($modules as $module => $row)
				{
					$this->recursiveChmod(APPPATH.'modules/'.$module.'/language', 0777, 0777);
					if(is_readable(APPPATH.'modules/'.$module.'/css/.cache')) @chmod(APPPATH.'modules/'.$module.'/css/.cache', 0777);
				}
			}
			@chmod(APPPATH.'views/'.$this->obj->system->theme.'/img', 0777);
			@chmod(APPPATH.'views/'.$this->obj->system->theme.'/css/.cache', 0777);
		}

		public function recursiveChmod($path, $filePerm = 0644, $dirPerm = 0755)
		{
			if(!file_exists($path))
			{
				return false;
			}
			if(is_file($path))
			{
				@chmod($path, $filePerm);
			}
			elseif(is_dir($path))
			{
				$foldersAndFiles = scandir($path);
				$entries = array_slice($foldersAndFiles, 2);
				foreach($entries as $entry)
				{
					$this->recursiveChmod($path."/".$entry, $filePerm, $dirPerm);
				}
				@chmod($path, $dirPerm);
			}
			return true;
		}

		public function cache_size()
		{
			$cache_size_folder_cache = recursive_directory_size($this->obj->config->item('cache_folder'), FALSE);
			$cache_size_css = recursive_directory_size(APPPATH.'views/'.$this->obj->config->item('theme_admin').'/css/.cache', FALSE);

			$cache_size = $cache_size_folder_cache+$cache_size_css;

			return formatfilesize($cache_size);
		}

		public function get_uri($uri = '')
		{			
			$uri = ((substr($uri, 0, 7) == "http://") || (substr($uri, 0, 8) == "https://") || (substr($uri, 0, 6) == "ftp://") || (substr($uri, 0, 7) == "mailto:")) ? $uri : site_url($this->obj->language->get_uri_language().$uri);
			return $uri;
		}

		public function get_first_segment_uri($uri = '')
		{
			$uri = str_replace(site_url(), '', $uri);
			$uri = str_replace($this->obj->language->get_uri_language(), '', $uri);
			$uri = str_replace('/pages/index', '', $uri);			
			$uri = explode('/', $uri);						
			if(isset($uri[0]) && $uri[0]) return $uri[0];
		}

		public function set_session_brut_force()
		{
			$this->obj->session->set_userdata('brut_force', $this->obj->session->userdata('brut_force')+1);
		}

		public function set_cookie_brut_force()
		{
			$cookie = array(
				'name'   => 'bf',
				'value'  => get_cookie('ccmsbf')+1,
				'expire' => '86500',
				'domain' => '',
				'path'   => '/',
				'prefix' => '',
				'secure' => FALSE
			);

			$this->obj->input->set_cookie($cookie);
		}

	}