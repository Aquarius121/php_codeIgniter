<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class PRWeb_Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$prweb_data = $this->iella_in->prweb_data;

		$prweb_company_ids = array();

		foreach ($prweb_data as $prweb_company)
		{
			if (!$prweb_comp = Model_PRWeb_Company::find($prweb_company->m_prweb_company->id))
				$prweb_comp = new Model_PRWeb_Company();

			$prweb_comp->id = $prweb_company->m_prweb_company->id;
			$prweb_comp->name = $prweb_company->m_prweb_company->name;
			$prweb_comp->date_fetched = $prweb_company->m_prweb_company->date_fetched;
			$prweb_comp->prweb_category_id = $prweb_company->m_prweb_company->prweb_category_id;
			$prweb_comp->is_name_valid = $prweb_company->m_prweb_company->is_name_valid;
			$prweb_comp->date_last_pr_submitted = $prweb_comp->m_prweb_company->date_last_pr_submitted;
			$prweb_comp->save();
					
			$prweb_company_ids[] = $prweb_comp->id;
			
			if (!$prweb_comp_data = Model_PRWeb_Company_Data::find($prweb_company->prweb_company_id))
				$prweb_comp_data = new Model_PRWeb_Company_Data();

			$prweb_comp_data->prweb_company_id = $prweb_company->prweb_company_id;
			$prweb_comp_data->contact_name = $prweb_company->contact_name;
			$prweb_comp_data->email = $prweb_company->email;
			$prweb_comp_data->email_original = @$prweb_company->email_original;

			$prweb_comp_data->contact_page_url = $prweb_company->contact_page_url;
			
			$prweb_comp_data->cover_image_url = $prweb_company->cover_image_url;
			$prweb_comp_data->is_cover_image_fetched = $prweb_company->is_cover_image_fetched;
			$prweb_comp_data->prweb_website_url = $prweb_company->prweb_website_url;
			$prweb_comp_data->website = $prweb_company->website;
			$prweb_comp_data->num_website_fetch_tries = $prweb_company->num_website_fetch_tries;
			$prweb_comp_data->video = $prweb_company->video;
			$prweb_comp_data->short_description = $prweb_company->short_description;
			$prweb_comp_data->about_company = $prweb_company->about_company;
			$prweb_comp_data->logo_image_path = $prweb_company->logo_image_path;
			$prweb_comp_data->address = $prweb_company->address;
			$prweb_comp_data->city = $prweb_company->city;
			$prweb_comp_data->state = $prweb_company->state;
			$prweb_comp_data->zip = $prweb_company->zip;
			$prweb_comp_data->country_id = $prweb_company->country_id;
			$prweb_comp_data->phone = $prweb_company->phone;
			$prweb_comp_data->soc_fb = $prweb_company->soc_fb;
			$prweb_comp_data->soc_fb_feed_status = $prweb_company->soc_fb_feed_status;
			$prweb_comp_data->soc_twitter = $prweb_company->soc_twitter;
			$prweb_comp_data->soc_twitter_feed_status = $prweb_company->soc_twitter_feed_status;
			$prweb_comp_data->soc_gplus = $prweb_company->soc_gplus;
			$prweb_comp_data->soc_gplus_feed_status = $prweb_company->soc_gplus_feed_status;
			$prweb_comp_data->soc_linkedin = $prweb_company->soc_linkedin;
			$prweb_comp_data->soc_youtube = $prweb_company->soc_youtube;
			$prweb_comp_data->soc_youtube_feed_status = $prweb_company->soc_youtube_feed_status;
			$prweb_comp_data->soc_pinterest = $prweb_company->soc_pinterest;
			$prweb_comp_data->soc_pinterest_feed_status = $prweb_company->soc_pinterest_feed_status;
			$prweb_comp_data->blog_url = $prweb_company->blog_url;
			$prweb_comp_data->blog_rss = $prweb_company->blog_rss;
			$prweb_comp_data->is_website_read = $prweb_company->is_website_read;
			
			$prweb_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->prweb_company_ids = $prweb_company_ids;
		$this->send();
	}


	public function save_domainiq_emails()
	{
		$results = $this->iella_in->results;

		$prweb_company_ids = array();

		foreach ($results as $result)
		{
			if (!$prweb_comp = Model_PRWeb_Company::find($result->prweb_company_id))
			{
				$prweb_company_ids[] = $result->prweb_company_id;
				continue;
			}

			// the company has a newsroom
			if ($prweb_comp->company_id)
			{
				$prweb_company_ids[] = $result->prweb_company_id;
				continue;
			}

			if (!$c_data = Model_PRWeb_Company_Data::find($result->prweb_company_id))
			{
				$prweb_company_ids[] = $result->prweb_company_id;
				continue;
			}

			// there is already an email
			if (!empty($c_data->email))
			{
				$prweb_company_ids[] = $result->prweb_company_id;
				continue;
			}

			$c_data->email = $result->email;
			$c_data->save();
			$prweb_company_ids[] = $result->prweb_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->prweb_company_ids = $prweb_company_ids;
		$this->send();
	}


	public function save_contact_page_url()
	{
		$results = $this->iella_in->results;

		$prweb_company_ids = array();

		foreach ($results as $result)
		{
			if (!$prweb_comp = Model_PRWeb_Company::find($result->prweb_company_id))
			{
				$prweb_company_ids[] = $result->prweb_company_id;
				continue;
			}

			if (!$c_data = Model_PRWeb_Company_Data::find($result->prweb_company_id))
			{
				$prweb_company_ids[] = $result->prweb_company_id;
				continue;
			}

			$c_data->contact_page_url = $result->contact_page_url;
			$c_data->save();
			$prweb_company_ids[] = $result->prweb_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->prweb_company_ids = $prweb_company_ids;
		$this->send();
	}

	public function update_missing_phone_nums()
	{
		$prweb_c_data_recs = $this->iella_in->prweb_c_data_recs;

		$prweb_company_ids = array();

		$sql = "SELECT cp.* 
				FROM ac_nr_prweb_company pc
				INNER JOIN nr_company_profile cp
				ON pc.company_id = cp.company_id
				WHERE pc.id = ?";

		foreach ($prweb_c_data_recs as $prweb_c_data_rec)
		{
			$prweb_company_ids[] = $prweb_c_data_rec->prweb_company_id;

			if ($prweb_c_data = Model_PRWeb_Company_Data::find($prweb_c_data_rec->prweb_company_id))
			{
				$prweb_c_data->phone = value_or_null($prweb_c_data_rec->phone);
				$prweb_c_data->save();
				

				// Checking if we need to update
				// company profiles - if the NR
				// has been created
				$c_profile = Model_Company_Profile::from_sql($sql, array($prweb_c_data_rec->prweb_company_id));
				if (!$c_profile)
					continue;

				$c_profile->phone = $prweb_c_data_rec->phone;
				$c_profile->save();
			}
		}

		$this->iella_out->success = true;
		$this->iella_out->prweb_company_ids = $prweb_company_ids;
		$this->send();
	}
}

?>

