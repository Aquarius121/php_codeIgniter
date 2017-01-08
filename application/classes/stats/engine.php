<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stats_Engine {

	const HITS_BUCKET_SIZE = 1000000;

	public static function hits_bucket($context)
	{
		$bucket = floor($context / static::HITS_BUCKET_SIZE);
		return sprintf('sx_hits_bucket_%04d', $bucket);
	}

	public static function context_encode($context)
	{
		return base_convert($context, 10, 36);
	}

	public static function context_decode($context)
	{
		return base_convert($context, 36, 10);
	}

	public static function data_encode($data)
	{
		return base64_encode(json_encode($data));
	}

	public static function data_decode($data)
	{
		return json_decode(base64_decode($data));
	}
	
}

?>