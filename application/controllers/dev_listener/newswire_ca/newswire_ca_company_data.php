<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Newswire_CA_Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$newswire_ca_data = $this->iella_in->newswire_ca_data;

		$newswire_ca_company_ids = array();

		foreach ($newswire_ca_data as $newswire_ca_company)
		{
			if (!$newswire_ca_comp = Model_Newswire_CA_Company::find($newswire_ca_company->m_newswire_ca_company->id))
				$newswire_ca_comp = new Model_Newswire_CA_Company();

			$newswire_ca_comp->id = $newswire_ca_company->m_newswire_ca_company->id;
			$newswire_ca_comp->name = $newswire_ca_company->m_newswire_ca_company->name;
			$newswire_ca_comp->date_fetched = $newswire_ca_company->m_newswire_ca_company->date_fetched;
			$newswire_ca_comp->newswire_ca_category_id = $newswire_ca_company->m_newswire_ca_company->newswire_ca_category_id;
			$newswire_ca_comp->save();
					
			$newswire_ca_company_ids[] = $newswire_ca_comp->id;
			
			if (!$newswire_ca_comp_data = Model_Newswire_CA_Company_Data::find($newswire_ca_company->newswire_ca_company_id))
				$newswire_ca_comp_data = new Model_Newswire_CA_Company_Data();

			$newswire_ca_comp_data->newswire_ca_company_id = $newswire_ca_company->newswire_ca_company_id;
			$newswire_ca_comp_data->contact_name = $newswire_ca_company->contact_name;
			$newswire_ca_comp_data->email = $newswire_ca_company->email;
			$newswire_ca_comp_data->is_email_from_pr_text = $newswire_ca_company->is_email_from_pr_text;
			$newswire_ca_comp_data->email_original = @$newswire_ca_company->email_original;
			$newswire_ca_comp_data->newswire_ca_org_link = $newswire_ca_company->newswire_ca_org_link;
			
			$newswire_ca_comp_data->website = $newswire_ca_company->website;
			$newswire_ca_comp_data->website_source = $newswire_ca_company->website_source;
			$newswire_ca_comp_data->is_website_valid = $newswire_ca_company->is_website_valid;
			
			$newswire_ca_comp_data->num_website_fetch_tries = $newswire_ca_company->num_website_fetch_tries;
			$newswire_ca_comp_data->contact_page_url = $newswire_ca_company->contact_page_url;
			
			$newswire_ca_comp_data->video = $newswire_ca_company->video;
			$newswire_ca_comp_data->short_description = $newswire_ca_company->short_description;
			$newswire_ca_comp_data->about_company = $newswire_ca_company->about_company;
			$newswire_ca_comp_data->logo_image_path = $newswire_ca_company->logo_image_path;
			$newswire_ca_comp_data->is_logo_valid = $newswire_ca_company->is_logo_valid;

			$newswire_ca_comp_data->address = $newswire_ca_company->address;
			$newswire_ca_comp_data->city = $newswire_ca_company->city;
			$newswire_ca_comp_data->state = $newswire_ca_company->state;
			$newswire_ca_comp_data->zip = $newswire_ca_company->zip;
			$newswire_ca_comp_data->country_id = $newswire_ca_company->country_id;
			$newswire_ca_comp_data->phone = $newswire_ca_company->phone;
			$newswire_ca_comp_data->is_phone_from_pr_text = $newswire_ca_company->is_phone_from_pr_text;
			
			$newswire_ca_comp_data->soc_fb = $newswire_ca_company->soc_fb;
			$newswire_ca_comp_data->soc_fb_feed_status = $newswire_ca_company->soc_fb_feed_status;
			$newswire_ca_comp_data->soc_twitter = $newswire_ca_company->soc_twitter;
			$newswire_ca_comp_data->soc_twitter_feed_status = $newswire_ca_company->soc_twitter_feed_status;
			$newswire_ca_comp_data->soc_gplus = $newswire_ca_company->soc_gplus;
			$newswire_ca_comp_data->soc_gplus_feed_status = $newswire_ca_company->soc_gplus_feed_status;
			$newswire_ca_comp_data->soc_linkedin = $newswire_ca_company->soc_linkedin;
			$newswire_ca_comp_data->soc_youtube = $newswire_ca_company->soc_youtube;
			$newswire_ca_comp_data->soc_youtube_feed_status = $newswire_ca_company->soc_youtube_feed_status;
			$newswire_ca_comp_data->soc_pinterest = $newswire_ca_company->soc_pinterest;
			$newswire_ca_comp_data->soc_pinterest_feed_status = $newswire_ca_company->soc_pinterest_feed_status;
			$newswire_ca_comp_data->blog_url = $newswire_ca_company->blog_url;
			$newswire_ca_comp_data->blog_rss = $newswire_ca_company->blog_rss;
			$newswire_ca_comp_data->is_website_read = $newswire_ca_company->is_website_read;
			
			$newswire_ca_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->newswire_ca_company_ids = $newswire_ca_company_ids;
		$this->send();						
	}

	public function save_domainiq_emails()
	{
		$results = $this->iella_in->results;

		$newswire_ca_company_ids = array();

		foreach ($results as $result)
		{
			if (!$newswire_ca_comp = Model_Newswire_CA_Company::find($result->newswire_ca_company_id))
			{
				$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
				continue;
			}

			// the company has a newsroom
			if ($newswire_ca_comp->company_id)
			{
				$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
				continue;
			}

			if (!$c_data = Model_Newswire_CA_Company_Data::find($result->newswire_ca_company_id))
			{
				$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
				continue;
			}

			// there is already an email
			if (!empty($c_data->email))
			{
				$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
				continue;
			}

			$c_data->email = $result->email;
			$c_data->save();
			$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->newswire_ca_company_ids = $newswire_ca_company_ids;
		$this->send();
	}

	public function save_contact_page_url()
	{
		$results = $this->iella_in->results;

		$newswire_ca_company_ids = array();

		foreach ($results as $result)
		{
			if (!$newswire_ca_comp = Model_Newswire_CA_Company::find($result->newswire_ca_company_id))
			{
				$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
				continue;
			}

			if (!$c_data = Model_Newswire_CA_Company_Data::find($result->newswire_ca_company_id))
			{
				$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
				continue;
			}

			$c_data->contact_page_url = $result->contact_page_url;
			$c_data->save();
			$newswire_ca_company_ids[] = $result->newswire_ca_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->newswire_ca_company_ids = $newswire_ca_company_ids;
		$this->send();
	}
}

?>

