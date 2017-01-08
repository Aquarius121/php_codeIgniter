<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Extract_Domains_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index()
	{
		$counter = 0;
		
		while (true)
		{			
			set_time_limit(60);
			$sql = "SELECT id, email FROM nr_contact c
				LEFT JOIN nr_contact_keyword_builder ckb
				ON ckb.contact_id = c.id
				WHERE ckb.contact_id IS NULL
				AND c.is_media_db_contact = 1
				LIMIT 1000";
				
			$dbr = $this->db->query($sql);	
			if (!$dbr->num_rows()) break;
			foreach ($dbr->result() as $record) 
			{
				$ckb = new Model_Contact_Keyword_Builder();
				$ckb->contact_id = $record->id;
				$pattern = '#@([a-z0-9\-\.]+)$#is';
				if (preg_match($pattern, $record->email, $match))
					$ckb->domain = $match[1];
				$ckb->save();			
				$this->trace(++$counter, 
					$record->email, $ckb->domain);
			}
		}
	}

}

?>