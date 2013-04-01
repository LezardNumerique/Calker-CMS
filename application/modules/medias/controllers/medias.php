<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Medias extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');

        // directory where source images are stored
        $this->img_dir = './'.$this->config->item('medias_folder').'/images/';

        // directory where cache files will be stored.
        $this->cache_dir = $this->img_dir . '/.'.$this->config->item('cache_folder');

        // image library to use, GD, GD2,
        $this->img_lib = 'GD2';

        // image quality
        $this->quality = $this->system->quality_img;

        // max memory limit, used when resizing/cropping to handle large files
        $this->memory_limit = ini_get('memory_limit');

    }

    function resize () {

		$config['image_library'] = 'GD2';
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 400;
		$config['height'] = 300;
		$config['quality'] = $this->system->quality_img;

		$this->offset = $this->uri->segment(3);

		$config['source_image'] = './'.$this->config->item('medias_folder').'/images/'.$this->offset;
		if (!is_file($config['source_image'])) show_404();

		$config['new_image'] = './'.$this->config->item('medias_folder').'/images/.'.$this->config->item('cache_folder').'/'.$config['width'].'x'.$config['height'].'_'.$this->offset;

		$this->load->library('image_lib');

		$this->image_lib->initialize($config);
		$this->image_lib->resize();

		$thumb = array();

		$thumb = explode(".", $this->offset);

		$thumb = './'.$this->config->item('medias_folder').'/images/.'.$this->config->item('cache_folder').'/'.$config['width'].'x'.$config['height'].'_'.$thumb[0].'_thumb.'.$thumb[1];

		$mime = $this->_get_mime($thumb);

		header('Content-Type: '.$mime);
		readfile($thumb);

    }

    function images() {

        // offset used determining image. not hard coded because ci could be in a sub dir
        $this->offset = array_search(__FUNCTION__, $this->uri->segment_array())+1;

        $file = $this->_get_file_from_uri();

        $src_file = $this->img_dir.$file;
        $dst_size = $this->uri->segment($this->offset);
        $dst_file = $this->cache_dir.'/'.$dst_size.$file;

        if (!is_file($src_file)) show_404();

        if (is_file($dst_file) && (filemtime($src_file) > filemtime($dst_file))) {
            unlink($dst_file);
            clearstatcache();
        }

        $config = array(
            'new_image' => $dst_file,
            'quality' => $this->quality,
            'source_image' => $src_file
        );

        list($src['width'], $src['height']) = getimagesize($src_file);

        if ($dst_size == 'x') {
            show_404();

        } else if (strpos($dst_size, 'x') !== FALSE) {

            list($dst['width'], $dst['height']) = explode('x', $dst_size);

            if (($dst['width'] == $dst['height']) && !file_exists($dst_file)) {
                $crop = $src;
                $crop['new_image'] = dirname($config['new_image']).'/_'.basename($config['new_image']);
                if ($src['width'] < $src['height']) {
                    $crop['y_axis'] = round(($src['height'] - $src['width']) / 2);
                    $crop['height'] = $src['width'];
                    // if y_axis > height, this is a really tall image, so the focus is probably toward the top, adjust the y_axis
                    while ($crop['y_axis'] > $crop['height']) $crop['y_axis'] /= 2;
                    $crop['library_path'] = '/usr/bin/';

                } else {
                    $crop['x_axis'] = round(($src['width'] - $src['height']) / 2);
                    $crop['width'] = $src['height'];
                }
                $crop  = array_merge($config, $crop);
                $this->_crop_img($crop);
                $dst = array('source_image'=>$crop['new_image'], 'width'=>$dst_size, 'height'=>$dst_size);

            } else {
                // calculate width, height
                if (empty($dst['height'])) $dst['height'] = floor($src['height']*($dst['width']/$src['width']));
                if (empty($dst['width'])) $dst['width'] = floor($src['width']*($dst['height']/$src['height']));
            }

        } else $dst = array('width'=>$dst_size, 'height'=>$dst_size);

        // check that the resized image won't be larger than the original
        if (($src['width'] < $dst['width']) && ($src['height'] < $dst['height'])) $dst = $src;
        $config = array_merge($config, $dst);

        $this->_serve_cache_img($config);
    }

    function _crop_img($config=array()) {

        // increase memory limit to handle large files
        ini_set('memory_limit', $this->memory_limit);
        $this->load->library('image_lib');

        $default = array(
            'image_library' => $this->img_lib,
            'maintain_ratio' => FALSE
        );
        $config = array_merge($default, $config);
        $this->image_lib->initialize($config);

        $this->_mk_dir(dirname($config['new_image']));
        $result = $this->image_lib->crop();

        if (!$result || !file_exists($config['new_image'])) echo 'Crop: '.$this->image_lib->display_errors();
    }

    function _get_ext($file) {
		return substr($file, strrpos($file, '.')+1);
    }

    function _get_file_from_uri() {
        $seg_array = $this->uri->segment_array();
        return '/' . implode('/', array_slice($seg_array, $this->offset));
    }

    function _get_mime($file) {
        $mimes = array('bmp'=>'image/bmp', 'gif'=>'image/gif', 'jpg'=>'image/jpeg', 'jpeg' =>'image/jpeg', 'png'=>'image/png', 'BMP'=>'image/bmp', 'GIF'=>'image/gif', 'JPG'=>'image/jpeg', 'JPEG'=>'image/jpeg', 'PNG'=>'image/png');
        return $mimes[$this->_get_ext($file)];
    }

    function _mk_dir($path) {
        if (is_dir($path)) return TRUE;
        if (!$this->_mk_dir(dirname($path), 0777)) return FALSE;
        $old_umask = umask(0);
        $result = mkdir($path, 0777);
        umask($old_umask);
        return $result;
    }

    function _resize_img($config=array()) {

        // increase memory limit to handle large files
        ini_set('memory_limit', $this->memory_limit);
        $this->load->library('image_lib');
        $default = array(
            'image_library' => $this->img_lib,
            'maintain_ratio' => TRUE
        );
        $config = array_merge($default, $config);
        $this->image_lib->initialize($config);

        $this->_mk_dir(dirname($config['new_image']));
        $result = $this->image_lib->resize();

        if (!$result) echo $this->image_lib->display_errors();
    }

    function _serve_cache_img($config) {

        $src_file = $config['source_image'];
        $cache_file = $config['new_image'];

        if (!file_exists($cache_file)) {
			// cache file doesn't exist, create it
            $this->_resize_img($config);

            // if temp crop file, delete
            if ($src_file == dirname($cache_file).'/_'.basename($cache_file)) unlink($src_file);

        }
        else {
			// else check modified date
			if (function_exists('apache_request_headers'))
			{
				$request = apache_request_headers();
				if(isset($request['If-Modified-Since']))
				{
					$modified_since = explode(';', $request['If-Modified-Since']);
					$modified_since = strtotime($modified_since[0]);
				}
				else $modified_since = 0;
			}
            elseif(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
            {
        		$modified_since=strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    		}
    		else $modified_since = 0;
            if (filemtime($cache_file) <= $modified_since)
            {
				// if not modified, save some cpu and bandwidth
                header('HTTP/1.1 304 Not Modified');
                header('Etag: '.md5($cache_file));
                die;
            }
        }

        // serve cache file
        $mime = $this->_get_mime($cache_file);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($cache_file)));
        header('Content-Type: '.$mime);
        header('Content-Length: '.filesize($cache_file)."\n\n");
        header('Etag: '.md5($cache_file));

        readfile($cache_file);
        die;
    }

}