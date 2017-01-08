<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class MarketWired_Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$marketwired_data = $this->iella_in->marketwired_data;

		$marketwired_company_ids = array();

		foreach ($marketwired_data as $marketwired_company)
		{
			if (!$marketwired_comp = Model_MarketWired_Company::find($marketwired_company->m_marketwired_company->id))
				$marketwired_comp = new Model_MarketWired_Company();

			$marketwired_comp->id = $marketwired_company->m_marketwired_company->id;
			$marketwired_comp->name = $marketwired_company->m_marketwired_company->name;
			$marketwired_comp->date_fetched = $marketwired_company->m_marketwired_company->date_fetched;
			$marketwired_comp->marketwired_category_id = $marketwired_company->m_marketwired_company->marketwired_category_id;
			$marketwired_comp->save();
					
			$marketwired_company_ids[] = $marketwired_comp->id;
			
			if (!$marketwired_comp_data = Model_MarketWired_Company_Data::find($marketwired_company->marketwired_company_id))
				$marketwired_comp_data = new Model_MarketWired_Company_Data();

			$marketwired_comp_data->marketwired_company_id = $marketwired_company->marketwired_company_id;
			$marketwired_comp_data->contact_name = $marketwired_company->contact_name;
			$marketwired_comp_data->email = $marketwired_company->email;
			$marketwired_comp_data->email_original = @$marketwired_company->email_original;
			$marketwired_comp_data->cover_image_url = $marketwired_company->cover_image_url;
			$marketwired_comp_data->is_cover_image_fetched = $marketwired_company->is_cover_image_fetched;
			$marketwired_comp_data->marketwired_website_url = $marketwired_company->marketwired_website_url;
			$marketwired_comp_data->website = $marketwired_company->website;
			$marketwired_comp_data->num_website_fetch_tries = $marketwired_company->num_website_fetch_tries;
			$marketwired_comp_data->contact_page_url = $marketwired_company->contact_page_url;
			$marketwired_comp_data->video = $marketwired_company->video;
			$marketwired_comp_data->short_description = $marketwired_company->short_description;
			$marketwired_comp_data->about_company = $marketwired_company->about_company;
			$marketwired_comp_data->logo_image_path = $marketwired_company->logo_image_path;
			$marketwired_comp_data->contact_info = value_or_null($marketwired_company->contact_info);
			$marketwired_comp_data->is_logo_valid = $marketwired_company->is_logo_valid;
			$marketwired_comp_data->address = $marketwired_company->address;
			$marketwired_comp_data->city = $marketwired_company->city;
			$marketwired_comp_data->state = $marketwired_company->state;
			$marketwired_comp_data->zip = $marketwired_company->zip;
			$marketwired_comp_data->country_id = $marketwired_company->country_id;
			$marketwired_comp_data->phone = $marketwired_company->phone;
			$marketwired_comp_data->soc_fb = $marketwired_company->soc_fb;
			$marketwired_comp_data->soc_fb_feed_status = $marketwired_company->soc_fb_feed_status;
			$marketwired_comp_data->soc_twitter = $marketwired_company->soc_twitter;
			$marketwired_comp_data->soc_twitter_feed_status = $marketwired_company->soc_twitter_feed_status;
			$marketwired_comp_data->soc_gplus = $marketwired_company->soc_gplus;
			$marketwired_comp_data->soc_gplus_feed_status = $marketwired_company->soc_gplus_feed_status;
			$marketwired_comp_data->soc_linkedin = $marketwired_company->soc_linkedin;
			$marketwired_comp_data->soc_youtube = $marketwired_company->soc_youtube;
			$marketwired_comp_data->soc_youtube_feed_status = $marketwired_company->soc_youtube_feed_status;
			$marketwired_comp_data->soc_pinterest = $marketwired_company->soc_pinterest;
			$marketwired_comp_data->soc_pinterest_feed_status = $marketwired_company->soc_pinterest_feed_status;
			$marketwired_comp_data->blog_url = $marketwired_company->blog_url;
			$marketwired_comp_data->blog_rss = $marketwired_company->blog_rss;
			$marketwired_comp_data->is_website_read = $marketwired_company->is_website_read;
			
			$marketwired_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->marketwired_company_ids = $marketwired_company_ids;
		$this->send();						
	}

	public function save_domainiq_emails()
	{
		$results = $this->iella_in->results;

		$marketwired_company_ids = array();

		foreach ($results as $result)
		{
			if (!$marketwired_comp = Model_MarketWired_Company::find($result->marketwired_company_id))
			{
				$marketwired_company_ids[] = $result->marketwired_company_id;
				continue;
			}

			// the company has a newsroom
			if ($marketwired_comp->company_id)
			{
				$marketwired_company_ids[] = $result->marketwired_company_id;
				continue;
			}

			if (!$c_data = Model_MarketWired_Company_Data::find($result->marketwired_company_id))
			{
				$marketwired_company_ids[] = $result->marketwired_company_id;
				continue;
			}

			// there is already an email
			if (!empty($c_data->email))
			{
				$marketwired_company_ids[] = $result->marketwired_company_id;
				continue;
			}

			$c_data->email = $result->email;
			$c_data->save();
			$marketwired_company_ids[] = $result->marketwired_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->marketwired_company_ids = $marketwired_company_ids;
		$this->send();
	}

	public function save_contact_page_url()
	{
		$results = $this->iella_in->results;

		$marketwired_company_ids = array();

		foreach ($results as $result)
		{
			if (!$marketwired_comp = Model_MarketWired_Company::find($result->marketwired_company_id))
			{
				$marketwired_company_ids[] = $result->marketwired_company_id;
				continue;
			}

			if (!$c_data = Model_MarketWired_Company_Data::find($result->marketwired_company_id))
			{
				$marketwired_company_ids[] = $result->marketwired_company_id;
				continue;
			}

			$c_data->contact_page_url = $result->contact_page_url;
			$c_data->save();
			$marketwired_company_ids[] = $result->marketwired_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->marketwired_company_ids = $marketwired_company_ids;
		$this->send();
	}

	public function update_missing_phone_nums()
	{
		$marketwired_c_data_recs = $this->iella_in->marketwired_c_data_recs;

		$marketwired_company_ids = array();

		$sql = "SELECT cp.* 
				FROM ac_nr_marketwired_company pc
				INNER JOIN nr_company_profile cp
				ON pc.company_id = cp.company_id
				WHERE pc.id = ?
				AND (cp.phone IS NULL
				OR cp.phone = '')";

		foreach ($marketwired_c_data_recs as $marketwired_c_data_rec)
		{
			$marketwired_company_ids[] = $marketwired_c_data_rec->marketwired_company_id;

			if ($marketwired_c_data = Model_MarketWired_Company_Data::find($marketwired_c_data_rec->marketwired_company_id))
			{
				$marketwired_c_data->phone = value_or_null($marketwired_c_data_rec->phone);
				$marketwired_c_data->save();
				

				// Checking if we need to update
				// company profiles - if the NR
				// has been created
				$c_profile = Model_Company_Profile::from_sql($sql, array($marketwired_c_data_rec->marketwired_company_id));
				if (!$c_profile)
					continue;

				$c_profile->phone = $marketwired_c_data_rec->phone;
				$c_profile->save();
			}
		}

		$this->iella_out->success = true;
		$this->iella_out->marketwired_company_ids = $marketwired_company_ids;
		$this->send();
	}

	public function update_contact_info()
	{
		$marketwired_c_data_recs = $this->iella_in->marketwired_c_data_recs;

		$marketwired_company_ids = array();
		foreach ($marketwired_c_data_recs as $marketwired_c_data_rec)
		{
			$marketwired_company_ids[] = $marketwired_c_data_rec->marketwired_company_id;

			if ($marketwired_c_data = Model_MarketWired_Company_Data::find($marketwired_c_data_rec->marketwired_company_id))
			{
				$marketwired_c_data->contact_info = value_or_null($marketwired_c_data_rec->contact_info);
				$marketwired_c_data->save();
			}
		}

		$this->iella_out->success = true;
		$this->iella_out->marketwired_company_ids = $marketwired_company_ids;
		$this->send();
	}
}

?>

