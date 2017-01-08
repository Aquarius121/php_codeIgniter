<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admo {

	const MAX_RECENTS = 20;
	
	public static function url($relative_url = null, $admo_user_id = false)
	{
		$ci =& get_instance();
		$prefix = $ci->conf('admo_prefix');
		$suffix = $ci->conf('admo_suffix');
		$protocol = $ci->env['protocol'];

		// make sure we don't get double slashes
		if (str_starts_with($relative_url, '/'))
			$relative_url = substr($relative_url, 1);
		
		if (!$admo_user_id) 
			  $admo_user_id = Auth::user()->id;
		else $admo_user_id = (int) $admo_user_id;
		
		$host = "{$prefix}{$admo_user_id}{$suffix}";
		$url = "{$protocol}://{$host}/{$relative_url}";
		return $url;
	}

	public static function url_format($relative_url, $admo_user_id)
	{
		$ci =& get_instance();
		$prefix = $ci->conf('admo_prefix');
		$suffix = $ci->conf('admo_suffix');
		$protocol = $ci->env['protocol'];

		// make sure we don't get double slashes
		if (str_starts_with($relative_url, '/'))
			$relative_url = substr($relative_url, 1);
		
		$host = "{$prefix}{$admo_user_id}{$suffix}";
		$url = "{$protocol}://{$host}/{$relative_url}";
		return $url;
	}

	public static function save_recent_user($user_id = null)
	{
		$ci =& get_instance();
		if ($user_id === null)
			  $recent = (int) Auth::user()->id;
		else $recent = (int) $user_id;
		$recents = $ci->session->read('admo_recent_users');
		if (!$recents) $recents = array();
		array_remove_all($recents, $recent);
		$recents[] = $recent;
		$recents = array_slice($recents, -static::MAX_RECENTS);
		$ci->session->write('admo_recent_users', $recents);
	}

	public static function save_recent_newsroom($newsroom_id = null)
	{
		$ci =& get_instance();
		if ($newsroom_id === null) 
		     $recent = (int) $ci->newsroom->company_id;
		else $recent = (int) $newsroom_id;
		if (!$recent) return;
		$recents = $ci->session->read('admo_recent_newsrooms');
		if (!$recents) $recents = array();
		array_remove_all($recents, $recent);
		$recents[] = $recent;
		$recents = array_slice($recents, -static::MAX_RECENTS);
		$ci->session->write('admo_recent_newsrooms', $recents);
	}

	public static function recent_users()
	{
		$ci =& get_instance();
		$recents = $ci->session->read('admo_recent_users');
		if (!$recents) $recents = array();
		return $recents;
	}

	public static function recent_newsrooms()
	{
		$ci =& get_instance();
		$recents = $ci->session->read('admo_recent_newsrooms');
		if (!$recents) $recents = array();
		return $recents;
	}
	
}

?>