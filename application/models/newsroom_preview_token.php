<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Newsroom_Preview_Token extends Model {
	
	protected static $__table = 'nr_newsroom_preview_token';
	protected static $__primary = 'company_id';
	
	public function generate()
	{
		$length = 4;		
		$chars = 'abcdefghijklmnopqrstuvwxyz';
		$chars .= '0123456789';
	
		// generate a token from allowed characters
		for ($i = 0, $token = null; $i < $length; $i++)
			$token .= $chars[mt_rand(0, strlen($chars) - 1)];
			
		$this->access_token = $token;
		$this->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
		$this->date_expires = Date::days(+30)->format(DATE::FORMAT_MYSQL);
	}
	
}

?>