<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_GPlus_Profile {
	
	public static function parse_id($str)
	{		
		// google plus id number
		$pattern = '#([0-9]{15,30})#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[1];
		
		// more friendly name (extract)
		$pattern = '#plus\.google\.com/(\+[0-9a-z\-_]+)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[1];
		
		// more friendly name (already)
		$pattern = '#^(\+[0-9a-z\-_]+)$#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[1];
		
		return null;
	}
	
	public static function url($profile)
	{
		// matches a user id => user profile
		if (preg_match('#^[0-9]+$#', $profile))
			return "https://plus.google.com/{$profile}";
		// matches a friendly name => user profile
		if (preg_match('#^\+[0-9a-zA-Z\-_]+$#', $profile))
			return "https://plus.google.com/{$profile}";
		return null;
	}
	
}

?>