<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class SQLoader
{
	// directory to load sql from
	const SQL_DIR = 'application/sql';

	// @svars replaces ${{name}} with the
	// value specified in svars[name]
	public static function load($name, $svars = array())
	{
		return static::read($name, $svars);
	}
	
	// @svars replaces ${{name}} with the
	// value specified in svars[name]
	public static function read($name, $svars = array())
	{
		$filename = sprintf('%s.sql', $name);
		$file = implode(DIRECTORY_SEPARATOR, array(
			static::SQL_DIR, $filename));
		
		if (!is_file($file)) throw new Exception();
		$src = file_get_contents($file);
		
		// svars in the form ${{name}}
		foreach ($svars as $k => $v)
			$src = str_replace("\${{{$k}}}", $v, $src);
		
		return $src;
	}
}

?>