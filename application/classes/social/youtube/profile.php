<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Youtube_Profile {
	
	public static function parse_id($str)
	{		
		// extract user/channel from youtube website url
		$pattern = '#^(https?://|)(www\.|)([a-z\-\.]+\.)?youtube\.com/((user/|channel/)?([a-z0-9\_\-]+))#is';
		if (preg_match($pattern, $str, $match))
			return $match[4];

		// already valid id (user/channel)
		$pattern = '#^(user|channel)/[a-z0-9\_\-]+$#is';
		if (preg_match($pattern, $str)) 
			return $str;

		// already valid id (legacy)
		$pattern = '#^[a-z0-9\_\-]+$#is';
		if (preg_match($pattern, $str)) 
			return $str;
		
		return null;
	}
	
	public static function url($profile)
	{
		// matches a user/channel 
		if (preg_match('#^(user|channel)/[a-z0-9\_\-]+$#i', $profile))
			return "https://www.youtube.com/{$profile}";

		// matches a username (legacy)
		if (preg_match('#^[a-z0-9\_\-]+$#i', $profile))
			return "https://www.youtube.com/user/{$profile}";

		return null;
	}

	public static function channel_url($profile)
	{
		if (preg_match('#^channel#i', $profile))
			return "https://www.youtube.com/{$profile}";
			
		if (preg_match('#^[a-z0-9\_\-]+$#i', $profile))
			return "https://www.youtube.com/channel/{$profile}";
		
		return null;
	}
	
}

?>