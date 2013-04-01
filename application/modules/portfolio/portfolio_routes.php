<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
$route['portfolio/(:num)'] = 'portfolio/index/index/1/$1';
$route['(\w{2})/portfolio/(:num)'] = 'portfolio/index/index/1/$2';
$route['portfolio/(:any)/(:num)'] = 'portfolio/index/$1/$2';
$route['(\w{2})/portfolio/(:any)/(:num)'] = 'portfolio/index/$2/$3';

