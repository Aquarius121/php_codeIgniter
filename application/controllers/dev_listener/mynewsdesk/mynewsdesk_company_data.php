<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class MyNewsDesk_Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$mynewsdesk_data = $this->iella_in->mynewsdesk_data;

		$mynewsdesk_company_ids = array();

		foreach ($mynewsdesk_data as $mynewsdesk_company)
		{
			if (!$mynewsdesk_comp = Model_MyNewsDesk_Company::find($mynewsdesk_company->m_mynewsdesk_company->id))
				$mynewsdesk_comp = new Model_MyNewsDesk_Company();

			$mynewsdesk_comp->id = $mynewsdesk_company->m_mynewsdesk_company->id;
			$mynewsdesk_comp->name = $mynewsdesk_company->m_mynewsdesk_company->name;
			$mynewsdesk_comp->date_fetched = $mynewsdesk_company->m_mynewsdesk_company->date_fetched;
			$mynewsdesk_comp->mynewsdesk_category_id = $mynewsdesk_company->m_mynewsdesk_company->mynewsdesk_category_id;
			$mynewsdesk_comp->save();
					
			$mynewsdesk_company_ids[] = $mynewsdesk_comp->id;
			
			if (!$mynewsdesk_comp_data = Model_MyNewsDesk_Company_Data::find($mynewsdesk_company->mynewsdesk_company_id))
				$mynewsdesk_comp_data = new Model_MyNewsDesk_Company_Data();

			$mynewsdesk_comp_data->mynewsdesk_company_id = $mynewsdesk_company->mynewsdesk_company_id;
			$mynewsdesk_comp_data->country = $mynewsdesk_company->country;
			$mynewsdesk_comp_data->contact_name = $mynewsdesk_company->contact_name;
			$mynewsdesk_comp_data->email = $mynewsdesk_company->email;
			$mynewsdesk_comp_data->is_email_from_pr_text = $mynewsdesk_company->is_email_from_pr_text;
			$mynewsdesk_comp_data->email_original = @$mynewsdesk_company->email_original;
			$mynewsdesk_comp_data->newsroom_url = $mynewsdesk_company->newsroom_url;
			
			$mynewsdesk_comp_data->website = $mynewsdesk_company->website;
			$mynewsdesk_comp_data->website_source = $mynewsdesk_company->website_source;
			$mynewsdesk_comp_data->is_website_valid = $mynewsdesk_company->is_website_valid;
			$mynewsdesk_comp_data->contact_page_url = $mynewsdesk_company->contact_page_url;
			
			$mynewsdesk_comp_data->num_website_fetch_tries = $mynewsdesk_company->num_website_fetch_tries;
			$mynewsdesk_comp_data->video = $mynewsdesk_company->video;
			$mynewsdesk_comp_data->short_description = $mynewsdesk_company->short_description;
			$mynewsdesk_comp_data->about_company = $mynewsdesk_company->about_company;
			$mynewsdesk_comp_data->logo_image_path = $mynewsdesk_company->logo_image_path;
			$mynewsdesk_comp_data->is_logo_valid = $mynewsdesk_company->is_logo_valid;

			$mynewsdesk_comp_data->address = $mynewsdesk_company->address;
			$mynewsdesk_comp_data->city = $mynewsdesk_company->city;
			$mynewsdesk_comp_data->state = $mynewsdesk_company->state;
			$mynewsdesk_comp_data->zip = $mynewsdesk_company->zip;
			$mynewsdesk_comp_data->country_id = $mynewsdesk_company->country_id;
			$mynewsdesk_comp_data->phone = $mynewsdesk_company->phone;
			$mynewsdesk_comp_data->is_phone_from_pr_text = $mynewsdesk_company->is_phone_from_pr_text;
			
			$mynewsdesk_comp_data->soc_fb = $mynewsdesk_company->soc_fb;
			$mynewsdesk_comp_data->soc_fb_feed_status = $mynewsdesk_company->soc_fb_feed_status;
			$mynewsdesk_comp_data->soc_twitter = $mynewsdesk_company->soc_twitter;
			$mynewsdesk_comp_data->soc_twitter_feed_status = $mynewsdesk_company->soc_twitter_feed_status;
			$mynewsdesk_comp_data->soc_gplus = $mynewsdesk_company->soc_gplus;
			$mynewsdesk_comp_data->soc_gplus_feed_status = $mynewsdesk_company->soc_gplus_feed_status;
			$mynewsdesk_comp_data->soc_linkedin = $mynewsdesk_company->soc_linkedin;
			$mynewsdesk_comp_data->soc_youtube = $mynewsdesk_company->soc_youtube;
			$mynewsdesk_comp_data->soc_youtube_feed_status = $mynewsdesk_company->soc_youtube_feed_status;
			$mynewsdesk_comp_data->soc_pinterest = $mynewsdesk_company->soc_pinterest;
			$mynewsdesk_comp_data->soc_pinterest_feed_status = $mynewsdesk_company->soc_pinterest_feed_status;
			$mynewsdesk_comp_data->blog_url = $mynewsdesk_company->blog_url;
			$mynewsdesk_comp_data->blog_rss = $mynewsdesk_company->blog_rss;
			$mynewsdesk_comp_data->is_website_read = $mynewsdesk_company->is_website_read;
			
			$mynewsdesk_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->mynewsdesk_company_ids = $mynewsdesk_company_ids;
		$this->send();						
	}


	public function save_about_blurb()
	{
		$mynewsdesk_data = $this->iella_in->mynewsdesk_data;

		$mynewsdesk_company_ids = array();

		foreach ($mynewsdesk_data as $mynewsdesk_company)
		{
			if ($mynewsdesk_comp_data = Model_MyNewsDesk_Company_Data::find($mynewsdesk_company->mynewsdesk_company_id))
			{
				$mynewsdesk_company_ids[] = $mynewsdesk_company->mynewsdesk_company_id;

				$mynewsdesk_comp_data->about_company = $mynewsdesk_company->about_company;
				$mynewsdesk_comp_data->short_description = $mynewsdesk_company->about_company;
				$mynewsdesk_comp_data->about_company_lang = $mynewsdesk_company->about_company_lang;
				$mynewsdesk_comp_data->save();
			}
		}
		
		$this->iella_out->success = true;
		$this->iella_out->mynewsdesk_company_ids = $mynewsdesk_company_ids;
		$this->send();						
	}

	public function save_domainiq_emails()
	{
		$results = $this->iella_in->results;

		$mynewsdesk_company_ids = array();

		foreach ($results as $result)
		{
			if (!$mynewsdesk_comp = Model_MyNewsDesk_Company::find($result->mynewsdesk_company_id))
			{
				$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
				continue;
			}

			// the company has a newsroom
			if ($mynewsdesk_comp->company_id)
			{
				$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
				continue;
			}

			if (!$c_data = Model_MyNewsDesk_Company_Data::find($result->mynewsdesk_company_id))
			{
				$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
				continue;
			}

			// there is already an email
			if (!empty($c_data->email))
			{
				$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
				continue;
			}

			$c_data->email = $result->email;
			$c_data->save();
			$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->mynewsdesk_company_ids = $mynewsdesk_company_ids;
		$this->send();
	}

	public function save_contact_page_url()
	{
		$results = $this->iella_in->results;

		$mynewsdesk_company_ids = array();

		foreach ($results as $result)
		{
			if (!$mynewsdesk_comp = Model_MyNewsDesk_Company::find($result->mynewsdesk_company_id))
			{
				$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
				continue;
			}

			if (!$c_data = Model_MyNewsDesk_Company_Data::find($result->mynewsdesk_company_id))
			{
				$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
				continue;
			}

			$c_data->contact_page_url = $result->contact_page_url;
			$c_data->save();
			$mynewsdesk_company_ids[] = $result->mynewsdesk_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->mynewsdesk_company_ids = $mynewsdesk_company_ids;
		$this->send();
	}
}

?>

