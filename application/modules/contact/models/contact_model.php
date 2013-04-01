<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class contact_Model extends CI_Model {

	var $tmplistcontacts;
	var $tmplistcontactsmessages;

	public function __construct()
	{
		parent::__construct();

	}

	public function get_contacts($where = '')
	{
		if (is_array($where))
		{
			foreach ($where as $key => $value)
			{
				$this->db->where($key, $value);
			}
		}

		$query = $this->db->get($this->config->item('table_contact'), 1);

		if ( $query->num_rows() == 1 )
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}

	}

	public function list_contacts($or_like = null, $params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'firstname, lastname',
			'limit' 		=> 20,
			'start' 		=> null,
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['select']))
		{
			$this->db->select($params['select']);
		}

		if (!is_null($params['where']))
		{
			$this->db->where($params['where']);
		}

		$this->db->order_by($params['order_by']);

		if (!is_null($params['limit']) && !is_null($params['start']))
		{
			$this->db->limit($params['limit'], $params['start']);
		}

		if (is_array($or_like))
		{
			foreach ($or_like as $key => $value)
			{
				$this->db->or_like($key, $value);
			}
		}

		$query = $this->db->from($this->config->item('table_contact'));
		$query = $this->db->get();

		if ($query->num_rows() > 0 )
		{
			foreach ($query->result_array() as $row) {
				$this->tmplistcontacts[] = $row;
			}

			return $this->tmplistcontacts;
		}
		else
		{
			return false;
		}

	}

	public function total_list_contacts($or_like = null, $params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'firstname, lastname',
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['select']))
		{
			$this->db->select($params['select']);
		}

		if (!is_null($params['where']))
		{
			$this->db->where($params['where']);
		}

		$this->db->order_by($params['order_by']);

		if (is_array($or_like))
		{
			foreach ($or_like as $key => $value)
			{
				$this->db->or_like($key, $value);
			}
		}

		$query = $this->db->from($this->config->item('table_contact'));
		$query = $this->db->get();

		return $query->num_rows();
	}

	public function list_contacts_messages($or_like = null, $params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'date DESC',
			'limit' 		=> 20,
			'start' 		=> null,
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['select']))
		{
			$this->db->select($params['select']);
		}

		if (!is_null($params['where']))
		{
			$this->db->where($params['where']);
		}

		$this->db->order_by($params['order_by']);

		if (!is_null($params['limit']) && !is_null($params['start']))
		{
			$this->db->limit($params['limit'], $params['start']);
		}

		if (is_array($or_like))
		{
			foreach ($or_like as $key => $value)
			{
				$this->db->or_like($key, $value);
			}
		}

		$query = $this->db->from($this->config->item('table_contact_message'));
		$query = $this->db->get();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row) {
				$this->tmplistcontactsmessages[] = $row;
			}

			return $this->tmplistcontactsmessages;
		}
		else
		{
			return false;
		}

	}

	public function total_list_contacts_messages($or_like = null, $params = array())
	{
		$default_params = array
		(
			'select' 		=> '*',
			'order_by' 		=> 'date DESC',
			'where' 		=> null
		);

		foreach ($default_params as $key => $value)
		{
			$params[$key] = (isset($params[$key]))? $params[$key]: $default_params[$key];
		}

		if (!is_null($params['select']))
		{
			$this->db->select($params['select']);
		}

		if (!is_null($params['where']))
		{
			$this->db->where($params['where']);
		}

		$this->db->order_by($params['order_by']);

		if (is_array($or_like))
		{
			foreach ($or_like as $key => $value)
			{
				$this->db->or_like($key, $value);
			}
		}

		$query = $this->db->from($this->config->item('table_contact_message'));
		$query = $this->db->get();

		return $query->num_rows();
	}

	public function exists($fields)
	{
		$query = $this->db->get_where($this->config->item('table_contact'), $fields, 1, 0);

		if($query->num_rows() == 1)
			return TRUE;
		else
			return FALSE;
	}

	public function get_qrcode()
	{
		$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
		$PNG_WEB_DIR = $this->config->item('medias_folder').'/tmp/';
		include './'.APPPATH.'/libraries/qrcode/qrlib.php';
		$filename = './'.$PNG_WEB_DIR.'qrcode.png';

		$adress = $this->system->site_name."\n";
		$adress .= $this->system->site_adress."\n";;
		$adress .= $this->system->site_post_code.' '.$this->system->site_city."\n";
		$adress .=  format_phone($this->system->site_phone)."\n";;
		$adress .=  $this->system->site_email."\n";;
		$adress .=  site_url();
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
		return '<img src="'.site_url($PNG_WEB_DIR.basename($filename)).'" alt="QRCODE '.$this->system->site_name.'"/>';
	}

}
