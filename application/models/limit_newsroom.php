<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Limit_Newsroom extends Model_Limit_Base {
	
	protected static $__table   = 'nr_limit_newsroom';
	protected static $__primary = 'limit_id';
	
	public function consume($context) {}
	
	public static function find_user($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		$criteria = array('user_id', $user);
		return static::find($criteria);
	}
	
	public function used()
	{
		return 0;
	}
	
	public function total()
	{
		return $this->amount_total;
	}
	
	public function available()
	{
		return $this->total();
	}
	
}

?>