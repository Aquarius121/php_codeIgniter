<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Owler_Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$owler_data = $this->iella_in->owler_data;

		$owler_company_ids = array();

		foreach ($owler_data as $owler_company)
		{
			if (!$owler_comp = Model_Owler_Company::find($owler_company->m_owler_company->id))
				$owler_comp = new Model_Owler_Company();

			$owler_comp->id = $owler_company->m_owler_company->id;
			$owler_comp->name = $owler_company->m_owler_company->name;
			$owler_comp->date_fetched = $owler_company->m_owler_company->date_fetched;
			$owler_comp->owler_category_id = $owler_company->m_owler_company->owler_category_id;
			$owler_comp->save();
					
			$owler_company_ids[] = $owler_comp->id;
			
			if (!$owler_comp_data = Model_Owler_Company_Data::find($owler_company->owler_company_id))
				$owler_comp_data = new Model_Owler_Company_Data();

			$owler_comp_data->owler_company_id = $owler_company->owler_company_id;
			$owler_comp_data->contact_name = $owler_company->contact_name;
			$owler_comp_data->email = $owler_company->email;
			$owler_comp_data->email_original = @$owler_company->email_original;
			$owler_comp_data->cover_image_url = $owler_company->cover_image_url;
			$owler_comp_data->is_cover_image_fetched = $owler_company->is_cover_image_fetched;
			$owler_comp_data->owler_website_url = $owler_company->owler_website_url;
			$owler_comp_data->website = $owler_company->website;
			$owler_comp_data->num_website_fetch_tries = $owler_company->num_website_fetch_tries;
			$owler_comp_data->contact_page_url = $owler_company->contact_page_url;
			$owler_comp_data->video = $owler_company->video;
			$owler_comp_data->short_description = $owler_company->short_description;
			$owler_comp_data->about_company = $owler_company->about_company;
			$owler_comp_data->logo_image_path = $owler_company->logo_image_path;
			$owler_comp_data->is_logo_valid = $owler_company->is_logo_valid;
			$owler_comp_data->address = $owler_company->address;
			$owler_comp_data->city = $owler_company->city;
			$owler_comp_data->state = $owler_company->state;
			$owler_comp_data->zip = $owler_company->zip;
			$owler_comp_data->country_id = $owler_company->country_id;
			$owler_comp_data->phone = $owler_company->phone;
			$owler_comp_data->soc_fb = $owler_company->soc_fb;
			$owler_comp_data->soc_fb_feed_status = $owler_company->soc_fb_feed_status;
			$owler_comp_data->soc_twitter = $owler_company->soc_twitter;
			$owler_comp_data->soc_twitter_feed_status = $owler_company->soc_twitter_feed_status;
			$owler_comp_data->soc_gplus = $owler_company->soc_gplus;
			$owler_comp_data->soc_gplus_feed_status = $owler_company->soc_gplus_feed_status;
			$owler_comp_data->soc_linkedin = $owler_company->soc_linkedin;
			$owler_comp_data->soc_youtube = $owler_company->soc_youtube;
			$owler_comp_data->soc_youtube_feed_status = $owler_company->soc_youtube_feed_status;
			$owler_comp_data->soc_pinterest = $owler_company->soc_pinterest;
			$owler_comp_data->soc_pinterest_feed_status = $owler_company->soc_pinterest_feed_status;
			$owler_comp_data->blog_url = $owler_company->blog_url;
			$owler_comp_data->blog_rss = $owler_company->blog_rss;
			$owler_comp_data->is_website_read = $owler_company->is_website_read;
			
			$owler_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->owler_company_ids = $owler_company_ids;
		$this->send();						
	}

	public function save_domainiq_emails()
	{
		$results = $this->iella_in->results;

		$owler_company_ids = array();

		foreach ($results as $result)
		{
			if (!$owler_comp = Model_Owler_Company::find($result->owler_company_id))
			{
				$owler_company_ids[] = $result->owler_company_id;
				continue;
			}

			// the company has a newsroom
			if ($owler_comp->company_id)
			{
				$owler_company_ids[] = $result->owler_company_id;
				continue;
			}

			if (!$c_data = Model_Owler_Company_Data::find($result->owler_company_id))
			{
				$owler_company_ids[] = $result->owler_company_id;
				continue;
			}

			// there is already an email
			if (!empty($c_data->email))
			{
				$owler_company_ids[] = $result->owler_company_id;
				continue;
			}

			$c_data->email = $result->email;
			$c_data->save();
			$owler_company_ids[] = $result->owler_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->owler_company_ids = $owler_company_ids;
		$this->send();
	}

	public function save_contact_page_url()
	{
		$results = $this->iella_in->results;

		$owler_company_ids = array();

		foreach ($results as $result)
		{
			if (!$owler_comp = Model_Owler_Company::find($result->owler_company_id))
			{
				$owler_company_ids[] = $result->owler_company_id;
				continue;
			}

			if (!$c_data = Model_Owler_Company_Data::find($result->owler_company_id))
			{
				$owler_company_ids[] = $result->owler_company_id;
				continue;
			}

			$c_data->contact_page_url = $result->contact_page_url;
			$c_data->save();
			$owler_company_ids[] = $result->owler_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->owler_company_ids = $owler_company_ids;
		$this->send();
	}
}

?>

