<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Google_News_Search_Results {
	
	public static function find($title)
	{
		$url = static::feed_url($title);
		$user_agent = User_Agent::random();
		$request = new HTTP_Request($url);
		$request->set_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
		$request->set_header('Accept-Language', 'en-us,en;q=0.5');
		$request->set_header('User-Agent', $user_agent);
		$response = @$request->get();
		if (!$response)
			return array();
		
		$rss = new RSS_Reader();
		$items = $rss->read_string($response->data);
		if (!$items) return array();
		return $items;
	}
	
	protected static function feed_url($title)
	{
		$params = array();
		$params['output'] = 'rss';
		$params['filter'] = 0;
		$params['q'] = sprintf('"%s"', $title);
		$params = http_build_query($params);
		$url = "https://news.google.com/news/feeds?{$params}";
		return $url;
	}

	public static function extract_content_url($url)
	{
		$params = array();
		parse_str(parse_url($url, PHP_URL_QUERY), $params);
		if (!isset($params['url'])) return null;
		return $params['url'];
	}

	public static function url($title)
	{
		$params = array();
		$params['ie'] = 'utf-8';
		$params['oe'] = 'utf-8';
		$params['q'] = "\"{$title}\"";
		$params['filter'] = '0';
		$params = http_build_query($params);
		$url = "https://news.google.com/news/search?{$params}";
		return $url;
	}
	
}

?>