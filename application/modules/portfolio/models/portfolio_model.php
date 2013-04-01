<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class portfolio_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	//---------------------------------------------- Categories
	public function create_categories()
	{
		$data_categories = array(
			'parent_id' 		=> set_value('parent_id'),
			'active' 			=> set_value('active'),
			'ordering' 			=> 0
		);

		$data_categories_lang = array(
			'lang' 				=> $this->user->lang,
			'title' 			=> set_value('title'),
			'uri' 				=> format_title($this->input->post('uri')),
			'body' 				=> $this->input->post('body'),
			'meta_title' 		=> $this->input->post('meta_title'),
			'meta_description' 	=> $this->input->post('meta_description'),
			'meta_keywords' 	=> $this->input->post('meta_keywords')
		);

		if ($this->input->post('uri') == '')
		{
			$data_categories['uri'] = format_title($this->input->post('title'));
		}

		if($id = $this->input->post('id'))
		{
			$data_categories['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_portfolio_categories'), $data_categories);
			$last_id = $id;
			$this->db->where(array('categories_id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_portfolio_categories_lang'), $data_categories_lang);
		}
		else
		{
			$data_categories['date_added'] = mktime();
			$this->db->insert($this->config->item('table_portfolio_categories'), $data_categories);
			$last_id = $this->db->insert_id();
			$data_categories_lang['categories_id'] = $last_id;
			foreach($this->language->codes as $lang)
			{
				$data_categories_lang['lang'] = $lang;
				$this->db->insert($this->config->item('table_portfolio_categories_lang'), $data_categories_lang);
			}
		}

		//-----Medias
		if (isset($_FILES['image']['name']) &&  $_FILES['image']['name'] != '')
		{
			$config['upload_path'] = './'.$this->config->item('medias_folder').'/images/';
			$config['allowed_types'] = 'jpg|gif|png';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('image'))
			{
				$error = array('error' => $this->upload->display_errors());
			}
			else
			{
				//Delete
				if($media = $this->medias->get_medias(array('src_id' => $last_id, 'module' => 'portfolio_categories')))
				{
					$this->medias->delete_medias($media['id']);
				}

				//Insert
				$this->load->library('image_lib');
				$image_data = $this->upload->data();

				$file = $image_data['file_name'];
				$file_extension = $image_data['file_ext'];
				$file_rewrite = 'portfolio_categories-'.$last_id.'-'.time().$file_extension;

				$options = array(
					'cover' 	=> 0,
					'legend' 	=> $this->input->post('legend')
				);

				if(is_readable('./'.$this->config->item('medias_folder').'/images/'.$file))  rename('./'.$this->config->item('medias_folder').'/images/'.$file, './'.$this->config->item('medias_folder').'/images/'.$file_rewrite);

				$this->db->set('src_id', $last_id);
				$this->db->set('module', 'portfolio_categories');
				$this->db->set('file', $file_rewrite);
				$this->db->set('options', serialize($options));
				$this->db->set('ordering', 0);
				$this->db->insert($this->config->item('table_medias'));

			}

		}

		return $last_id;
	}

	public function move_categories($id = '', $direction = '')
	{
		$query = $this->db->get_where($this->config->item('table_portfolio_categories'), array('id' => $id));

		if ($row = $query->row())
		{
			$parent_id = $row->parent_id;
		}
		else
		{
			$parent_id = 0;
		}

		$move = ($direction == 'up') ? -1 : 1;
		$this->db->where(array('id' => $id));

		$this->db->set('ordering', 'ordering+'.$move, FALSE);
		$this->db->update($this->config->item('table_portfolio_categories'));

		$this->db->where(array('id' => $id));
		$query = $this->db->get($this->config->item('table_portfolio_categories'));
		$row = $query->row();
		$new_ordering = $row->ordering;

		if ($move > 0)
		{
			$this->db->set('ordering', 'ordering-1', FALSE);
			$this->db->where(array('ordering <=' => $new_ordering, 'id <>' => $id, 'parent_id' => $parent_id, 'lang' => $this->user->lang));
			$this->db->update($this->config->item('table_portfolio_categories'));
		}
		else
		{
			$this->db->set('ordering', 'ordering+1', FALSE);
			$where = array('ordering >=' => $new_ordering, 'id <>' => $id, 'parent_id' => $parent_id, 'lang' => $this->user->lang);

			$this->db->where($where);
			$this->db->update($this->config->item('table_portfolio_categories'));
		}

		//Reordinate
		$i = 0;
		$this->db->order_by('ordering');
		$this->db->where(array('parent_id' => $parent_id, 'lang' => $this->user->lang));

		$query = $this->db->get($this->config->item('table_portfolio_categories'));

		if ($rows = $query->result())
		{
			foreach ($rows as $row)
			{
				$this->db->set('ordering', $i);
				$this->db->where('id', $row->id);
				$this->db->update($this->config->item('table_portfolio_categories'));
				$i++;
			}
		}
	}

	public function delete_categories($categories_id = '')
	{
		if($categories_id != 1)
		{
			$this->db->where(array('id' => $categories_id))->delete($this->config->item('table_portfolio_categories'));
			$this->db->where(array('categories_id' => $categories_id))->delete($this->config->item('table_portfolio_categories_lang'));
			$this->db->delete($this->config->item('table_medias'), array('src_id' => $categories_id, 'module' => 'portfolio_categories'));
		}
	}

	public function delete_categories_to_medias($categories_id = '')
	{
		$this->db->where(array('categories_id' => $categories_id))->delete($this->config->item('table_portfolio_categories_to_medias'));
	}

	//---------------------------------------------- Medias
	public function create_medias()
	{
		$data_medias = array(
			'is_box' 					=> set_value('is_box'),
			'categories_id_default' 	=> set_value('categories_id_default'),
			'active' 					=> set_value('active'),
		);

		$data_medias_lang = array(
			'lang' 				=> $this->user->lang,
			'title' 			=> $this->input->post('title'),
			'uri' 				=> format_title($this->input->post('uri')),
			'legend' 			=> $this->input->post('legend'),
			'alt' 				=> $this->input->post('alt'),
			'body' 				=> $this->input->post('body')
		);

		if ($this->input->post('uri') == '')
		{
			$data_medias_lang['uri'] = format_title($this->input->post('title'));
		}

		if($id = $this->input->post('id'))
		{
			$data_medias['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_portfolio_medias'), $data_medias);
			$last_id = $id;
			$this->db->where(array('medias_id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_portfolio_medias_lang'), $data_medias_lang);
		}
		else
		{
			$data_medias['date_added'] = mktime();
			$this->db->insert($this->config->item('table_portfolio_medias'), $data_medias);
			$last_id = $this->db->insert_id();
			$data_medias_lang['medias_id'] = $last_id;
			foreach($this->language->codes as $lang)
			{
				$data_medias_lang['lang'] = $lang;
				$this->db->insert($this->config->item('table_portfolio_medias_lang'), $data_medias_lang);
			}
		}

		//--------------- Upload
		if (isset($_FILES['image']['name']) &&  $_FILES['image']['name'] != '')
		{
			$config['upload_path'] = './'.$this->config->item('medias_folder').'/images/';
			$config['allowed_types'] = 'jpg|gif|png';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('image'))
			{
				$error = array('error' => $this->upload->display_errors());
			}
			else
			{
				//---- Delete
				if($media = $this->portfolio->get_medias(array('id' => $last_id)))
				{
					$this->portfolio->delete_medias_files($media['id']);
				}
				if($media = $this->medias->get_medias(array('src_id' => $last_id, 'module' => 'portfolio_medias')))
				{
					$this->medias->delete_medias($media['id']);
				}

				//----- Insert
				$this->load->library('image_lib');
				$image_data = $this->upload->data();

				$file = $image_data['file_name'];
				$file_extension = $image_data['file_ext'];
				$file_rewrite = 'portfolio-'.$last_id.'-'.time().$file_extension;

				if(is_readable('./'.$this->config->item('medias_folder').'/images/'.$file)) rename('./'.$this->config->item('medias_folder').'/images/'.$file, './'.$this->config->item('medias_folder').'/images/'.$file_rewrite);

				$this->db->where('id', $last_id);
				$this->db->set('file', $file_rewrite);
				$this->db->update($this->config->item('table_portfolio_medias'));


				$this->db->set('src_id', $last_id);
				$this->db->set('module', 'portfolio_medias');
				$this->db->set('file', $file_rewrite);
				$this->db->set('ordering', 0);
				$this->db->insert($this->config->item('table_medias'));

			}
		}

		//--------------- Categories
		if ($categories_in = $this->input->post('categories'))
		{			
			if($categories_out = $this->portfolio->list_categories_to_medias(array('where' => array('medias_id' => $last_id), 'where_in' => $categories_in)))
			{
				//---- Delete
				$this->db->where(array('medias_id' => $last_id))->delete($this->config->item('table_portfolio_categories_to_medias'));
				foreach($categories_in as $key)
				{
					$data_categories = array(
						'medias_id' 	=> $last_id,
						'categories_id' => $key,
						'ordering' 		=> (isset($categories_out[$key]) ? $categories_out[$key]['ordering'] : -1)
					);
					//---- Insert
					$this->db->insert($this->config->item('table_portfolio_categories_to_medias'), $data_categories);
				}

			}
			else
			{
				foreach($categories_in as $key)
				{
					$this->db->set('medias_id', $last_id);
					$this->db->set('categories_id', $key);
					$this->db->insert($this->config->item('table_portfolio_categories_to_medias'));
				}
			}
		}
		else
		{
			$this->db->set('medias_id', $last_id);
			$this->db->set('categories_id', 1);
			$this->db->insert($this->config->item('table_portfolio_categories_to_medias'));
		}

		return $last_id;
	}

	public function delete_medias($medias_id = '')
	{
		$this->portfolio->delete_medias_files($medias_id);
		$this->db->delete($this->config->item('table_portfolio_categories_to_medias'), array('medias_id' => $medias_id));
		$this->db->delete($this->config->item('table_portfolio_medias'), array('id' => $medias_id));
		$this->db->delete($this->config->item('table_portfolio_medias_lang'), array('medias_id' => $medias_id));
		$this->db->delete($this->config->item('table_medias'), array('src_id' => $medias_id, 'module' => 'portfolio_medias'));

	}

}
