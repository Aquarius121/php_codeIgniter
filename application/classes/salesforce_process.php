<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SalesForce_Process {
	
	// source for all new leads
	const WEB_SOURCE = 'www.i-newswire.com';
	const LP_SOURCE = 'Landing Pages';
	
	// the product families that a product must fit into
	const FAMILY_SINGLE_PERIOD_SUB = 'Monthly Subscriptions';
	const FAMILY_MULTI_PERIOD_SUB = 'Extended Subscriptions';
	const FAMILY_NON_SUB = 'Non Subscriptions';
	
	// status of the lead when first registered
	const LEAD_STATUS_OPEN = 'Open - Not Contacted';
	const LEAD_STATUS_SIGNED_UP = 'Signed Up';
	const LEAD_STATUS_VERIFIED = 'Verified';
	
	// status when the lead is converted
	const LEAD_QUALIFIED = 'Qualified';
	
	// whether or not the customer is a subscriber
	const ACCOUNT_TYPE_SUB = 'Subscription Customer';
	const ACCOUNT_TYPE_BASIC = 'Non-subscription Customer';
	
	// whether or not the customer has
	// an active subscription
	const SUB_STATUS_ACTIVE = 'Active';
	const SUB_STATUS_CANCELLED = 'Cancelled';
	
	// different types of opportunities
	const OPPORTUNITY_TYPE_SUBSCRIPTION = 'New Subscription';
	const OPPORTUNITY_TYPE_PURCHASE = 'New Purchase';
	const OPPORTUNITY_TYPE_RENEWAL = 'Recurring Payment';
	
	// stage for all new opportunities
	const OPPORTUNITY_STAGE = 'Closed Won';

	protected $sources = array(
		self::WEB_SOURCE,
		self::LP_SOURCE,
	);
	
	public $client;
	public $config;
	
	public function __construct()
	{
		$this->client = SalesForce_Client_Factory::create();
		$this->config = SalesForce_Client_Factory::load_config();
	}

	// find a lead that exists with this email
	public function find_lead_by_email($email)
	{
		$sosl_email = single_quote($email);
		$sosl = "SELECT Id, IsConverted, ConvertedAccountId, 
			ConvertedContactId FROM Lead WHERE 
			Email = {$sosl_email}";

		$response = $this->client->query($sosl);
		if (!isset($response->done)) return false;
		if (!isset($response->records[0]->Id)) return false;
		$lead = $response->records[0];
		return $lead;
	}
	
	public function create_lead($user, $newsroom = null, $custom = array())
	{
		if (($lead = $this->find_lead_by_email($user->email)))
		{
			if ($user->id)
			{
				$update = new stdClass();
				$update->Status = $user->is_verified
					? static::LEAD_STATUS_VERIFIED
					: static::LEAD_STATUS_SIGNED_UP;
				$update->User_ID__c = $user->id;
				$date_c = Date::utc($user->date_created)->format(Date::FORMAT_SF);
				$update->SignupTime__c = $date_c;
				$lead = $this->__update_object('Lead', $lead->Id, $update);
			}

			return $lead;
		}

		$lead = new stdClass();
		$lead->Status = static::LEAD_STATUS_OPEN;
		$lead->OwnerId = $this->config['objects']['owner'];
		$lead->LeadSource = static::WEB_SOURCE;
		if ($user->source && in_array($user->source, $this->sources))
			$lead->LeadSource = $user->source;
		$lead->FirstName = $user->first_name;
		$lead->LastName = $user->last_name;
		if (!$lead->FirstName) $lead->FirstName = 'Unknown';
		if (!$lead->LastName) $lead->LastName = 'Unknown';
		$lead->Email = $user->email;
		
		if ($user->id) 
		{
			$lead->User_ID__c = $user->id;
			$lead->Status = $user->is_verified
				? static::LEAD_STATUS_VERIFIED
				: static::LEAD_STATUS_SIGNED_UP;
			$date_c = Date::utc($user->date_created)->format(Date::FORMAT_SF);
			$lead->SignupTime__c = $date_c;
		}
		
		// these are available for 
		// mock users captured in landing
		// pages but aren't populated for 
		// registered users unless
		// a newsroom object is available
		$lead->Phone = $user->phone;
		$lead->Company = $user->company;

		if (!empty($newsroom->company_name))
			$lead->Company = $newsroom->company_name;
		else if (!$lead->Company)
			$lead->Company = $user->email;

		// add custom fields
		foreach ($custom as $k => $v)
			$lead->$k = $v;
			
		return $this->__create_object('Lead', $lead);
	}
	
	public function create_product($item)
	{
		$item_data = $item->raw_data();
		
		$product = new stdClass();
		$product->ProductCode = sprintf('V2-%06d', $item->id);
		$product->Item_ID__c = $item->id;
		$product->IsActive = !$item->is_disabled;
		
		if (@$item_data->is_auto_renew_enabled)
			  if (@$item_data->period_repeat_count > 1)
		          $product->Family = static::FAMILY_MULTI_PERIOD_SUB;
		     else $product->Family = static::FAMILY_SINGLE_PERIOD_SUB;
		else      $product->Family = static::FAMILY_NON_SUB;
			
		if ($item->comment)
			  // include the comment at the end of the product name for clarity
		     $product->Name = sprintf('%s - %s', $item->name, $item->comment);
		else $product->Name = $item->name;
		
		return $this->__create_object('Product2', $product);
	}
	
	public function create_pricebook_entry($item, $product_id)
	{		
		$entry = new stdClass();
		$entry->IsActive = !$item->is_disabled;
		$entry->UnitPrice = $item->price;
		$entry->Pricebook2Id = $this->config['objects']['pricebook'];
		$entry->Product2Id = $product_id;
		
		return $this->__create_object('PricebookEntry', $entry);
	}
	
	// data must contain the following information:
	// ->cart_item = instance of Cart_Item
	// ->is_renewal = whether it's a new order or not
	// ->transaction_id = uuid of our transaction
	public function create_opportunity($account_id, $data)
	{
		$cart_item = $data->cart_item;
		$item = $cart_item->item();
		
		$transaction_id = $data->transaction_id;
		$is_renewal = (bool) $data->is_renewal;
		$is_subscription = (bool) @$item->raw_data()->is_auto_renew_enabled;
		
		$month = (int) Date::$now->format('n');
		if ($month <= 3) $quarter = 1;
		else if ($month <= 6) $quarter = 2;
		else if ($month <= 9) $quarter = 3;
		else $quarter = 4;
		
		if ($is_renewal)
		     $type = static::OPPORTUNITY_TYPE_RENEWAL;
		else if ($is_subscription)
		     $type = static::OPPORTUNITY_TYPE_SUBSCRIPTION;
		else $type = static::OPPORTUNITY_TYPE_PURCHASE;
		
		$opportunity = new stdClass();
		$opportunity->CloseDate = Date::$now->format(Date::FORMAT_SF);
		$opportunity->Pricebook2Id = $this->config['objects']['pricebook'];
		$opportunity->StageName = static::OPPORTUNITY_STAGE;
		$opportunity->TotalOpportunityQuantity = $cart_item->quantity;
		$opportunity->Type = $type;
		$opportunity->LeadSource = static::WEB_SOURCE;
		$opportunity->Transaction_Number__c = $transaction_id;
		$opportunity->Name = $cart_item->item()->name;
		$opportunity->AccountID = $account_id;
		
		$opportunity_id = $this->__create_object('Opportunity', $opportunity);
		if (!$opportunity_id) return false;
		
		$pb_entry_id = $this->locate_pricebook_entry($item);
		if (!$pb_entry_id) return $opportunity;
		
		$line_item = new stdClass();
		$line_item->OpportunityId = $opportunity_id;
		$line_item->PricebookEntryId = $pb_entry_id;
		$line_item->Quantity = $cart_item->quantity;
		$line_item->UnitPrice = $cart_item->price;
		
		$this->__create_object('OpportunityLineItem', $line_item);
		
		return $opportunity_id;		
	}
	
	// data must contain the following information:
	// ->description = the description of the task
	// ->subject = the subject of the task
	public function create_cancellation_task($user, $data)
	{
		$c_status_ob = $this->conversion_status($user);
		// no such account found so exit
		if (!$c_status_ob->account_id) return;
		
		$task = new stdClass();
		$task->WhatId = $c_status_ob->account_id;
		$task->WhoId = $c_status_ob->contact_id;
		$due_date = clone Date::$now;
		$due_date->setTimezone(new DateTimeZone('America/New_York'));
		$task->ActivityDate = $due_date->format(Date::FORMAT_SF);
		$task->Description = $data->description;
		$task->IsReminderSet = true;
		$task->ReminderDateTime = $due_date->format(Date::FORMAT_SF);
		$task->Subject = $data->subject;
		$task->Type = 'Email';
		
		$task_id = $this->__create_object('Task', $task);
		if (!$task_id) return false;
		return $task_id;
	}
	
	public function convert_lead($lead_id)
	{
		$conversion = new stdClass();
		$conversion->convertedStatus = static::LEAD_QUALIFIED;
		$conversion->leadId = $lead_id;
		$conversion->overwriteLeadSource = true;
		$conversion->sendNotificationEmail = true;
		$conversion->OwnerId = $this->config['objects']['owner'];
		$conversion->doNotCreateOpportunity = true;
	 
		$response = $this->client->convertLead(array($conversion));
		if (!isset($response->result[0]->success) 
			|| !$response->result[0]->success)
			throw new Exception(var_export($response, true));
		
		$record = $response->result[0];
		$status = new stdClass();
		$status->lead_id = $record->leadId;
		$status->account_id = @$record->accountId;
		$status->contact_id = @$record->contactId;
		return $status;
	}
	
	public function conversion_status($user)
	{
		// select the conversion info for the user lead
		$sosl = "SELECT Id, IsConverted, ConvertedAccountId, 
			ConvertedContactId FROM Lead WHERE 
			User_ID__c = {$user->id}";
			
		$response = $this->client->query($sosl);
		if (!isset($response->done)) return false;
		if (!isset($response->records[0]->Id)) return false;
		
		$record = $response->records[0];
		$status = new stdClass();
		$status->lead_id = $record->Id;
		$status->account_id = @$record->ConvertedAccountId;
		$status->contact_id = @$record->ConvertedContactId;
		$status->is_converted = $record->IsConverted;
		
		// attempt to locate account
		// independent of the lead
		if (!$status->is_converted)
			$this->conversion_status_fallback($user, $status);
		
		if ($status->is_converted)
		     $status->main_object_id = $status->account_id;
		else $status->main_object_id = $status->lead_id;
		if ($status->is_converted)
		     $status->main_object_class = 'Account';
		else $status->main_object_class = 'Lead';
		return $status;
	}
	
	protected function conversion_status_fallback($user, &$status)
	{
		// select the conversion info for an independent account 
		$sosl = "SELECT Id FROM Account WHERE User_ID__c = {$user->id}";
		$response = $this->client->query($sosl);
		if (isset($response->records[0]->Id))
			$status->account_id = $response->records[0]->Id;
		if (!$status->account_id) return;
		$status->is_converted = true;
		
		// select the conversion info for an independent contact 
		$sosl = "SELECT Id FROM Contact WHERE AccountId = '{$status->account_id}'";
		$response = $this->client->query($sosl);
		if (isset($response->records[0]->Id))
			$status->contact_id = $response->records[0]->Id;
	}
		
	public function locate_pricebook_entry($item)
	{
		$sosl = "SELECT Id FROM PricebookEntry WHERE 
			product2.Item_ID__c = {$item->id}";
		$response = $this->client->query($sosl);
		if (!isset($response->records[0]->Id)) return false;
		return $response->records[0]->Id;		
	}
	
	public function __update_object($class, $object_id, $values)
	{
		$object = new stdClass();
		$object->Id = $object_id;		
		foreach ($values as $k => $v)
			$object->{$k} = $v;
			
		return $this->client->update(array($object), $class);
	}
	
	public function __create_object($class, $values)
	{
		$object = new stdClass();
		foreach ($values as $k => $v)
			$object->{$k} = $v;
			
		$response = $this->client->create(array($object), $class);
		if (isset($response[0]->success) && $response[0]->success)
			return $response[0]->id;
		
		throw new Exception(var_export($response, true));
	}
	
	public function __query_object($class, $object_id, $fields)
	{
		// select the listed fields from salesforce
		$fields = implode(',', $fields);
		$sosl = "SELECT Id, {$fields} FROM {$class}
			WHERE Id = '{$object_id}'";
			
		$response = $this->client->query($sosl);
		if (!isset($response->records[0])) return false;
		return $response->records[0];
	}
	
}