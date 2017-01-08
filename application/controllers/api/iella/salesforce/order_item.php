<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Order_Item_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		// $cart_item is actually just stdClass
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$item = $cart_item->item();
		$item_data = $item->raw_data();
		$user = Model_User::from_object($this->iella_in->user);
		$salesforce = $this->new_salesforce_process();
		$status = $salesforce->conversion_status($user);
		
		if ($item->type === Model_Item::TYPE_PLAN)
		{
			$plan = Model_Plan::find($item_data->plan_id);
			$period = $plan->period;
			if (isset($item_data->period_repeat_count))
			     $period_repeat_count = $item_data->period_repeat_count;
			else $period_repeat_count = 1;
			$next_renewal_days = $period * $period_repeat_count;
			
			$values = new stdClass();
			$values->Last_Plan__c = $plan->name;
			$values->Subscription_Start_Date__c = Date::$now->format(Date::FORMAT_SF);
			$values->Last_Renewal_Date__c = Date::$now->format(Date::FORMAT_SF);
			$values->Next_Renewal_Date__c = Date::days($next_renewal_days)->format(Date::FORMAT_SF);
			$values->Subscription_Status__c = SalesForce_Process::SUB_STATUS_ACTIVE;
			
			$salesforce->__update_object('Account', $status->account_id, $values);
		}
				
		$values = new stdClass();
		$values->cart_item = $cart_item;
		$values->transaction_id = $this->iella_in->transaction->id;
		$values->is_renewal = (bool) @$this->iella_in->is_renewal;
		$salesforce->create_opportunity($status->account_id, $values);
	}
	
}

?>