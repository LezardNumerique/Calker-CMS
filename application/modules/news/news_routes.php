<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
$route['news/(:num)'] = 'news/index';
$route['(\w{2})/news/(:num)'] = 'news/index';
$route['news/view/(:any)'] = 'news/view/$1';
$route['(\w{2})/news/view/(:any)'] = 'news/view/$2';