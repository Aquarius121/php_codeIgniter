<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Passthru_Email_Obfuscator extends Email_Obfuscator {
	
	const PATTERN_EXTRACT = '#^([^@]+)(@[^\.]+)(\..+)$#is';
	
	public function obfuscate_parts($email, $replacement = null)
	{
		$parts = new stdClass();
		$parts->pre = null;
		$parts->obfuscated = null;
		$parts->post = null;

		if (!preg_match(static::PATTERN_EXTRACT, $email, $extracted))
		{
			$parts->pre = $email;
			return $parts;
		}

		$parts->pre = $extracted[1];
		$parts->obfuscated = $extracted[2];
		$parts->post = $extracted[3];
		
		return $parts;
	}
	
}

?>