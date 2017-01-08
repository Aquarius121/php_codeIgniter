<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Newsroom_Claim_Token extends Model {
	
	protected static $__table = 'ac_nr_newsroom_claim_token';
	protected static $__primary = 'company_id';
	
	public function generate()
	{
		$length = 6;		
		$chars = 'abcdefghijklmnopqrstuvwxyz';
		$chars .= '0123456789';
	
		$token = '';
		// generate a token from allowed characters
		for ($i = 0, $token = null; $i < $length; $i++)
			$token .= $chars[mt_rand(0, strlen($chars) - 1)];
			
		$this->token = $token;
	}
	
}

?>