<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Recheck_Youtube_Accounts_Controller extends CLI_Base {

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT * 
				FROM nr_company_profile
				WHERE NOT ISNULL(NULLIF(soc_youtube, ''))
				AND soc_youtube_is_feed_valid = 0
				AND is_soc_youtube_rechecked = 0
				ORDER BY company_id DESC
				LIMIT 1";
		
		while (1)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) return;
			
			$comp_profile = Model_Company_Profile::from_db($result);
			if (!$comp_profile) return;

			if (!empty($comp_profile->soc_youtube))
				if (Social_Youtube_Feed::is_valid($comp_profile->soc_youtube))
					$comp_profile->soc_youtube_is_feed_valid = 1;
			
			$comp_profile->is_soc_youtube_rechecked = 1;	
			$comp_profile->save();
		}

		
	}	
}

?>
