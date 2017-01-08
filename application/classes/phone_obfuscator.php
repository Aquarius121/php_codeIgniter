<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Phone_Obfuscator {
	
	public function obfuscate($phone)
	{
		return preg_replace('#....$#', '????', $phone);
	}	
	
}

?>