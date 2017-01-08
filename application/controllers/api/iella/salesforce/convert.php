<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Convert_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		$user = Model_User::from_object($this->iella_in->user);
		$billing = Model_Billing::from_object($this->iella_in->billing);
		$billing_data = $billing->raw_data();
		
		$salesforce = $this->new_salesforce_process();
		$status = $salesforce->conversion_status($user);
		
		if (!$status)
		{
			$status = new stdClass();
			$lead_id = $salesforce->create_lead($user, null);
			$status = $salesforce->convert_lead($lead_id);
			$status->is_converted = true;
		}
		
		if (!$status->is_converted)
		{
			$status = $salesforce->convert_lead($status->lead_id);
			$status->is_converted = true;
		}
		
		$values = new stdClass();
		$values->Name = $billing->company_name;
		$values->BillingStreet = sprintf('%s%s%s',
			$billing->street_address, PHP_EOL,
			$billing->extended_address);
		$values->BillingCountry = 
			Model_Country::find($billing->country_id)->name;
		$values->BillingCountry = 
			Model_Country::find($billing->country_id)->name;
		$values->CC_Last_4__c = @$billing_data->card_details->last4;
		$salesforce->__update_object('Account', $status->account_id, $values);
		
		$values = new stdClass();
		$values->Phone = $billing->phone;
		$values->FirstName = $billing->first_name;
		$values->LastName = $billing->last_name;
		$salesforce->__update_object('Contact', $status->contact_id, $values);
	}
	
}

?>