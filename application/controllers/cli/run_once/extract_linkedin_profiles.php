<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Extract_Linkedin_Profiles_Controller extends CLI_Base {
	
	public function index()
	{
		set_time_limit(20000);

		$lastid = 0;
		$sql = "SELECT c.*, {{ cp.* as profile using Model_Contact_Profile }}
				FROM nr_contact c
				INNER JOIN nr_contact_x_contact_profile cxcp
				ON cxcp.contact_id = c.id
				INNER JOIN nr_contact_profile cp
				ON cxcp.contact_profile_id = cp.id
				WHERE c.is_media_db_contact = 1
				AND c.id > ?
				ORDER BY c.id ASC
				LIMIT 2000";

		while (1)
		{
			$contacts = Model_Contact::from_sql_all($sql, array($lastid));
			if (!count($contacts)) break;

			foreach ($contacts as $contact)
			{
				$lastid = $contact->id;
				$raw_data = $contact->profile->raw_data();
				$linkedin = value_or_null($raw_data->linkedin);

				if ($linkedin)
				{
					$linkedin_url = sprintf("https://www.linkedin.com/%s", $linkedin);
					$link = sprintf("<a href=\"%s\">%s</a>", $linkedin_url, $linkedin_url);
					$this->console($link);
				}
			}
		}
	}

}
