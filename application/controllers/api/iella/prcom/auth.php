<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');

class Auth_Controller extends PRCom_API_Base {
	
	public function create_user()
	{
		$virtual_user = $this->iella_in->virtual_user;
		$user = Model_User::create();
		$user->values($virtual_user);
		$user->email = Virtual_User::create_virtual_email();
		$user->virtual_source_email = $virtual_user->email;
		$user->virtual_source_id = Model_Virtual_Source::ID_PRESSRELEASECOM;
		$raw_data = new stdClass();
		$raw_data->virtual_user = $virtual_user;
		$raw_data->virtual_user->virtual_source_id = 
			Model_Virtual_Source::ID_PRESSRELEASECOM;
		$user->raw_data($raw_data);
		$user->is_enabled = 1;
		$user->is_verified = 1;
		$user->save();

		if (isset($this->iella_in->virtual_company))
		{
			$virtual_company = $this->iella_in->virtual_company;
			$newsroom = Model_Newsroom::create($user, $virtual_company->name);
			$newsroom->name = Newsroom_Assist::random_name();
			$newsroom->save();
			
			$company = Model_Company::find($newsroom->company_id);
			$this->iella_out->newswire_company = $company;
		}

		$this->iella_out->newswire_user = $user;
	}

	public function update_user()
	{
		$virtual_user = Raw_Data_Model::from_object($this->iella_in->virtual_user);
		$user = Model_User::find($this->iella_in->newswire_user->id);
		$raw_data = $user->raw_data();
		$raw_data->virtual_user = $virtual_user;		
		$raw_data->virtual_user->virtual_source_id = 
			Model_Virtual_Source::ID_PRESSRELEASECOM;
		$user->raw_data($raw_data);		
		$user->virtual_source_email = $virtual_user->email;
		$user->virtual_source_id = Model_Virtual_Source::ID_PRESSRELEASECOM;
		$user->first_name = $virtual_user->first_name;
		$user->last_name = $virtual_user->last_name;
		$user->save();

		if (isset($this->iella_in->virtual_company))
		{
			$virtual_company = Raw_Data_Model::from_object($this->iella_in->virtual_company);
			$company = Model_Company::find($this->iella_in->newswire_company->id);
			$newsroom = $company->newsroom();
			$company_profile = $newsroom->profile();
			$raw_data = $virtual_company->raw_data();

			$company->name = $virtual_company->name;
			$company->save();

			if (!$company_profile)
			{
				$company_profile = new Model_Company_Profile();
				$company_profile->company_id = $company->id;

				// these should have been default to 0 automatically!
				$company_profile->is_enable_social_wire = 0;
				$company_profile->is_enable_blog_posts = 0;
				$company_profile->is_enable_social_widget = 0;
			}
			
			$company_profile->soc_twitter = $raw_data->soc_twitter;
			$company_profile->soc_facebook = $raw_data->soc_facebook;
			$company_profile->soc_linkedin = $raw_data->soc_linkedin;
			$company_profile->soc_youtube = $raw_data->soc_youtube;
			$company_profile->soc_pinterest = $raw_data->soc_pinterest;
			$company_profile->soc_gplus = $raw_data->soc_gplus;
			$company_profile->clean_soc();

			$company_profile->email = $raw_data->email;
			$company_profile->summary = $raw_data->summary;
			$company_profile->website = $raw_data->website;
			$company_profile->save();

			if (isset($this->iella_files['company_logo']))
			{
				$si_logo = Stored_Image::from_file($this->iella_files['company_logo']);

				if ($si_logo->exists() && $si_logo->is_valid_image())
				{
					if (!($nr_custom = $newsroom->custom()))
					{
						$nr_custom = new Model_Newsroom_Custom();
						$nr_custom->company_id = $company->id;
					}

					$image = Quick_Image::import('logo', $si_logo->actual_filename());
					$nr_custom->logo_image_id = $image->id;
					$nr_custom->save();
				}				
			}
		}
	}
	
}

?>