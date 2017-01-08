<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Fin_Content_Dead_Source extends Model {
	
	protected static $__table = 'nr_fin_content_dead_source';
	protected static $__primary = 'hash';
	
	public static function find_all_dead()
	{
		return static::find_all(array('date_clear', '>', Date::$now));
	}

	public static function find_or_create($hash)
	{
		$instance = static::find($hash);
		if (!$instance) $instance = new static();
		$instance->hash = $hash;
		return $instance;
	}

}