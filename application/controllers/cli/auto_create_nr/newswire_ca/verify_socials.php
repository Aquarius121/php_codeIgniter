<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Verify_Socials_Controller extends CLI_Base { 
	
	public function index()
	{
		error_reporting(E_ALL);
		
		$ci =& get_instance();
		$cnt = 1;
		while ($cnt <= 15)
		{
			$cnt++;

			$sql = "SELECT * FROM ac_nr_newswire_ca_company_data 
					WHERE (soc_fb IS NOT NULL AND soc_fb <> '' AND soc_fb_feed_status = ?)
					OR (soc_twitter IS NOT NULL AND soc_twitter <> '' AND soc_twitter_feed_status = ?)
					OR (soc_gplus IS NOT NULL AND soc_gplus <> '' AND soc_gplus_feed_status = ?)
					OR (soc_youtube IS NOT NULL AND soc_youtube <> '' AND soc_youtube_feed_status = ?)
					OR (soc_pinterest IS NOT NULL AND soc_pinterest <> '' AND soc_pinterest_feed_status = ?)
					ORDER by newswire_ca_company_id
					LIMIT 1";
			
			$nc = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
			$result = $this->db->query($sql, array($nc, $nc, $nc, $nc, $nc));
			if (!$result->num_rows()) break;
			
			$c_data = Model_Newswire_CA_Company_Data::from_db($result);
			if (!$c_data) break;

			
			if (!empty($c_data->soc_fb))
				if (Social_Facebook_Feed::is_valid($c_data->soc_fb))
					$c_data->soc_fb_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
				else
				{
					// $c_data->soc_fb = null;
					$c_data->soc_fb_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
				}
					

			if (!empty($c_data->soc_twitter))
				if (Social_Twitter_Feed::is_valid($c_data->soc_twitter))
					$c_data->soc_twitter_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
				else
				{
					// $c_data->soc_twitter = null;
					$c_data->soc_twitter_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
				}

			if (!empty($c_data->soc_gplus))
				if (Social_GPlus_Feeds::is_valid($c_data->soc_gplus))
					$c_data->soc_gplus_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
				else
				{
					// $c_data->soc_gplus = null;
					$c_data->soc_gplus_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
				}

			if (!empty($c_data->soc_youtube))
				if (Social_Youtube_Feed::is_valid($c_data->soc_youtube))
					$c_data->soc_youtube_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
				else
				{
					// $c_data->soc_youtube = null;
					$c_data->soc_youtube_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
				}

			if (!empty($c_data->soc_pinterest))
				if (Social_Pinterest_Feed::is_valid($c_data->soc_pinterest))
					$c_data->soc_pinterest_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_VALID;
				else
				{
					// $c_data->soc_pinterest = null;
					$c_data->soc_pinterest_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_INVALID;
				}

			$c_data->save();

			// sleep(1);
			
		}

		
	}
}

?>
