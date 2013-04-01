<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Paragraph
{
	var $tmpparagraphs;
	var $tmpparagtypes;

	function Paragraph() {
		$this->obj =& get_instance();
		$this->obj->load->library('form_validation');
	}

	public function get_paragraphs($where = '')
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

		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$query = $this->obj->db->get($this->obj->config->item('table_paragraphs'), 1);

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

	public function list_paragraphs($params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> $this->obj->config->item('table_paragraphs').'.id DESC',
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key])) ? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['where']))
		{
			$this->obj->db->where($params['where']);
		}

		$this->obj->db->order_by($params['order_by']);

		$this->obj->db->select($params['select']);
		$this->obj->db->from($this->obj->config->item('table_paragraphs'));
		$this->obj->db->join($this->obj->config->item('table_paragraphs_types'), $this->obj->config->item('table_paragraphs').'.types_id = '.$this->obj->config->item('table_paragraphs_types').'.id');
		$query = $this->obj->db->get();

		foreach ($query->result_array() as $row)
		{
			$this->tmpparagraphs[] = $row;
		}
		return $this->tmpparagraphs;

	}

	public function get_paragraphs_types($where = '')
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

		$query = $this->obj->db->get($this->obj->config->item('table_paragraphs_types'), 1);

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

	public function list_paragraphs_types($where = '')
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

		$query = $this->obj->db->get($this->obj->config->item('table_paragraphs_types'));

		foreach ($query->result_array() as $row)
		{
			$this->tmpparagtypes[$row['id']] = $row;
		}
		return $this->tmpparagtypes;
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

		if (!is_null($params['where']))
		{
			$this->obj->db->where($params['where']);
		}

		$this->obj->db->order_by($params['order_by']);

		$this->obj->db->select($params['select']);
		$this->obj->db->from($this->obj->config->item('table_medias'));
		$query = $this->obj->db->get();
		$list_medias = array();
		foreach ($query->result_array() as $row)
		{
			$row['options'] = unserialize($row['options']);
			$list_medias[] = $row;
		}
		return $list_medias;
	}

	public function move_paragraphs($src_id = '', $id = '', $direction = '', $module = '')
	{
		$query = $this->obj->db->get_where($this->obj->config->item('table_paragraphs'), array('id' => $id));

		$move = ($direction == 'up') ? -1 : 1;
		$this->obj->db->where(array('id' => $id));

		$this->obj->db->set('ordering', 'ordering+'.$move, FALSE);
		$this->obj->db->update($this->obj->config->item('table_paragraphs'));

		$this->obj->db->where(array('id' => $id));
		$query = $this->obj->db->get($this->obj->config->item('table_paragraphs'));
		$row = $query->row();
		$new_ordering = $row->ordering;

		if ($move > 0)
		{
			$this->obj->db->set('ordering', 'ordering-1', FALSE);
			$this->obj->db->where(array('ordering <=' => $new_ordering, 'src_id' => $src_id, 'id <>' => $id, 'module' => $module));
			$this->obj->db->update($this->obj->config->item('table_paragraphs'));
		}
		else
		{
			$this->obj->db->set('ordering', 'ordering+1', FALSE);
			$where = array('ordering >=' => $new_ordering, 'src_id' => $src_id, 'id <>' => $id, 'module' => $module);

			$this->obj->db->where($where);
			$this->obj->db->update($this->obj->config->item('table_paragraphs'));
		}

		//Reordinate
		$i = 0;
		$this->obj->db->order_by('ordering');
		$this->obj->db->where(array('src_id' => $src_id, 'module' => $module));
		$query = $this->obj->db->get($this->obj->config->item('table_paragraphs'));
		if ($rows = $query->result())
		{
			foreach ($rows as $row)
			{
				$this->obj->db->set('ordering', $i);
				$this->obj->db->where('id', $row->id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$i++;
			}
		}
	}

	public function delete_recursive($src_id = '', $module = '')
	{
		if($paragraphs = $this->list_paragraphs(array('select' => '*, '.$this->obj->config->item('table_paragraphs').'.id as pID', 'where' => array('src_id' => $src_id, $this->obj->config->item('table_paragraphs').'.module' => $module))))
		{
			foreach($paragraphs as $paragraph)
			{
				if($media = $this->get_medias(array('src_id' => $paragraph['pID'], 'module' => $module)))
				{
					$file = $media['file'];
					if(is_file('./'.$this->obj->config->item('medias_folder').'/images/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/images/'.$file);
					if(is_file('./'.$this->obj->config->item('medias_folder').'/swf/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/swf/'.$file);
					if(is_file('./'.$this->obj->config->item('medias_folder').'/videos/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/videos/'.$file);
					$this->obj->db->delete($this->obj->config->item('table_medias'), array('id' => $media['id']));
				}
				$this->obj->db->delete($this->obj->config->item('table_paragraphs'), array('id' => $paragraph['pID']));
			}
		}


	}

	public function delete($paragraphs_id = '', $module = '')
	{
		if($paragraph = $this->get_paragraphs(array('id' => $paragraphs_id)))
		{
			if($medias = $this->list_medias(array('where' => array('src_id' => $paragraphs_id, 'module' => $module))))
			{
				foreach($medias as $media)
				{
					$file = $media['file'];
					if(is_file('./'.$this->obj->config->item('medias_folder').'/images/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/images/'.$file);
					if(is_file('./'.$this->obj->config->item('medias_folder').'/swf/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/swf/'.$file);
					if(is_file('./'.$this->obj->config->item('medias_folder').'/videos/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/videos/'.$file);
					$this->obj->db->delete($this->obj->config->item('table_medias'), array('id' => $media['id']));
				}
			}
			$this->obj->db->delete($this->obj->config->item('table_paragraphs'), array('id' => $paragraphs_id));
		}

	}

	public function delete_parag_media($medias_id = '')
	{
		if($media = $this->get_medias(array('id' => $medias_id)))
		{
			$file = $media['file'];
			if(is_file('./'.$this->obj->config->item('medias_folder').'/images/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/images/'.$file);
			if(is_file('./'.$this->obj->config->item('medias_folder').'/swf/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/swf/'.$file);
			if(is_file('./'.$this->obj->config->item('medias_folder').'/videos/'.$file)) @unlink('./'.$this->obj->config->item('medias_folder').'/videos/'.$file);
			$this->obj->db->delete($this->obj->config->item('table_medias'), array('id' => $media['id']));

		}
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

	//------------------------------------------------- Traitements

	public function traitement_parag_type_1($module = '')
	{
		$fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'body',
				'label'   => 	$this->obj->lang->line('validation_body'),
				'rules'   => 	'trim|required'
			)
		);
		$this->obj->form_validation->set_rules($fields_validation);

		$this->obj->form_validation->set_error_delimiters('', '<br />');

		$last_id = 0;

		if ($this->obj->form_validation->run() == FALSE)
		{
			$this->obj->session->set_flashdata('alerte', validation_errors());
			$this->obj->session->set_flashdata('post', $this->obj->input->post());
			redirect($this->obj->input->post('redirect_uri'));
		}
		else
		{
			$src_id = $this->obj->input->post('src_id');
			$data = array('src_id' => $src_id, 'title' => htmlentities($this->obj->input->post('title')), 'class' => $this->obj->input->post('class'), 'body' => $this->obj->input->post('body'), 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
			if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
			{
				$data['date_modified'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->where('id', $paragraphs_id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$last_id = $paragraphs_id;
			}
			else
			{
				$data['date_added'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
				$last_id = $this->obj->db->insert_id();
			}
		}

		return $last_id;

	}

	public function traitement_parag_type_2($module = '')
	{
		$fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			)
		);

		$this->obj->form_validation->set_rules($fields_validation);

		$this->obj->form_validation->set_error_delimiters('', '<br />');

		$last_id = 0;

		if ($this->obj->form_validation->run() == FALSE)
		{
			$this->obj->session->set_flashdata('alerte', validation_errors());
			$this->obj->session->set_flashdata('post', $this->obj->input->post());
			redirect($this->obj->input->post('redirect_uri'));
		}
		else
		{
			$src_id = $this->obj->input->post('src_id');

			if($this->obj->input->post('paragraphs_id')) $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/editParag/'.$src_id.'/'.$this->obj->input->post('paragraphs_id');
			else $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/createParag/'.$src_id;

			if($this->obj->input->post('live_view')) $redirect = $this->obj->session->userdata('redirect_admin_live_view');

			if(!$this->obj->input->post('paragraphs_id')) $this->_check_ext(array('gif', 'jpg', 'jpeg', 'png'), $module, $src_id, $redirect, $_POST);

			$data = array('src_id' => $src_id, 'title' => htmlentities($this->obj->input->post('title')), 'class' => $this->obj->input->post('class'), 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
			if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
			{
				$data['date_modified'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->where('id', $paragraphs_id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$last_id = $paragraphs_id;
			}
			else
			{
				$data['date_added'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
				$last_id = $this->obj->db->insert_id();
			}

			$config['upload_path'] = './'.$this->obj->config->item('medias_folder').'/images';
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['overwrite'] = FALSE;

			$options_post = $this->obj->input->post('options');

			if($options_post['alt']) $config['file_name'] = format_title($options_post['alt']).'-'.$last_id.'_'.time();
			elseif($options_post['legend']) $config['file_name'] = format_title($options_post['legend']).'-'.$last_id.'_'.time();
			elseif($this->obj->input->post('title')) $config['file_name'] = format_title($this->obj->input->post('title')).'-'.$last_id.'_'.time();
			else $config['file_name'] = 'paragraph-'.$last_id.'_'.time();

			$this->obj->load->library('upload', $config);

			$options = serialize($options_post);

			$data_media = array(
					'module' 	=> $module,
					'src_id' 	=> $last_id,
					'options' 	=> $options
			);

			if ($this->obj->upload->do_upload('media'))
			{
				$image = array('upload_data' => $this->obj->upload->data());
				$data_media['file'] = $image['upload_data']['file_name'];

			}

			if(!$media = $this->get_medias(array('src_id' => $last_id, 'module' => $module)))
			{
				$this->obj->db->insert($this->obj->config->item('table_medias'), $data_media);
			}
			else
			{
				$this->obj->db->where(array('src_id' => $last_id, 'module' => $module))->update($this->obj->config->item('table_medias'), $data_media);
			}
		}

		return $last_id;

	}

	public function traitement_parag_type_3($module = '')
	{
		$fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'body',
				'label'   => 	$this->obj->lang->line('validation_body'),
				'rules'   => 	'trim|required'
			)
		);
		$this->obj->form_validation->set_rules($fields_validation);

		$this->obj->form_validation->set_error_delimiters('', '<br />');

		$last_id = 0;

		if ($this->obj->form_validation->run() == FALSE)
		{
			$this->obj->session->set_flashdata('alerte', validation_errors());
			$this->obj->session->set_flashdata('post', $this->obj->input->post());
			redirect($this->obj->input->post('redirect_uri'));
		}
		else
		{
			$src_id = $this->obj->input->post('src_id');

			if($this->obj->input->post('paragraphs_id')) $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/editParag/'.$src_id.'/'.$this->obj->input->post('paragraphs_id');
			else $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/createParag/'.$src_id;

			if($this->obj->input->post('live_view')) $redirect = $this->obj->session->userdata('redirect_admin_live_view');

			if(!$this->obj->input->post('paragraphs_id')) $this->_check_ext(array('gif', 'jpg', 'jpeg', 'png'), $module, $src_id, $redirect, $_POST);

			$data = array('src_id' => $src_id, 'title' => htmlentities($this->obj->input->post('title')), 'class' => $this->obj->input->post('class'), 'body' => $this->obj->input->post('body'), 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
			if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
			{
				$data['date_modified'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->where('id', $paragraphs_id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$last_id = $paragraphs_id;
			}
			else
			{
				$data['date_added'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
				$last_id = $this->obj->db->insert_id();
			}

			$config['upload_path'] = './'.$this->obj->config->item('medias_folder').'/images/';
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['overwrite'] = TRUE;

			$options_post = $this->obj->input->post('options');

			if($options_post['alt']) $config['file_name'] = format_title($options_post['alt']).'-'.$last_id.'_'.time();
			elseif($options_post['legend']) $config['file_name'] = format_title($options_post['legend']).'-'.$last_id.'_'.time();
			elseif($this->obj->input->post('title')) $config['file_name'] = format_title($this->obj->input->post('title')).'-'.$last_id.'_'.time();
			else $config['file_name'] = 'paragraph-'.$last_id.'_'.time();

			$this->obj->load->library('upload', $config);

			$options = serialize($options_post);

			$data_media = array(
				'module' 	=> $module,
				'src_id' 	=> $last_id,
				'options' 	=> $options
			);
			if ($this->obj->upload->do_upload('media'))
			{
				$image = array('upload_data' => $this->obj->upload->data());
				$data_media['file'] = $image['upload_data']['file_name'];

			}

			if(!$media = $this->get_medias(array('src_id' => $last_id, 'module' => $module)))
			{
				$this->obj->db->insert($this->obj->config->item('table_medias'), $data_media);
			}
			else
			{
				$this->obj->db->where(array('src_id' => $last_id, 'module' => $module))->update($this->obj->config->item('table_medias'), $data_media);
			}
		}

		return $last_id;

	}

	public function traitement_parag_type_5($module = '')
	{
		$fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			)
		);

		$this->obj->form_validation->set_rules($fields_validation);

		$this->obj->form_validation->set_error_delimiters('', '<br />');

		$last_id = 0;

		if ($this->obj->form_validation->run() == FALSE)
		{
			$this->obj->session->set_flashdata('alerte', validation_errors());
			$this->obj->session->set_flashdata('post', $this->obj->input->post());
			redirect($this->obj->input->post('redirect_uri'));
		}
		else
		{
			$src_id = $this->obj->input->post('src_id');

			if($this->obj->input->post('paragraphs_id')) $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/editParag/'.$src_id.'/'.$this->obj->input->post('paragraphs_id');
			else $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/createParag/'.$src_id;

			if($this->obj->input->post('live_view')) $redirect = $this->obj->session->userdata('redirect_admin_live_view');

			if(!$this->obj->input->post('paragraphs_id')) $this->_check_ext(array('swf'), $module, $src_id, $redirect, $_POST);

			$data = array('src_id' => $src_id, 'title' => htmlentities($this->obj->input->post('title')), 'class' => $this->obj->input->post('class'), 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
			if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
			{
				$data['date_modified'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->where('id', $paragraphs_id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$last_id = $paragraphs_id;
			}
			else
			{
				$data['date_added'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
				$last_id = $this->obj->db->insert_id();
			}

			$config['upload_path'] = './'.$this->obj->config->item('medias_folder').'/swf';
			$config['allowed_types'] = 'swf';
			$config['overwrite'] = FALSE;

			$options_post = $this->obj->input->post('options');

			if($this->obj->input->post('title')) $config['file_name'] = format_title($this->obj->input->post('title')).'-'.$last_id.'_'.time();
			else $config['file_name'] = 'paragraph-'.$last_id.'_'.time();

			$this->obj->load->library('upload', $config);

			$options = serialize($options_post);

			$data_media = array(
					'module' 	=> $module,
					'src_id' 	=> $last_id,
					'options' 	=> $options
			);

			if ($this->obj->upload->do_upload('media'))
			{
				$image = array('upload_data' => $this->obj->upload->data());
				$data_media['file'] = $image['upload_data']['file_name'];
			}

			if(!$media = $this->get_medias(array('src_id' => $last_id, 'module' => $module)))
			{
				$this->obj->db->insert($this->obj->config->item('table_medias'), $data_media);
			}
			else
			{
				$this->obj->db->where(array('src_id' => $last_id, 'module' => $module))->update($this->obj->config->item('table_medias'), $data_media);
			}
		}

		return $last_id;

	}

	public function traitement_parag_type_6($module = '')
	{
		$fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			)
		);

		$this->obj->form_validation->set_rules($fields_validation);

		$this->obj->form_validation->set_error_delimiters('', '<br />');

		$last_id = 0;

		if ($this->obj->form_validation->run() == FALSE)
		{
			$this->obj->session->set_flashdata('alerte', validation_errors());
			$this->obj->session->set_flashdata('post', $this->obj->input->post());
			redirect($this->obj->input->post('redirect_uri'));
		}
		else
		{
			$src_id = $this->obj->input->post('src_id');

			if($this->obj->input->post('paragraphs_id')) $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/editParag/'.$src_id.'/'.$this->obj->input->post('paragraphs_id');
			else $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/createParag/'.$src_id;

			if($this->obj->input->post('live_view')) $redirect = $this->obj->session->userdata('redirect_admin_live_view');

			if(!$this->obj->input->post('paragraphs_id')) $this->_check_ext(array('flv', 'mp4'), $module, $src_id, $redirect, $_POST);

			$data = array('src_id' => $src_id, 'title' => htmlentities($this->obj->input->post('title')), 'class' => $this->obj->input->post('class'), 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
			if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
			{
				$data['date_modified'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->where('id', $paragraphs_id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$last_id = $paragraphs_id;
			}
			else
			{
				$data['date_added'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
				$last_id = $this->obj->db->insert_id();
			}

			$config['upload_path'] = './'.$this->obj->config->item('medias_folder').'/videos';
			$config['allowed_types'] = 'flv|mp4';
			$config['overwrite'] = FALSE;

			$options_post = $this->obj->input->post('options');

			if($this->obj->input->post('title')) $config['file_name'] = format_title($this->obj->input->post('title')).'-'.$last_id.'_'.time();
			else $config['file_name'] = 'paragraph-'.$last_id.'_'.time();

			$this->obj->load->library('upload', $config);

			$options = serialize($options_post);

			$data_media = array(
					'module' 	=> $module,
					'src_id' 	=> $last_id,
					'options' 	=> $options
			);

			if ($this->obj->upload->do_upload('media'))
			{
				$image = array('upload_data' => $this->obj->upload->data());
				$data_media['file'] = $image['upload_data']['file_name'];

			}

			if(!$media = $this->get_medias(array('src_id' => $last_id, 'module' => $module)))
			{
				$this->obj->db->insert($this->obj->config->item('table_medias'), $data_media);
			}
			else
			{
				$this->obj->db->where(array('src_id' => $last_id, 'module' => $module))->update($this->obj->config->item('table_medias'), $data_media);
			}

		}

		return $last_id;

	}

	public function traitement_parag_type_7($module = '')
	{
		$fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'title_2',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'class_2',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'body',
				'label'   => 	$this->obj->lang->line('validation_body').' 1',
				'rules'   => 	'trim|required'
			),
			array(
				'field'   => 	'body_2',
				'label'   => 	$this->obj->lang->line('validation_body').' 2',
				'rules'   => 	'trim|required'
			)
		);
		$this->obj->form_validation->set_rules($fields_validation);

		$this->obj->form_validation->set_error_delimiters('', '<br />');

		$last_id = 0;

		if ($this->obj->form_validation->run() == FALSE)
		{
			$this->obj->session->set_flashdata('alerte', validation_errors());
			$this->obj->session->set_flashdata('post', $this->obj->input->post());
			redirect($this->obj->input->post('redirect_uri'));
		}
		else
		{
			$src_id = $this->obj->input->post('src_id');
			$data = array('src_id' => $src_id, 'title' => htmlentities($this->obj->input->post('title')), 'title_2' => htmlentities($this->obj->input->post('title_2')), 'class' => $this->obj->input->post('class'), 'class_2' => $this->obj->input->post('class_2'), 'body' => $this->obj->input->post('body'), 'body_2' => $this->obj->input->post('body_2'), 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
			if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
			{
				$data['date_modified'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->where('id', $paragraphs_id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$last_id = $paragraphs_id;
			}
			else
			{
				$data['date_added'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
				$last_id = $this->obj->db->insert_id();
			}
		}

		return $last_id;

	}

	public function traitement_parag_type_8($module = '')
	{
		$fields_validation = array(
			array(
				'field'   => 	'title',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'title_2',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'title_3',
				'label'   => 	$this->obj->lang->line('validation_title'),
				'rules'   => 	'trim|max_length[64]|xss_clean'
			),
			array(
				'field'   => 	'class',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'class_2',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'class_3',
				'label'   => 	$this->obj->lang->line('validation_class'),
				'rules'   => 	'trim|max_length[32]|xss_clean'
			),
			array(
				'field'   => 	'body',
				'label'   => 	$this->obj->lang->line('validation_body').' 1',
				'rules'   => 	'trim|required'
			),
			array(
				'field'   => 	'body_2',
				'label'   => 	$this->obj->lang->line('validation_body').' 2',
				'rules'   => 	'trim|required'
			),
			array(
				'field'   => 	'body_3',
				'label'   => 	$this->obj->lang->line('validation_body').' 3',
				'rules'   => 	'trim|required'
			)
		);
		$this->obj->form_validation->set_rules($fields_validation);

		$this->obj->form_validation->set_error_delimiters('', '<br />');

		$last_id = 0;

		if ($this->obj->form_validation->run() == FALSE)
		{
			$this->obj->session->set_flashdata('alerte', validation_errors());
			$this->obj->session->set_flashdata('post', $this->obj->input->post());
			redirect($this->obj->input->post('redirect_uri'));
		}
		else
		{
			$src_id = $this->obj->input->post('src_id');
			$data = array('src_id' => $src_id, 'title' => htmlentities($this->obj->input->post('title')), 'title_2' => htmlentities($this->obj->input->post('title_2')), 'title_3' => htmlentities($this->obj->input->post('title_3')), 'class' => $this->obj->input->post('class'), 'class_2' => $this->obj->input->post('class_2'), 'class_3' => $this->obj->input->post('class_3'), 'body' => $this->obj->input->post('body'), 'body_2' => $this->obj->input->post('body_2'), 'body_3' => $this->obj->input->post('body_3'), 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
			if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
			{
				$data['date_modified'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->where('id', $paragraphs_id);
				$this->obj->db->update($this->obj->config->item('table_paragraphs'));
				$last_id = $paragraphs_id;
			}
			else
			{
				$data['date_added'] = mktime();
				$this->obj->db->set($data);
				$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
				$last_id = $this->obj->db->insert_id();
			}
		}

		return $last_id;

	}

	public function traitement_parag_type_9($module = '')
	{
		$src_id = $this->obj->input->post('src_id');

		if($this->obj->input->post('paragraphs_id')) $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/editParag/'.$src_id.'/'.$this->obj->input->post('paragraphs_id');
		else $redirect = $this->obj->config->item('admin_folder').'/'.$module.'/createParag/'.$src_id;

		if($this->obj->input->post('live_view')) $redirect = $this->obj->session->userdata('redirect_admin_live_view');

		if(!$this->obj->input->post('paragraphs_id')) $this->_check_ext(array('gif', 'jpg', 'jpeg', 'png'), $module, $src_id, $redirect, $_POST);

		$data = array('src_id' => $src_id, 'module' => $module, 'types_id' => $this->obj->input->post('types_id'), 'lang' => $this->obj->user->lang);
		if($paragraphs_id = $this->obj->input->post('paragraphs_id'))
		{
			$data['date_modified'] = mktime();
			$this->obj->db->set($data);
			$this->obj->db->where('id', $paragraphs_id);
			$this->obj->db->update($this->obj->config->item('table_paragraphs'));
			$last_id = $paragraphs_id;
		}
		else
		{
			$data['date_added'] = mktime();
			$this->obj->db->set($data);
			$this->obj->db->insert($this->obj->config->item('table_paragraphs'));
			$last_id = $this->obj->db->insert_id();
		}

		$config['upload_path'] = './'.$this->obj->config->item('medias_folder').'/images';
		$config['allowed_types'] = 'gif|jpg|jpeg|png|zip';
		$config['overwrite'] = FALSE;

		$options_post = $this->obj->input->post('options');

		$config['file_name'] = 'paragraph-'.$last_id.'_'.time();

		$this->obj->load->library('upload', $config);

		$options = serialize($options_post);

		$data_media = array(
				'module' 	=> $module,
				'src_id' 	=> $last_id,
				'options' 	=> $options
		);

		if ($this->obj->upload->do_upload('media'))
		{
			$image = array('upload_data' => $this->obj->upload->data());
			$data_media['file'] = $image['upload_data']['file_name'];
		}

		$this->obj->db->insert($this->obj->config->item('table_medias'), $data_media);

		return $last_id;

	}

	private function _check_ext($array = '', $module = '', $src_id = '', $redirect = '', $post = '')
	{
		if(isset($_FILES['media']['name']) && $_FILES['media']['name'] == '')
		{
			$this->obj->session->set_flashdata('alerte', $this->obj->lang->line('alert_file_empty'));
			$this->obj->session->set_flashdata('post', $post);
			redirect($redirect);
		}
		else
		{
			$ext = substr(strtolower(strrchr(basename($_FILES['media']['name']), ".")), 1);
			if(!in_array($ext, $array))
			{
				$this->obj->session->set_flashdata('alerte', $this->obj->lang->line('alert_file_ext'));
				$this->obj->session->set_flashdata('post', $post);
				redirect($redirect);
			}

		}
	}

	//------------------------------------------------- Display

	public function print_paragraph ($where = '', $module = '', $size = '')
	{
		$this->obj->db->select('*');

		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->obj->db->where($key, $value);
			}
		}

		$this->obj->db->where(array('lang' => $this->obj->user->lang));
		$this->obj->db->order_by('ordering', 'asc');
		$query = $this->obj->db->get($this->obj->config->item('table_paragraphs'));

		$size = ($size) ? $size : $this->obj->layout->size_stylesheets();

		$html = '';
		$i=1;
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$types_id = 'print_paragraph_type_'.$row['types_id'];
				$paragraphs_types = $this->list_paragraphs_types(array('active' => 1));
				$html .= $this->$types_id($row, $module, $size, $i, $query->num_rows(), $paragraphs_types, $query->num_rows());
				$i++;
			}
		}

		return $html;
	}

	public function print_live_view($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '')
	{
		//pre_affiche($row);
		$html = '';
		$html .= '<div class="box_live_view">';
		$html .= '<span>'.$this->obj->lang->line('title_paragraph').' : '.$this->obj->lang->line('text_paragraph_type_'.(isset($paragraphs_types[$row['types_id']]) ? $paragraphs_types[$row['types_id']]['code'] : '')).'</span>';
		$html .= '<ul>';
		$html .= '<li>';
		if ($row['active'] == 1)
		{
			$html .= '<a href="'.site_url($this->obj->config->item('admin_folder').'/'.$module.'/flagParagLiveView/'.$row['id'].'/'.$row['active']).'" title="'.$this->obj->lang->line('btn_desactivate_paragraph').'" class="tooltip"><img src="'.site_url(APPPATH.'views/assets/img/icons/status_green.png').'" alt="'.$this->obj->lang->line('btn_desactivate').'"/></a>';
		}
		else
		{
			$html .= '<a href="'.site_url($this->obj->config->item('admin_folder').'/'.$module.'/flagParagLiveView/'.$row['id'].'/'.$row['active']).'" title="'.$this->obj->lang->line('btn_activate_paragraph').'" class="tooltip"><img src="'.site_url(APPPATH.'views/assets/img/icons/status_red.png').'" alt="'.$this->obj->lang->line('btn_activate').'"/></a>';
		}
		$html .= '</li>';
		if($i != 1)
		{
			$html .= '<li><a href="'.site_url($this->obj->config->item('admin_folder').'/'.$module.'/moveParagLiveView/'.$row['id'].'/up').'" title="'.$this->obj->lang->line('btn_sort_ascending_paragraph').'" class="tooltip"><img src="'.site_url(APPPATH.'views/assets/img/icons/sort_ascending.png').'" width="16" height="16" alt="'.$this->obj->lang->line('btn_sort_ascending').'"/></a></li>';
		}
		if(($num_rows) != $i)
		{
			$html .= '<li><a href="'.site_url($this->obj->config->item('admin_folder').'/'.$module.'/moveParagLiveView/'.$row['id'].'/down').'" title="'.$this->obj->lang->line('btn_sort_descending_paragraph').'" class="tooltip"><img src="'.site_url(APPPATH.'views/assets/img/icons/sort_descending.png').'" width="16" height="16" alt="'.$this->obj->lang->line('btn_sort_descending').'"/></a></li>';
		}
		$html .= '<li><a href="'.site_url($this->obj->config->item('admin_folder').'/'.$module.'/selectParagLiveView/'.$row['src_id']).'" title="'.$this->obj->lang->line('btn_create_paragraph').'" data-title="'.$this->obj->lang->line('btn_create_paragraph').'" class="dialog tooltip"><img src="'.site_url(APPPATH.'views/assets/img/icons/create.png').'" alt="'.$this->obj->lang->line('btn_create').'" width="16px" height="16px"/></a></li>';
		$html .= '<li><a href="'.site_url($this->obj->config->item('admin_folder').'/'.$module.'/editParagLiveView/'.$row['id']).'" title="'.$this->obj->lang->line('btn_edit_paragraph').' : '.$this->obj->lang->line('text_paragraph_type_'.(isset($paragraphs_types[$row['types_id']]) ? $paragraphs_types[$row['types_id']]['code'] : '')).'" data-title="'.$this->obj->lang->line('btn_edit_paragraph').' : '.$this->obj->lang->line('text_paragraph_type_'.(isset($paragraphs_types[$row['types_id']]) ? $paragraphs_types[$row['types_id']]['code'] : '')).'" class="dialog tooltip"><img src="'.site_url(APPPATH.'views/assets/img/icons/edit.png').'" alt="'.$this->obj->lang->line('btn_edit').'" width="16px" height="16px"/></a></li>';
		$html .= '<li><a href="'.site_url($this->obj->config->item('admin_folder').'/'.$module.'/deleteParagLiveView/'.$row['id']).'" class="tooltip" onclick="javascript:return confirmDelete();" title="'.$this->obj->lang->line('btn_delete_paragraph').'"><img src="'.site_url(APPPATH.'views/assets/img/icons/delete.png').'" alt="'.$this->obj->lang->line('btn_delete').'" width="16px" height="16px"/></a></li>';
		$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}

	public function print_paragraph_type_1($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			$html .= '<div class="cms_row_t1 cms_row'.($row['class'] ? ' '.$row['class'] : '').($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">'."\n";
			if($this->obj->user->liveView)
				$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
			$html .= '<div class="parag">'."\n";
			$html .= (!$row['title']) ? '' : '<h2 class="'.$row['class'].'"><span>'.html_entity_decode($row['title']).'</span></h2>';
			$html .= '<div class="cms_row_inner '.$row['class'].'">'."\n";
			$html .= $row['body']."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
		}

		return $html;
	}

	public function print_paragraph_type_2($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			if($media = $this->get_medias(array('src_id' => $row['id'], 'module' => $module)))
			{
				if($media['options']['ratio'] != 0)
				{
					$width = round($size['main']*$media['options']['ratio']);
					$height = round($width/(get_media_size($this->obj->config->item('medias_folder').'/images/'.$media['file'], 0)/get_media_size($this->obj->config->item('medias_folder').'/images/'.$media['file'], 1)));
				}
				else
				{
					$width = get_media_size($this->obj->config->item('medias_folder').'/images/'.$media['file'], 0);
					$height = get_media_size($this->obj->config->item('medias_folder').'/images/'.$media['file'], 1);
				}
				$html .= '<div class="cms_row_t2 cms_row'.($row['class'] ? ' '.$row['class'] : '').($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">'."\n";
				if($this->obj->user->liveView)
					$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
				$html .= '<div class="parag">'."\n";
				$html .= (!$row['title']) ? '' : '<h2 class="'.$row['class'].' '.$media['options']['position'].'"><span>'.html_entity_decode($row['title']).'</span></h2>';
				$html .= '<div class="cms_row_inner '.$row['class'].' '.$media['options']['position'].'">'."\n";
				if(isset($media['options']['popup']) && $media['options']['popup'] == 1)
				{
					$html .= '<div class="cms_row_inner_media '.$row['class'].'"><a href="'.site_url($this->obj->config->item('medias_folder').'/images/x600/'.$media['file']).'" class="colorbox" rel="paragraphs"><img src="'.site_url('medias/images/'.$width.'x/'.$media['file']).'" alt="'.$media['options']['alt'].'" width="'.$width.'" height="'.$height.'"/></a></div>'."\n";
					if($media['options']['legend'])
					{
						$html .= '<div class="cms_row_inner_legend '.$row['class'].'"><a href="'.site_url($this->obj->config->item('medias_folder').'/images/x600/'.$media['file']).'" class="colorbox" rel="paragraphs">'.$media['options']['legend'].'</a></div>'."\n";
					}
				}
				elseif(isset($media['options']['link']) && $media['options']['link'])
				{
					$uri = $this->obj->system->get_uri($media['options']['link']);
					$target = ($media['options']['target']) ? ' onclick="window.open(this.href);return false;"' : '';
					$html .= '<div class="cms_row_inner_media '.$row['class'].'"><a href="'.$uri.'"'.$target.'><img src="'.site_url($this->obj->config->item('medias_folder').'/images/'.$width.'x/'.$media['file']).'" alt="'.$media['options']['alt'].'" width="'.$width.'" height="'.$height.'"/></a></div>'."\n";
					if($media['options']['legend'])
					{
						$html .= '<div class="cms_row_inner_legend '.$row['class'].'"><a href="'.$uri.'"'.$target.'>'.$media['options']['legend'].'</a></div>'."\n";
					}
				}
				else
				{
					if($media['file']) $html .= '<div class="cms_row_inner_media '.$row['class'].'"><img src="'.site_url($this->obj->config->item('medias_folder').'/images/'.$width.'x/'.$media['file']).'" alt="'.$media['options']['alt'].'" width="'.$width.'" height="'.$height.'"/></div>'."\n";
					if($media['options']['legend']) $html .= '<div class="cms_row_inner_legend '.$row['class'].'">'.$media['options']['legend'].'</div>'."\n";
				}
				$html .= '</div>'."\n";
				$html .= '</div>'."\n";
				$html .= '</div>'."\n";
			}
		}

		return $html;
	}

	public function print_paragraph_type_3($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			if($media = $this->get_medias(array('src_id' => $row['id'], 'module' => $module)))
			{
				$width = round($size['main']*$media['options']['ratio']);
				$height = get_media_size($this->obj->config->item('medias_folder').'/images/.cache/'.$width.'x/'.$media['file'], 1);
				$css_float_media = 'float_'.$media['options']['position'];
				if($css_float_media == 'float_left') $css_float_text = 'float_right';
				if($css_float_media == 'float_right') $css_float_text = 'float_left';
				$html .= '<div class="cms_row_t3 cms_row'.($row['class'] ? ' '.$row['class'] : '').($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">'."\n";
				if($this->obj->user->liveView)
					$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
				$html .= '<div class="parag">'."\n";
				$html .= (!$row['title']) ? '' : '<h2 class="'.$row['class'].'"><span>'.html_entity_decode($row['title']).'</span></h2>';
				$html .= '<div class="cms_row_inner '.$row['class'].' '.$media['options']['position'].'">'."\n";
				$html .= '<div class="cms_row_inner_text '.$row['class'].' '.$css_float_text.'" style="width:'.($size['main']-$width-$size['padding']).'px;">'.$row['body'].'</div>'."\n";
				if(isset($media['options']['popup']) && $media['options']['popup'] == 1)
				{
					$html .= '<div class="cms_row_inner_media '.$row['class'].' '.$css_float_media.'"><a href="'.site_url($this->obj->config->item('medias_folder').'/images/x600/'.$media['file']).'" class="colorbox" rel="paragraphs"><img src="'.site_url('medias/images/'.$width.'x/'.$media['file']).'" alt="'.$media['options']['alt'].'" width="'.$width.'" height="'.$height.'"/></a></div>'."\n";
					if($media['options']['legend'])
					{
						$html .= '<div class="cms_row_inner_legend clear '.$row['class'].' '.$css_float_media.'"><a href="'.site_url($this->obj->config->item('medias_folder').'/images/x600/'.$media['file']).'" class="colorbox" rel="paragraphs">'.$media['options']['legend'].'</a></div>'."\n";
					}
				}
				elseif(isset($media['options']['link']) && $media['options']['link'])
				{
					$uri = $this->obj->system->get_uri($media['options']['link']);
					$target = ($media['options']['target']) ? ' onclick="window.open(this.href);return false;"' : '';
					$html .= '<div class="cms_row_inner_media '.$row['class'].' '.$css_float_media.'"><a href="'.$uri.'"'.$target.'><img src="'.site_url($this->obj->config->item('medias_folder').'/images/'.$width.'x/'.$media['file']).'" alt="'.$media['options']['alt'].'" width="'.$width.'" height="'.$height.'"/></a></div>'."\n";
					if($media['options']['legend'])
					{
						$html .= '<div class="cms_row_inner_legend clear '.$row['class'].' '.$css_float_media.'"><a href="'.$uri.'"'.$target.'>'.$media['options']['legend'].'</a></div>'."\n";
					}
				}
				else
				{
					if($media['file']) $html .= '<div class="cms_row_inner_media '.$row['class'].'"><img src="'.site_url($this->obj->config->item('medias_folder').'/images/'.$width.'x/'.$media['file']).'" alt="'.$media['options']['alt'].'" width="'.$width.'" height="'.$height.'"/></div>'."\n";
					if($media['options']['legend']) $html .= '<div class="cms_row_inner_legend clear '.$row['class'].'">'.$media['options']['legend'].'</div>'."\n";
				}
				$html .= '</div>'."\n";
				$html .= '</div>'."\n";
				$html .= '</div>'."\n";
			}
		}

		return $html;
	}

	public function print_paragraph_type_5($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			if($media = $this->get_medias(array('src_id' => $row['id'], 'module' => $module)))
			{
				$file = './'.$this->obj->config->item('medias_folder').'/swf/'.$media['file'];
				if(is_file($file))
				{
					$width = get_media_size($file, 0);
					$height = get_media_size($file, 1);
					if($width > $size['main'])
					{
						$width = $size['main'];
						$height = round($width/round($width/$height));
					}
					$html = '
					<script type="text/javascript">
					swfobject.embedSWF("'.site_url($this->obj->config->item('medias_folder').'/swf/'.$media['file']).'", "cms_row_swf_'.$media['id'].'", "'.$width.'", "'.$height.'", "9.0.0", "'.site_url($this->obj->config->item('medias_folder').'/swf/expressInstall.swf').'");
					</script>
					';
					$html .= '<div class="cms_row_t5 cms_row'.($row['class'] ? ' '.$row['class'] : '').($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">'."\n";
					if($this->obj->user->liveView)
						$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
					$html .= '<div class="parag">'."\n";
					$html .= (!$row['title']) ? '' : '<h2 class="'.$row['class'].' '.$media['options']['position'].'"><span>'.$row['title'].'</span></h2>';
					$html .= '<div class="cms_row_inner '.$row['class'].' '.$media['options']['position'].'">'."\n";
					$html .= '<div class="cms_row_inner_media '.$row['class'].'"><div id="cms_row_swf_'.$media['id'].'"><a href="http://www.adobe.com/go/getflashplayer" title="'.$this->obj->lang->line('text_get_adobe_flash_player').'"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="'.$this->obj->lang->line('text_get_adobe_flash_player').'"/></a></div></div>'."\n";
					$html .= '</div>'."\n";
					$html .= '</div>'."\n";
					$html .= '</div>'."\n";
				}

			}
		}

		return $html;
	}

	public function print_paragraph_type_6($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			if($media = $this->get_medias(array('src_id' => $row['id'], 'module' => $module)))
			{
				$file = './'.$this->obj->config->item('medias_folder').'/videos/'.$media['file'];
				if(is_file($file))
				{
					$width = round($size['main']*$media['options']['ratio']);
					$height = round($width*3/4);
					(isset($media['options']['autostart']) && $media['options']['autostart'] == 1) ? $autostart = 'true' : $autostart = 'false';
					(isset($media['options']['fullscreen']) && $media['options']['fullscreen'] == 1) ? $fullscreen = 'true' : $fullscreen = 'false';
					$html = '
					<script type="text/javascript">
					var flashvars = {
						"file":               "'.site_url($this->obj->config->item('medias_folder').'/videos/'.$media['file']).'",
						"autostart":          "'.$autostart.'"
					};
					var params = {
						"allowfullscreen":    "'.$fullscreen.'",
						"allowscriptaccess":  "always",
						"bgcolor":            "#000",
						"wmode": 			  "opaque"
					};
					var attributes = {
						"id":                 "cms_row_videos",
						"name":               "cms_row_videos"
					};
					swfobject.embedSWF("'.site_url($this->obj->config->item('medias_folder').'/swf/player.swf').'", "cms_row_videos_'.$media['id'].'", "'.$width.'", "'.$height.'", "9", "false", flashvars, params, attributes);
					</script>
					';
					$html .= '<div class="cms_row_t6 cms_row'.($row['class'] ? ' '.$row['class'] : '').($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">'."\n";
					if($this->obj->user->liveView)
						$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
					$html .= '<div class="parag">'."\n";
					$html .= (!$row['title']) ? '' : '<h2 class="'.$row['class'].' '.$media['options']['position'].'"><span>'.$row['title'].'</span></h2>';
					$html .= '<div class="cms_row_inner '.$row['class'].' '.$media['options']['position'].'">'."\n";
					$html .= '<div class="cms_row_inner_media '.$row['class'].'"><div id="cms_row_videos_'.$media['id'].'"><a href="http://www.adobe.com/go/getflashplayer" title="'.$this->obj->lang->line('text_get_adobe_flash_player').'"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="'.$this->obj->lang->line('text_get_adobe_flash_player').'"/></a></div></div>'."\n";
					$html .= '</div>'."\n";
					$html .= '</div>'."\n";
					$html .= '</div>'."\n";
				}

			}
		}

		return $html;
	}

	public function print_paragraph_type_7($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			$html .= '<div class="cms_row_t7'.($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">';
			if($this->obj->user->liveView)
				$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
			$html .= '<div class="parag">'."\n";
			//First col
			$html .= '<div class="cms_row_2_cols '.$row['class'].'">'."\n";
			$html .= (!$row['title']) ? '' : '<h2 class="'.$row['class'].'"><span>'.html_entity_decode($row['title']).'</span></h2>';
			$html .= '<div class="cms_row_inner_text cms_row_inner_2_cols '.$row['class'].'">'."\n";
			$html .= $row['body']."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			//Second col
			$html .= '<div class="cms_row_2_cols '.$row['class_2'].'" style="margin-right:0;">'."\n";
			$html .= (!$row['title_2']) ? '' : '<h2 class="'.$row['class_2'].'"><span>'.html_entity_decode($row['title_2']).'</span></h2>';
			$html .= '<div class="cms_row_inner_text cms_row_inner_2_cols '.$row['class_2'].'">'."\n";
			$html .= $row['body_2']."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			$html .= '</div>';
		}

		return $html;
	}

	public function print_paragraph_type_8($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			$html .= '<div class="cms_row_t8 cms_row'.($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">';
			if($this->obj->user->liveView)
				$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
			$html .= '<div class="parag">'."\n";
			//First col
			$html .= '<div class="cms_row_3_cols '.$row['class'].'">'."\n";
			$html .= (!$row['title']) ? '' : '<h2 class="'.$row['class'].'"><span>'.html_entity_decode($row['title']).'</span></h2>';
			$html .= '<div class="cms_row_inner_text cms_row_inner_2_cols '.$row['class'].'">'."\n";
			$html .= $row['body']."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			//Second col
			$html .= '<div class="cms_row_3_cols '.$row['class_2'].'">'."\n";
			$html .= (!$row['title_2']) ? '' : '<h2 class="'.$row['class_2'].'"><span>'.html_entity_decode($row['title_2']).'</span></h2>';
			$html .= '<div class="cms_row_inner_text cms_row_inner_2_cols '.$row['class_2'].'">'."\n";
			$html .= $row['body_2']."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			//three col
			$html .= '<div class="cms_row_3_cols '.$row['class_3'].'" style="margin-right:0;">'."\n";
			$html .= (!$row['title_3']) ? '' : '<h2 class="'.$row['class_3'].'"><span>'.html_entity_decode($row['title_3']).'</span></h2>';
			$html .= '<div class="cms_row_inner_text cms_row_inner_3_cols '.$row['class_3'].'">'."\n";
			$html .= $row['body_3']."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			$html .= '</div>'."\n";
			$html .= '</div>';
		}

		return $html;
	}

	public function print_paragraph_type_9($row = '', $module = '', $size = '', $i = 1, $num_rows = 0, $paragraphs_types = '', $count_total = 0)
	{
		$html = '';
		if($row && is_array($row))
		{
			if($medias = $this->list_medias(array('order_by' => 'ordering asc','where' => array('src_id' => $row['id'], 'module' => $module))))
			{
				$html .= '<div class="cms_row'.($i == 1 ? ' first' : '').($count_total == $i ? ' last' : '').'">'."\n";
				if($this->obj->user->liveView)
					$html .= $this->print_live_view($row, $module, $size, $i, $num_rows, $paragraphs_types);
				$html .= '<div class="parag">'."\n";
				$html .= '<div class="cms_row_t9 cms_row_slider_images" id="slider_'.$row['id'].'" style="width:'.$size['main'].'px;">'."\n";
				$html .= '<ul>'."\n";
				foreach($medias as $media)
				{
					if($media['file'] && is_file('medias/images/'.$media['file']))
					{
						$width = $size['main'];
						$height = round($width/(get_media_size($this->obj->config->item('medias_folder').'/images/'.$media['file'], 0)/get_media_size($this->obj->config->item('medias_folder').'/images/'.$media['file'], 1)));
						$html .= '<li><img src="'.site_url($this->obj->config->item('medias_folder').'/images/'.$width.'x/'.$media['file']).'" alt="'.$media['options']['alt'].'" width="'.$width.'" height="'.$height.'"/></li>'."\n";
					}

				}
				$html .= '</ul>'."\n";
				$html .= '</div>'."\n";
				$html .= '</div>'."\n";
				$html .= '</div>'."\n";
				$html .= '
				<script type="text/javascript">
				$(document).ready(function(){
					$("#slider_'.$row['id'].'").easySlider({
						auto: true,
						continuous: true,
						numeric: true,
						pause: 5000
					});
				});
				</script>
				';
			}
		}

		return $html;
	}

}
