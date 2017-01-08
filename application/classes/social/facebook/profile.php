
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Facebook_Profile {
	
	public static function parse_id($str)
	{		
		// if its of the form http://facebook.com/pages/abc/123123631248
		$pattern = '#https?\://(?:www\.)?facebook\.com/pages/(.*)/(\d+|[A-Za-z0-9\.]+)/?#';
		if (preg_match($pattern, $str, $match))
			return $match[2];


		// extract username or page from url
		$pattern = '#^(https?://|)(www\.|)([a-z\-\.]+\.)?facebook\.com/([a-z0-9\_\-\.]+)#is';
		if (preg_match($pattern, $str, $match)) 
			return $match[4];
		
		// already a valid id
		$pattern = '#^[a-z0-9\_\-\.]+$#is';
		if (preg_match($pattern, $str)) 
			return $str;
		
		return null;
	}
	
	public static function url($profile)
	{
		// matches a username => user profile
		if (preg_match('#^[a-z0-9\_\-\.]+$#i', $profile))
			return "https://www.facebook.com/{$profile}";
		return null;
	}
	
	public static function details($auth, $id = null)
	{
		if (!$id) $id = 'me';
		if (!$auth) return null;
		if (!$auth->is_valid()) return null;
		$facebook = Social_Facebook_API::instance();
		$facebook->setAccessToken($auth->access_token);
		try { return $facebook->api("/{$id}"); }
		catch (Exception $e) { return null; }
	}
	
	public static function name($auth)
	{
		$details = static::details($auth);
		if (isset($details['name']))
		    return $details['name'];
		return null;
	}

	public static function get_numeric_id($profile)
	{
		if ($profile === null) return 0;
		$facebook = Social_Facebook_API::instance();
		//$facebook->setAccessToken($auth->access_token);
		try {			
			$data = $facebook->api("/{$profile}/");
			if ($id = $data['id'])
				return $id;
			else
				return null;			
		} 			
		catch (Exception $e) { return null; }
	}
	
}
