<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/salesforce/base');

class Content_Identity_Locked_Controller extends Salesforce_Iella_Base {
	
	public function index()
	{
		$content = Model_Content::from_object($this->iella_in->content);
		$user = $content->owner();
		
		$salesforce = $this->new_salesforce_process();
		$status = $salesforce->conversion_status($user);
		if (!$status) return;
			
		if ($content->type === Model_Content::TYPE_PR)
		{
			if ($content->is_premium)
			     $field = 'No_of_Premium_PRs__c';
			else $field = 'No_of_Basic_PRs__c';
			
			$response = $salesforce->__query_object($status->main_object_class,
				$status->main_object_id, array($field));
			if (!isset($response->{$field}))
				$response->{$field} = 0;
			
			$values = new stdClass();
			$values->{$field} = ((int) $response->{$field}) + 1;
			$values->Last_PR_Date__c = Date::$now->format(Date::FORMAT_SF);
			$salesforce->__update_object($status->main_object_class,
				$status->main_object_id, $values);
		}
	}
	
}

?>