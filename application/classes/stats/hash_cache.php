<?php

// -------- dev notes -------------

// * this should be implemented using 
// server pools and not be static

// * we cannot use the Memcache build in 
// support for pools because we have
// several use-cases for memcache. 

// * this can be used for short term
// caching but is not for more 
// persistent data such as sessions

// ---------------------------------

class Stats_Hash_Cache {

	public static function context($hash)
	{
		$hash = bin2hex($hash);
		$context = Data_Cache_LT::read($hash);
		if ($context === false) return false;
		return (int) $context;
	}	

	public static function hash($context)
	{
		$hash = Data_Cache_LT::read($context);
		if ($hash === false) return false;
		$hash = hex2bin($hash);
		return $hash;
	}
	
	public static function write($context, $hash)
	{
		$hash = bin2hex($hash);
		Data_Cache_LT::write($context, $hash, 0);
		Data_Cache_LT::write($hash, $context, 0);
	}
	
}

?>