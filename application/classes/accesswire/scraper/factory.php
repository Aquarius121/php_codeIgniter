<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Accesswire_Scraper_Factory {

	public static function create()
	{
		$ci =& get_instance();
		$config = $ci->conf('accesswire');
		return new Accesswire_Scraper_Client($config);
	}

}