<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Instagram_Profile {
	
	public static function parse_id($str)
	{		
		// extract username from instagram website url
		$pattern = '#^(https?://|)(www\.|)([a-z\-\.]+\.)?instagram\.com/([a-z0-9\_\-\.]+)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[4];
		
		// already valid id
		$pattern = '#^[a-z0-9\_\-]+$#is';
		if (preg_match($pattern, $str)) 
			return $str;
		
		return null;
	}
	
	public static function url($profile)
	{
		if (preg_match('#^[a-z0-9\_\-]+$#i', $profile))
			return "https://www.instagram.com/{$profile}";

		return null;
	}
}

?>