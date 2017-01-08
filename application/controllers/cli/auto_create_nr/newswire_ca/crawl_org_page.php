<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Crawl_Org_Page_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT cd.newswire_ca_org_link, cd.newswire_ca_company_id
				FROM ac_nr_newswire_ca_company_data cd
				LEFT JOIN ac_nr_newswire_ca_org_page_crawled w
				ON w.newswire_ca_company_id = cd.newswire_ca_company_id
				WHERE w.newswire_ca_company_id IS NULL 
				AND NOT ISNULL(NULLIF(newswire_ca_org_link, ''))
				ORDER BY cd.newswire_ca_company_id
				LIMIT 1";

		while ($cnt++ <= 10)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_Newswire_CA_Company_Data::from_db($result);
			if (!$c_data) break;

			$this->get($c_data);
		}
	}

	public function get($c_data)
	{
		if (empty($c_data->newswire_ca_org_link))
			return false;

		lib_autoload('simple_html_dom');
		$html = @file_get_html($c_data->newswire_ca_org_link);

		$org_page_crawled = new Model_Newswire_CA_Org_Page_Crawled();
		$org_page_crawled->newswire_ca_company_id = $c_data->newswire_ca_company_id;
		$org_page_crawled->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$org_page_crawled->is_page_read_success = 0;
			$org_page_crawled->save();
			return;
		}

		$org_page_crawled->is_page_read_success = 1;

		$c_data = Model_Newswire_CA_Company_Data::find($c_data->newswire_ca_company_id);
		$logo_image_path = @$html->find('img[id=org-company-img]', 0)->src;

		if ($small_area1 = @$html->find('section[class=organization-landing] div[class=page-header] small', 0))
			$website = @$small_area1->find('a', 0)->href;
		
		$address = @$html->find('span[class=org-locations]', 0)->innertext;

		$about = "";

		foreach (@$html->find('h3') as $element)
		{
			if (!empty($element->plaintext) && trim($element->plaintext) == "ORGANIZATION PROFILE")
			{				
				$org_profile_parent_div = $element->parent();
				foreach (@$org_profile_parent_div->find('p') as $p)
					$about .= $p->plaintext;
					
				break;
			}
		}

		if (!empty($about))
			$about = trim($about);			
		
		
		foreach (@$html->find('h3') as $element)
		{
			if (!empty($element->plaintext) && trim($element->plaintext) == "CONTACT INFORMATION")
			{				
				$contact_info_div = $element->parent();
				$contact_info = $contact_info_div->innertext;

				$phone = $this->extract_phone_number($contact_info);
				$email = $this->extract_email_address($contact_info);

				break;
			}
		}


		if (!empty($contact_info_div))
		{
			$pattern_fb = '/facebook.com/';
			$pattern_twitter = '/twitter.com/';
			$pattern_linkedin = '/linkedin.com/';
			$pattern_pinterest = '/pinterest.com/';
			$pattern_youtube = '/youtube.com/';
			$pattern_gplus = '/plus.google.com/';

			foreach(@$contact_info_div->find('a') as $element)
			{
				$href = $element->href;
				if (preg_match($pattern_twitter, $href, $match)) 
				{
					$soc_twitter = $href;
					$soc_twitter = Social_Twitter_Profile::parse_id($soc_twitter);
				}

				if (preg_match($pattern_fb, $href, $match)) 
				{
					$soc_fb = $href;
					$soc_fb = Social_Facebook_Profile::parse_id($soc_fb);
				}

				if (preg_match($pattern_linkedin, $href, $match)) 
				{
					$soc_linkedin = $href;
					$soc_linkedin = Social_Linkedin_Profile::parse_id($soc_linkedin);
				}

				if (preg_match($pattern_pinterest, $href, $match)) 
				{
					$soc_pinterest = $href;
					$soc_pinterest = Social_Pinterest_Profile::parse_id($soc_pinterest);
				}

				if (preg_match($pattern_youtube, $href, $match)) 
				{
					$soc_youtube = $href;
					$soc_youtube = Social_Youtube_Profile::parse_id($soc_youtube);
				}

				if (preg_match($pattern_gplus, $href, $match)) 
				{
					$soc_gplus = $href;
					$soc_gplus = Social_GPlus_Profile::parse_id($soc_gplus);
				}

			}			
		}
				

		if (!empty($website) && (empty($c_data->website) || $c_data->is_website_valid == 0))
		{
			if (!empty($website))
			{
				$info = parse_url($website);
				$host = $info['host'];
				$website = $info['scheme']."://".$info['host'];
			}
			
			$org_page_crawled->is_website_read = 1;
			$c_data->website = $website;
			$c_data->website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_ORG_PROFILE_PAGE;
			$c_data->is_website_valid = 1;
		}


		if (!empty($logo_image_path))
		{
			$org_page_crawled->is_logo_read = 1;
			$c_data->logo_image_path = $logo_image_path;
			$c_data->is_logo_valid = 1;
		}

		if (!empty($about))
		{
			$org_page_crawled->is_about_blurb_read = 1;
			$c_data->short_description = $about;
			$c_data->about_company = $about;
		}

		if (!empty($phone))
		{
			$org_page_crawled->is_phone_read = 1;
			$c_data->phone = $phone;
			$c_data->is_phone_from_pr_text = 0;
		}

		if (!empty($email))
		{
			$org_page_crawled->is_email_read = 1;
			$c_data->email = $email;
			$c_data->is_email_from_pr_text = 0;
		}

		if (!empty($address))
		{
			$org_page_crawled->is_location_read = 1;
			$c_data->address = $address;
		}

		if (!empty($soc_fb))
		{
			$org_page_crawled->is_soc_fb_read = 1;
			$c_data->soc_fb = $soc_fb;
			$c_data->soc_fb_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (!empty($soc_twitter))
		{
			$org_page_crawled->is_soc_twitter_read = 1;
			$c_data->soc_twitter = $soc_twitter;
			$c_data->soc_twitter_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;			
		}

		if (!empty($soc_linkedin))
		{
			$org_page_crawled->is_soc_linkedin_read = 1;
			$c_data->soc_linkedin = $soc_linkedin;
		}
		
		if (!empty($soc_pinterest))
		{
			$org_page_crawled->is_soc_pinterest_read = 1;
			$c_data->soc_pinterest = $soc_pinterest;
			$c_data->soc_pinterest_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}	

		if (!empty($soc_youtube) && $soc_youtube !== "watch"  && $soc_youtube !== "embed")
		{
			$org_page_crawled->is_soc_youtube_read = 1;
			$c_data->soc_youtube = $soc_youtube;
			$c_data->soc_youtube_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (!empty($soc_gplus))
		{
			$org_page_crawled->is_soc_gplus_read = 1;
			$c_data->soc_gplus = $soc_gplus;
			$c_data->soc_gplus_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}
		

		$c_data->save();
		
		$org_page_crawled->save();			

	}
	
}

?>
