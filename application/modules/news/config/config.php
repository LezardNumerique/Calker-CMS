<?php
$obj =& get_instance();
$config['table_news'] = 'news';
$config['table_news_lang'] = 'news_lang';
if($obj->uri->segment(1) != 'admin') $config['pagination_url_suffix'] = TRUE;
?>