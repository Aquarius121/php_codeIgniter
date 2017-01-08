<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Old_Email_Campaigns_Update_Spam_Score_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index()
	{
		$sql = "UPDATE nr_campaign 
				set spam_score = -1 
				WHERE date_send > date_sub(utc_timestamp(), interval 30 day)";
		$this->db->query($sql);

		$sql = "SELECT *
				FROM nr_campaign 
				WHERE spam_score = -1
				ORDER BY id DESC
				LIMIT 1";

		while (true)
		{
			if (!$campaign = Model_Campaign::from_sql($sql))
				break;

			$this->trace($campaign->id);
			$campaign->spam_score = $campaign->spam_vulnerability_score();
			$campaign->save();
		}
	}

}
