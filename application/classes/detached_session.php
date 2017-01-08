<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Detached_Session {

	public static $expires = 3600;

	protected static $data = array();
	protected static $id;

	public static function load($id)
	{
		static::$id = $id;
		$data_str = Data_Cache_LT::read(static::__cache_key());
		$data = @unserialize($data_str);
		if (!$data) $data = array();
		static::$data = $data;
		return static::read('__newsroom');
	}

	protected static function __cache_key()
	{
		return sprintf('detached-%s', static::__id());
	}

	protected static function __id()
	{
		if (static::$id) return static::$id;
		static::$id = substr(md5(UUID::create()), 0, 16);
		return static::$id;
	}

	protected static function __write($name, $model_str)
	{
		static::$data[$name] = $model_str;
		$data_str = serialize(static::$data);
		Data_Cache_LT::write(static::__cache_key(), 
			$data_str, static::$expires);
	}

	protected static function __read($name)
	{
		if (isset(static::$data[$name]))
			return static::$data[$name];
		return null;
	}
	
	public static function write($name, $model)
	{
		$class_name = get_class($model);
		$class_vars = (object) get_object_vars($model);
		$model_data = new stdClass();
		$model_data->name = $class_name;
		$model_data->vars = $class_vars;
		$model_str = serialize($model_data);
		static::__write($name, $model_str);
	}
	
	public static function read($name)
	{
		$model_str = static::__read($name);
		if (!$model_str) return;
		
		$model_data = unserialize($model_str);
		$class_name = $model_data->name;
		$class_vars = $model_data->vars;

		if ($name !== '__newsroom')
		{
			// automatically use a detached
			// version of the specified class
			$detached_class_name = preg_replace(
				'#^Model_(?!Detached_)([a-z_]+)$#i',
				'Model_Detached_${1}', 
				$class_name);

			// check the class exists before use
			if (class_exists($detached_class_name))
				$class_name = $detached_class_name;
		}
		
		$model = $class_name::from_object($class_vars);
		return $model;
	}

	public static function save($m_newsroom, $relative_url = null)
	{
		// save the target newsroom to the session
		static::write('__newsroom', $m_newsroom);

		$ci =& get_instance();
		$prefix = $ci->conf('detached_prefix');
		$suffix = $ci->conf('detached_suffix');
		$id = static::__id();
		
		$host = "{$prefix}{$id}{$suffix}";
		$url = "http://{$host}/{$relative_url}";
		return $url;
	}

	public static function reset()
	{
		static::$data = array();	
		static::$id = null;
	}
	
}

?>