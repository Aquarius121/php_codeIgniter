<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Update_NR_Subscriptions_Controller extends CLI_Base {

	// Add subcription hash
	// also, add subscription contacts
	public function index()
	{
		$this->add_hashes();
		$this->add_contacts();
	}

	protected function add_contacts()
	{
		$sql = "SELECT * 
				FROM nr_subscription s
				WHERE s.contact_id IS NULL";

		$results = Model_Subscription::from_sql_all($sql);
		foreach ($results as $result)
		{
			if (!$contact = Model_Contact::find_subscriber($result->company_id, $result->email))
			{
				$contact = new Model_Contact();
				$contact->company_id = $result->company_id;
				$contact->email = $result->email;
				$contact->is_nr_subscriber = 1;
				$contact->is_unsubscribed = !$result->is_activated;
				$contact->save();
			}

			$result->contact_id = $contact->id;
			$result->save();

			$this->update_contact_list($result);
		}
	}

	protected function update_contact_list($m_sub)
	{
		$m_contact = Model_Contact::find($m_sub->contact_id);
		if (!$m_contact || !$m_contact->is_nr_subscriber)
			return false;

		$c_list_criteria = array();
		$c_list_criteria[] = array('company_id', $m_contact->company_id);
		$c_list_criteria[] = array('is_nr_subscriber_list', '1');

		if (!$m_contact->is_unsubscribed)
		{
			// Add or update the contact list			
			if (!$nr_contact_list = Model_Contact_List::find($c_list_criteria))
			{
				$nr_contact_list = new Model_Contact_List();
				$nr_contact_list->name = 'Newsroom Subscribers';
				$nr_contact_list->company_id = $m_contact->company_id;
				$nr_contact_list->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$nr_contact_list->is_nr_subscriber_list = 1;
				$nr_contact_list->save();
			}

			$nr_contact_list->add_contact($m_contact);
		}
		elseif ($nr_contact_list = Model_Contact_List::find($c_list_criteria))
		{
			$nr_contact_list->remove_contact($m_contact);
			if (! $nr_contact_list->count_contacts())
				$nr_contact_list->delete();
		}	
	}

	protected function add_hashes()
	{
		$sql = "SELECT * 
				FROM nr_subscription s
				LEFT JOIN nr_subscription_hash h
				ON h.subscription_id = s.id
				WHERE h.subscription_id IS NULL
				LIMIT 1";

		while (1)
		{
			if (!$result = Model_Subscription::from_sql($sql))
				break;

			$sub_hash = new Model_Subscription_Hash();
			$sub_hash->subscription_id = $result->id;
			$sub_hash->hash = Data_Hash::__hash_hex($result->id, 'sha256');
			$sub_hash->save();
		}
	}

}