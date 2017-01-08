<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Linkedin_Profile {
	
	public static function parse_id($str)
	{
		// this is a company profile page
		$pattern = '#(https?://|)(www\.|)?linkedin\.com/(company/[a-z0-9\-_\.&]+)([^a-z0-9\-_\.&]|$)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[3];

		$pattern = '#(company/[a-z0-9\-_\.&]+)([^0-9a-z\-_\.&]|$)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[1];
		
		// this is a user profile page
		$pattern = '#id=([0-9]+)([^0-9]|$)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[1];
		
		// this is a user profile page (username ALT)
		$pattern = '#(in/[a-z0-9\-_\.&]+)([^a-z0-9\-_\.&]|$)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[1];

		// this is another public profile page (found in mymediainfo)
		$pattern = '#(https?://|)(www\.|)?linkedin\.com/(pub/[^\#\?]+)$#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[3];
		
		// already an a valid profile
		$pattern = '#^([0-9]+|company/[a-z0-9\-_\.&]+)$#is';
		if (preg_match($pattern, $str))
			return $str;
		
		// already an a valid profile (username ALT)
		$pattern = '#^(in/[^a-z0-9\-_\.&]+)$#is';
		if (preg_match($pattern, $str))
			return $str;
		
		return null;
	}
	
	public static function url($profile)
	{
		// just digits => user profile
		if (preg_match('#^[0-9]+$#', $profile))
			return "https://www.linkedin.com/profile/view?id={$profile}";
		
		// company then digits => company profile
		if (preg_match('#^company/[0-9a-z\-_\.&]+$#i', $profile))
			return "https://www.linkedin.com/{$profile}";
		
		// in then username => user profile (ALT)
		if (preg_match('#^in/[a-z0-9\-_\.&]+$#i', $profile))
			return "https://www.linkedin.com/{$profile}";

		// alternative public profile page
		if (preg_match('#^pub/.+$#i', $profile))
			return "https://www.linkedin.com/{$profile}";
			
		return null;
	}

	public static function numeric_id($str)
	{
		$pattern = '#(company)/([0-9]+)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[2];
	}
	
}

?>