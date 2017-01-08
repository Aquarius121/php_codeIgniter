<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Activate_Plan_Controller extends Iella_Base {
	
	public function index()
	{
		$this->iella_out->status = false;
		$component_item = Model_Component_Item::from_object($this->iella_in->component_item);
		$item = Model_Item::from_object($this->iella_in->item);
		$item_data = $item->raw_data();
		if (!isset($item_data->plan_id)) return;
		$plan_id = $item_data->plan_id;
		
		// find component set (for user id)
		$set_id = $component_item->component_set_id;
		$component_set = Model_Component_Set::find($set_id);
		if (!$component_set) return;
		
		$active_plan = Model_User_Plan::find_active($component_set->user_id);
		if ($active_plan) $active_plan->deactivate();
		
		// put user onto specified plan
		$user_plan = new Model_User_Plan();
		$user_plan->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		$user_plan->date_expires = $component_item->date_expires;
		$user_plan->user_id = $component_set->user_id;
		$user_plan->plan_id = $plan_id;
		$user_plan->item_id = $item->id;
		$user_plan->is_active = 1;
		$user_plan->save();
		
		// create user credit records for each credit available
		$plan_credits = Model_Plan_Credit::find_all_plan($plan_id);
		foreach ($plan_credits as $plan_credit)
		{
			if (Credit::is_common($plan_credit->type))
			{
				// credit uses common table 
				$common_held = Model_Limit_Common_Held::create($user_plan->user_id, 
					$plan_credit->type, $plan_credit->available);
				$common_held->save();
			}
			else
			{
				// credit is defined (does not use common table)
				$user_plan_credit = new Model_User_Plan_Credit();
				$user_plan_credit->user_plan_id = $user_plan->id;
				$user_plan_credit->plan_credit_id = $plan_credit->id;
				$user_plan_credit->total = $plan_credit->available;
				$user_plan_credit->period = $plan_credit->period;
				$user_plan_credit->used = 0;
				$user_plan_credit->save();
			}			
		}
		
		$this->iella_out->user_plan_id = $user_plan->id;
		$this->iella_out->status = true;
	}
	
}