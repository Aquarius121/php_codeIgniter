<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class UUID {

	protected static $increment = 0;
	
	public static function hashed($source)
	{
		$hashed = md5((string) $source);
		$blocks = array();
		$blocks[] = substr($hashed,  0,  8);
		$blocks[] = substr($hashed,  8,  4);
		$blocks[] = substr($hashed, 12,  4);
		$blocks[] = substr($hashed, 16,  4);
		$blocks[] = substr($hashed, 20, 12);
		return implode(chr(45), $blocks);
	}
	
	public static function create()
	{
		$source = uniqid(get_instance()->env['server_id'], true);
		$source = sprintf('%s:%d:%f', $source, 
			++static::$increment, microtime(true));
		return static::hashed($source);
	}

	public static function validate($uuid)
	{
		return (bool) preg_match('#^[a-f0-9]{8}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}$#i', $uuid);
	}
	
	public static function short($uuid)
	{
		return substr($uuid, 0, 8);
	}

	public static function nice($uuid)
	{
		return strtoupper(substr($uuid, 0, 8));
	}

}

?>