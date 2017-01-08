<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Update_Social_Wire_Settings_Controller extends CLI_Base {

	public function index()
	{
		set_time_limit(7200);

		$sql = "SELECT * 
				FROM nr_company_profile
				WHERE social_wire_settings IS NULL
				ORDER BY company_id
				LIMIT 10";

		while(1)
		{
			$c_profiles = Model_Company_Profile::from_sql_all($sql);

			if (!count($c_profiles))
				break;

			foreach ($c_profiles as $c_profile)
			{
				$this->trace(sprintf('updated %d', $c_profile->company_id));
				$this->update_profile($c_profile);
			}
		}
	}

	protected function update_profile($c_profile)
	{
		$sw_settings = new stdClass();
		$sw_settings->soc_twitter_is_feed_valid = $c_profile->soc_twitter_is_feed_valid;
		$sw_settings->soc_facebook_is_feed_valid = $c_profile->soc_facebook_is_feed_valid;
		$sw_settings->soc_gplus_is_feed_valid = $c_profile->soc_gplus_is_feed_valid;
		$sw_settings->soc_youtube_is_feed_valid = $c_profile->soc_youtube_is_feed_valid;
		$sw_settings->soc_linkedin_is_feed_valid = $c_profile->soc_linkedin_is_feed_valid;
		$sw_settings->soc_pinterest_is_feed_valid = $c_profile->soc_pinterest_is_feed_valid;
		$sw_settings->soc_vimeo_is_feed_valid = $c_profile->soc_vimeo_is_feed_valid;
		$sw_settings->soc_instagram_is_feed_valid = $c_profile->soc_instagram_is_feed_valid;

		$sw_settings->is_inc_twitter_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
			$c_profile->soc_twitter && $c_profile->soc_twitter_is_feed_valid);

		$sw_settings->is_inc_facebook_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
				$c_profile->soc_facebook && $c_profile->soc_facebook_is_feed_valid);

		$sw_settings->is_inc_gplus_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
			$c_profile->soc_gplus && $c_profile->soc_gplus_is_feed_valid);

		$sw_settings->is_inc_youtube_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
			$c_profile->soc_youtube && $c_profile->soc_youtube_is_feed_valid);

		$sw_settings->is_inc_pinterest_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
			$c_profile->soc_pinterest && $c_profile->soc_pinterest_is_feed_valid);

		$sw_settings->is_inc_vimeo_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
			$c_profile->soc_vimeo && $c_profile->soc_vimeo_is_feed_valid);

		$sw_settings->is_inc_instagram_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
			$c_profile->soc_instagram && $c_profile->soc_instagram_is_feed_valid);

		$sw_settings->is_inc_linkedin_in_soc_wire = (int) ($c_profile->is_enable_social_wire && 
			$c_profile->soc_linkedin && $c_profile->soc_linkedin_is_feed_valid);

		$c_profile->raw_data_write('social_wire_settings', $sw_settings);
		$c_profile->save();	
	}
}