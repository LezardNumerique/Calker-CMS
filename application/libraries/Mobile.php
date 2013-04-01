<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * --------------------------------------------
 * Sources
 * http://www.pcmechanix.ca/mobile_redirect.htm
 * --------------------------------------------
 * */
class Mobile {

	function Mobile()
	{
		$this->obj =& get_instance();
		if($this->checkmobile())
		{
			//$this->obj->session->set_userdata('redirect_mobile', true);
			//redirect('http://m.'.$_SERVER['HTTP_HOST']);
		}
	}

	function checkmobile()
	{
		if(preg_match("/iphone/i",$_SERVER["HTTP_USER_AGENT"])) return true;

		if(preg_match("/Trident/i",$_SERVER["HTTP_USER_AGENT"])) return true;

		if(isset($_SERVER["HTTP_X_WAP_PROFILE"])) return true;

		if(preg_match("/wap\.|\.wap/i",$_SERVER["HTTP_ACCEPT"])) return true;

		if(isset($_SERVER["HTTP_USER_AGENT"]))
		{
			if(preg_match("/Creative\ AutoUpdate/i",$_SERVER["HTTP_USER_AGENT"])) return false;

			if(preg_match("/MSIE/i",$_SERVER["HTTP_USER_AGENT"])) return false;

			$uamatches = array("midp", "j2me", "avantg", "docomo", "novarra", "palmos", "palmsource", "240x320", "opwv", "chtml", "pda", "windows\ ce", "mmp\/", "blackberry", "mib\/", "symbian", "wireless", "nokia", "hand", "mobi", "phone", "cdm", "up\.b", "audio", "SIE\-", "SEC\-", "samsung", "HTC", "mot\-", "mitsu", "sagem", "sony", "alcatel", "lg", "erics", "vx", "NEC", "philips", "mmm", "xx", "panasonic", "sharp", "wap", "sch", "rover", "pocket", "benq", "java", "pt", "pg", "vox", "amoi", "bird", "compal", "kg", "voda", "sany", "kdd", "dbt", "sendo", "sgh", "gradi", "jb", "\d\d\di", "moto");

			foreach($uamatches as $uastring){
				if(preg_match("/".$uastring."/i",$_SERVER["HTTP_USER_AGENT"])) return true;
			}

		}
		return false;
	}

}