<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Pinterest_Feed {
	
	public static function get($id)
	{
		if ($id === null) return 0;
		$feedURL = "http://www.pinterest.com/{$id}/feed.rss";
		$sxml = simplexml_load_file($feedURL);
		return $sxml;
	}

	public static function is_valid($id)
	{
		if ($id === null) return 0;
		$feedURL = "http://www.pinterest.com/{$id}/feed.rss";
		$sxml = simplexml_load_file($feedURL);
		if ( ! count($sxml->channel->item))
			return false;		
		else
			return true;
	}
	
}
