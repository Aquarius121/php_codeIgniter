<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Setting extends Model {
	
	const TYPE_INTEGER  = 'INTEGER';
	const TYPE_STRING   = 'STRING';
	const TYPE_BOOLEAN  = 'BOOLEAN';
	const TYPE_TEXT     = 'TEXT';
	const TYPE_VIDEO    = 'VIDEO';
	const TYPE_YAML     = 'YAML';
	
	protected static $__table = 'nr_setting';
	protected static $__primary = 'name';

	// cache the query result for 3 minutes
	protected static $__cache_enabled = true;
	protected static $__cache_duration = 60;

	// per-request cache of all settings
	protected static $__setting_cache = null;

	public static function find($name, $default = null)
	{
		if (static::$__setting_cache === null)
			static::populate_cache();
		if (isset(static::$__setting_cache[$name]))
			return static::$__setting_cache[$name];
		return $default;
	}
	
	protected static function populate_cache()
	{
		static::$__setting_cache = array();
		$all = Model_Setting::find_all();
		foreach ($all as $setting)
			static::$__setting_cache[$setting->name] = $setting;
	}
	
	public function set($value)
	{
		if ($this->type === Model_Setting::TYPE_INTEGER)
			$value = (int) $value;
		if ($this->type === Model_Setting::TYPE_BOOLEAN)
			$value = (bool) $value;
		if ($this->type === Model_Setting::TYPE_VIDEO)
			$value = Video_Youtube::parse_video_id($value);
		$this->value = $value;
	}
	
	public static function value($name)
	{
		$setting = static::find($name);
		if (!$setting) return null;
		if ($setting->type === static::TYPE_INTEGER)
			return (int) $setting->value;
		if ($setting->type === static::TYPE_BOOLEAN)
			return (bool) $setting->value;
		if ($setting->type === static::TYPE_YAML)
			return static::parse_yaml($setting->value);
		return $setting->value;
	}

	// parse a block value and discard 
	// comments and whitespace
	public static function parse_block($value)
	{
		// http://goo.gl/urE9eM
		// normalize new lines to be windows-safe	
		$value = preg_replace('#(?>\r\n|\r|\n)#is', CRLF, $value);
		$lines = explode("\r\n", $value);
		$valid_lines = array();

		foreach ($lines as $line)
		{
			if (!($line = trim($line))) continue;
			if (str_starts_with($line, '#')) continue;
			$valid_lines[] = $line;
		}

		return $valid_lines;
	}

	public static function parse_yaml($value)
	{
		return Symfony\Component\Yaml\Yaml::parse($value);
	}
	
}