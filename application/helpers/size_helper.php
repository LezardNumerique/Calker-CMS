<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
function get_media_size($file = '', $key = '')
{
	if(is_file('./'.$file))
	{
		$attr = getimagesize('./'.$file);
		return $attr[$key];
	}
}