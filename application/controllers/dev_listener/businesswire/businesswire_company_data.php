<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class BusinessWire_Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$businesswire_data = $this->iella_in->businesswire_data;

		$businesswire_company_ids = array();

		foreach ($businesswire_data as $businesswire_company)
		{
			if (!$businesswire_comp = Model_BusinessWire_Company::find($businesswire_company->m_businesswire_company->id))
				$businesswire_comp = new Model_BusinessWire_Company();

			$businesswire_comp->id = $businesswire_company->m_businesswire_company->id;
			$businesswire_comp->name = $businesswire_company->m_businesswire_company->name;
			$businesswire_comp->date_fetched = $businesswire_company->m_businesswire_company->date_fetched;
			$businesswire_comp->businesswire_category_id = $businesswire_company->m_businesswire_company->businesswire_category_id;
			$businesswire_comp->save();
					
			$businesswire_company_ids[] = $businesswire_comp->id;
			
			if (!$businesswire_comp_data = Model_BusinessWire_Company_Data::find($businesswire_company->businesswire_company_id))
				$businesswire_comp_data = new Model_BusinessWire_Company_Data();

			$businesswire_comp_data->businesswire_company_id = $businesswire_company->businesswire_company_id;
			$businesswire_comp_data->contact_name = $businesswire_company->contact_name;
			$businesswire_comp_data->email = $businesswire_company->email;
			$businesswire_comp_data->email_original = @$businesswire_company->email_original;
			$businesswire_comp_data->cover_image_url = $businesswire_company->cover_image_url;
			$businesswire_comp_data->is_cover_image_fetched = $businesswire_company->is_cover_image_fetched;
			$businesswire_comp_data->businesswire_website_url = $businesswire_company->businesswire_website_url;
			$businesswire_comp_data->website = $businesswire_company->website;
			$businesswire_comp_data->num_website_fetch_tries = $businesswire_company->num_website_fetch_tries;
			$businesswire_comp_data->contact_page_url = $businesswire_company->contact_page_url;
			$businesswire_comp_data->video = $businesswire_company->video;
			$businesswire_comp_data->short_description = $businesswire_company->short_description;
			$businesswire_comp_data->about_company = $businesswire_company->about_company;
			$businesswire_comp_data->logo_image_path = $businesswire_company->logo_image_path;
			$businesswire_comp_data->is_logo_valid = $businesswire_company->is_logo_valid;
			$businesswire_comp_data->address = $businesswire_company->address;
			$businesswire_comp_data->city = $businesswire_company->city;
			$businesswire_comp_data->state = $businesswire_company->state;
			$businesswire_comp_data->zip = $businesswire_company->zip;
			$businesswire_comp_data->country_id = $businesswire_company->country_id;
			$businesswire_comp_data->phone = $businesswire_company->phone;
			$businesswire_comp_data->soc_fb = $businesswire_company->soc_fb;
			$businesswire_comp_data->soc_fb_feed_status = $businesswire_company->soc_fb_feed_status;
			$businesswire_comp_data->soc_twitter = $businesswire_company->soc_twitter;
			$businesswire_comp_data->soc_twitter_feed_status = $businesswire_company->soc_twitter_feed_status;
			$businesswire_comp_data->soc_gplus = $businesswire_company->soc_gplus;
			$businesswire_comp_data->soc_gplus_feed_status = $businesswire_company->soc_gplus_feed_status;
			$businesswire_comp_data->soc_linkedin = $businesswire_company->soc_linkedin;
			$businesswire_comp_data->soc_youtube = $businesswire_company->soc_youtube;
			$businesswire_comp_data->soc_youtube_feed_status = $businesswire_company->soc_youtube_feed_status;
			$businesswire_comp_data->soc_pinterest = $businesswire_company->soc_pinterest;
			$businesswire_comp_data->soc_pinterest_feed_status = $businesswire_company->soc_pinterest_feed_status;
			$businesswire_comp_data->blog_url = $businesswire_company->blog_url;
			$businesswire_comp_data->blog_rss = $businesswire_company->blog_rss;
			$businesswire_comp_data->is_website_read = $businesswire_company->is_website_read;
			
			$businesswire_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->businesswire_company_ids = $businesswire_company_ids;
		$this->send();						
	}

	public function save_domainiq_emails()
	{
		$results = $this->iella_in->results;

		$businesswire_company_ids = array();

		foreach ($results as $result)
		{
			if (!$businesswire_comp = Model_BusinessWire_Company::find($result->businesswire_company_id))
			{
				$businesswire_company_ids[] = $result->businesswire_company_id;
				continue;
			}

			// the company has a newsroom
			if ($businesswire_comp->company_id)
			{
				$businesswire_company_ids[] = $result->businesswire_company_id;
				continue;
			}

			if (!$c_data = Model_BusinessWire_Company_Data::find($result->businesswire_company_id))
			{
				$businesswire_company_ids[] = $result->businesswire_company_id;
				continue;
			}

			// there is already an email
			if (!empty($c_data->email))
			{
				$businesswire_company_ids[] = $result->businesswire_company_id;
				continue;
			}

			$c_data->email = $result->email;
			$c_data->save();
			$businesswire_company_ids[] = $result->businesswire_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->businesswire_company_ids = $businesswire_company_ids;
		$this->send();
	}

	public function save_contact_page_url()
	{
		$results = $this->iella_in->results;

		$businesswire_company_ids = array();

		foreach ($results as $result)
		{
			if (!$businesswire_comp = Model_BusinessWire_Company::find($result->businesswire_company_id))
			{
				$businesswire_company_ids[] = $result->businesswire_company_id;
				continue;
			}

			if (!$c_data = Model_BusinessWire_Company_Data::find($result->businesswire_company_id))
			{
				$businesswire_company_ids[] = $result->businesswire_company_id;
				continue;
			}

			$c_data->contact_page_url = $result->contact_page_url;
			$c_data->save();
			$businesswire_company_ids[] = $result->businesswire_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->businesswire_company_ids = $businesswire_company_ids;
		$this->send();
	}
}

?>

