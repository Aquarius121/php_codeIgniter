<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class GZIP {

	public static function encode($data, $level = -1, $encoding_mode = FORCE_GZIP)
	{
		return gzencode($data, $level, $encoding_mode);
	}
	
	public static function decode($data, $length = PHP_INT_MAX)
	{
		return @gzdecode($data, $length);
	}

	public static function has_header($data)
	{
		// http://www.zlib.org/rfc-gzip.html
		if (strlen($data) >= 18 && 
			 ord($data[0]) === 31 && 
			 ord($data[1]) === 139)
			return true;
		return false;
	}
	
}

?>