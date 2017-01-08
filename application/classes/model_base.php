<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Base extends stdClass {

	// match and extract smart prefixes (model, alias, column)
	const PREFIX_RX_SMART = '#^\$(model_[a-z0-9_]+)?\$([a-z0-9_]+)\$([a-z0-9_]+)$#i';

	// match and extract legacy prefixes (alias, column)
	const PREFIX_RX_LEGACY = '#^([a-z0-9_]+)__([a-z0-9_]+)$#i';	

	protected $__source;
	
	public function __construct() {}
	public function __get($name)
	{
		return null;
	}
	
	protected static function from_object_sub($source, $prefixes = array(), $save_source = false)
	{
		$ob = new static();
		$ob->__source = new stdClass();
		$__prefix_sources = array();
				
		// extract the relevant data from the source
		// to construct the prefixed object
		// and remove from this __source
		foreach ($source as $k => $v)
		{
			// convert legacy definitions to smart
			if (preg_match(static::PREFIX_RX_LEGACY, $k, $ex))
				$k = sprintf('$$%s$%s', $ex[1], $ex[2]);

			if (preg_match(static::PREFIX_RX_SMART, $k, $ex))
			{
				// don't set null values
				// so that we can correctly
				// test for an empty 
				// (null) object
				if ($v === null)
					continue;

				$model = $ex[1];
				$alias = $ex[2];
				$k     = $ex[3];

				// no model specified 
				// => guess based on alias
				if (!strlen($model))
					$model = sprintf('model_%s', 
						$alias);

				// model is provided as an argument?
				// => use that instead of guess
				if (isset($prefixes[$alias]))
					$model = $prefixes[$alias];

				if (!isset($__prefix_sources[$alias]))
				{
					$__prefix_source = new stdClass();
					$__prefix_sources[$alias] = $__prefix_source;
					$__prefix_source->model = $model;
					$__prefix_source->data = new stdClass();
				}

				$__prefix_source = $__prefix_sources[$alias];
				$__prefix_source->data->$k = $v;
			}
			else
			{
				$ob->__source->$k = $v;
			}
		}

		// any on-load transformations
		static::load_data_transform($ob->__source);

		foreach ($__prefix_sources as $alias => $__prefix_source)
		{
			// create the sub object
			$class = $__prefix_source->model;
			$data = $__prefix_source->data;
			$sub = $class::from_object_sub($data, 
				array(), $save_source);
		   $ob->$alias = $sub;
		}
		
		foreach ($ob->__source as $k => $v) $ob->$k = $v;
		if (!$save_source) unset($ob->__source);
		return $ob;
	}
	
	public static function from_auto($source, $prefixes = array())
	{
		if (is_array($source)) 
			$source = (object) $source;
		if (!is_object($source)) return;
		if (isset($source->__source))
			  return static::from_object_sub($source, $prefixes, true);
		else return static::from_object_sub($source, $prefixes, false);
	}
	
	public static function from_object($source, $prefixes = array())
	{
		return static::from_object_sub($source, $prefixes);
	}
	
	public static function from_db_object($source, $prefixes = array())
	{
		return static::from_object_sub($source, $prefixes, true);
	}
	
	public static function from_db($db_result, $prefixes = array())
	{
		$rows = $db_result->num_rows();
		if ($rows === 0) return false;
		$db_object = $db_result->row();
		$ob = static::from_db_object($db_object, $prefixes);
		$db_result->free_result();
		return $ob;
	}
	
	public static function from_db_all($db_result, $prefixes = array()) 
	{
		$obs = array();
		foreach ($db_result->result() as $result)
			$obs[] = static::from_db_object($result, $prefixes);
		$db_result->free_result();
		return $obs;
	}

	public static function values_from_db($db_result, $name) 
	{
		$values = array();
		foreach ($db_result->result() as $result)
			if (isset($result->{$name}))
				$values[] = $result->{$name};
		$db_result->free_result();
		return $values;
	}

	protected static function load_data_transform(&$load_data)
	{
		// ------------------------
	}

}

?>