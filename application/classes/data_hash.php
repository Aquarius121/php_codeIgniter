<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// the default hashing function 
define('DATA_HASH_FUNCTION', 'sha256');

class Data_Hash {

	protected $data = array();
	
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	public function __get($name)
	{
		return $this->data[$name];
	}

	public function hash($func = DATA_HASH_FUNCTION)
	{
		foreach ($this->data as $k => $v) 
		{
			if (is_array($v) || is_object($v))
			{
				$dh_r = new Data_Hash();
				$dh_r->data = (array) $v;
				$this->data[$k] = bin2hex($dh_r->hash($func));
			}
		}

		$chunks = array();
		ksort($this->data, SORT_STRING);
		foreach ($this->data as $k => $v) 
		{
			if (!is_string($k)) $k = (string) $k;
			if (!is_string($v)) $v = (string) $v;
			
			$chunks[] = hash($func, $k);
			$chunks[] = PHP_EOL;
			$chunks[] = hash($func, $v);
			$chunks[] = PHP_EOL;
		}

		return hash($func, implode($chunks), true);
	}

	public function hash_hex($func = DATA_HASH_FUNCTION)
	{
		return bin2hex($this->hash($func));
	}

	public static function __hash($str, $func = DATA_HASH_FUNCTION)
	{
		$dh = new static();
		$dh->static = $str;
		return $dh->hash($func);
	}

	public static function __hash_hex($str, $func = DATA_HASH_FUNCTION)
	{
		return bin2hex(static::__hash($str, $func));
	}
	
}

?>