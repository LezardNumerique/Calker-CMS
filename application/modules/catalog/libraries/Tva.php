<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tva {

	var $tva;

	function Tva() {
		$this->obj =& get_instance();
	}

	function list_tva()
	{
		$this->obj->db->select('*');
		$this->obj->db->order_by('rate','asc');
		$query = $this->obj->db->get($this->obj->config->item('table_tva'));

		foreach ($query->result_array() as $row) {
			$this->tva[] = $row;
		}
		return $this->tva;
	}

	function get_price_ttc($price_ht = '', $qty = 1, $tva = '')
	{
		$price_ht = $price_ht * $qty;
		$tva = round(($price_ht * $tva/100),2);
		$price_ttc = $price_ht + $tva;
		return round($price_ttc, 2);
	}

	function display_price($price = '', $qty = 1, $tva = '')
	{
		$settings = unserialize($this->obj->system->catalog_settings);

		if($settings['display_tax'] == 1)
		{
			$price = $this->get_price_ttc($price, $qty, $tva);
			if($settings['display_tax_prefix'] == 1) return format_price($price).' TTC';
			else return format_price($price);
		}
		else
		{
			if($settings['display_tax_prefix'] == 1) return format_price($price).' HT';
			else return format_price($price);
		}

	}

}