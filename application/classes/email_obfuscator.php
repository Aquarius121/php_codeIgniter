<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_Obfuscator {
	
	const PATTERN_GENEROUS = '#^[^@]{6,}@.{2,}\.[^\.]+$#is';
	const PATTERN_NORMAL = '#^[^@]{5,}@.{2,}\.[^\.]+$#is';
	const PATTERN_TIGHT = '#^[^@]{4,}@.{2,}\.[^\.]+$#is';
	const PATTERN_EXTRACT = '#^(.{%d})(.*)(.{%d}\.[^\.]+)$#is';
	
	const TRANSFORM_MATCH = '#[^@]#is';
	const TRANSFORM_REPLACE = "*";
	const PATTERN_LIMIT = '#(%s){%d,}#is';
	const TRANSFORM_LIMIT = 3;
	
	public function obfuscate($email, $replacement = null)
	{
		$parts = $this->obfuscate_parts($email, $replacement);
		$combined = array($parts->pre, $parts->obfuscated, $parts->post);
		return implode($combined);
	}
	
	public function obfuscate_parts($email, $replacement = null)
	{
		if ($replacement === null)
			$replacement = static::TRANSFORM_REPLACE;
		$parts = new stdClass();
		$parts->pre = null;
		$parts->obfuscated = null;
		$parts->post = null;
		$visible_length = $this->calculate_visible_length($email);
		$pattern_extract = sprintf(static::PATTERN_EXTRACT,
			$visible_length, min(1, max(0, ($visible_length - 1))));
		if (!preg_match($pattern_extract, $email, $extracted))
			return $parts;
		$parts->pre = $extracted[1];
		$parts->post = $extracted[3];
		$parts->obfuscated = preg_replace(static::TRANSFORM_MATCH, 
			$replacement, $extracted[2]);
		$pattern_limit = sprintf(static::PATTERN_LIMIT,
			preg_quote($replacement, '#'), static::TRANSFORM_LIMIT);
		$parts->obfuscated = preg_replace($pattern_limit,
			str_repeat($replacement, static::TRANSFORM_LIMIT),
			$parts->obfuscated);
		return $parts;
	}
	
	protected function calculate_visible_length($email)
	{
		if (preg_match(static::PATTERN_GENEROUS, $email)) return 4;
		if (preg_match(static::PATTERN_NORMAL, $email)) return 3;
		if (preg_match(static::PATTERN_TIGHT, $email)) return 2;
		return 0;
	}
	
}

?>