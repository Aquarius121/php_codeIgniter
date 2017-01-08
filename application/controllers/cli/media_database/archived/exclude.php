<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Exclude_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function email_providers()
	{
		$exclusions = array(
			'gmail.com',
			'hotmail.com',
			'live.com',
			'live.co.uk',
			'hotmail.co.uk',
			'yahoo.com',
			'mail.com',
			'ymail.com',
			'googlemail.com',
		);
		
		$list = sql_in_list($exclusions);
		$sql = "UPDATE nr_contact_keyword_builder SET
			is_excluded = 1 WHERE domain IN ({$list})";
		$this->db->query($sql);
		$this->trace($this->db->affected_rows());
	}
	
	public function multi_beats()
	{
		set_time_limit(3600);
		$sql_select = "SELECT domain FROM (
			SELECT domain, count(*) AS count
			FROM (
				SELECT domain, beat_1_id AS beat_id
				FROM nr_contact_keyword_builder ckb
				INNER JOIN nr_contact c ON c.id = ckb.contact_id
				GROUP BY ckb.domain, beat_1_id				
				UNION				
				SELECT domain, beat_2_id AS beat_id
				FROM nr_contact_keyword_builder ckb
				INNER JOIN nr_contact c ON c.id = ckb.contact_id
				GROUP BY ckb.domain, beat_2_id
			) gg GROUP BY domain
		) gc WHERE count > 4";

		$sql_update = "UPDATE nr_contact_keyword_builder SET 
			is_excluded = 1 WHERE domain = ?";
			
		$affected = 0;
		$dbr = $this->db->query($sql_select);
		foreach ($dbr->result() as $record)
		{
			if (!$record->domain) continue;
			$this->db->query($sql_update, array($record->domain));
			$affected += $this->db->affected_rows();
			$this->trace($affected, $record->domain);
		}
	}

}

?>