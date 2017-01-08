<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Renewal_Cancel_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		$component_set = Model_Component_Set::from_object($this->iella_in->component_set);
		$component_item = Model_Component_Item::from_object($this->iella_in->component_item);
		$item = Model_Item::find($component_item->item_id);
		$user = Model_User::find($component_set->user_id);
		
		$salesforce = $this->new_salesforce_process();
		$status = $salesforce->conversion_status($user);
		if (!$status->is_converted) return;
		
		if ($item->type === Model_Item::TYPE_PLAN)
		{
			$values = new stdClass();
			$values->fieldsToNull = array('Next_Renewal_Date__c');
			$values->Subscription_Status__c = SalesForce_Process::SUB_STATUS_CANCELLED;
			
			$salesforce->__update_object('Account', $status->account_id, $values);
		}
	}
	
}

?>