<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait Company_Trait {

	public function find_company($company_id)
	{
		$newsroom = Model_Newsroom::find_company_id($company_id);
		if (!$newsroom) return null;
		if ($newsroom->user_id != Auth::user()->id)
			return null;		
		
		$result = array();
		$result['company_id'] = $newsroom->company_id;
		$result['company_name'] = $newsroom->company_name;
		$result['company_is_active'] = $newsroom->is_active;
		$result['company_is_archived'] = $newsroom->is_archived;
		$result['company_date_created'] = $newsroom->date_created;
		
		$company_profile = Model_Company_Profile::find($newsroom->company_id);
		$result['company_email'] = $company_profile->email;
		$result['company_website'] = $company_profile->website;
		$result['company_address_street'] = $company_profile->address_street;
		$result['company_address_apt_suite'] = $company_profile->address_apt_suite;
		$result['company_address_city'] = $company_profile->address_city;
		$result['company_address_state'] = $company_profile->address_state;
		$result['company_address_state'] = $company_profile->address_state;
		$result['company_address_zip'] = $company_profile->address_zip;
		$result['company_phone'] = $company_profile->phone;
		$result['company_address_country_id'] = $company_profile->address_country_id;
		$result['company_address_country_name'] = @Model_Country::find($company_profile->address_country_id)->name;
		$result['company_summary'] = $company_profile->summary;
		$result['company_description'] = $company_profile->description;
		$result['company_twitter'] = $company_profile->soc_twitter;
		$result['company_facebook'] = $company_profile->soc_facebook;
		$result['company_gplus'] = $company_profile->soc_gplus;
		$result['company_youtube'] = $company_profile->soc_youtube;
						
		$nr_custom = Model_Newsroom_Custom::find($newsroom->company_id);
		
		if (isset($nr_custom->logo_image_id))
		{
			$lo_im = Model_Image::find($nr_custom->logo_image_id);
			$lo_variant = $lo_im->variant('original');
			$lo_url = Stored_Image::url_from_filename($lo_variant->filename);
			$result['company_logo'] = $this->common()->url($lo_url);
		}
		
		$c_contact = Model_Company_Contact::find($newsroom->company_contact_id);
		
		if ($c_contact)
		{
			$result['company_contact_name'] = $c_contact->name;
			$result['company_contact_title'] = $c_contact->title;
			$result['company_contact_email'] = $c_contact->email;
			$result['company_contact_description'] = $c_contact->description;
			$result['company_contact_website'] = $c_contact->website;
			$result['company_contact_skype'] = $c_contact->skype;
			$result['company_contact_phone'] = $c_contact->phone;
			$result['company_contact_linkedin'] = $c_contact->linkedin;
			$result['company_contact_facebook'] = $c_contact->facebook;
			$result['company_contact_twitter'] = $c_contact->twitter;
		}
		
		return $result;
    }
	
	public function add_company_validation()
	{
		$required_fields = array(
			'company_name',
			'company_website',
			'company_email',
			'company_summary',
			'company_contact_email',
			'company_address_country_id',
		);
		
		foreach ($required_fields as $field)
		{
			if (!@$this->iella_in->{$field})
			{
				$this->iella_out->success = false;
				$this->iella_out->errors[] = "<{$field}> field is required";
			}
		}
		
		if (str_word_count(@$this->iella_in->company_summary) < 10)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = '<company_summary> must have at least 10 words';
		}
		
		return $this->iella_out->success;
	}
	
	public function add_company()
	{
		$reseller_id = Auth::user()->id;
				
		$name = @$this->iella_in->company_name;
		$website = @$this->iella_in->company_website;
		$address_country_id = @$this->iella_in->company_address_country_id;
		$summary = @$this->iella_in->company_summary;
		$email = @$this->iella_in->company_email;
		$contact_email = @$this->iella_in->company_contact_email;
		
		$address_street = value_or_null(@$this->iella_in->company_address_street);
		$address_apt_suite = value_or_null(@$this->iella_in->company_address_apt_suite);
		$address_city = value_or_null(@$this->iella_in->company_address_city);
		$address_state = value_or_null(@$this->iella_in->company_address_state);
		$address_zip = value_or_null(@$this->iella_in->company_address_zip);
		$phone = value_or_null(@$this->iella_in->company_phone);

		$logo = @$this->iella_in->company_logo;
		$description = $this->vd->pure(@$this->iella_in->company_description);
		$twitter = value_or_null(@$this->iella_in->company_twitter);
		$facebook = value_or_null(@$this->iella_in->company_facebook);
		$gplus = value_or_null(@$this->iella_in->company_gplus);
		$youtube = value_or_null(@$this->iella_in->company_youtube);
		
		$contact_name = value_or_null(@$this->iella_in->company_contact_name);
		$contact_title = value_or_null(@$this->iella_in->company_contact_title);
		$contact_description = $this->vd->pure(@$this->iella_in->company_contact_description);
		$contact_website = value_or_null(@$this->iella_in->company_contact_website);
		$contact_skype = value_or_null(@$this->iella_in->company_contact_skype);
		$contact_phone = value_or_null(@$this->iella_in->company_contact_phone);
		$contact_linkedin = value_or_null(@$this->iella_in->company_contact_linkedin);
		$contact_facebook = value_or_null(@$this->iella_in->company_contact_facebook);
		$contact_twitter = value_or_null(@$this->iella_in->company_contact_twitter);
		
		$gplus = Social_GPlus_Profile::parse_id($gplus);
		$facebook = Social_Facebook_Profile::parse_id($facebook);
		$youtube = Social_Youtube_Profile::parse_id($youtube);
		$twitter = Social_Twitter_Profile::parse_id($twitter);
		
		$contact_linkedin = Social_Linkedin_Profile::parse_id($contact_linkedin);
		$contact_facebook = Social_Facebook_Profile::parse_id($contact_facebook);
		$contact_twitter = Social_Twitter_Profile::parse_id($contact_twitter);
		
		$newsroom = Model_Newsroom::create($reseller_id, $name);
		$newsroom->is_archived = (int) ((bool) @$this->iella_in->company_is_archived);
	
		if ($logo)
		{
			$logo_file = File_Util::buffer_file();
			
			if (@copy($logo, $logo_file))
			{			
				$raw_image = Image::from_file($logo_file);
				
				if ($raw_image->is_valid())
				{
					$raw_image->save($logo_file);
					
					$logo_im = LEGACY_Image::import('logo', $logo_file);
					$logo_im->company_id = $newsroom->company_id;
					$logo_im->save();
					
					if (!($newsroom_custom = Model_Newsroom_Custom::find($newsroom->company_id)))
					{
						// Create newsroom customization object
						// And assign to the new company
						$newsroom_custom = new Model_Newsroom_Custom();
						$newsroom_custom->company_id = $newsroom->company_id;
					}
					 
					// Set it to use the new logo image and save
					$newsroom_custom->logo_image_id = $logo_im->id;
					$newsroom_custom->save();
				}
				else
				{	
					$this->iella_out->warnings[] = '<company_logo> is not a valid image';
				}
			}
			else
			{
				$this->iella_out->warnings[] = 'failed to download the <company_logo>';
			}			
		}
				
		$company_profile = new Model_Company_Profile();			
		$company_profile->company_id = $newsroom->company_id;
		$company_profile->address_street = $address_street;
		$company_profile->address_apt_suite = $address_apt_suite;
		$company_profile->address_city = $address_city;
		$company_profile->email = $email;		
		$company_profile->address_state = $address_state;
		$company_profile->address_zip = $address_zip;
		$company_profile->website = $website;
		$company_profile->phone = $phone;
		$company_profile->summary = $summary;
		$company_profile->description = $description;
		$company_profile->soc_twitter = $twitter;
		$company_profile->soc_facebook = $facebook;
		$company_profile->soc_gplus = $gplus;
		$company_profile->soc_youtube = $youtube;		
		$company_profile->address_country_id = $address_country_id;
		$company_profile->save();		
		
		$company_contact = new Model_Company_Contact();
		$company_contact->company_id = $newsroom->company_id;		 
		$company_contact->name = $contact_name;
		$company_contact->title = $contact_title;
		$company_contact->email = $contact_email;		
		$company_contact->description = $contact_description;
		$company_contact->website = $contact_website;
		$company_contact->skype = $contact_skype;
		$company_contact->phone = $contact_phone;
		$company_contact->linkedin = $contact_linkedin;
		$company_contact->facebook = $contact_facebook;
		$company_contact->twitter = $contact_twitter;
		$company_contact->save();
		
		$newsroom->company_contact_id = $company_contact->id;
		$newsroom->save();
		
		$this->iella_out->id = $newsroom->company_id;
		return $newsroom;
	}
}

?>
