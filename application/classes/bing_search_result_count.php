<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bing_Search_Result_Count {
	
	public static function count($title)
	{
		// TODO
	}
	
	public static function url($title)
	{
		$params = array();
		$params['qs'] = 'n';
		$params['form'] = 'QBLH';
		$params['q'] = "\"{$title}\"";
		$params['pq'] = "\"{$title}\"";
		$params['filter'] = '0';
		$params = http_build_query($params);
		$url = "http://www.bing.com/search?{$params}";
		return $url;
	}
	
}

?>