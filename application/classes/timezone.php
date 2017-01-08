<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class TimeZone {

	const UTC = 'UTC';
	const EST = 'America/New_York';

	public static function common_name($timezone) 
	{
		$ci =& get_instance();
		$ci->config->load('timezones', false);
		$common_timezones = $ci->config->item('common_timezones');
		if ($name = array_search($timezone, $common_timezones))
			return $name;
		return $timezone;
	}

	public static function abbreviation($timezone)
	{
		if (!$timezone) return static::UTC;
		$timezone = new DateTimeZone($timezone);
		$datetime = new DateTime('now', $timezone);
		return $datetime->format('T');
	}


}