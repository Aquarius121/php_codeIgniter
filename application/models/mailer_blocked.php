<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Mailer_Blocked extends Model {
	
	protected static $__table = 'nr_mailer_blocked';
	protected static $__primary = 'email';

	public static function find_email($email)
	{
		return parent::find_id(strtolower($email));
	}
	
}

?>