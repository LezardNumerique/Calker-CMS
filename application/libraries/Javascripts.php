<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Javascripts
{

    var $javascripts;
    var $CI;

	function Javascripts($params = null)
	{
        $this->clear();
        $this->CI =& get_instance();
        $config = $this->CI->config->item('javascripts');
        if ($config) $this->add($config);
        if ($params) $this->add($params);
	}

    // clear all javascripts
    public function clear()
    {
        $this->javascripts = array();
    }

    // add a javascript
    public function add($items)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                if (!in_array($item, $this->javascripts)) {
                    $this->javascripts[] = $item.'.js';
                }
            }
        } else {
            if (!in_array($items, $this->javascripts)) {
                $this->javascripts[] = $items.'.js';
            }
        }
    }

    // return the array of javascripts
    public function get()
    {
        return $this->javascripts;
    }

    // output the array of javascripts
	public  function to_string()
    {
        return 'javascripts are: '.implode(',', $this->javascripts);
    }
}