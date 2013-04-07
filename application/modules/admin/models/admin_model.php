<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class admin_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();

	}

	public function list_modules($where = '')
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->db->where($key, $value);
			}
		}
		else
		{
			if($where != '') $this->db->where($where);
		}
		$this->db->order_by('ordering');
		$query = $this->db->get($this->config->item('table_modules'));

		if($rows = $query->result_array())
		{
			$modules = array();
			foreach ($rows as $module)
			{
				$modules[$module['name']] = $module;
			}
			return $modules;
		}

	}

	public function get_qrcode()
	{		
		$PNG_WEB_DIR = $this->config->item('medias_folder').'/tmp/';
		include './'.APPPATH.'/libraries/qrcode/qrlib.php';
		$filename = './'.$PNG_WEB_DIR.'qrcode.png';

		$adress = "BEGIN:VCARD
VERSION:3.0
N:".$this->system->site_name."
PHOTO;
TEL;TYPE=HOME:".format_phone($this->system->site_phone)."
ADR;TYPE=WORK:".$this->system->site_adress.";".$this->system->site_post_code.";".$this->system->site_city.";".$this->system->site_country."
EMAIL;TYPE=PREF,INTERNET:".$this->system->site_email."
URL;type=pref:".site_url()."
REV:20080424T195243Z
END:VCARD";

		QRcode::png($adress, $filename, 'L', 4, 1);
		return '<img src="'.$PNG_WEB_DIR.basename($filename).'" alt="QRCODE '.$this->system->site_name.'"/>';
	}

}
