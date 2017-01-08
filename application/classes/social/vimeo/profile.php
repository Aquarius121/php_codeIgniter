<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Vimeo_Profile {
	
	public static function parse_id($str)
	{		
		// extract user/channels from vimeo website url
		$pattern = '#^(https?://|)(www\.|)([a-z\-\.]+\.)?vimeo\.com/(channels/)?([a-z0-9\_\-]+)#is';
		if (preg_match($pattern, $str, $match))
			return $match[4].$match[5];

		// already valid id (channels/something)
		$pattern = '#^(channels)/[a-z0-9\_\-]+$#is';
		if (preg_match($pattern, $str)) 
			return $str;

		// already valid id
		$pattern = '#^[a-z0-9\_\-]+$#is';
		if (preg_match($pattern, $str)) 
			return $str;
		
		return null;
	}
	
	public static function url($profile)
	{
		return "https://vimeo.com/{$profile}";
	}
}

?>