<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Activate_Plan_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		$component_item = Model_Component_Item::from_object($this->iella_in->component_item);
		$component_set = Model_Component_Set::find($component_item->component_set_id);
		$user = Model_User::find($component_set->user_id);
		
		// record the event within salesforce 
		// after 5 minutes to ensure correct order
		$future = Date::minutes(5);
		$iella_event = new Scheduled_Iella_Event();
		$iella_event->data->user = $user;
		$iella_event->schedule('salesforce_activate_plan', $future);
	}
	
	public function scheduled()
	{
		$user = Model_User::from_object($this->iella_in->user);
		$sub_status = Model_SalesForce_Subscriber_Status::find($user->id);
		if (!$sub_status) $sub_status = new Model_SalesForce_Subscriber_Status();
		$sub_status->user_id = $user->id;
		$sub_status->is_subscriber = 1;
		$sub_status->save();
		
		$salesforce = $this->new_salesforce_process();
		$status = $salesforce->conversion_status($user);
		if (!$status->account_id) return;
			
		$values = new stdClass();
		$values->Type = SalesForce_Process::ACCOUNT_TYPE_SUB;
		$values->Subscription_Status__c = SalesForce_Process::SUB_STATUS_ACTIVE;
		$values->Subscription_Months__c = $this->calculate_subscription_months($user);
		
		$salesforce->__update_object('Account', $status->account_id, $values);
	}
	
	protected function calculate_subscription_months($user)
	{
		$total_period = 0;
		$total_period += $this->calculate_subscription_period_v1($user);
		$total_period += $this->calculate_subscription_period_v2($user);
		return $total_period / 30;
	}
	
	protected function calculate_subscription_period_v1($user)
	{
		$item_silver = Model_Item::find_slug('silver-plan-2011');
		$item_gold = Model_Item::find_slug('gold-plan-2011');
		$item_platinum = Model_Item::find_slug('platinum-plan-2011');		
		$items = array($item_silver->id, $item_gold->id, $item_platinum->id);
		$items_list = sql_in_list($items);
		
		// we assume that the older plans have a exactly 1 period
		$sql = "SELECT SUM(period) AS period FROM co_component_item ci 
			INNER JOIN co_component_set cs
			ON cs.id = ci.component_set_id 
			WHERE cs.user_id = {$user->id}
			AND ci.item_id IN ({$items_list})
			AND cs.is_legacy = 1";
			
		$result = Model_Base::from_db($this->db->query($sql));
		if (!$result) return 0;
		return (int) $result->period;
	}
	
	protected function calculate_subscription_period_v2($user)
	{
		$total_period = 0;
		$sql = "SELECT ci.id, ci.date_created, ci.date_expires
			FROM co_component_item ci 
			INNER JOIN co_component_set cs
			ON cs.id = ci.component_set_id 
			INNER JOIN co_item i 
			ON i.id = ci.item_id 
			WHERE cs.user_id = {$user->id}
			AND cs.is_legacy = 0
			AND i.type = ?";
			
		$dbr = $this->db->query($sql, array(Model_Item::TYPE_PLAN));
		$results = Model_Base::from_db_all($dbr);
		
		foreach ($results as $result)
		{
			$dt_created = Date::utc($result->date_created);
			$dt_expires = Date::utc($result->date_expires);
			$ts_created = $dt_created->getTimestamp();
			$ts_expires = $dt_expires->getTimestamp();
			$ts_difference = $ts_expires - $ts_created;
			$total_period += abs($ts_difference) / 86400;
		}
		
		return $total_period;
	}
	
}

?>