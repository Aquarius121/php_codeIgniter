<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Limit_Email extends Model_Limit_Base {
	
	protected static $__table   = 'nr_limit_email';
	protected static $__primary = 'limit_id';
	
	public static function find_user($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		$criteria = array('user_id', $user);
		return static::find($criteria);
	}
	
	public function consume($count)
	{
		$consumed = min($count, $this->available());
		$this->amount_used += $consumed;
		$this->save();
		return $consumed;
	}
	
	public function available()
	{
		return max(0, ($this->total() - $this->used()));
	}
	
	public function used()
	{
		return $this->amount_used;
	}
	
	public function total()
	{
		return $this->amount_total;
	}
	
}

?>