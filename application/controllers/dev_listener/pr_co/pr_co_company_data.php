<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class PR_Co_Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$pr_co_data = $this->iella_in->pr_co_data;

		$pr_co_company_ids = array();

		foreach ($pr_co_data as $pr_co_company)
		{
			if (!$pr_co_comp = Model_PR_Co_Company::find($pr_co_company->m_pr_co_company->id))
				$pr_co_comp = new Model_PR_Co_Company();

			$pr_co_comp->id = $pr_co_company->m_pr_co_company->id;
			$pr_co_comp->name = $pr_co_company->m_pr_co_company->name;
			$pr_co_comp->date_fetched = $pr_co_company->m_pr_co_company->date_fetched;
			$pr_co_comp->pr_co_category_id = $pr_co_company->m_pr_co_company->pr_co_category_id;
			$pr_co_comp->save();
					
			$pr_co_company_ids[] = $pr_co_comp->id;
			
			if (!$pr_co_comp_data = Model_PR_Co_Company_Data::find($pr_co_company->pr_co_company_id))
				$pr_co_comp_data = new Model_PR_Co_Company_Data();

			$pr_co_comp_data->pr_co_company_id = $pr_co_company->pr_co_company_id;
			$pr_co_comp_data->contact_name = $pr_co_company->contact_name;
			$pr_co_comp_data->email = $pr_co_company->email;
			$pr_co_comp_data->email_original = @$pr_co_company->email_original;
			$pr_co_comp_data->newsroom_url = $pr_co_company->newsroom_url;
			
			$pr_co_comp_data->website = $pr_co_company->website;
			$pr_co_comp_data->is_website_valid = $pr_co_company->is_website_valid;
			$pr_co_comp_data->contact_page_url = $pr_co_company->contact_page_url;
			
			$pr_co_comp_data->num_website_fetch_tries = $pr_co_company->num_website_fetch_tries;
			$pr_co_comp_data->short_description = $pr_co_company->short_description;
			$pr_co_comp_data->about_company = $pr_co_company->about_company;
			$pr_co_comp_data->about_company_lang = $pr_co_company->about_company_lang;
			$pr_co_comp_data->logo_image_path = $pr_co_company->logo_image_path;
			$pr_co_comp_data->is_logo_valid = $pr_co_company->is_logo_valid;

			$pr_co_comp_data->address = $pr_co_company->address;
			$pr_co_comp_data->city = $pr_co_company->city;
			$pr_co_comp_data->state = $pr_co_company->state;
			$pr_co_comp_data->zip = $pr_co_company->zip;
			$pr_co_comp_data->country_id = $pr_co_company->country_id;
			$pr_co_comp_data->phone = $pr_co_company->phone;
			
			$pr_co_comp_data->soc_fb = $pr_co_company->soc_fb;
			$pr_co_comp_data->soc_fb_feed_status = $pr_co_company->soc_fb_feed_status;
			$pr_co_comp_data->soc_twitter = $pr_co_company->soc_twitter;
			$pr_co_comp_data->soc_twitter_feed_status = $pr_co_company->soc_twitter_feed_status;
			$pr_co_comp_data->soc_gplus = $pr_co_company->soc_gplus;
			$pr_co_comp_data->soc_gplus_feed_status = $pr_co_company->soc_gplus_feed_status;
			$pr_co_comp_data->soc_linkedin = $pr_co_company->soc_linkedin;
			$pr_co_comp_data->soc_youtube = $pr_co_company->soc_youtube;
			$pr_co_comp_data->soc_youtube_feed_status = $pr_co_company->soc_youtube_feed_status;
			$pr_co_comp_data->soc_pinterest = $pr_co_company->soc_pinterest;
			$pr_co_comp_data->soc_pinterest_feed_status = $pr_co_company->soc_pinterest_feed_status;
			$pr_co_comp_data->blog_url = $pr_co_company->blog_url;
			$pr_co_comp_data->blog_rss = $pr_co_company->blog_rss;
			$pr_co_comp_data->is_website_read = $pr_co_company->is_website_read;
			
			$pr_co_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->pr_co_company_ids = $pr_co_company_ids;
		$this->send();						
	}

	public function save_domainiq_emails()
	{
		$results = $this->iella_in->results;

		$pr_co_company_ids = array();

		foreach ($results as $result)
		{
			if (!$pr_co_comp = Model_PR_Co_Company::find($result->pr_co_company_id))
			{
				$pr_co_company_ids[] = $result->pr_co_company_id;
				continue;
			}

			// the company has a newsroom
			if ($pr_co_comp->company_id)
			{
				$pr_co_company_ids[] = $result->pr_co_company_id;
				continue;
			}

			if (!$c_data = Model_PR_Co_Company_Data::find($result->pr_co_company_id))
			{
				$pr_co_company_ids[] = $result->pr_co_company_id;
				continue;
			}

			// there is already an email
			if (!empty($c_data->email))
			{
				$pr_co_company_ids[] = $result->pr_co_company_id;
				continue;
			}

			$c_data->email = $result->email;
			$c_data->save();
			$pr_co_company_ids[] = $result->pr_co_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->pr_co_company_ids = $pr_co_company_ids;
		$this->send();
	}

	public function save_contact_page_url()
	{
		$results = $this->iella_in->results;

		$pr_co_company_ids = array();

		foreach ($results as $result)
		{
			if (!$pr_co_comp = Model_PR_Co_Company::find($result->pr_co_company_id))
			{
				$pr_co_company_ids[] = $result->pr_co_company_id;
				continue;
			}

			if (!$c_data = Model_PR_Co_Company_Data::find($result->pr_co_company_id))
			{
				$pr_co_company_ids[] = $result->pr_co_company_id;
				continue;
			}

			$c_data->contact_page_url = $result->contact_page_url;
			$c_data->save();
			$pr_co_company_ids[] = $result->pr_co_company_id;
		}

		$this->iella_out->success = true;
		$this->iella_out->pr_co_company_ids = $pr_co_company_ids;
		$this->send();
	}


}

?>

