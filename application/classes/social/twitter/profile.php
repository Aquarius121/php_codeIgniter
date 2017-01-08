<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Twitter_Profile {
	
	public static function parse_id($str)
	{		
		// extract the username from a full twitter url
		$pattern = '#^https?://([a-z\-\.]+\.)?twitter\.com/([a-z0-9\_\-]+)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[2];
		
		// already valid id
		$pattern = '#^[a-z0-9\_\-]+$#is';
		if (preg_match($pattern, $str)) 
			return $str;
		
		// already valid id (with @)
		$pattern = '#^@([a-z0-9\_\-]+)$#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[1];
		
		return null;
	}
	
	public static function url($profile)
	{
		// matches a username => user profile
		if (preg_match('#^[a-z0-9\_\-]+$#i', $profile))
			return "https://www.twitter.com/{$profile}";
		return null;
	}
	
}