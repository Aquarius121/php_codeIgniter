<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Plan_Credit extends Model {
	
	const PERIOD_MONTHLY  = 'MONTHLY';
	const PERIOD_WEEKLY   = 'WEEKLY';
	const PERIOD_DAILY    = 'DAILY';

	protected static $__table = 'co_plan_credit';
	
	public static function find_all_plan($plan)
	{
		if ($plan instanceof Model_Plan)
			$plan = $plan->id;
		$criteria = array('plan_id', $plan);
		$plan_credits = static::find_all($criteria);
		
		foreach ($plan_credits as $k => $plan_credit)
		{
			$plan_credits[$plan_credit->type] = $plan_credit;
			unset($plan_credits[$k]);
		}
		
		return $plan_credits;
	}
	
	public static function find_all_rollover_to_held($plan)
	{
		if ($plan instanceof Model_Plan)
			$plan = $plan->id;
		$criteria = array();
		$criteria[] = array('plan_id', $plan);
		$criteria[] = array('is_rollover_to_held_enabled', 1);
		return static::find_all($criteria);
	}
	
}

?>