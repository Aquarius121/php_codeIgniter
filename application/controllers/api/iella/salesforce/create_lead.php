<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Create_Lead_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		$user = Model_User::from_object($this->iella_in->user);
		if ($this->iella_in->newsroom)
		     $newsroom = Model_Newsroom::from_object($this->iella_in->newsroom);
		else $newsroom = null;
		$salesforce = $this->new_salesforce_process();
		$salesforce->create_lead($user, $newsroom);
	}
	
}

?>