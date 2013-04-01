<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class news_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	//---------------------------------------------- News

	public function save_news()
	{
		$data_news = array(
			'active' 			=> set_value('active')
		);

		$data_news_lang = array(
			'lang' 				=> $this->user->lang,
			'title' 			=> $this->input->post('title'),
			'uri' 				=> format_title($this->input->post('uri')),
			'body' 				=> $this->input->post('body'),
			'meta_title' 		=> $this->input->post('meta_title'),
			'meta_description' 	=> $this->input->post('meta_description'),
			'meta_keywords' 	=> $this->input->post('meta_keywords')
		);

		if ($this->input->post('uri') == '')
		{
			$data_news_lang['uri'] = format_title($this->input->post('title'));
		}

		if($id = $this->input->post('id'))
		{
			$data_news['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_news'), $data_news);
			$last_id = $id;
			$this->db->where(array('news_id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_news_lang'), $data_news_lang);
		}
		else
		{
			$data_news['date_added'] = mktime();
			$this->db->insert($this->config->item('table_news'), $data_news);
			$last_id = $this->db->insert_id();
			$data_news_lang['news_id'] = $last_id;
			foreach($this->language->codes as $lang)
			{
				$data_news_lang['lang'] = $lang;
				$this->db->insert($this->config->item('table_news_lang'), $data_news_lang);
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
				$this->load->library('medias');
				if($media = $this->medias->get_medias(array('src_id' => $last_id, 'module' => 'news')))
				{
					$this->medias->delete_medias($media['id']);
				}

				//Insert
				$this->load->library('image_lib');
				$image_data = $this->upload->data();

				$file = $image_data['file_name'];
				$file_extension = $image_data['file_ext'];
				$file_rewrite = 'news-'.$last_id.'-'.time().$file_extension;

				if(is_readable('./'.$this->config->item('medias_folder').'/images/'.$file))  rename('./'.$this->config->item('medias_folder').'/images/'.$file, './'.$this->config->item('medias_folder').'/images/'.$file_rewrite);

				$this->db->set('src_id', $last_id);
				$this->db->set('module', 'news');
				$this->db->set('file', $file_rewrite);
				$this->db->set('ordering', 0);
				$this->db->insert($this->config->item('table_medias'));

			}

		}

		return $last_id;
	}

	public function delete_news($news_id = '')
	{
		$this->load->library('medias');

		if($medias = $this->medias->list_medias(array('where' => array('src_id' => $news_id, 'module' => 'news'))))
		{
			foreach($medias as $media)
			{
				$this->medias->delete_medias($media['id']);
			}
		}
		$this->db->where(array('id' => $news_id))->delete($this->config->item('table_news'));
		$this->db->where(array('news_id' => $news_id))->delete($this->config->item('table_news_lang'));
	}

}
