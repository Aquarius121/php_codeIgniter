<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class URL {
   
	// safe urls are email and http/https links
	private static $__safe_pattern = '#^(mailto:|https?://)#is';

	// looks like its just missing a protocol 
	private static $__fix_protocol = '#^([a-z0-9\-]+\.)+[a-z0-9\-]+(/|$)#is';
   
	public static function safe($url)
	{
		if ($url === null) return null;

		// url is already safe looking, return it
		if (preg_match(static::$__safe_pattern, $url))
			return $url;

		// url has missing protocol, add default
		if (preg_match(static::$__fix_protocol, $url))
			$url = sprintf('http://%s', $url);

		// check if the url is safe now and return
		if (preg_match(static::$__safe_pattern, $url))
			return $url;

		return null;
	}

	public static function secure($url)
	{
		return preg_replace('#^https?://#is', 'https://', $url);
	}

	public static function nice($url)
	{
		$url = preg_replace('#^https?://#is', null, $url);
		$url = preg_replace('#(\.[a-z0-9]+)/$#is', '$1', $url);
		return $url;
	}
   
}