<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Content_Release_Plus extends Model {

	use Raw_Data_Trait;
	
	const PROVIDER_ACCESSWIRE      = 'ACCESSWIRE';
	const PROVIDER_DIGITAL_JOURNAL = 'DIGITAL_JOURNAL';
	const PROVIDER_PRNEWSWIRE      = 'PRNEWSWIRE';
	const PROVIDER_WORLDNOW        = 'WORLDNOW';
	
	protected static $__table = 'nr_content_release_plus';
	protected static $__primary = 'id';
	
	protected static $names = array(
		self::PROVIDER_ACCESSWIRE => 'Accesswire',
		self::PROVIDER_DIGITAL_JOURNAL => 'Digital Journal',
		self::PROVIDER_PRNEWSWIRE => 'PR Newswire',
		self::PROVIDER_WORLDNOW => 'World Now',
	);

	protected static $codes = array(
		self::PROVIDER_ACCESSWIRE => 'ACW',
		self::PROVIDER_DIGITAL_JOURNAL => 'DJ',
		self::PROVIDER_PRNEWSWIRE => 'PRN',
		self::PROVIDER_WORLDNOW => 'WN',
	);

	public static function names()
	{
		return static::$names;
	}

	public static function codes()
	{
		return static::$codes;
	}
	
	public static function find_content_with_provider($content_id, $provider)
	{
		$criteria = array();
		$criteria[] = array('content_id', $content_id);
		$criteria[] = array('provider', $provider);
		return static::find($criteria);
	}
	
	public static function find_all_content($content_id)
	{
		$criteria = array(array('content_id', $content_id));
		return static::find_all($criteria);
	}
	
	public function name()
	{
		return static::$names[$this->provider];
	}

	public function code()
	{
		return static::$codes[$this->provider];
	}
	
}