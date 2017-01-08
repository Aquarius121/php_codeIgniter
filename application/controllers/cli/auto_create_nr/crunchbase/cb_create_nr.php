<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class CB_Create_NR_Controller extends CLI_Base { // for crunchbase companies
	
	public function index()
	{
		$filter = "1";
		$condition_social = "(ISNULL(NULLIF(cd.soc_fb,'')) + ISNULL(NULLIF(cd.soc_twitter,'')) + 
							ISNULL(NULLIF(cd.soc_gplus,'')) + ISNULL(NULLIF(cd.soc_youtube,'')) + 
							ISNULL(NULLIF(cd.soc_pinterest,''))) <= 3
							AND (cd.soc_fb_feed_status = 'valid' OR cd.soc_twitter_feed_status = 'valid' OR
								cd.soc_gplus_feed_status = 'valid' OR cd.soc_youtube_feed_status = 'valid' OR
								cd.soc_pinterest_feed_status = 'valid')";
		
		$condition_website = "NULLIF(cd.website, '') IS NOT NULL";
		$condition_email = "NULLIF(cd.email, '') IS NOT NULL";
		$condition_logo = "NULLIF(cd.logo_image_path, '') IS NOT NULL";
		
		$filter = "{$filter} AND {$condition_website}";
		$filter = "{$filter} AND {$condition_email}";
		$filter = "{$filter} AND {$condition_logo}";
		$filter = "{$filter} AND {$condition_social}";
		

		$cnt = 1;
		while ($cnt <= 20)
		{
			$cnt++;

			$sql = "SELECT cd.company_id as company_id  
				FROM ac_nr_cb_company c
				INNER JOIN ac_nr_cb_company_data cd 
				ON cd.company_id = c.id 
				WHERE {$filter} 
				AND c.company_id IS NULL 
				AND c.raw_data IS NOT NULL
				ORDER BY c.id DESC
				LIMIT 1";

			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			$c_data = Model_CB_Company_Data::from_db($result);
			$this->build($c_data->company_id);
			sleep(1);
		}
		
	}

	public function build($id = null)
	{
		if (!$id)
			return;

		$comp = Model_CB_Company::find($id);
		$c_data = Model_CB_Company_Data::find($id);
		
		if (empty($c_data->email))
		{
			echo 'ERROR: Email address can not be blank';
			$comp->company_id = -1;
			$comp->save();
			return;
		}

		if ($m_user = Model_User::find('email', $c_data->email))
		{
			echo 'ERROR: A user with this email address already exists';
			$comp->company_id = -2;
			$comp->save();			
			return;
		}

		$user = Model_User::create();
		$pass = Model_User::generate_password();
		$user->set_password($pass);
		$user->first_name = $comp->name;
		$user->last_name = '';
		$user->email = $c_data->email;
		$user->is_admin = 0;
		$user->is_reseller = 0;
		$user->is_enabled = 1;
		$user->is_verified = 1;
		$user->save();

		

		$newsroom = Model_Newsroom::create($user->id, $comp->name);		
		$newsroom->save();

		$nr_custom = new Model_Newsroom_Custom();
		$company_profile = new Model_Company_Profile();
		$nr_custom->company_id = $newsroom->company_id;

		// fetching and setting the logo
		if (!empty($c_data->logo_image_path))
		{
			$logo_file = "logo";
			$logo_url = $c_data->logo_image_path;
			@copy($logo_url, $logo_file);

			if (Image::is_valid_file($logo_file))
			{
				// import the logo image into the system
				$logo_im = LEGACY_Image::import("logo", $logo_file);
				 
				// assign to the new company and save
				$logo_im->company_id = $newsroom->company_id;
				$logo_im->save();
				 
				// set it to use the new logo image and save
				$nr_custom->logo_image_id = $logo_im->id;
			}
			}
		
		$nr_custom->save();

		$company_profile->company_id = $newsroom->company_id;
		$company_profile->address_street = value_or_null($c_data->address);
		$company_profile->address_city = value_or_null($c_data->city);
		$company_profile->address_state = value_or_null($c_data->state);
		$company_profile->address_zip = value_or_null($c_data->zip);
		$company_profile->website = value_or_null($c_data->website);
		$company_profile->phone = value_or_null($c_data->phone);
		$company_profile->summary = value_or_null($c_data->short_description);
		$company_profile->description = value_or_null($c_data->about_company);
		$company_profile->address_country_id = value_or_null($c_data->country_id);

		
		if (!empty($c_data->soc_fb))
		{
			if ($c_data->soc_fb_feed_status == Model_CB_Company_Data::SOCIAL_VALID ||
					Social_Facebook_Feed::is_valid($c_data->soc_fb))
			{
				$company_profile->soc_facebook = $c_data->soc_fb;
				$company_profile->soc_facebook_is_feed_valid = 1;
			}
			else
				$company_profile->soc_facebook = $c_data->soc_fb;		
		}
		
		if (!empty($c_data->soc_twitter))
		{
			if ($c_data->soc_twitter_feed_status == Model_CB_Company_Data::SOCIAL_VALID ||
				Social_Twitter_Feed::is_valid($c_data->soc_twitter))
			{
				$company_profile->soc_twitter = $c_data->soc_twitter;
				$company_profile->soc_twitter_is_feed_valid = 1;	
			}
			else
				$company_profile->soc_twitter = $c_data->soc_twitter;
		}

		if (!empty($c_data->soc_gplus))
		{
			if ($c_data->soc_gplus_feed_status == Model_CB_Company_Data::SOCIAL_VALID ||
				Social_GPlus_Feeds::is_valid($c_data->soc_gplus))
			{
				$company_profile->soc_gplus = $c_data->soc_gplus;
				$company_profile->soc_gplus_is_feed_valid = 1;
			}
			else
				$company_profile->soc_gplus = $c_data->soc_gplus;
		}

		if (!empty($c_data->soc_youtube))
		{
			if ($c_data->soc_youtube_feed_status == Model_CB_Company_Data::SOCIAL_VALID ||
				Social_Youtube_Feed::is_valid($c_data->soc_youtube))
			{
				$company_profile->soc_youtube = $c_data->soc_youtube;
				$company_profile->soc_youtube_is_feed_valid = 1;
			}
			else
				$company_profile->soc_youtube = $c_data->soc_youtube;

		}

		if (!empty($c_data->soc_pinterest))
		{
			if ($c_data->soc_pinterest_feed_status == Model_CB_Company_Data::SOCIAL_VALID ||
				Social_Pinterest_Feed::is_valid($c_data->soc_pinterest))
			{
				$company_profile->soc_pinterest = $c_data->soc_pinterest;
				$company_profile->soc_pinterest_is_feed_valid = 1;
			}
			else
				$company_profile->soc_pinterest = $c_data->soc_pinterest;
		}
		
		$company_profile->soc_linkedin = $c_data->soc_linkedin;
		$company_profile->soc_linkedin_is_feed_valid = 1;

		$company_profile->soc_rss = value_or_null($c_data->blog_rss);

		$company_profile->save();

		//$comp->password = $pass;
		$comp->company_id = $newsroom->company_id;
		$comp->save();

		// making the newsroom active
		$comp = Model_Company::find($newsroom->company_id);
		$comp->newsroom_is_active = 1;
		$comp->save();
		
		$token = new Model_Newsroom_Claim_Token();
		$token->company_id = $newsroom->company_id;
		$token->generate();
		$token->save();

	}
	
}

?>
