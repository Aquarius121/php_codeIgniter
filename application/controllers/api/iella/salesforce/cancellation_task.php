<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Cancellation_Task_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		$cancellation = Model_Cancellation::from_object($this->iella_in->cancellation);
		$item = Model_Item::from_object($this->iella_in->item);
		$user = Model_User::from_object($this->iella_in->user);
		$raw_data = $cancellation->raw_data();
		
		$data = new stdClass();
		$data->subject = "Subscription Cancelled";
		$data->description  = "The customer requested cancellation of their subscription.\r\n\r\n";
		$data->description .= "Name: {$user->first_name} {$user->last_name}\r\n";
		$data->description .= "Email: {$user->email}\r\n";
		$data->description .= "Item: {$item->name}\r\n\r\n";
		$data->description .= "Reason: \r\n{$raw_data->reason}";
		
		$salesforce = $this->new_salesforce_process();
		$salesforce->create_cancellation_task($user, $data);
	}
	
}

?>