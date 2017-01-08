<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_GPlus_Feeds {
	
	public static function get($id)
	{
		$key = 'AIzaSyCcCf8wuTd2zUDbSUwY94eizzqL7TiLkTs';
		$data_url = "https://www.googleapis.com/plus/v1/people/{$id}/activities/public?key={$key}";
		$feed = @file_get_contents($data_url);
		$feed = json_decode($feed);
		return $feed;
	}

	public static function is_valid($id)
	{		
		$key = 'AIzaSyCcCf8wuTd2zUDbSUwY94eizzqL7TiLkTs';
		$data_url = "https://www.googleapis.com/plus/v1/people/{$id}/activities/public?key={$key}";
		$feed = file_get_contents($data_url);
		$feed = json_decode($feed);
		if ( ! is_array($feed->items) || ! count($feed->items))
			return false;
		else
			return true;
	}
	
}

?>