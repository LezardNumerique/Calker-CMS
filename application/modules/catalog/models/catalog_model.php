<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class catalog_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	//---------------------------------------------- Categories
	public function create_categories()
	{
		$data_categories = array(
			'parent_id' 		=> $this->input->post('parent_id'),
			'active' 			=> $this->input->post('active'),
			'ordering' 			=> 0
		);

		$data_categories_lang = array(
			'title' 			=> ucfirst($this->input->post('title')),
			'uri' 				=> $this->input->post('uri'),
			'body' 				=> $this->input->post('body'),
			'meta_title' 		=> $this->input->post('meta_title'),
			'meta_description' 	=> $this->input->post('meta_description'),
			'meta_keywords' 	=> $this->input->post('meta_keywords')
		);

		if ($this->input->post('uri') == '')
		{
			$data_categories['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_categories'), $data_categories);
			$last_id = $id;
			$this->db->where(array('categories_id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_categories_lang'), $data_categories_lang);
		}

		if($id = $this->input->post('id'))
		{
			$data_categories['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_categories'), $data_categories);
			$last_id = $id;
			$this->db->where(array('categories_id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_categories_lang'), $data_categories_lang);
		}
		else
		{
			$data_categories['date_added'] = mktime();
			$this->db->insert($this->config->item('table_categories'), $data_categories);
			$last_id = $this->db->insert_id();
			$data_categories_lang['categories_id'] = $last_id;
			foreach($this->language->codes as $lang)
			{
				$data_categories_lang['lang'] = $lang;
				$this->db->insert($this->config->item('table_categories_lang'), $data_categories_lang);
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
				if($media = $this->medias->get_medias(array('src_id' => $last_id, 'module' => 'categories_products')))
				{
					$this->medias->delete_medias($media['id']);
				}

				//Insert
				$this->load->library('image_lib');
				$image_data = $this->upload->data();

				$file = $image_data['file_name'];
				$file_extension = $image_data['file_ext'];
				$file_rewrite = 'catalog-categories-'.$last_id.'-'.time().$file_extension;

				$options = array(
					'cover' 	=> 0,
					'legend' 	=> $this->input->post('legend')
				);

				rename('./'.$this->config->item('medias_folder').'/images/'.$file, './'.$this->config->item('medias_folder').'/images/'.$file_rewrite);

				$this->db->set('src_id', $last_id);
				$this->db->set('module', 'catalog_categories');
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
		$query = $this->db->get_where($this->config->item('table_categories'), array('id' => $id));

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
		$this->db->update($this->config->item('table_categories'));

		$this->db->where(array('id' => $id));
		$query = $this->db->get($this->config->item('table_categories'));
		$row = $query->row();
		$new_ordering = $row->ordering;

		if ($move > 0)
		{
			$this->db->set('ordering', 'ordering-1', FALSE);
			$this->db->where(array('ordering <=' => $new_ordering, 'id <>' => $id, 'parent_id' => $parent_id));
			$this->db->update($this->config->item('table_categories'));
		}
		else
		{
			$this->db->set('ordering', 'ordering+1', FALSE);
			$where = array('ordering >=' => $new_ordering, 'id <>' => $id, 'parent_id' => $parent_id);

			$this->db->where($where);
			$this->db->update($this->config->item('table_categories'));
		}

		//Reordinate
		$i = 0;
		$this->db->order_by('ordering');
		$this->db->where(array('parent_id' => $parent_id));

		$query = $this->db->get($this->config->item('table_categories'));

		if ($rows = $query->result())
		{
			foreach ($rows as $row)
			{
				$this->db->set('ordering', $i);
				$this->db->where('id', $row->id);
				$this->db->update($this->config->item('table_categories'));
				$i++;
			}
		}
	}

	public function delete_categories($categories_id = '')
	{
		$this->db->where(array('id' => $categories_id))->delete($this->config->item('table_categories'));
	}

	public function delete_categories_to_products($categories_id = '')
	{
		$this->db->where(array('categories_id' => $categories_id))->delete($this->config->item('table_products_to_categories'));
	}

	//---------------------------------------------- Products

	public function create_products()
	{
		if($this->input->post('categories_id_default') != '')
		{
			$categories_id_default = $this->input->post('categories_id_default');
		}
		elseif($this->input->post('categories') == '')
		{
			$categories_id_default = 1;
		}
		else
		{
			$categories_id_default = 1;
		}
		$data_products = array(
			'categories_id_default'	 	=> $categories_id_default,
			'manufacturers_id'	 		=> $this->input->post('manufacturers_id'),
			'active' 					=> $this->input->post('active'),
			'reference' 				=> $this->input->post('reference'),
			'price' 					=> $this->input->post('price'),
			'tva' 						=> $this->input->post('tva')
		);

		$data_products_lang = array(
			'title' 					=> ucfirst($this->input->post('title')),
			'uri' 						=> $this->input->post('uri'),
			'body' 						=> $this->input->post('body'),
			'meta_title' 				=> $this->input->post('meta_title'),
			'meta_description' 			=> $this->input->post('meta_description'),
			'meta_keywords' 			=> $this->input->post('meta_keywords')
		);

		if ($this->input->post('uri') == '')
		{
			$data['uri'] = format_title($this->input->post('title'));
		}

		if($id = $this->input->post('id'))
		{
			$data_products['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_products'), $data_products);
			$last_id = $id;
			$this->db->where(array('products_id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_products_lang'), $data_products_lang);
		}
		else
		{
			$data_products['date_added'] = mktime();
			$this->db->insert($this->config->item('table_products'), $data_products);
			$last_id = $this->db->insert_id();
			$data_products_lang['products_id'] = $last_id;
			foreach($this->language->codes as $lang)
			{
				$data_products_lang['lang'] = $lang;
				$this->db->insert($this->config->item('table_products_lang'), $data_products_lang);
			}

		}

		//-----Products_to_categories
		//Delete
		$this->db->where(array('products_id' => $last_id))->delete($this->config->item('table_products_to_categories'));
		if ($categories = $this->input->post('categories'))
		{
			//Insert
			foreach($categories as $categorie)
			{
				$this->db->set('products_id', $last_id);
				$this->db->set('categories_id', $categorie);
				$this->db->insert($this->config->item('table_products_to_categories'));
			}
		}
		else
		{
			$this->db->set('products_id', $last_id);
			$this->db->set('categories_id', 1);
			$this->db->insert($this->config->item('table_products_to_categories'));
		}

		//-----Products_to_products
		//Delete
		$this->db->where('products_id_x', $last_id);
		$this->db->or_where('products_id_y', $last_id);
		$query = $this->db->delete($this->config->item('table_products_to_products'));
		if ($products = $this->input->post('products_combos'))
		{
			//Insert
			foreach($products as $product)
			{
				$this->db->set('products_id_x', $last_id);
				$this->db->set('products_id_y', $product);
				$this->db->insert($this->config->item('table_products_to_products'));
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
				$this->load->library('image_lib');
				$image_data = $this->upload->data();

				$file = $image_data['file_name'];
				$file_extension = $image_data['file_ext'];
				$file_rewrite = 'catalog-products-'.$last_id.'-'.time().$file_extension;

				$options = array(
					'cover' 	=> 0,
					'legend' 	=> $this->input->post('legend')
				);

				rename('./'.$this->config->item('medias_folder').'/images/'.$file, './'.$this->config->item('medias_folder').'/images/'.$file_rewrite);

				$this->db->set('src_id', $last_id);
				$this->db->set('module', 'catalog_products');
				$this->db->set('file', $file_rewrite);
				$this->db->set('options', serialize($options));
				$this->db->set('ordering', 0);
				$this->db->insert($this->config->item('table_medias'));

			}

		}

		//-----Products_to_attributes_values
		if ($attributes_id = $this->input->post('attributes_id'))
		{
			$attributes_color = NULL;
			if($this->input->post('attributes_color')) $attributes_color = $this->input->post('attributes_color');
			$data_attributes = array(
				'products_id' 			=> $last_id,
				'attributes_id' 		=> $attributes_id,
				'attributes_values_id' 	=> $this->input->post('attributes_values_id'),
				'price' 				=> $this->input->post('attributes_price'),
				'suffix' 				=> $this->input->post('suffix'),
				'ordering' 				=> 0,
				'color' 				=> $attributes_color,
			);
			$this->db->insert($this->config->item('table_products_to_attributes_values'), $data_attributes);
		}

		return $last_id;
	}

	public function delete_products($products_id = '')
	{
		$this->db->where(array('id' => $products_id))->delete($this->config->item('table_products'));
	}

	public function delete_products_to_categories($products_id = '')
	{
		$this->db->where(array('products_id' => $products_id))->delete($this->config->item('table_products_to_categories'));
	}

	public function delete_products_to_products($products_id = '')
	{
		$this->db->where('products_id_x = '.$products_id.' OR products_id_y = '.$products_id)->delete($this->config->item('table_products_to_products'));
	}

	public function delete_products_combos($products_id_x = '', $products_id_y = '')
	{
		$this->db->where(array('products_id_x' => $products_id_x, 'products_id_y' => $products_id_y))->or_where(array('products_id_x' => $products_id_y, 'products_id_y' => $products_id_x))->delete($this->config->item('table_products_to_products'));
	}

	public function move_products($products_id = '', $categories_id = '', $direction = '')
	{
		$move = ($direction == 'up') ? -1 : 1;
		$this->db->where(array('products_id' => $products_id, 'categories_id' => $categories_id));

		$this->db->set('ordering', 'ordering+'.$move, FALSE);
		$this->db->update($this->config->item('table_products_to_categories'));

		$this->db->where(array('products_id' => $products_id, 'categories_id' => $categories_id));
		$query = $this->db->get($this->config->item('table_products_to_categories'));
		$row = $query->row();
		$new_ordering = $row->ordering;

		if ($move > 0)
		{
			$this->db->set('ordering', 'ordering-1', FALSE);
			$this->db->where(array('ordering <=' => $new_ordering, 'products_id <>' => $products_id, 'categories_id' => $categories_id));
			$this->db->update($this->config->item('table_products_to_categories'));
		}
		else
		{
			$this->db->set('ordering', 'ordering+1', FALSE);
			$where = array('ordering >=' => $new_ordering, 'products_id <>' => $products_id, 'categories_id' => $categories_id);

			$this->db->where($where);
			$this->db->update($this->config->item('table_products_to_categories'));
		}

		//Reordinate
		$i = 0;
		$this->db->order_by('ordering');
		$this->db->where(array('categories_id' => $categories_id));

		$query = $this->db->get($this->config->item('table_products_to_categories'));

		if ($rows = $query->result())
		{
			foreach ($rows as $row)
			{
				$this->db->set('ordering', $i);
				$this->db->where(array('products_id' => $row->products_id, 'categories_id' => $row->categories_id));
				$this->db->update($this->config->item('table_products_to_categories'));
				$i++;
			}
		}
	}

	public function move_products_images($products_id, $direction)
	{
		$move = ($direction == 'up') ? -1 : 1;
		$this->db->where(array('src_id' => $products_id, 'module' => 'products'));

		$this->db->set('ordering', 'ordering+'.$move, FALSE);
		$this->db->update($this->config->item('table_medias'));

		$this->db->where(array('src_id' => $products_id, 'module' => 'products'));
		$query = $this->db->get($this->config->item('table_medias'));
		$row = $query->row();
		$new_ordering = $row->ordering;

		if ($move > 0)
		{
			$this->db->set('ordering', 'ordering-1', FALSE);
			$this->db->where(array('ordering <=' => $new_ordering, 'src_id <>' => $products_id, 'module' => 'products'));
			$this->db->update($this->config->item('table_medias'));
		}
		else
		{
			$this->db->set('ordering', 'ordering+1', FALSE);
			$where = array('ordering >=' => $new_ordering, 'src_id <>' => $products_id, 'module' => 'products');

			$this->db->where($where);
			$this->db->update($this->config->item('table_medias'));
		}

		//Reordinate
		$i = 0;
		$this->db->order_by('ordering');
		$this->db->where(array('src_id' => $products_id, 'module' => 'products'));

		$query = $this->db->get($this->config->item('table_medias'));

		if ($rows = $query->result())
		{
			foreach ($rows as $row)
			{
				$this->db->set('ordering', $i);
				$this->db->where(array('id' => $row->id));
				$this->db->update($this->config->item('table_medias'));
				echo $this->db->last_query();
				$i++;
			}
		}

	}

	public function cover_products_images($products_id = '', $images_id = '', $module = '')
	{
		if($medias = $this->medias->list_medias(array('where' => array('src_id' => $products_id, 'module' => $module))))
		{
			foreach($medias as $media)
			{
				$flag = 0;
				if ($media['id'] == $images_id) $flag = 1;
				$data = array(
					'options' => serialize(array('cover' => $flag, 'legend' => $media['options']['legend']))
				);
				$this->db->where(array('id' => $media['id']));
				$this->db->update($this->config->item('table_medias'), $data);
			}
		}
	}

	public function delete_products_attributes ($products_attributes_id = '')
	{
		$this->db->where(array('id' => $products_attributes_id))->delete($this->config->item('table_products_to_attributes_values'));
	}

	public function exists_products_attributes($fields)
	{
		$query = $this->db->get_where($this->config->item('table_products_to_attributes_values'), $fields, 1, 0);

		if($query->num_rows() == 1)
			return TRUE;
		else
			return FALSE;
	}

	//---------------------------------------------- Specials

	public function create_specials()
	{
		$data = array(
			'products_id' 		=> $this->input->post('products_id'),
			'new_price' 		=> $this->input->post('new_price'),
			'tva'	 			=> $this->input->post('tva'),
			'active' 			=> $this->input->post('active')
		);

		if($date_begin = $this->input->post('date_begin'))
		{
			$day = substr($date_begin, 0,2);
			$month = substr($date_begin, 3, 2);
			$year = substr($date_begin, 6, 4);
			$data['date_begin'] = $year.'-'.$month.'-'.$day;
		}
		else
		{
			$data['date_begin'] = NULL;
		}
		if($date_begin = $this->input->post('date_end'))
		{
			$day = substr($date_begin, 0,2);
			$month = substr($date_begin, 3, 2);
			$year = substr($date_begin, 6, 4);
			$data['date_end'] = $year.'-'.$month.'-'.$day;
		}
		else
		{
			$data['date_end'] = NULL;
		}

		if($id = $this->input->post('id'))
		{
			$data['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_specials'), $data);
			$last_id = $id;
		}
		else
		{
			$data['date_added'] = mktime();
			$this->db->insert($this->config->item('table_specials'), $data);
			$last_id = $this->db->insert_id();
		}

		return $last_id;
	}

	public function delete_specials($specials_id = '')
	{
		$this->db->where(array('id' => $specials_id))->delete($this->config->item('table_specials'));
	}

	public function exists_specials($fields)
	{
		$query = $this->db->get_where($this->config->item('table_specials'), $fields, 1, 0);

		if($query->num_rows() == 1)
			return TRUE;
		else
			return FALSE;
	}

	//---------------------------------------------- Attributes

	public function create_attributes()
	{
		$data_attributes = array(
			'is_color' 	=> $this->input->post('is_color')
		);

		$languages = $this->language->list_languages();

		if($id = $this->input->post('id'))
		{
			$this->db->where(array('id' => $id))->update($this->config->item('table_attributes'), $data_attributes);
			$last_id = $id;

			$data_attributes_lang = array(
				'id ' 	=> $last_id,
				'lang' 	=> $this->user->lang,
				'name' 	=> $this->input->post('name')
			);
			$this->db->where(array('id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_attributes_lang'), $data_attributes_lang);

		}
		else
		{
			$this->db->insert($this->config->item('table_attributes'), $data_attributes);
			$last_id = $this->db->insert_id();

			foreach($languages as $language)
			{
				$data_attributes_lang = array(
					'id ' 	=> $last_id,
					'lang' 	=> $language['code'],
					'name' 	=> $this->input->post('name')
				);
				$this->db->insert($this->config->item('table_attributes_lang'), $data_attributes_lang);
			}
		}

		return $last_id;
	}

	public function delete_attributes($attributes_id = '')
	{
		$this->db->where(array('id' => $attributes_id))->delete($this->config->item('table_attributes'));
		$this->db->where(array('id' => $attributes_id))->delete($this->config->item('table_attributes_lang'));
		if($attributes_values = $this->catalog->list_attributes_values(array('where' => array('attributes_id' => $attributes_id))))
		{
			foreach($attributes_values as $attribute_value)
			{
				$this->db->where(array('id' => $attribute_value['id']))->delete($this->config->item('table_attributes_values_lang'));
			}
			$this->db->where(array('attributes_id' => $attributes_id))->delete($this->config->item('table_attributes_values'));
		}


	}

	public function create_attributes_values()
	{
		$data_attributes_values = array(
			'attributes_id' 	=> $this->input->post('attributes_id'),
			'color' 				=> $this->input->post('color')
		);

		$languages = $this->language->list_languages();

		if($id = $this->input->post('id'))
		{
			$this->db->where(array('id' => $id))->update($this->config->item('table_attributes_values'), $data_attributes_values);
			$last_id = $id;

			$data_attributes_values_lang = array(
				'id ' 	=> $last_id,
				'lang' 	=> $this->user->lang,
				'name' 	=> $this->input->post('name')
			);
			$this->db->where(array('id' => $last_id, 'lang' => $this->user->lang))->update($this->config->item('table_attributes_values_lang'), $data_attributes_values_lang);

		}
		else
		{
			$this->db->insert($this->config->item('table_attributes_values'), $data_attributes_values);
			$last_id = $this->db->insert_id();

			foreach($languages as $language)
			{
				$data_attributes_values_lang = array(
					'id ' 	=> $last_id,
					'lang' 	=> $language['code'],
					'name' 	=> $this->input->post('name')
				);
				$this->db->insert($this->config->item('table_attributes_values_lang'), $data_attributes_values_lang);
			}
		}

		return $last_id;
	}

	public function delete_attributes_values($attributes_values_id = '')
	{
		$this->db->where(array('id' => $attributes_values_id))->delete($this->config->item('table_attributes_values'));
		$this->db->where(array('id' => $attributes_values_id))->delete($this->config->item('table_attributes_values'));
	}
	
	//---------------------------------------------- Manufacturers

	public function create_manufacturers()
	{

		$data = array(
			'title' => $this->input->post('title'),
			'uri' 	=> $this->input->post('uri'),
		);

		if ($this->input->post('uri') == '')
		{
			$data['uri'] = format_title($this->input->post('title'));
		}

		if($id = $this->input->post('id'))
		{
			$data['date_modified'] = mktime();
			$this->db->where(array('id' => $id))->update($this->config->item('table_manufacturers'), $data);
			$last_id = $id;
		}
		else
		{
			$data['date_added'] = mktime();
			$this->db->insert($this->config->item('table_manufacturers'), $data);
			$last_id = $this->db->insert_id();
		}

		return $last_id;
	}

	public function delete_manufacturers($manufacturers_id = '')
	{
		$this->db->where(array('id' => $manufacturers_id))->delete($this->config->item('table_manufacturers'));
	}


}