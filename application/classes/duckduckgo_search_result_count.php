<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DuckDuckGo_Search_Result_Count {
	
	public static function count($title)
	{
		// TODO
	}
	
	public static function url($title)
	{
		$params = array();
		$params['q'] = "\"{$title}\"";
		$params = http_build_query($params);
		$url = "https://duckduckgo.com/?{$params}";
		return $url;
	}
	
}

?>