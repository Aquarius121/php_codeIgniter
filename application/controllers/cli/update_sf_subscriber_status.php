<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Update_SF_Subscriber_Status_Controller extends CLI_Base {
	
	public function index()
	{	
		$sql = "SELECT u.id, u.package, sss.user_id AS SalesForce_Subscriber_Status__user_id,
			sss.is_subscriber AS SalesForce_Subscriber_Status__is_subscriber FROM nr_user u
			INNER JOIN nr_salesforce_subscriber_status sss
			ON u.id = sss.user_id 
			WHERE (u.package > 0 AND sss.is_subscriber = 0) 
			OR (u.package = 0 AND sss.is_subscriber = 1)";
			
		$users = Model_User::from_db_all($this->db->query($sql), array(
			'SalesForce_Subscriber_Status' => 'Model_SalesForce_Subscriber_Status',
		));
		
		if (!$users) return;		
		$salesforce = new SalesForce_Process();
		
		foreach ($users as $user)
		{
			set_time_limit(300);
			$sub_status = $user->SalesForce_Subscriber_Status;
			$sub_status->is_subscriber = (int) (!$sub_status->is_subscriber);
			$sub_status->save();
				
			$status = $salesforce->conversion_status($user);
			if (!$status->account_id) continue;
			
			$values = new stdClass();
			$values->Type = $sub_status->is_subscriber 
				? SalesForce_Process::ACCOUNT_TYPE_SUB
				: SalesForce_Process::ACCOUNT_TYPE_BASIC;
			$values->Subscription_Status__c = 
				SalesForce_Process::SUB_STATUS_ACTIVE;
				
			$salesforce->__update_object('Account',
				$status->account_id, $values);
		}
	}

}

?>
