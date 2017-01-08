<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Process_Captured_Landing_Page_Customers_Controller extends CLI_Base {
	
	protected $trace_enabled = false;
	protected $trace_time = true;
	
	public function index()
	{
		$sql = "SELECT * FROM nr_captured_landing_page_customers
			WHERE is_lead_created_in_salesforce = 0
			AND date_created < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 HOUR)";
		$customers = Model_Captured_Landing_Page_Customers::from_sql_all($sql);
		$sf = new SalesForce_Process();

		foreach ($customers as $customer)
		{
			if (($user = Model_User::find_email($customer->customer_email)))
			{
				$customer->is_account_found = 1;
				$customer->is_lead_created_in_salesforce = 1;
				$customer->user_id = $user->id;
				$customer->save();
				continue;
			}

			$names = Raw_Data::from(explode(' ',
				$customer->customer_name));

			$user = new Mock_User();
			$user->first_name = $names[0];
			$user->last_name = $names[1];
			$user->email = $customer->customer_email;
			$user->phone = $customer->customer_phone;
			$user->company = $customer->customer_company;
			$user->source = SalesForce_Process::LP_SOURCE;
			$custom = array('Landing_Page__c' => $customer->page);
			$this->trace($sf->create_lead($user, null, $custom));

			$customer->is_lead_created_in_salesforce = 1;
			$customer->save();
		}
	}

}