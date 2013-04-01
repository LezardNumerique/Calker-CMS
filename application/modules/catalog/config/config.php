<?php
$obj =& get_instance();
$config['table_attributes'] = 'attributes';
$config['table_attributes_lang'] = 'attributes_lang';
$config['table_attributes_values'] = 'attributes_values';
$config['table_attributes_values_lang'] = 'attributes_values_lang';
$config['table_categories'] = 'categories';
$config['table_categories_lang'] = 'categories_lang';
$config['table_manufacturers'] = 'manufacturers';
$config['table_products'] = 'products';
$config['table_products_lang'] = 'products_lang';
$config['table_products_to_attributes_values'] = 'products_to_attributes_values';
$config['table_products_to_categories'] = 'products_to_categories';
$config['table_products_to_products'] = 'products_to_products';
$config['table_specials'] = 'specials';
$config['table_tva'] = 'tva';
if($obj->uri->segment(1) != 'admin') $config['pagination_url_suffix'] = TRUE;
?>