<?php
$obj =& get_instance();
$config['table_portfolio_categories'] = 'portfolio_categories';
$config['table_portfolio_categories_lang'] = 'portfolio_categories_lang';
$config['table_portfolio_categories_to_medias'] = 'portfolio_categories_to_medias';
$config['table_portfolio_medias'] = 'portfolio_medias';
$config['table_portfolio_medias_lang'] = 'portfolio_medias_lang';
if($obj->uri->segment(1) != 'admin') $config['pagination_url_suffix'] = TRUE;
?>
