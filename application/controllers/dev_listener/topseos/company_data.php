<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Company_Data_Controller extends Iella_Base {
	
	public function save()
	{
		$topseos_data = $this->iella_in->topseos_data;

		$topseos_company_ids = array();

		foreach ($topseos_data as $topseos_company)
		{
			if (!$topseos_comp = Model_TopSeos_Company::find($topseos_company->m_topseos_company->id))
				$topseos_comp = new Model_TopSeos_Company();

			$topseos_comp->id = $topseos_company->m_topseos_company->id;
			$topseos_comp->name = $topseos_company->m_topseos_company->name;
			$topseos_comp->date_fetched = $topseos_company->m_topseos_company->date_fetched;
			$topseos_comp->topseos_category_id = $topseos_company->m_topseos_company->topseos_category_id;
			$topseos_comp->save();
					
			$topseos_company_ids[] = $topseos_comp->id;
			
			if (!$topseos_comp_data = Model_TopSeos_Company_Data::find($topseos_company->topseos_company_id))
				$topseos_comp_data = new Model_TopSeos_Company_Data();

			$topseos_comp_data->topseos_company_id = $topseos_company->topseos_company_id;
			$topseos_comp_data->contact_name = $topseos_company->contact_name;
			$topseos_comp_data->email = $topseos_company->email;
			$topseos_comp_data->email_original = @$topseos_company->email_original;
			
			$topseos_comp_data->website = $topseos_company->website;
			$topseos_comp_data->contact_page_url = $topseos_company->contact_page_url;
			
			$topseos_comp_data->num_website_fetch_tries = $topseos_company->num_website_fetch_tries;
			$topseos_comp_data->short_description = $topseos_company->short_description;
			$topseos_comp_data->about_company = $topseos_company->about_company;
			$topseos_comp_data->about_company_lang = $topseos_company->about_company_lang;
			$topseos_comp_data->logo_image_path = $topseos_company->logo_image_path;
			$topseos_comp_data->is_logo_valid = $topseos_company->is_logo_valid;

			$topseos_comp_data->address = $topseos_company->address;
			$topseos_comp_data->city = $topseos_company->city;
			$topseos_comp_data->state = $topseos_company->state;
			$topseos_comp_data->zip = $topseos_company->zip;
			$topseos_comp_data->country_id = $topseos_company->country_id;
			$topseos_comp_data->phone = $topseos_company->phone;
			
			$topseos_comp_data->soc_fb = $topseos_company->soc_fb;
			$topseos_comp_data->soc_fb_feed_status = $topseos_company->soc_fb_feed_status;
			$topseos_comp_data->soc_twitter = $topseos_company->soc_twitter;
			$topseos_comp_data->soc_twitter_feed_status = $topseos_company->soc_twitter_feed_status;
			$topseos_comp_data->soc_gplus = $topseos_company->soc_gplus;
			$topseos_comp_data->soc_gplus_feed_status = $topseos_company->soc_gplus_feed_status;
			$topseos_comp_data->soc_linkedin = $topseos_company->soc_linkedin;
			$topseos_comp_data->soc_youtube = $topseos_company->soc_youtube;
			$topseos_comp_data->soc_youtube_feed_status = $topseos_company->soc_youtube_feed_status;
			$topseos_comp_data->soc_pinterest = $topseos_company->soc_pinterest;
			$topseos_comp_data->soc_pinterest_feed_status = $topseos_company->soc_pinterest_feed_status;
			
			$topseos_comp_data->save();
			
			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->topseos_company_ids = $topseos_company_ids;
		$this->send();						
	}


}

?>

