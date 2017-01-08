<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HTML2Text extends Html2Text\Html2Text {
	
	public static function basic($html)
	{
		$meta = '<meta charset=utf-8 />';
		$html = concat($meta, $html);
		
		$errors = libxml_use_internal_errors();
		libxml_use_internal_errors(true);
		$plain = parent::convert($html);
		libxml_clear_errors();
		libxml_use_internal_errors($errors);
		return $plain;
	}

	public static function plain($html, $utf8 = true, $allowed_tags = array())
	{
		if ($utf8) $html = to_utf8_3b($html);

		if (is_array($allowed_tags) && count($allowed_tags))
		{
			$allowed_tags = array_map(function($v) {
				return sprintf('<%s>', preg_replace('#[<>]#s', null, $v));
			}, $allowed_tags);
		}

		return html_entity_decode(strip_tags($html, implode($allowed_tags)));
	}

	public static function email($html)
	{
		return static::basic($html);
	}

	public static function convert($html, $utf8 = true, $allowed_tags = array())
	{
		return static::plain($html, $utf8, $allowed_tags);
	}
	
}