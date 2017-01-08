<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Download_Full_Profiles_Controller extends CLI_Base {
	
	const FILES_DIR = 'raw/mmi_contacts';
		
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index($last_id = 0)
	{
		set_memory_limit('1024M');
		set_time_limit(0);
		
		$last_id = (int) $last_id; 
		
		$sql = "select * from nr_mmi_contact where remote_id > ? 
			order by remote_id asc limit 100";

		// $sql = "select * from nr_mmi_contact where remote_id > ? 
		// 	and is_created = 0
		// 	order by remote_id asc
		// 	limit 100";

		$files_dir = static::FILES_DIR;
		@mkdir("{$files_dir}/profiles");

		while (true)
		{
			$dbr = $this->db->query($sql, array($last_id));
			$contacts = Model_MMI_Contact::from_db_all($dbr);
			if (!$contacts) return;

			foreach ($contacts as $contact)
			{
				$file = "{$files_dir}/profiles/{$contact->remote_id}";
				$last_id = $contact->remote_id;

				if (file_exists($file))
				{
					$failed = 0;
					$this->trace($contact->remote_id, 'exists');
					continue;
				}

				$cmd = "{$files_dir}/download_profile.sh {$contact->remote_id} profiles/{$contact->remote_id} 2>&1";
				shell_exec($cmd);
				sleep(1);

				$content = file_get_contents($file);
				
				if (str_contains($content, 'main-content-profile'))
				{
					$failed = 0;
					$this->trace($contact->remote_id, 'success');
				}
				else
				{
					$failed++;
					$this->trace($contact->remote_id, 'failed');
					unlink($file);
				}

				// failed 20 in a row
				// => authenticaiton problem
				if ($failed > 20)
				{
					return;
				}
			}
		}
	}
		
}

?>