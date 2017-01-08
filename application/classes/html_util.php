<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HTML_Util {
	
	public static function parser($html)
	{
		return static::queryPath($html);
	}

	public static function queryPath($html)
	{
		if (preg_match('#\s*<!DOCTYPE html(\s[^>]*)?>#is', $html))
		     return html5qp($html);
		else return htmlqp($html);
	}
	
}