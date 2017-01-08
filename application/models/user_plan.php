<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_User_Plan extends Model {
	
	protected static $__table = 'co_user_plan';
	
	public static function find_active($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		
		$criteria = array();
		$criteria[] = array('user_id', $user);
		$criteria[] = array('is_active', 1);
		return static::find($criteria);
	}
	
	public static function find_all_expired()
	{
		$criteria = array();
		$criteria[] = array('date_expires', '<', 
			Date::utc()->format(Date::FORMAT_MYSQL));
		$criteria[] = array('is_active', 1);
		return static::find_all($criteria);
	}
	
	public function deactivate($immediate = false)
	{
		$this->is_active = 0;
		$this->save();
		
		// non-immediate cancellation implies rollover
		if (!$immediate) $this->exec_rollover_to_held();
	}
	
	protected function exec_rollover_to_held()
	{
		// requires date created to be known
		if (!$this->date_created) return;
		
		$dt_created = Date::utc($this->date_created);
		$dt_expires = Date::utc($this->date_expires);
		$held_period = Model_Setting::value('held_credit_period');
		$dt_held_expires = Date::days($held_period, $dt_created);
		
		// already reached the held period during plan
		if ($dt_held_expires < $dt_expires) return;
		
		// find all plan credits that have rollover to held enabled
		$plan_credits = Model_Plan_Credit::find_all_rollover_to_held($this->plan_id);
		
		foreach ($plan_credits as $plan_credit)
		{
			// the plan credit should not have a period
			if ($plan_credit->period) throw new Exception();
			
			// find the user plan credit instance	
			$user_plan_credit = Model_User_Plan_Credit::find_id(array($this->id, $plan_credit->id));
			$used = $user_plan_credit->used;
			$total = $plan_credit->available;
			
			if ($plan_credit->type === Credit::TYPE_PREMIUM_PR)
			{
				$credit = new Model_Limit_PR_Held();
				$credit->user_id = $this->user_id;
				$credit->date_expires = $dt_held_expires->format(Date::FORMAT_MYSQL);
				$credit->type = Model_Content::PREMIUM;
				$credit->amount_used = $used;
				$credit->amount_total = $total;
				$credit->save();
			}
			
			if ($plan_credit->type === Credit::TYPE_BASIC_PR)
			{
				$credit = new Model_Limit_PR_Held();
				$credit->user_id = $this->user_id;
				$credit->date_expires = $dt_held_expires->format(Date::FORMAT_MYSQL);
				$credit->type = Model_Content::BASIC;
				$credit->amount_used = $used;
				$credit->amount_total = $total;
				$credit->save();
			}
			
			if ($plan_credit->type === Credit::TYPE_EMAIL)
			{
				$credit = new Model_Limit_Email_Held();
				$credit->user_id = $this->user_id;
				$credit->date_expires = $dt_held_expires->format(Date::FORMAT_MYSQL);
				$credit->amount_used = $used;
				$credit->amount_total = $total;
				$credit->save();
			}
			
			if ($plan_credit->type === Credit::TYPE_WRITING)
			{
				$credit = new Model_Limit_Writing_Held();
				$credit->user_id = $this->user_id;
				$credit->date_expires = $dt_held_expires->format(Date::FORMAT_MYSQL);
				$credit->amount_used = $used;
				$credit->amount_total = $total;
				$credit->save();
			}
		}
	}
	
}

?>