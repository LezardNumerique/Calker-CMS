<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class import_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function get_categories_id($categorie_name = '')
	{
		if($categorie = $this->catalog->get_categories(array('lang' => $this->user->lang, 'title' => $categorie_name)))
			return $categorie['categories_id'];
		else
			return 1;
	}

	/*
	public function get_attributes_id($attribute_name = '')
	{
		//echo $attribute_name;
		if($attribute = $this->catalog->get_attributes(array('lang' => $this->user->lang, 'name' => $attribute_name)))
			return $attribute['id'];
		else
			return false;
	}

	public function get_attributes_values_id($attribute_values_name = '')
	{
		//echo $attribute_values_name;
		if($attribute_value = $this->catalog->get_attributes_values(array('lang' => $this->user->lang, 'name' => htmlspecialchars($attribute_values_name))))
			return $attribute_value['id'];
		else
			return false;
	}
	*/

	public function import_products($rows = '', $file = '')
	{
		set_time_limit(0);
		$data['config'] = array(
			'truncate' => true,
		);

		//---------------------------------------------------- Reset
		if($data['config']['truncate'])
		{
			$this->db->query('TRUNCATE TABLE ci_attributes');
			$this->db->query('TRUNCATE TABLE ci_attributes_lang');
			$this->db->query('TRUNCATE TABLE ci_attributes_values');
			$this->db->query('TRUNCATE TABLE ci_attributes_values_lang');
			$this->db->query('TRUNCATE TABLE ci_categories');
			$this->db->query('TRUNCATE TABLE ci_categories_lang');
			$this->db->query('TRUNCATE TABLE ci_products');
			$this->db->query('TRUNCATE TABLE ci_products_lang');			
			$this->db->query('TRUNCATE TABLE ci_products_to_categories');
			$this->db->query('TRUNCATE TABLE ci_manufacturers');		
			$this->db->query('TRUNCATE TABLE ci_products_to_attributes_values');
			$this->db->query('TRUNCATE TABLE ci_products_to_products');

			$cat = array(
				'id' 	=> 1,
				'parent_id' 	=> 0,
				'active'		=> 1,
				'date_added'	=> mktime()
			);
			$this->db->insert($this->config->item('table_categories'), $cat);
			foreach($this->language->codes as $lang)
			{
				$cat_lang = array(
					'categories_id' 	=> 1,
					'lang'				=> $lang,
					'title'				=> 'Accueil',
					'uri'				=> 'accueil'
				);
				$this->db->insert($this->config->item('table_categories_lang'), $cat_lang);
			}
		}
		//exit;

		if(isset($rows) && $rows)
		{
			//pre_affiche($rows);
			foreach($rows as $row)
			{
				if(isset($row['[CAT1]']) && isset($row['[CAT2]']) && isset($row['[CAT3]']) && isset($row['NOM']) && isset($row['REFERENCE']) && isset($row['PRIX']) && isset($row['TVA']) && isset($row['DESCRIPTION']) && isset($row['FABRICANT']))
				{
					//----- On clean les chaines
					$row['product_title'] = htmlspecialchars(trim(ucfirst($row['NOM'])));
					$row['product_reference'] = trim($row['REFERENCE']);					
					$row['product_body'] = htmlspecialchars(trim($row['DESCRIPTION']));
					
					$row['product_price'] = trim($row['PRIX']);
					$row['product_price'] = str_replace(',', '.', $row['product_price']);
					$row['product_price'] = str_replace('€', '', $row['product_price']);
					$row['product_price'] = trim($row['product_price']);
					
					$row['product_tva'] = trim($row['TVA']);
					$row['product_tva'] = str_replace(',', '.', $row['product_tva']);
					$row['product_tva'] = str_replace('%', '', $row['product_tva']);
					$row['product_tva'] = trim($row['product_tva']);
										
					//----------------------------------------------------Insert ou update fiche produit
					$data_product = array(
						'active' 		=> 1,
						'reference' 	=> $row['product_reference'],
						'price'		 	=> $row['product_price'],
						'tva'		 	=> $row['product_tva']
					);
					$data_product_lang = array(
						'title' 		=> $row['product_title'],
						'uri' 			=> format_title($row['product_title']),
						'body' 			=> $row['product_body']
					);
					
					if($product = $this->catalog->get_products(array('reference' => $row['product_reference'], 'title' => $row['product_title'])))
					{
						$data_product['date_modified'] = mktime();
						$this->db->where(array('id' => $product['id']))->update($this->config->item('table_products'), $data_product);
						$products_id = $product['id'];
						foreach($this->language->codes as $lang)
						{
							$data_product_lang['lang'] = $lang;
							$this->db->where(array('products_id' => $products_id, 'lang' => $lang))->update($this->config->item('table_products_lang'), $data_product_lang);
						}
					}
					else
					{
						$data_product['date_added'] = mktime();
						$this->db->insert($this->config->item('table_products'), $data_product);
						$products_id = $this->db->insert_id();
						$data_product_lang['products_id'] = $products_id;
						foreach($this->language->codes as $lang)
						{
							$data_product_lang['lang'] = $lang;
							$this->db->insert($this->config->item('table_products_lang'), $data_product_lang);
						}
					}

					$this->db->where(array('products_id' => $products_id));
					$this->db->delete($this->config->item('table_products_to_categories'));

					//----------------------------------------------------Insert ou update fiche categories + products to categories
					for($i = 1;$i < 4;$i++)
					{						
						$categorie_name[$i] = trim(ucfirst($row['[CAT'.$i.']']));										

						if($categorie_name[$i] != '')
						{
							$categorie_name[$i] = htmlspecialchars($categorie_name[$i]);
							$parent_id = (isset($categorie_name[$i-1]) ? $this->get_categories_id($categorie_name[$i-1]) : 1);

							$data_categories = array(
								'parent_id'		=> $parent_id,
								'active'		=> 1
							);
							$data_categories_lang = array(
								'title'				=> $categorie_name[$i],
								'uri'				=> format_title($categorie_name[$i])
							);
							$data_products_to_categories = array(
								'products_id'	=> $products_id
							);							

							//----------------------------------------------------La catégorie existe elle ?
							if($categorie = $this->catalog->get_categories(array('title' => $categorie_name[$i], 'lang' => $lang, 'parent_id' => $parent_id)))
							{
								//----------------------------------------------------On update la catégorie
								$data_categories['date_modified'] = mktime();
								$this->db->where(array('id' => $categorie['id']))->update($this->config->item('table_categories'), $data_categories);
								$categories_id = $categorie['id'];
								foreach($this->language->codes as $lang)
								{
									$data_categories_lang['lang'] = $lang;
									$this->db->where(array('categories_id' => $categories_id, 'lang' => $lang))->update($this->config->item('table_categories_lang'), $data_categories_lang);
								}
							}
							else
							{
								//----------------------------------------------------On crée la catégorie
								$data_categories['date_added'] = mktime();
								$this->db->insert($this->config->item('table_categories'), $data_categories);
								$categories_id = $this->db->insert_id();
								$data_categories_lang['categories_id'] = $categories_id;
								foreach($this->language->codes as $lang)
								{
									$data_categories_lang['lang'] = $lang;
									$this->db->insert($this->config->item('table_categories_lang'), $data_categories_lang);
								}
							}
							//----------------------------------------------------Products to categories
							$data_products_to_categories['categories_id'] = $categories_id;
							if(!$get_product_to_categorie = $this->catalog->get_products_to_categories($data_products_to_categories))
							{
								$this->db->insert($this->config->item('table_products_to_categories'), $data_products_to_categories);
								//----------------------------------------------------Mise à jour catégorie default
								$this->db->where(array('id' => $products_id))->update($this->config->item('table_products'), array('categories_id_default' => $categories_id));
							}
							else
							{
								//----------------------------------------------------Mise à jour catégorie default
								$this->db->where(array('id' => $products_id))->update($this->config->item('table_products'), array('categories_id_default' => $categories_id));
							}
						}
					}					
					
					//----------------------------------------------------Insert ou update fiche manufacturer					
					if($row['FABRICANT'])
					{
						$row['manufacturer'] = htmlspecialchars(trim(ucfirst($row['FABRICANT'])));
						$data_manufacturer = array(
							'title' 		=> $row['manufacturer'],
							'uri '			=> format_title($row['manufacturer'])
						);
						if($manufacturer = $this->catalog->get_manufacturers(array('title' => $row['manufacturer'])))
						{
							//----------------------------------------------------On update le fabricant
							$data_manufacturer['date_modified'] = mktime();
							$this->db->where(array('id' => $manufacturer['id']))->update($this->config->item('table_manufacturers'), $data_manufacturer);
							$manufacturers_id = $manufacturer['id'];
						}
						else
						{
							//----------------------------------------------------On insert le fabricant
							$data_manufacturer['date_added'] = mktime();
							$this->db->insert($this->config->item('table_manufacturers'), $data_manufacturer);
							$manufacturers_id = $this->db->insert_id();
						}

						//----------------------------------------------------Insert products to manufacturers si trouvé
						if($manufacturers_id)
							$this->db->where(array('id' => $products_id))->update($this->config->item('table_products'), array('manufacturers_id' => $manufacturers_id));
						
					}
				
				}
			}
		}
		if (is_file('./'.$this->config->item('medias_folder').'/tmp/'.$file)) unlink('./'.$this->config->item('medias_folder').'/tmp/'.$file);
		$this->cache->remove_group('catalog_categories');
		$this->cache->remove_group('products');
		//exit;
	}	

}