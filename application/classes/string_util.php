<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class String_Util {

	public static function abbr($str, $length)
	{
		// split $str into words or letters (I.B.M)
		$words = preg_split('/(\s|\.)/', $str);

		if (count($words) > 1)
		{
			$abbr = null;
			foreach ($words as $k => $word)
				$abbr .= substr($word, 0, 1);
		}
		else
		{
			// only 1 word so don't abbreviate
			// just cut to length instead
			$abbr = $words[0];
		}
		
		$abbr = preg_replace('/[^a-z0-9]/i', null, $abbr);
		$abbr = strtoupper($abbr);
		if (strlen($abbr) > $length)
			$abbr = substr($abbr, 0, $length);

		return $abbr;
	}

	public static function ends_with($haystack, $needle)
	{
		return substr($haystack, -strlen($needle)) == $needle;
	}

	public static function starts_with($haystack, $needle)
	{
		return strpos($haystack, $needle) === 0;
	}

	public static function contains($haystack, $needle)
	{
		return strpos($haystack, $needle) !== false;
	}

	public static function inject($str, $params)
	{
		if (is_object($params))
		     $params = Raw_Data::from_object($params);
		else $params = Raw_Data::from_array($params);

		// matches named injections such as {{name}}
		// and replaces with the value in $params
		$pattern = '#\{\{\s*([a-z0-9\-_]+)\s*\}\}#i';
		$callback = function($m) use ($params) {
			return $params->{$m[1]};
		};

		return preg_replace_callback($pattern, 
			$callback, $str);
	}

	public static function normalize($str)
	{		
		$str = preg_replace('#[^a-z0-9]#is', null, $str);
		$str = strtolower($str);
		return $str;
	}

}