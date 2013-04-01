<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Layout {

		var $theme;
		function Layout()
		{
			$this->obj =& get_instance();
			if(isset($this->obj->system->theme)) $this->theme = $this->obj->system->theme;
		}

		public function load($data, $path, $view)
		{
			$breadcrumb = array();

			if (empty($data['breadcrumb'])) $data['breadcrumb'] = array();

			$data['view'] = $view;

			if($redirect_mobile = $this->obj->session->userdata('redirect_mobile'))
			{
				if($path == $this->obj->config->item('admin_folder'))
				{
					$data['view'] = 'index';
					$output = $this->obj->load->view('../mobile/index', $data, true);
				}
				else
				{
					$output = $this->obj->load->view('../mobile/index', $data, true);
				}
			}
			else
			{
				$view = 'index';
				if($this->obj->uri->segment(1) == 'admin' && $this->obj->uri->segment(2) == 'login') $view = 'login';
				$output = $this->obj->load->view('../'.$path.'/'.$view, $data, true);
			}

			if (isset($this->obj->system->debug) && $this->obj->system->debug == 1 && $this->obj->user->root == 1 && $path != 'ajax')
			{
				$this->obj->output->enable_profiler(true);
			}

			$here = $this->obj->uri->uri_string();

			$this->obj->session->set_userdata(array('last_uri' => $here));

			$this->obj->output->set_output($output);
		}

		public function list_themes()
		{
			$handle = opendir(APPPATH.'views');

			if ($handle)
			{
				while (false !== ($theme = readdir($handle)))
				{
					if (substr($theme, 0, 1) != "." && $theme != 'index.html' && $theme != 'admin' && $theme != 'ajax' && $theme != 'mails' && $theme != 'assets' && $theme != 'mobile' && $theme != 'install')
					{
						$themes[] = $theme;
					}
				}
			}

			asort($themes);

			return $themes;
		}

		public function list_stylesheets($theme = '')
		{
			$theme = (!$theme) ? $this->theme : $theme;

			$this->obj->load->helper('xml');
			$xmldata = join('', file(APPPATH.'views/'.$theme.'/config.xml'));
			$xmlarray = xmlize($xmldata);

			$tab_stylesheets = $xmlarray['config']['#']['css'][0]['#']['stylesheet'];

			$this->stylesheets = '';

			foreach ($tab_stylesheets as $key => $value)
			{
				$this->stylesheets[] = array('color' => $value['#']['color'][0]['#'], 'file' => $value['#']['file'][0]['#'], 'hexa' => $value['#']['hexa'][0]['#']);

			}

			return $this->stylesheets;
		}

		public function size_stylesheets($theme = '')
		{
			$theme = (!$theme) ? $this->theme : $theme;

			$this->obj->load->helper('xml');
			$xmldata = join('', file(APPPATH.'views/'.$theme.'/config.xml'));
			$xmlarray = xmlize($xmldata);

			$tab_stylesheets = $xmlarray['config']['#']['size'][0]['#'];

			$size  = array();
			foreach ($tab_stylesheets as $key => $value)
			{
				$size[$key] = $value[0]['#']['value'][0]['#'];
			}

			return $size;
		}

	}