<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Crawl_NR_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT cd.*
				FROM ac_nr_pr_co_company_data cd
				LEFT JOIN ac_nr_pr_co_nr_crawled w
				ON w.pr_co_company_id = cd.pr_co_company_id
				WHERE w.pr_co_company_id IS NULL 
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				ORDER BY cd.pr_co_company_id DESC
				LIMIT 1";

		while ($cnt++ <= 10)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_PR_Co_Company_Data::from_db($result);
			if (!$c_data) break;

			$this->get($c_data);
		}
	}

	public function get($c_data)
	{
		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$html = @file_get_html($c_data->newsroom_url);

		$nr_crawled = new Model_PR_Co_NR_Crawled();
		$nr_crawled->pr_co_company_id = $c_data->pr_co_company_id;
		$nr_crawled->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$nr_crawled->is_page_read_success = 0;
			$nr_crawled->save();
			return;
		}

		$nr_crawled->is_page_read_success = 1;

		$pressroom_contact_info = @$html->find('p[id=pressroom_contact_info]', 0)->innertext;
		if (empty($pressroom_contact_info))
			$pressroom_contact_info = @$html->find('p[id=company_contact_info]', 0)->innertext;

		$email = $this->extract_email_address($pressroom_contact_info);

		// checking if the email is not found 
		// in contact info part. now searching 
		// in the entire sidebar
		if (empty($email))
		{
			if ($sidebar = @$html->find('aside[id=sidebar]', 0));
			else
				$sidebar = @$html->find('div[id=sidebar]', 0);

			if ($sidebar_text = @$sidebar->innertext)
				$email = $this->extract_email_address($sidebar_text);
		}

		$phone = $this->extract_phone_number($pressroom_contact_info);

		if (empty($phone))
		{
			if ($sidebar = @$html->find('aside[id=sidebar]', 0));
			else
				$sidebar = @$html->find('div[id=sidebar]', 0);

			if ($sidebar_text = @$sidebar->innertext)
				$phone = $this->extract_phone_number($sidebar_text);
		}

		
		foreach (@$html->find('ul[id=pressroom_links] li a') as $link)
			if (@$link->innertext == "Main website")
				$website = $link->href;

		if (empty($website))
			foreach (@$html->find('ul[id=company_links] li a') as $link)
				if (@$link->innertext == "Main website")
					$website = $link->href;

		$anchors = array();

		foreach(@$html->find('div[id=panel_social] a') as $element)
			$anchors[] = $element->href;

		if (!count($anchors))
			foreach(@$html->find('div[id=social_circles] a') as $element)
				$anchors[] = $element->href;
			

		$about = "";
		
		if ($about_area = @$html->find('section[id=pressroom_description]', 0));
		else
			$about_area = @$html->find('div[id=company_description_text]', 0);

		if (!empty($about_area->innertext))
		{
			foreach (@$about_area->find('p') as $p)
				if (!empty($p->innertext))
					$about = "{$about}<p>{$p->innertext}</p>";
		}

		if (!empty($about))
			$short_description = strip_tags($about);

		
		$logo_image_path = @$html->find('section[id=logo] a img', 0)->src;
		if (empty($logo_image_path))
			$logo_image_path = @$html->find('div[id=logo] a img', 0)->src;

		
		$socials = $this->extract_socials($anchors);

		if (!empty($socials['soc_twitter']))
			$soc_twitter = Social_Twitter_Profile::parse_id($socials['soc_twitter']);
		
		if (!empty($socials['soc_fb'])) 
			$soc_fb = Social_Facebook_Profile::parse_id($socials['soc_fb']);
		

		if (!empty($socials['soc_linkedin']))
			$soc_linkedin = Social_Linkedin_Profile::parse_id($socials['soc_linkedin']);
		
		
		if (!empty($socials['soc_pinterest'])) 
			$soc_pinterest = Social_Pinterest_Profile::parse_id($socials['soc_pinterest']);
		

		if (!empty($socials['soc_youtube']))
			$soc_youtube = Social_Youtube_Profile::parse_id($socials['soc_youtube']);
			
		if (!empty($socials['soc_gplus'])) 
			$soc_gplus = Social_GPlus_Profile::parse_id($socials['soc_gplus']);

		
		if (!empty($website) && (empty($c_data->website) || $c_data->is_website_valid == 0))
		{
			$nr_crawled->is_website_read = 1;
			$c_data->website = $website;
			$c_data->is_website_valid = 1;
		}

		if (!empty($about) && empty($c_data->about))
		{
			$nr_crawled->is_about_blurb_read = 1;
			$c_data->short_description = value_or_null($short_description);
			$c_data->about_company = $about;
		}

		if (!empty($phone) && empty($c_data->phone))
		{
			$nr_crawled->is_phone_read = 1;
			$c_data->phone = $phone;
		}

		if (!empty($email) && empty($c_data->email))
		{
			$nr_crawled->is_email_read = 1;
			$c_data->email = $email;
		}

		if (!empty($soc_fb) && empty($c_data->soc_fb))
		{
			$nr_crawled->is_soc_fb_read = 1;
			$c_data->soc_fb = $soc_fb;
			$c_data->soc_fb_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (!empty($soc_twitter) && empty($c_data->soc_twitter))
		{
			$nr_crawled->is_soc_twitter_read = 1;
			$c_data->soc_twitter = $soc_twitter;
			$c_data->soc_twitter_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;			
		}

		if (!empty($soc_linkedin) && empty($c_data->soc_linkedin))
		{
			$nr_crawled->is_soc_linkedin_read = 1;
			$c_data->soc_linkedin = $soc_linkedin;
		}
		
		if (!empty($soc_pinterest) && empty($c_data->soc_pinterest))
		{
			$nr_crawled->is_soc_pinterest_read = 1;
			$c_data->soc_pinterest = $soc_pinterest;
			$c_data->soc_pinterest_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}	

		if (!empty($soc_youtube) && $soc_youtube !== "watch"  && $soc_youtube !== "embed" && empty($c_data->soc_youtube))
		{
			$nr_crawled->is_soc_youtube_read = 1;
			$c_data->soc_youtube = $soc_youtube;
			$c_data->soc_youtube_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (!empty($soc_gplus) && empty($c_data->soc_gplus))
		{
			$nr_crawled->is_soc_gplus_read = 1;
			$c_data->soc_gplus = $soc_gplus;
			$c_data->soc_gplus_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (!empty($logo_image_path) && empty($c_data->logo_image_path))
		{
			$nr_crawled->is_logo_read = 1;
			$c_data->logo_image_path = $logo_image_path;
			$c_data->is_logo_valid = 1;
		}
		

		$c_data->save();
		
		$nr_crawled->save();			

	}
}

?>
