<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

	class Contact extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->config->load('config');
			$this->template['module'] = 'contact';
			$this->load->model('contact_model', 'model');
			$this->settings = isset($this->system->contact_settings) ? unserialize($this->system->contact_settings) : array();

			if($this->system->modules[$this->template['module']]['active'] != 1)
				redirect('pages/unauthorized/'.$this->template['module'].'/1');

		}

		public function index()
		{
			$this->load->library('sanitation');

			if($this->settings['active_map'] == 1) $this->plugin->add_action('header', array(&$this, '_write_header'));

			$this->template['title'] = $this->lang->line('title_contact');
			$this->template['meta_title'] = $this->lang->line('title_contact');
			$this->template['meta_keywords'] = $this->system->meta_keywords;
			$this->template['meta_description'] = $this->system->meta_description;

        	$this->load->library('form_validation');
        	$this->load->helper('format');

        	//--- BEGIN QRCODE
        	if($this->settings['active_qrcode'] == 1)
        	{
				if (!$data = $this->cache->get('qrcode', $this->template['module']))
				{
					$data = $this->model->get_qrcode();
					if($this->system->cache == 1) $this->cache->save('qrcode', $data, $this->template['module'], 0);
				}
				$this->template['qrcode'] = $data;
			}
			//--- END QRCODE

			if($this->settings['active_field_firstname'] == 1)
			{
				$fields[] = array(
					'field'   => 	'contact_firstname',
					'label'   => 	$this->lang->line('validation_firstname'),
					'rules'   => 	'trim|required|max_length[64]|xss_clean'
				);
			}

			if($this->settings['active_field_lastname'] == 1)
			{
				$fields[] = array(
					'field'   => 	'contact_lastname',
					'label'   => 	$this->lang->line('validation_lastname'),
					'rules'   => 	'trim|required|max_length[64]|xss_clean'
				);
			}

			$fields[] = array(
				'field'   => 	'contact_email',
				'label'   => 	$this->lang->line('validation_email'),
				'rules'   => 	'trim|required|max_length[128]|xss_clean|valid_email'
			);

			if($this->settings['active_field_phone'] == 1)
			{
				$fields[] = array(
					'field'   => 	'contact_phone',
					'label'   => 	$this->lang->line('validation_phone'),
					'rules'   => 	'trim|required|max_length[16]|numeric|xss_clean'
				);
			}

			if($this->settings['active_field_message'] == 1)
			{
				$fields[] = array(
					'field'   => 	'contact_message',
					'label'   => 	$this->lang->line('validation_message'),
					'rules'   => 	'trim|required|max_length[255]|xss_clean'
				);
			}

			$fields[] = array(
				'field'   => 	'contact_captcha',
				'label'   => 	$this->lang->line('validation_captcha'),
				'rules'   => 	'trim|required|xss_clean|numeric|exact_length['.$this->system->per_captcha.']|callback_captcha_check'
			);

			$this->form_validation->set_rules($fields);

			if($this->settings['active_field_firstname'] == 1) $this->fields['contact_firstname'] 	= $this->lang->line('validation_firstname');
			if($this->settings['active_field_lastname'] == 1) $this->fields['contact_lastname'] 	= $this->lang->line('validation_lastname');
			$this->fields['contact_email'] 		= $this->lang->line('validation_email');
			if($this->settings['active_field_phone'] == 1) $this->fields['contact_phone'] 	= $this->lang->line('validation_phone');
			if($this->settings['active_field_message'] == 1) $this->fields['contact_message'] 	= $this->lang->line('validation_message');
			$this->fields['contact_captcha'] 	= $this->lang->line('validation_captcha');

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == FALSE)
			{
				$pool = '0123456789';
				$str = '';
				for ($i = 0; $i < $this->system->per_captcha; $i++)
				{
					$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
				}

				$word = $str;
				$this->load->helper('captcha');
				$vals = array(
					'img_path'	 	=> './'.$this->config->item('medias_folder').'/captcha/',
					'img_url'	 	=> site_url($this->config->item('medias_folder').'/captcha').'/',
					'font_path'	 	=> APPPATH . 'views/assets/fonts/Fatboy_Slim.ttf',
					'img_width'	 	=> 100,
					'img_height' 	=> 30,
					'expiration' 	=> 180,
					'time'			=> mktime(),
					'word' 			=> $word
				);

				$cap = create_captcha($vals);
				$data = array(
					'captcha_time'	=> $cap['time'],
					'ip_address'	=> $this->input->ip_address(),
					'word'			=> $cap['word']
				);
				$this->db->insert('captcha', $data);
				$this->template['captcha'] = $cap['image'];

				$this->template['contact_firstname'] = $this->sanitation->xss_clean($this->input->post('contact_firstname'));
				$this->template['contact_lastname'] = $this->sanitation->xss_clean($this->input->post('contact_lastname'));
				$this->template['contact_email'] = $this->sanitation->xss_clean($this->input->post('contact_email'));
				$this->template['contact_phone'] = $this->sanitation->xss_clean($this->input->post('contact_phone'));
				$this->template['contact_message'] = $this->sanitation->xss_clean($this->input->post('contact_message'));

			}
			else
			{
				$firstname = $this->sanitation->xss_clean($this->input->post('contact_firstname'));
				$lastname = $this->sanitation->xss_clean($this->input->post('contact_lastname'));
				$email = $this->sanitation->xss_clean($this->input->post('contact_email'));
				$phone = $this->sanitation->xss_clean($this->input->post('contact_phone'));
				$message = $this->sanitation->xss_clean($this->input->post('contact_message'));

				$data = array(
					'firstname'		=> $firstname,
					'lastname'		=> $lastname,
					'email'			=> $email,
					'phone'			=> $phone,
					'message'		=> $message,
					'lang'			=> $this->user->lang,
					'date'			=> date('Y-m-d H:i:s'),
					'ip_address'	=> $this->input->ip_address(),
					'trash'			=> 0
				);

				$this->db->insert($this->config->item('table_contact'), $data);

				$this->load->library('email');

				$this->email->from($email, $name);
				$this->email->to($this->system->site_email);
				$this->email->subject($this->lang->line('text_subject'));

				$text = '';
				if($firstname) $text .= $this->lang->line('mail_text_firstname').' '.$firstname."\n\n";
				if($lastname) $text .= $this->lang->line('mail_text_lastname').' '.$lastname."\n\n";
				if($phone) $text .= $this->lang->line('mail_text_phone').' '.$phone."\n\n";
				if($message) $text .= $message;

				$this->email->message($text);
				$this->email->send();

				redirect($this->language->get_uri_language().$this->template['module'].'/success');

			}

			$this->css->add($this->template['module']);
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, $this->system->theme, 'index');

		}

		public function success()
		{
			$this->template['title'] = $this->lang->line('title_contact');
			$this->template['meta_title'] = $this->lang->line('title_contact');
			$this->template['meta_keywords'] = $this->system->meta_keywords;
			$this->template['meta_description'] = $this->system->meta_description;
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, $this->system->theme, 'success');

		}

		/*
		*
		* Callback functions
		*
		*/

		public function captcha_check()
    	{
		    $exp = time()-180;

		    //---- On supprime en base
		   	$this->db->query("DELETE FROM ".$this->db->dbprefix($this->config->item('table_captcha'))." WHERE captcha_time < ".$exp);

		    $sql = "SELECT captcha_time, COUNT(*) AS count FROM ".$this->db->dbprefix($this->config->item('table_captcha'))." WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		    $binds = array($this->input->post('contact_captcha'), $this->input->ip_address(), $exp);
		    $query = $this->db->query($sql, $binds);
		   	$row = $query->row();

		    if ($row->count == 0)
		    {
				$this->system->set_session_brut_force();
				$this->system->set_cookie_brut_force();
		        $this->form_validation->set_message('captcha_check', $this->lang->line('alert_invalid_captcha'));
		        return FALSE;
		    }
		    else
		    {
		        return TRUE;
		    }

        }

		public  function _write_header()
		{
			echo '<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>'."\n";
			echo '<link href="http://code.google.com/apis/maps/documentation/javascript/examples/standard.css" rel="stylesheet" type="text/css" />'."\n";
		}
	}
