<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Yahoo_Search_Result_Count {
	
	public static function count($title)
	{
		// TODO
	}
	
	public static function url($title)
	{
		$params = array();
		$params['p'] = "\"{$title}\"";
		$params = http_build_query($params);
		$url = "https://search.yahoo.com/search?{$params}";
		return $url;
	}
	
}

?>