<?php

class Data_Cache_ST {

	public static $data_cache;
	
	public static function delete($name)
	{
		static::$data_cache->delete($name);
	}

	public static function read($name)
	{
		return static::$data_cache->read($name);
	}	
	
	public static function write($name, $value, $expires = 86400)
	{
		return static::$data_cache->write($name, $value, MEMCACHE_COMPRESSED, $expires);
	}

	public static function read_object($name)
	{
		return static::$data_cache->read($name);
	}	
	
	public static function write_object($name, $value, $expires = 86400)
	{
		return static::$data_cache->write($name, $value, MEMCACHE_COMPRESSED, $expires);
	}
	
}