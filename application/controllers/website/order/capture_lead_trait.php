<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Capture_Lead_Trait {

	protected function capture_lead()
	{
		$name = $this->input->post('first_name');
		$email = $this->input->post('email');
		
		if (filter_var($email, FILTER_VALIDATE_EMAIL) && 
			!Model_Captured_Landing_Page_Customers::find('customer_email', $email)) 
		{
			$customer = Model_Captured_Landing_Page_Customers::create();
			$customer->customer_name = $name;
			$customer->customer_email = $email;
			$customer->page = $this->env['headers']['referer'];
			$customer->save();
		}
	}
	
}
				
		