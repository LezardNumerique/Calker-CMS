<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
function pre_affiche($data)
{
	print_r('<pre>');
	print_r($data);
	print_r('</pre>');
}