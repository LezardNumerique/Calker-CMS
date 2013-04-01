<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Translation {

	function Translation() {
		$this->obj =& get_instance();
	}

	public function save($module = '', $data = array())
	{
		if($module == 'default')
		{
			$file = './'.APPPATH.'language/'.$this->obj->user->lang.'/default_lang.php';
		}
		else
		{
			$file = './'.APPPATH.'modules/'.$module.'/language/'.$this->obj->user->lang.'/'.$module.'_lang.php';
		}

		unset($data['attr_1a']);
		ksort($data);

		if(isset($data) && $data)
		{
			$file = fopen($file, 'w+');
			$html = '<?php'."\n";
			foreach($data as $key => $value)
			{
				$html .= '$lang[\''.$key.'\'] = "'.$value.'";'."\n";
			}
			$html .= '?>';
			fputs($file, $html);
			fclose($file);
		}

	}

	/*
	public function save__($module = 'admin', $data = '')
	{
		$file = './'.APPPATH.'modules/'.$module.'/language/'.$this->obj->user->lang.'/index_lang.php';

		$html = '<?php'."\n";

		if($data)
		{
			foreach($data as $key => $value)
			{
				echo $key.'-'.$value.'<br />';
			}
		}

		$html = '?>'."\n";

		$file = fopen($file, 'a');
		fputs($file, $html);
		fclose($file);

		exit;
	}
	* */
}