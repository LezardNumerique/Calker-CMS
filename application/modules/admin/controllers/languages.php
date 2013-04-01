<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Languages extends CI_Controller
	{

		public function __construct()
		{
			parent::__construct();

			$this->config->load('config');
			$this->load->library('administration');
			$this->load->library('form_validation');

			$this->template['module'] = 'admin';

			$this->fields_validation = array(
				array(
					'field'   => 	'name',
					'label'   => 	$this->lang->line('validation_name'),
					'rules'   => 	'trim|required|max_length[64]|xss_clean|callback__verify_name'
				),
				array(
					'field'   => 	'code',
					'label'   => 	$this->lang->line('validation_code'),
					'rules'   => 	'trim|required|exact_length[2]|xss_clean|callback__verify_code'
				)
			);

		}

		public function index()
		{
			$this->user->check_level($this->template['module'], LEVEL_VIEW);

			$this->template['langs'] = $this->language->list_languages(false);

			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'ui', 'tooltip', 'tablesorter', 'sitelib'));
			$this->layout->load($this->template, $this->config->item('theme_admin'), 'languages/index');
		}

		public function create()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);

			$this->form_validation->set_rules($this->fields_validation);

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == FALSE)
			{
				$this->template['language'] = array('id' => '', 'code' => '', 'name' => '', 'active' => '');

				$this->css->add(array('admin'));
				$this->javascripts->add(array('jquery', 'sitelib'));
				$this->layout->load($this->template, $this->config->item('theme_admin'), 'languages/create');
			}
			else
			{
				$data = array(
					'name'		=> strip_tags($this->input->post('name')),
					'code'		=> strip_tags($this->input->post('code')),
					'active'	=> strip_tags($this->input->post('active'))
				);

				$this->db->insert($this->config->item('table_languages'), $data);

				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

				if($this->system->cache == 1) $this->cache->remove_group('languages');

				redirect($this->config->item('admin_folder').'/languages');
			}

		}

		public function edit($languages_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			$this->form_validation->set_rules($this->fields_validation);

			$this->form_validation->set_error_delimiters('', '<br />');

			if ($this->form_validation->run() == FALSE)
			{
				$this->db->where('id', $languages_id);
				$query = $this->db->get($this->config->item('table_languages'));

				$this->template['language'] = $query->row_array();

				$this->css->add(array('admin'));
				$this->javascripts->add(array('jquery', 'sitelib'));
				$this->layout->load($this->template, $this->config->item('theme_admin'), 'languages/create');
			}
			else
			{
				$data = array(
					'name'		=> strip_tags($this->input->post('name')),
					'code'		=> strip_tags($this->input->post('code')),
					'active'	=> strip_tags($this->input->post('active'))
				);

				$this->db->where('id', $this->input->post('id'));
				$this->db->update($this->config->item('table_languages'), $data);

				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

				if($this->system->cache == 1) $this->cache->remove_group('languages');

				redirect($this->config->item('admin_folder').'/languages');
			}
		}

		public function delete($languages_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_DEL);

			$this->db->delete($this->config->item('table_languages'), array('id' => $languages_id));

			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

			if($this->system->cache == 1) $this->cache->remove_group('languages');

			redirect($this->config->item('admin_folder').'/languages');
		}

		public function flag($languages_id = '', $flag = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			if ($flag == '1')
			{
				$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_languages'))." set active = '0' where id = '" . (int)$languages_id . "'");
			}
			elseif ($flag == '0')
			{
				$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_languages'))." set active = '1' where id = '" . (int)$languages_id . "'");
			}
			else
			{
				redirect($this->config->item('admin_folder').'/languages');
			}

			if($this->system->cache == 1) $this->cache->remove_group('languages');

			$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

			redirect($this->config->item('admin_folder').'/languages');
		}

		public function setDefault($languages_id = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			if(isset($languages_id)) {

				$this->db->update($this->db->dbprefix($this->config->item('table_languages')), array('default' => 0));
				$data = array('default' => 1);
				$this->db->where('id', $languages_id);
				$this->db->update($this->db->dbprefix($this->config->item('table_languages')), $data);
				if($this->system->cache == 1) $this->cache->remove_group('languages');
				$this->session->set_flashdata('notification', $this->lang->line('notification_save'));

			}

			redirect($this->config->item('admin_folder').'/languages');
		}

		public function move($id = '', $direction = '')
		{
			$this->user->check_level($this->template['module'], LEVEL_EDIT);

			if (!isset($direction) || !isset($id))
			{
				redirect($this->config->item('admin_folder').'/languages');
			}

			$move = ($direction == 'up') ? -1 : 1;
			$this->db->where(array('id' => $id));

			$this->db->set('ordering', 'ordering+'.$move, FALSE);
			$this->db->update($this->config->item('table_languages'));

			$this->db->where(array('id' => $id));
			$query = $this->db->get($this->config->item('table_languages'));
			$row = $query->row();
			$new_ordering = $row->ordering;

			if ( $move > 0 )
			{
				$this->db->set('ordering', 'ordering-1', FALSE);
				$this->db->where(array('ordering <=' => $new_ordering, 'id <>' => $id));
				$this->db->update($this->config->item('table_languages'));
			}
			else
			{
				$this->db->set('ordering', 'ordering+1', FALSE);
				$where = array('ordering >=' => $new_ordering, 'id <>' => $id);
				$this->db->where($where);
				$this->db->update($this->config->item('table_languages'));
			}

			$i = 0;
			$this->db->order_by('ordering');
			$query = $this->db->get($this->config->item('table_languages'));
			if ($rows = $query->result())
			{
				foreach ($rows as $row)
				{
					$this->db->set('ordering', $i);
					$this->db->where('id', $row->id);
					$this->db->update($this->config->item('table_languages'));
					$i++;
				}
			}

			if($this->system->cache == 1) $this->cache->remove_group('languages');

			redirect($this->config->item('admin_folder').'/languages');
		}

		public function sortOrder()
		{
			$this->user->check_level($this->template['module'], LEVEL_ADD);
			if($items = $this->input->post('items'))
			{
				foreach ($items as $position => $item)
				{
					$query = $this->db->query("update ".$this->db->dbprefix($this->config->item('table_languages'))." set ordering = ".$position." where id = '".(int)$item."'");
				}
			}
			if($this->system->cache == 1)  $this->cache->remove_group('languages');
		}

		/*
		 *
		 * Callback functions
		 *
		 */

		function _verify_name($data)
		{
			$name = $this->input->post('name');
			$id = $this->input->post('id');

			if ($this->language->exists(array('name' => $name, 'id !=' => $id)))
			{
				$this->form_validation->set_message('_verify_name', $this->lang->line('alert_language_name_already_used'));
				return FALSE;
			}

		}

		function _verify_code($data)
		{
			$code = $this->input->post('code');
			$id = $this->input->post('id');

			if ($this->language->exists(array('code' => $code, 'id !=' => $id)))
			{
				$this->form_validation->set_message('_verify_code', $this->lang->line('alert_language_code_already_used'));
				return FALSE;
			}

		}


	}
