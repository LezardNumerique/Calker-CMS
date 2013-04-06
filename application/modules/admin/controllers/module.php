<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Module extends CI_Controller {

		public function __construct()
		{
			parent::__construct();
			$this->config->load('config');
			$this->load->library('administration');
			$this->load->model('admin_model', 'model');
			$this->template['module'] = 'admin';

		}

		public function index()
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);

			$this->load->helper('xml');

			if (!$modules = $this->cache->get('list_modules', 'modules'))
			{
				if (!$modules = $this->model->list_modules()) $modules = array();
				if($this->system->cache == 1) $this->cache->save('list_modules', $modules, 'modules', 0);
			}

			unset($module);
			$handle = opendir(APPPATH.'modules');

			if ($handle)
			{
				while (false !== ($module = readdir($handle)))
				{
					if ( (substr($module, 0, 1) != ".") && file_exists(APPPATH.'modules/'.$module.'/setup.xml') )
					{
						if ( !isset($modules[$module]))
						{
							$modules[$module] = array(
								'name' 			=> $module,
								'active' 		=> -1,
								'description' 	=> null,
								'version' 		=> null
							);
						}
						else
						{
							$xmldata = join('', file(APPPATH.'modules/'.$module.'/setup.xml'));
							$xmlarray = xmlize($xmldata);
							if (isset($xmlarray['module']['#']['name'][0]['#']) && trim($xmlarray['module']['#']['name'][0]['#']) == $module)
							{
								$modules[$module]['nversion'] = isset($xmlarray['module']['#']['version'][0]['#']) ? trim($xmlarray['module']['#']['version'][0]['#']) : '';
							}
						}
					}

				}
			}
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'tablesorter', 'tooltip', 'sitelib'));
			$this->template['modules'] = $modules;
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'module/index');

		}

		public function activate($module = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);
			if (is_null($module))
			{
				$this->session->set_flashdata('notification', $this->lang->line('alert_module_not_found'));
				redirect('admin/module');
			}
			$data = array('active' => 1);
			$this->db->where(array('name'=> $module, 'ordering >=' => 100));
			$this->db->update('modules', $data);
			if($this->system->cache == 1) $this->cache->remove_group('modules');
			if($this->system->cache == 1) $this->cache->remove_group('system');
			$this->session->set_flashdata('notification', $this->lang->line('notification_module_activate'));
			redirect($this->config->item('admin_folder').'/module');
		}

		public function desactivate($module = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);
			if (is_null($module))
			{
				$this->session->set_flashdata('notification', $this->lang->line('alert_module_not_found'));
				redirect($this->config->item('admin_folder').'/module');
			}
			$data = array('active' => 0);
			$this->db->where(array('name'=> $module, 'ordering >=' => 100));
			$this->db->update('modules', $data);
			if($this->system->cache == 1) $this->cache->remove_group('modules');
			if($this->system->cache == 1) $this->cache->remove_group('system');
			$this->session->set_flashdata('notification', $this->lang->line('notification_module_desactivate'));
			redirect($this->config->item('admin_folder').'/module');
		}

		public function move($direction = null, $module = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);
			if (is_null($module) || is_null($direction))
			{
				redirect('admin/module');
			}

			$move = ($direction == 'up') ? -1 : 1;
			$this->db->where(array('name' => $module, 'ordering >=' => 100));
			$this->db->set('ordering', 'ordering+'.$move, FALSE);
			$this->db->update('modules');

			$this->db->where(array('name' => $module, 'ordering >=' => 100));
			$query = $this->db->get('modules');
			$row = $query->row();
			$new_ordering = $row->ordering;

			if ($move > 0)
			{
				$this->db->set('ordering', 'ordering-1', FALSE);
				$this->db->where(array('ordering <=' => $new_ordering, 'name <>' => $module));
				$this->db->update('modules');
			}
			else
			{
				$this->db->set('ordering', 'ordering+1', FALSE);
				$this->db->where(array('ordering >=' => $new_ordering, 'name <>' => $module));
				$this->db->update('modules');
			}

			$i = 101;
			$this->db->order_by('ordering');
			$this->db->where(array('ordering >=' => 100));
			$query = $this->db->get('modules');
			if ($rows = $query->result())
			{
				foreach ($rows as $row)
				{
					$this->db->set('ordering', $i);
					$this->db->where('name', $row->name);
					$this->db->update('modules');
					$i++;
				}
			}
			if($this->system->cache == 1) $this->cache->remove_group('modules');
			if($this->system->cache == 1) $this->cache->remove_group('system');

			redirect($this->config->item('admin_folder').'/module');
		}

		public function update($module = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);
			if (is_null($module))
			{
				$this->session->set_flashdata('notification', $this->lang->line('alert_module_not_found'));
				redirect($this->config->item('admin_folder').'/module');
			}
			if (is_readable(APPPATH.'modules/'.$module.'/' . $module .'_update.php'))
			{
				if($this->system->cache == 1) $this->cache->remove_group('modules');
				if($this->system->cache == 1) $this->cache->remove_group('system');
				include( APPPATH.'modules/'.$module.'/' . $module .'_update.php' );				
			}			
			$this->session->set_flashdata('notification', $this->lang->line('notification_module_update'));
			redirect($this->config->item('admin_folder').'/module');

		}

		public function uninstall($module = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);
			if (is_null($module))
			{
				$this->session->set_flashdata('notification', $this->lang->line('alert_module_not_found'));
				redirect($this->config->item('admin_folder').'/module');
			}

			if (is_file(APPPATH.'modules/'.$module.'/'.$module.'_uninstall.php'))
			{
				@include(APPPATH.'modules/'.$module.'/'.$module.'_uninstall.php');
			}

			$this->db->where(array('name'=> $module, 'ordering >=' => 100));
			$this->db->delete('modules');
			if($this->system->cache == 1) $this->cache->remove_group('modules');
			if($this->system->cache == 1) $this->cache->remove_group('system');
			if($this->system->cache == 1) $this->cache->remove_group('settings');
			$this->session->set_flashdata('notification', $this->lang->line('notification_module_remove'));
			redirect($this->config->item('admin_folder').'/module');
		}

		public function install($module = null)
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);
			if (is_null($module))
			{
				$this->session->set_flashdata('notification', $this->lang->line('alert_module_not_found'));
				redirect($this->config->item('admin_folder').'/module');
			}

			if ($this->_is_installed($module))
			{
				$this->session->set_flashdata('notification', $this->lang->line('notification_module_already_installed'));
				redirect($this->config->item('admin_folder').'/module');
			}

			if (is_readable(APPPATH.'modules/'.$module.'/setup.xml'))
			{
				$this->load->helper('xml');
				$xmldata = join('', file(APPPATH.'modules/'.$module.'/setup.xml'));
				$xmlarray = xmlize($xmldata);
				if (isset($xmlarray['module']['#']['name'][0]['#']) && trim($xmlarray['module']['#']['name'][0]['#']) == $module)
				{
					$data['name'] = trim($xmlarray['module']['#']['name'][0]['#']);
					$data['description'] = isset($xmlarray['module']['#']['description'][0]['#']) ? trim($xmlarray['module']['#']['description'][0]['#']): '';
					$data['version'] = isset($xmlarray['module']['#']['version'][0]['#']) ? trim($xmlarray['module']['#']['version'][0]['#']) : '';
					$data['active'] = 0;
					$data['ordering'] = 1000;
					$info['date'] = $xmlarray['module']['#']['date'][0]['#'];
					$info['author'] = $xmlarray['module']['#']['author'][0]['#'];
					$info['email'] = $xmlarray['module']['#']['email'][0]['#'];
					$info['url'] = $xmlarray['module']['#']['url'][0]['#'];
					$info['copyright'] = $xmlarray['module']['#']['copyright'][0]['#'];
					$data['info'] = serialize($info);

					if (file_exists(APPPATH.'modules/'.$module.'/controllers/admin.php') || file_exists(APPPATH.'modules/'.$module.'/controllers/admin/admin.php'))
					{
						$data['admin'] = 1;
					}

					$this->db->insert('modules', $data);

					if (isset($xmlarray['module']['#']['install'][0]['#']['query']))
					{
						$queries = $xmlarray['module']['#']['install'][0]['#']['query'];
						foreach ($queries as $query)
						{
							$this->db->query($query['#']);
						}
					}

					if (is_file(APPPATH.'modules/'.$module.'/'.$module.'_install.php'))
					{
						@include(APPPATH.'modules/'.$module.'/'.$module.'_install.php');
					}
					if($this->system->cache == 1) $this->cache->remove_group('modules');
					if($this->system->cache == 1) $this->cache->remove_group('system');
					if($this->system->cache == 1) $this->cache->remove_group('settings');
					$this->session->set_flashdata('notification', $this->lang->line('notification_module_installed'));
					redirect($this->config->item('admin_folder').'/module');
				}
				else
				{
					$this->session->set_flashdata('notification', $this->lang->line('alert_module_invalid_xml'));
					redirect($this->config->item('admin_folder').'/module');
				}

			}
			else
			{
				$this->session->set_flashdata('notification',  $this->lang->line('alert_module_not_found_xml'));
				redirect($this->config->item('admin_folder').'/module');
			}
		}


		function _is_installed($module)
		{
			$query = $this->db->get_where('modules', array('name' => $module), 1);
			if ($query->num_rows() > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}


	}
