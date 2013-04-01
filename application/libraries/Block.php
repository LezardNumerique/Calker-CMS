<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Block {

	var $_blocks = array();
	var $obj;

	function Block() {

		$this->obj =& get_instance();
		if (is_array($this->obj->system->modules))
		{
			foreach($this->obj->system->modules as $module)
			{
				$block_file = APPPATH.'modules/'.$module['name'].'/'.$module['name'].'_blocks.php';
				if (file_exists($block_file))
				{
					$this->obj->benchmark->mark($module['name'] . 'block_start');
					include($block_file);
					$this->obj->benchmark->mark($module['name'] . 'block_end');
				}
			}
		}
		log_message('debug', 'Block Class Initialized');
	}

	public function set($block_name, $call_back, $priority = 10)
	{
		$this->_blocks[$block_name][$priority][serialize($call_back)] = array('function' => $call_back);
	}

	public function get($block_name)
	{
		$params = array();
		for ($a = 1; $a < func_num_args(); $a++)
			$params[] = func_get_arg($a);

		$this->merge_blocks($block_name);

		if (!isset($this->_blocks[$block_name]))
			return;

		do{
			foreach((array) current($this->_blocks[$block_name]) as $the_)
				if (!is_null($the_['function']))
					return call_user_func_array($the_['function'], $params);

		} while (next($this->_blocks[$block_name]));

		if (is_array($this->_blocks))
			$this->_blocks[] = $block_name;
		else
			$this->_blocks = array($block_name);

	}

	public function merge_blocks($block_name) {

		if (isset($this->_blocks['all']))
			$this->_blocks[$block_name] = array_merge($this->_blocks['all'], (array) $this->_blocks[$block_name]);

		if (isset($this->_blocks[$block_name])){
			reset($this->_blocks[$block_name]);
			uksort($this->_blocks[$block_name], "strnatcasecmp");
		}
	}

}