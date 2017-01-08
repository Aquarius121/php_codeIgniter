<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fix_New_York_Controller extends CLI_Base {
		
	// to be run after parse_full_profiles 
	// and before create_within_database

	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index($last_id = 0, $max_id = 999999999)
	{
		set_memory_limit('2048M');
		set_time_limit(0);
		$counter = 0;	

		$sql_process = "SELECT * FROM nr_mmi_contact mc
			WHERE remote_id > ?
			AND remote_id < {$max_id}
			ORDER BY remote_id ASC
			LIMIT 1000";

		$locality = Model_Locality::find('name', 'New York City');

		while (true)
		{			
			$db_result = $this->db->query($sql_process, array($last_id));
			$mmi_contacts = Model_MMI_Contact::from_db_all($db_result);
			if (!$mmi_contacts) break;
			
			foreach ($mmi_contacts as $mmi_contact)
			{
				$counter++;
				$last_id = $mmi_contact->remote_id;
				$raw_data = $mmi_contact->raw_data();
				
				if ($raw_data->address && 
					in_array('New York', $raw_data->address) &&
					in_array('NY', $raw_data->address) && 
					!$raw_data->address_locality_id)
				{
					$raw_data->address_locality_id = $locality->id;
					$raw_data->address_locality = 'New York City';
					$mmi_contact->raw_data($raw_data);
					$mmi_contact->save();

					$this->trace($mmi_contact->remote_id);
				}

				if ($raw_data->city == 'New York')
				{
					$raw_data->city = 'New York City';
					$mmi_contact->raw_data($raw_data);
					$mmi_contact->save();

					$this->trace($mmi_contact->remote_id);
				}
			}
		}
	}
		
}

?>