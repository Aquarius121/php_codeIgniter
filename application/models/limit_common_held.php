<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Limit_Common_Held extends Model_Limit_Held_Collection {
	
	protected static $__table = 'nr_limit_common_held';	
	
	public static function create($user, $type, $amount = 1)
	{
		$ci =& get_instance();
		$held_period = $ci->conf('held_credit_period');
		
		if ($user instanceof Model_User)
			$user = $user->id;
		
		$credit = new static();	
		$credit->user_id = $user;
		$credit->date_expires = Date::days($held_period);
		$credit->amount_total = $amount;
		$credit->amount_used = 0;
		$credit->type = $type;
		return $credit;
	}
	
	public static function find_collection($user, $type)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		
		$criteria = array();
		$criteria[] = array('user_id', $user);
		$criteria[] = array('type', $type);
		$criteria[] = array('date_expires >= UTC_TIMESTAMP()');
		
		$order = array(static::$__primary, 'asc');
		$collection = static::find_all($criteria, $order);
		$virtual = new static();
		$virtual->collection = $collection;
		$virtual->is_collection = true;
		
		return $virtual;
	}
	
	public static function find_user($user, $type)
	{
		return static::find_collection($user, $type);
	}
	
	public function consume($count)
	{
		if ($this->is_collection)
		{
			$initial_count = $count;
			foreach ($this->collection as $_this)
				$count -= $_this->consume($count);
			return $initial_count - $count;
		}
		else
		{
			if (!$this->available()) return;
			$consume = min($count, $this->available());
			$this->amount_used += $consume;
			$this->save();
			return $consume;
		}
	}
	
}

?>