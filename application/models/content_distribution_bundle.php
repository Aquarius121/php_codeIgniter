<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Content_Distribution_Bundle extends Model {

	use Raw_Data_Trait;

	const DIST_BASIC = 'BASIC';
	const DIST_PREMIUM = 'PREMIUM';
	const DIST_PREMIUM_PLUS = 'PREMIUM_PLUS';
	const DIST_PREMIUM_PLUS_STATE = 'PREMIUM_PLUS_STATE';
	const DIST_PREMIUM_PLUS_NATIONAL = 'PREMIUM_PLUS_NATIONAL';
	const DIST_PREMIUM_FINANCIAL = 'PREMIUM_FINANCIAL';

	protected static $__table = 'nr_content_distribution_bundle';
	protected static $__primary = 'content_id';

	protected static $names = array(
		self::DIST_BASIC => 'Basic',
		self::DIST_PREMIUM => 'Premium',
		self::DIST_PREMIUM_PLUS => 'Premium Plus',
		self::DIST_PREMIUM_PLUS_STATE => 'Premium Plus State Newsline',
		self::DIST_PREMIUM_PLUS_NATIONAL => 'Premium Plus National',
		self::DIST_PREMIUM_FINANCIAL => 'Premium Financial',
	);

	protected static $shorts = array(
		self::DIST_BASIC => 'Basic',
		self::DIST_PREMIUM => 'Premium',
		self::DIST_PREMIUM_PLUS => 'PP',
		self::DIST_PREMIUM_PLUS_STATE => 'PP State',
		self::DIST_PREMIUM_PLUS_NATIONAL => 'PP National',
		self::DIST_PREMIUM_FINANCIAL => 'Financial',
	);

	protected static $bundlers = array(
		self::DIST_BASIC => 'Distribution_Bundle_Basic',
		self::DIST_PREMIUM => 'Distribution_Bundle_Premium',
		self::DIST_PREMIUM_PLUS => 'Distribution_Bundle_Premium_Plus',
		self::DIST_PREMIUM_PLUS_STATE => 'Distribution_Bundle_Premium_Plus_State',
		self::DIST_PREMIUM_PLUS_NATIONAL => 'Distribution_Bundle_Premium_Plus_National',
		self::DIST_PREMIUM_FINANCIAL => 'Distribution_Bundle_Premium_Financial',
	);

	public static function instance($bundle)
	{
		$instance = new static();
		$instance->bundle = $bundle;
		return $instance;
	}

	public static function names()
	{
		return static::$names;
	}

	public static function shorts()
	{
		return static::$shorts;
	}

	public function name()
	{
		return static::$names[$this->bundle];
	}

	public function short()
	{
		return static::$shorts[$this->bundle];
	}

	public function bundler_class()
	{
		return static::$bundlers[$this->bundle];
	}

	public function bundler()
	{
		$class = $this->bundler_class();
		return new $class($this);
	}

	public function enable()
	{
		$this->bundler()->enable();
	}

	public function disable()
	{
		$this->bundler()->disable();
	}

	public function confirm()
	{
		$this->bundler()->confirm();
	}

	public function customize(Raw_Data $raw)
	{
		$this->bundler()->customize($raw);
	}

	public function providers()
	{
		return $this->bundler()->providers();
	}

	public function has_provider($provider)
	{
		return $this->bundler()->has_provider($provider);
	}
	
}