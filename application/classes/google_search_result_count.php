<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Google_Search_Result_Count {
	
	public static function count($title)
	{
		return 0;



		
		$class = get_class();
		$cache_name = "{$class}_{$title}";
		$cache_name = md5($cache_name);
		
		$value = Data_Cache_LT::read($cache_name);
		if ($value !== false) 
			return $value;
		
		$url = static::url($title);
		$user_agent = User_Agent::random();
		$request = new HTTP_Request($url);
		$request->set_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
		$request->set_header('Accept-Language', 'en-us,en;q=0.5');
		$request->set_header('User-Agent', $user_agent);
		$response = @$request->get();
		if (!$response)
			return 0;
		
		// extract the approximate number of results
		$pattern = '#(about\s+)?([0-9,\.]+)\s+results#is';

		if (!preg_match($pattern, $response->data, $match)) 
		{
			Data_Cache_LT::write($cache_name, 0, 3600);
			return 0;
		}
		
		$count = (int) preg_replace('#[^0-9]#', null, $match[2]);
		if ($count > 1000000) $count = sprintf('%dM', floor($count / 1000000));
		elseif ($count > 1000) $count = sprintf('%dM', floor($count / 1000));
		Data_Cache_LT::write($cache_name, $count, 21600);
		return $count;
	}
	
	public static function url($title)
	{
		$params = array();
		$params['ie'] = 'utf-8';
		$params['oe'] = 'utf-8';
		$params['q'] = "\"{$title}\"";
		$params['filter'] = '0';
		$params = http_build_query($params);
		$url = "http://www.google.com/search?{$params}";
		return $url;
	}
	
}

?>