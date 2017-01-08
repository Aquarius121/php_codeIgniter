<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fetch_Twitter_Pics_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index()
	{
		set_memory_limit('2048M');
		set_time_limit(86400*180);		

		$this->twitter = Social_Twitter_API::instance();

		$last_id = 0;
		$sql = "SELECT c.id, c.twitter
			FROM nr_contact c 
			LEFT JOIN nr_contact_picture cp
			ON cp.contact_id = c.id
			WHERE cp.contact_id IS NULL
			AND c.twitter IS NOT NULL
			AND c.twitter != ''
			AND c.is_media_db_contact = 1
			AND c.id > ?
			GROUP BY c.id
			ORDER BY c.id ASC
			LIMIT 1";
		
		while (true)
		{
			$dbr = $this->db->query($sql, array($last_id));
			$contact = Model_Contact::from_db($dbr);
			if (!$contact) break;
			$last_id = $contact->id;

			$contact->twitter = Social_Twitter_Profile::parse_id($contact->twitter);
			if (!$contact->twitter) continue;
			$url = $this->lookup($contact);
			$status = $this->download($contact, $url);
			if ($status) $this->transfer_to_other_contacts($contact);
			$this->trace((int) $status, $contact->id, $contact->twitter);
			sleep(10);
		}
	}		

	protected function lookup($contact)
	{
		$params = array();
		$params['screen_name'] = $contact->twitter;
		$params['include_entities'] = false;
		$response = $this->twitter->get('users/lookup', $params);
		if (is_array($response) && isset($response[0]->profile_image_url))
			return preg_replace('#_normal#', null, $response[0]->profile_image_url);
		return false;
	}

	protected function download($contact, $url)
	{
		$status = false;
		if (!$url) return $status;
		$buffer_file = File_Util::buffer_file();
		@copy($url, $buffer_file);
		
		if ($contact_picture = Model_Contact_Picture::create($buffer_file))
		{
			$contact_picture->contact_id = $contact->id;
			$contact_picture->save();
			$status = true;
		}
		
		if (is_file($buffer_file))
			unlink($buffer_file);

		return $status;
	}

	protected function transfer_to_other_contacts($contact)
	{
		$from = Model_Contact_Picture::find($contact->id);
		$profile = Model_Contact_Profile::find_for_contact($contact);
		$sql = "SELECT ccp.contact_id FROM nr_contact_x_contact_profile ccp
			LEFT JOIN nr_contact_picture cp on ccp.contact_id = cp.contact_id
			WHERE ccp.contact_profile_id = {$profile->id} 
			AND ccp.contact_id != {$contact->id}
			AND cp.contact_id IS NULL";

		$dbr = $this->db->query($sql, array($last_id));
		$contacts = Model_Contact::from_db_all($dbr);

		foreach ($contacts as $_contact)
		{
			$to = new Model_Contact_Picture();
			$to->values($from->values());
			$to->contact_id = $_contact->contact_id;
			$to->save();
		}
	}

}

?>