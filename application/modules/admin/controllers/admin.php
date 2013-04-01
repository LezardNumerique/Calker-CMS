<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Admin extends CI_Controller {

		public function __construct()
		{
			parent::__construct();

			$this->config->load('config');
			$this->load->library('administration');
			$this->load->model('admin_model', 'model');
			$this->template['module'] = 'admin';

		}
		
		public function setLanguages ()
		{
			$lang = $this->uri->segment(3);
			if (in_array($lang, $this->language->codes))			
				$this->session->set_userdata('lang', $lang);		
			else			
				$this->session->set_userdata('lang', $this->language->default);			
			$redirect = str_replace($this->config->item('admin_folder').'/setLanguages/'.$lang, '', $this->uri->uri_string());			
			redirect($redirect);
		}

		public function index()
		{
			if (!isset($this->user->level) ||  $this->user->level == 0)
			{
				redirect($this->config->item('admin_folder').'/unauthorized/admin/1');
			}

			$this->load->helper('format');

			//----------- Analytics BEGIN
			if($this->system->google_analytic_stats == 1)
			{
				$this->load->library('analytics');
				$this->template['visits'] = true;
				$this->template['referrers'] = $this->analytics->dimension('source')->metric('visits, pageviews')->when('1 month ago')->limit(10)->get_object();
				$this->template['cities'] = $this->analytics->dimension('city')->metric('visits, pageviews')->when('1 month ago')->limit(10)->get_object();
				$this->template['browsers'] = $this->analytics->dimension('browser')->metric('visits, pageviews')->when('1 month ago')->limit(10)->get_object();
				$this->template['operating_systems'] = $this->analytics->dimension('operatingSystem')->metric('visits, pageviews')->when('1 month ago')->limit(10)->get_object();
				$this->template['visits'] = $this->analytics->dimension('date')->metric('visits, pageviews')->when('1 month ago')->limit(30)->sort_by('date', true)->get_object();
			}
			//----------- Analytics END

			//----------- BEGIN QRCODE
        	if (!$data = $this->cache->get('qrcode', 'admin'))
			{
				$data = $this->model->get_qrcode();
				if($this->system->cache == 1) $this->cache->save('qrcode', $data, 'admin', 0);
			}
			$this->template['qrcode'] = $data;
			//----------- END QRCODE

			$this->plugin->add_action('header', array(&$this, '_write_header_google_map'));

			$this->css->add(array('admin', 'ui'));
			$this->javascripts->add(array('jquery', 'ui', 'highcharts', 'exporting', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'index');
		}

		public function unauthorized($module = '', $level = '')
		{
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->template['data']  = array('module' => $module, 'level' => $level);
			$this->layout->load($this->template, $this->config->item('theme_admin'), '403');
		}

		public function login()
		{
			if ($this->user->logged_in && !$this->input->post('submit'))
			{
				redirect($this->config->item('admin_folder'));
			}
			else
			{
				if (!$this->input->post('submit'))
				{
					$this->css->add(array('admin'));
					$this->javascripts->add(array('jquery', 'sitelib'));
					$this->layout->load($this->template, $this->config->item('theme_admin'), 'login');

				}
				else
				{
					if(!$username = $this->input->post('username'))
					{
						$this->session->set_flashdata('alerte', $this->lang->line('alert_username'));
						redirect($this->config->item('admin_folder').'/login');
					}

					if(!$password = $this->input->post('password'))
					{
						$this->session->set_flashdata('alerte', $this->lang->line('alert_password'));
						redirect($this->config->item('admin_folder').'/login');
					}

					if ($this->user->login($username, $password))
					{
						if ($this->input->post('redirect'))
						{
							redirect($this->input->post('redirect'));
							return;
						}
						redirect($this->config->item('admin_folder'));
					}
					else
					{
						if ($this->input->post('redirect'))
						{
							$this->session->set_flashdata('redirect', $this->input->post('redirect'));
						}
						redirect($this->config->item('admin_folder').'/login');
					}
				}
			}
		}

		public function logout()
		{
			$this->user->logout();
			redirect($this->config->item('admin_folder').'/login');
		}

		public function settings()
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			$fields = array(
				'site_email',
				'site_name',
				'meta_keywords',
				'meta_description',
				'cache',
				'cache_css',
				'theme',
				'stylesheet',
				'debug',
				'site_email',
				'site_adress',
				'site_adress_next',
				'site_post_code',
				'site_city',
				'site_country',
				'site_phone',
				'site_schedule',
				'google_analytic_stats',
				'google_analytic_visits',
				'google_analytic_ga_id',
				'google_analytic_ua_id',
				'google_analytics_email',
				'google_analytics_password',
				'google_analytic_domain',
				'google_analytic_code',
				'smtp_host',
				'smtp_username',
				'smtp_password',
				'smtp_port',
				'smtp_is',
				'per_page',
				'num_links',
				'per_captcha',
				'meta_more',
				'maintenance',
				'ip_allow',
				'active_visits'
			);

			if (!$this->input->post('submit') )
			{
				foreach ($fields as $field)
				{
					if (!isset($this->system->$field))
					{
						$this->system->$field = '';
					}
				}
				$this->template['themes'] = $this->layout->list_themes();
				$this->template['stylesheets'] = $this->layout->list_stylesheets();
				$this->css->add(array('admin', 'ui'));
				$this->javascripts->add(array('jquery', 'ui', 'filestyle', 'sitelib'));
				$this->layout->load($this->template, $this->config->item('theme_admin'), 'settings');
			}
			else
			{
				foreach ($fields as $field)
				{
					$this->system->set_settings($field, $this->input->post($field));
				}
				if ($_FILES['image']['name'] != '')
				{
					$config['upload_path'] = './'.$this->config->item('medias_folder').'/tmp/';
					$config['allowed_types'] = 'gif|jpg|jpeg|png';
					$this->load->library('upload', $config);

					if (!$this->upload->do_upload('image'))
					{
						$error = $this->upload->display_errors('', '<br />');
						$this->session->set_flashdata('alert', $error);
					}
					else
					{
						$this->load->library('image_lib');
						$image_data = $this->upload->data();

						$file = $image_data['file_name'];
						$file_extension = $image_data['file_ext'];
						$file_rewrite = 'logo'.$file_extension;

						if(is_file('./'.$this->config->item('medias_folder').'/tmp/'.$file)) @rename('./'.$this->config->item('medias_folder').'/tmp/'.$file, APPPATH.'views/'.$this->system->theme.'/img/'.$file_rewrite);

						$this->system->set('logo', $file_rewrite);

					}
				}

				$this->system->clear_cache();

				$this->session->set_flashdata('notification', $this->lang->line('notification_settings_save'));

				redirect($this->config->item('admin_folder').'/settings');
			}
		}

		public function AjaxChangeTheme()
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			$this->load->helper('form');

			$theme = $this->input->post('theme');

			if($stylesheets = $this->layout->list_stylesheets($theme))
			{
				$i=1;
				foreach ($stylesheets as $stylesheet)
				{
					echo '<div class="theme_colors">';
					echo '<label for="stylesheet_'.$i.'" style="background:'.$stylesheet['hexa'].'">'.ucfirst(str_replace('_', ' ', $stylesheet['color'])).'</label>';
					echo '<input type="radio" name="stylesheet" id="stylesheet_'.$i.'" value="'.$stylesheet['file'].'" '.(($stylesheet['file'] == $this->system->stylesheet) ? 'checked="checked"' : '').(($i == 1) ? 'checked="checked"' : '').'/>';
					echo '</div>';
					$i++;
				}
				echo '<br class="clear"/>';
			}

		}

		public function clearCache()
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			$this->system->clear_cache();

			$this->session->set_flashdata('notification', $this->lang->line('notification_cache_performed'));

			redirect($this->config->item('admin_folder'));
		}

		public function purge ()
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			$this->system->purge();

			$this->session->set_flashdata('notification', $this->lang->line('notification_purge_performed'));

			redirect($this->config->item('admin_folder'));
		}

		public function backupBdd ()
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			$this->load->dbutil();

			$backup =& $this->dbutil->backup();

			$filename = format_title($this->system->site_name).'-backup-sql-'.date("Y-m-d-h:i:s");

			$prefs = array(
				'ignore'      => array($this->db->dbprefix('sessions')),
				'format'      => 'gzip',
				'filename'    => $filename.'.sql',
				'add_drop'    => TRUE,
				'add_insert'  => TRUE,
				'newline'     => "\n"
			);

			$this->dbutil->backup($prefs);

			$this->load->helper('file');
			write_file('./'.$this->config->item('backup_folder').'/'.$filename.'.gz', $backup);

			$this->load->helper('download');
			force_download($filename.'.gz', $backup);

			redirect($this->config->item('admin_folder'));
		}

		public function phpInfo ()
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('admin_folder'), 'phpinfo');
		}

		public function Utf8Tables ()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$tables = $this->db->list_tables();

            foreach ($tables as $table)
			{
			    $this->db->query('ALTER TABLE '.$table.' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
			}

			$this->session->set_flashdata('notification', $this->lang->line('notification_utf8_performed'));

			redirect($this->config->item('admin_folder'));
		}

		public function optimizeTables ()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$this->load->dbutil();

			$tables = $this->db->list_tables();

            foreach ($tables as $table)
			{
			   	$this->dbutil->optimize_table($table);
			}

			$this->session->set_flashdata('notification', $this->lang->line('notification_optimize_tables_performed'));

			redirect($this->config->item('admin_folder'));
		}

		public function repairTables ()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$this->load->dbutil();

			$tables = $this->db->list_tables();
            foreach ($tables as $table)
			{
			   	$this->dbutil->repair_table($table);

			}

			$this->session->set_flashdata('notification', $this->lang->line('notification_repair_tables_performed'));

			redirect($this->config->item('admin_folder'));
		}

		public function chmod()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			$this->system->chmod();
			$this->session->set_flashdata('notification', $this->lang->line('notification_chmod_performed'));
			redirect($this->config->item('admin_folder'));
		}

		public function urlTitle()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			echo format_title($this->input->post('title'));
		}

		public function liveView($logout = false)
		{			
			if($logout == TRUE)	
				$this->session->set_userdata('liveView', false);			
			else			
				$this->session->set_userdata('liveView', true);
				
			$this->system->clear_cache();
						
			redirect($this->language->get_uri_language().'/');
		}
		
		public function listMediasTypesSizes($module = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);
			
			$this->load->library('medias');		
			$this->template['medias_sizes'] = $this->medias->list_medias_types_sizes(array('where' => array('theme' => $this->system->theme, 'module' => $module)));

			$this->load->view('medias-types-sizes', $this->template);
		}

		function _write_header_google_map()
		{
			echo '
			<script src="http://www.google.com/jsapi" type="text/javascript"></script>
			<script type="text/javascript">
				google.load("maps", "3", {other_params:"sensor=false"});
			</script>
			<script type="text/javascript">
				var map, cloud;
				var counter = 0;
				var markers = [];
				function init()
				{
					var options = {
						scrollwheel: false,
						zoom: 15,
						center: new google.maps.LatLng(0, 0),
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					map = new google.maps.Map(document.getElementById(\'google_map\'), options);
					geocoder = new google.maps.Geocoder();
					makeMarker("'.$this->system->site_adress.', '.$this->system->site_post_code.', '.$this->system->site_city.', '.$this->system->site_country.'");
				}
				function makeMarker(address)
				{
					geocoder.geocode({\'address\': address}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							map.setCenter(results[0].geometry.location);
							var markerOptions = {map: map, position: results[0].geometry.location};
							var marker = new google.maps.Marker(markerOptions);
							markers.push(marker);
							google.maps.event.addListener(marker, \'click\', function(e) {
								$("#google_map_dialog").dialog({
									title:"'.$this->lang->line('title_localisation').'",
									width:"520px",
									height:"300",
									draggable:false,
									resizable:false,
									modal:true
								});
							});
						}
					});
				}
			</script>';
		}
	}
