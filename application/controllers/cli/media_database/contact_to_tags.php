<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('shared/contact_to_tags_trait');

class Contact_To_Tags_Controller extends CLI_Base {
	
	use Contact_To_Tags_Trait;

	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index()
	{
		set_memory_limit('2048M');
		set_time_limit(86400);
		$counter = 0;
		
		Model_Region::enable_cache();
		Model_Locality::enable_cache();
		Model_Country::enable_cache();
		Model_Beat::enable_cache();
		Model_Contact_Role::enable_cache();
			
		while (true)
		{
			$sql = "SELECT * FROM nr_contact WHERE is_media_db_contact = 1
				LIMIT {$counter}, 1000";
			$dbr = $this->db->query($sql);
			if (!$dbr->num_rows()) break;
			
			foreach ($dbr->result() as $result)
			{
				$contact = Model_Contact::from_db_object($result);
				$tags = $this->generate_tags($contact);
				$contact->add_tags($tags);
				$this->trace(++$counter, 
					$contact->first_name,
					$contact->last_name);
			}
		}
	}

}