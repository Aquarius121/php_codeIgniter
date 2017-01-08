<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Verify_Lead_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		$user = Model_User::from_object($this->iella_in->user);
		$salesforce = $this->new_salesforce_process();
		$status = $salesforce->conversion_status($user);
		
		if ($status && !$status->is_converted)
		{
			$values = new stdClass();
			$values->Status = SalesForce_Process::LEAD_STATUS_VERIFIED;
			return $salesforce->__update_object('Lead', $status->lead_id, $values);
		}
	}
	
}

?>