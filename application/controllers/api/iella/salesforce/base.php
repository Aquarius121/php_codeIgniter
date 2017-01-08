<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Salesforce_Iella_Base extends Iella_Base {
	
	protected function new_salesforce_process()
	{
		try
		{
			return new SalesForce_Process();
		}
		catch (Exception $e)
		{
			$this->reschedule(Date::hours(1));
		}
	}

	
}

?>