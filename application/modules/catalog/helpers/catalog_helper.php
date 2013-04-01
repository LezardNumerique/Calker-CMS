<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

function format_price($price = '')
{
	if($price) return number_format($price,2, ',', ' ').'&nbsp;&euro;';
	else return $price;
}

