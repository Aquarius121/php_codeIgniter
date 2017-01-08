<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Distribution_Site extends Model {
	
	protected static $__table = 'nr_distribution_site';
	protected static $__primary = 'id';

	public static function find_hash($hash)
	{
		return static::find('hash', $hash);
	}

	public static function find_site_from_url($url)
	{
		$hash = static::hash_url($url);
		return static::find_hash($hash);
	}

	public static function create_site_from_url($url)
	{
		$hash = static::hash_url($url);
		$site = new static();
		$site->hash = $hash;
		$site->url = null;
		$site->name = static::extract_hostname($url);
		$site->logo_image_id = null;
		$site->quality = 0;
		$site->save();

		return $site;
	}

	// extracts the hostname from full url
	// and generates a hash to identify the site
	protected static function hash_url($url)
	{
		$hostname = static::extract_hostname($url);
		return md5($hostname);
	}

	// extracts the hostname from full url
	protected static function extract_hostname($url)
	{
		$hostname = parse_url($url, PHP_URL_HOST);
		if (preg_match('#^www\.#is', $hostname))
		     return substr($hostname, 4);
		else return $hostname;
	}
	
}

?>
