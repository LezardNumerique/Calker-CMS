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
				$html .= '$lang[\''.$key.'\'] = "'.htmlspecialchars($value).'";'."\n";
			}
			$html .= '?>';
			fputs($file, $html);
			fclose($file);
		}

	}
}