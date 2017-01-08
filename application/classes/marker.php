<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Marker {

	public static function replace_all($content, $markers)
	{
		foreach ($markers as $marker => $value)
			$content = static::replace($content, $marker, $value);
		return $content;
	}

	public static function replace($content, $marker, $value)
	{
		$marker = str_split($marker);
		foreach ($marker as $k => $v)
			$marker[$k] = preg_quote($v);
		array_unshift($marker, null);
		array_push($marker, null);
		$marker = implode('(<[^>]*>)*', $marker);
		$marker_pattern = sprintf('#\(\(%s\)\)#is', $marker);
		return preg_replace($marker_pattern, $value, $content);
	}

}