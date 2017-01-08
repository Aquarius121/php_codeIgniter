<?php 

class Raw_Data implements ArrayAccess, Countable {

	protected $__next_array_index = 0;
	protected $__null_value = null;

	public function __get($name)
	{
		return null;
	}

	public static function from_blob($blob, $recursive = false)
	{
		return static::from($blob, $recursive);
	}

	public static function from_array($arr, $recursive = false)
	{
		return static::from($arr, $recursive);
	}

	public static function from_auto($auto, $recursive = false)
	{
		return static::from($auto, $recursive);
	}

	public static function from_object($ob, $recursive = false)
	{
		return static::from($ob, $recursive);
	}

	public static function from($in, $recursive = false)
	{
		if (is_string($in))
			$in = static::decode($in);
		return static::__from($in, $recursive);
	}

	protected static function __from($in, $recursive = false)
	{
		if ($in instanceof Raw_Data)
		{
			return clone $in;
		}

		if (!is_object($in))
		{
			$in = (object) $in;
		}

		$instance = new static();

		foreach ($in as $k => $v)
		{
			if ($recursive && (is_object($v) || is_array($v)))
			     $instance->$k = static::from($v, true);
			else $instance->$k = $v;
		}

		return $instance;
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset) ||
		   !is_string($offset)) 
			return;

		$this->$offset = $value;
	}

	public function offsetExists($offset)
	{
		return isset($this->$offset);
	}

	public function offsetUnset($offset)
	{
		unset($this->$offset);
	}

	public function & offsetGet($offset)
	{
		if (isset($this->$offset))
			return $this->$offset;
		return $this->__null_value;
	}

	public function push($data)
	{
		$index = $this->__next_array_index++;
		$this->$index = $data;
	}

	public function count()
	{
		return $this->__next_array_index;
	}

	public static function encode($data, $compression = false)
	{
		$encoded = json_encode($data);
		if ($compression) $encoded = GZIP::encode($encoded);
		return $encoded;
	}

	public static function decode($blob, $compression = false)
	{
		if ($compression && GZIP::has_header($blob))
			$blob = GZIP::decode($blob);
		$decoded = json_decode($blob);
		return $decoded;
	}

}
