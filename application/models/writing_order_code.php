<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Writing_Order_Code extends Model {
	
	protected static $__table = 'rw_writing_order_code';
		
	public static function generate_code($length = 16)
	{
		// letters (capitals only) and numbers
		$chars  = 'ABCDEFGHIJKLMNOPQRSTUWXYZ';
		$chars .= '0123456789';
	
		// generate a code from allowed characters
		for ($i = 0, $code = null; $i < $length; $i++)
			$code .= $chars[mt_rand(0, strlen($chars) - 1)];
			
		return $code;
	}
	
	public static function find_code($code)
	{
		return static::find('writing_order_code', $code);
	}
	
	public function code()
	{
		return $this->writing_order_code;
	}
	
	public function nice_code()
	{
		return static::__nice_code($this->writing_order_code);
	}
	
	public static function __nice_code($code)
	{
		// matches a code from old orders => extract local part
		if (preg_match('/^[0-9]+\-([0-9a-z]+)$/i', $code, $matches))
			$code = $matches[1];
		
		$short = substr($code, 0, 8);
		$short = strtoupper($short);
		return $short;
	}
	
}

?>