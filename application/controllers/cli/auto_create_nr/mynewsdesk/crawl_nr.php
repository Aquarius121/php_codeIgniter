<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Crawl_NR_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT cd.newsroom_url, cd.mynewsdesk_company_id
				FROM ac_nr_mynewsdesk_company_data cd
				LEFT JOIN ac_nr_mynewsdesk_nr_crawled w
				ON w.mynewsdesk_company_id = cd.mynewsdesk_company_id
				WHERE w.mynewsdesk_company_id IS NULL 
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				ORDER BY cd.mynewsdesk_company_id
				LIMIT 1";

		while ($cnt++ <= 10)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_MyNewsDesk_Company_Data::from_db($result);
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

		$nr_crawled = new Model_MyNewsDesk_NR_Crawled();
		$nr_crawled->mynewsdesk_company_id = $c_data->mynewsdesk_company_id;
		$nr_crawled->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$nr_crawled->is_page_read_success = 0;
			$nr_crawled->save();
			return;
		}

		$nr_crawled->is_page_read_success = 1;

		$about = "";

		$about = @$html->find('div[id=newsroom-summary-box] div[class=expanded]', 0)->plaintext;
		if (empty($about))
			$about = @$html->find('div[id=newsroom-summary-box] div[class=collapsed]', 0)->plaintext;

		if (empty($about))
			$about = @$html->find('div[id=newsroom-summary-box]', 0)->plaintext;
		
		if (!empty($about))
			$about = trim($about);

		$address = "";

		if ($a_div = @$html->find('div[class=newsroom-summary] i[class=icon-home]', 0))
		{
			$address_div = @$a_div->parent()->parent();
			foreach (@$address_div->find('ul[class=unstyled] li') as $i => $li)
				if ($i == 0 && $c_name = @$li->find('b')) //ignore this is company name
					continue;
				else
				{
					@$li->find('a', 0)->innertext = "";
					$text = @$li->plaintext;
					$address = "{$address} {$text}";
				}
		
		}
			
		
		if (!empty($address))
			$address = trim($address);


		$contact_name = @$html->find('div[class=row-contacts] div[class=vcard] h2[class=newsroom-list-header] a', 0)->plaintext;

		$phone = @$html->find('div[class=row-contacts] li[class=tel]', 0)->plaintext;

		$email = "";

		if ($email_span = @$html->find('div[class=row-contacts] span[class=obfuscated-email]', 0))
		{
			foreach ($email_span->find('span[class=hide]') as $hide_element)
				$hide_element->innertext = "";

			foreach ($email_span->find('span') as $hide_element)
			{
				$text = $hide_element->innertext;
				$email = "{$email}{$text}";
			}
		}

		if (empty($email)) // means email is obfuscated in a different way
			if ($email_span = @$html->find('div[class=row-contacts] span[class=obfuscated-email]', 0))
			{
				$em = $email_span->innertext;
				//&#119;&#101;&#110;&#99;&#104;&#101;&#46;&#97;&#97;&#108;&#101;&#46;&#104;&#97;&#103;&#101;&#114;&#109;&#97;&#114;&#107;&#64;&#110;&#111;&#102;&#105;&#109;&#97;&#46;&#110;&#111;
				$em = str_replace("&#", "", $em);
				$chars = explode(";", $em);
				foreach ($chars as $ch)
					$email .= chr($ch);
			}


		foreach (@$html->find('div[class=newsroom-summary] h3[class=newsroom-list-header]') as $heading)
		{
			if ($h = @$heading->find('i[class=icon-share-alt]', 0))
			{
				$section = $heading->parent()->parent();
				$website = @$section->find('ul[class=links] li a', 0)->href;
			}
		}

		if (!empty($website))
			$this->get_web_address($website);
		

		$c_data = Model_MyNewsDesk_Company_Data::find($c_data->mynewsdesk_company_id);

		$anchors = array();

		foreach(@$html->find('div[class=social-media-feed] h2[class=newsroom-section-header] a') as $element)
			$anchors[] = $element->href;

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
			$c_data->website_source = Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_NEWSROOM;
			$c_data->is_website_valid = 1;
		}

		if (!empty($contact_name))
		{
			$nr_crawled->is_contact_name_read = 1;
			$c_data->contact_name = $contact_name;
		}

		if (!empty($about))
		{
			$nr_crawled->is_about_blurb_read = 1;
			$c_data->short_description = $about;
			$c_data->about_company = $about;
		}

		if (!empty($phone))
		{
			$nr_crawled->is_phone_read = 1;
			$c_data->phone = $phone;
			$c_data->is_phone_from_pr_text = 0;
		}

		if (!empty($email))
		{
			$nr_crawled->is_email_read = 1;
			$c_data->email = $email;
			$c_data->is_email_from_pr_text = 0;
		}

		if (!empty($address))
		{
			$nr_crawled->is_location_read = 1;
			$c_data->address = $address;
		}

		if (!empty($soc_fb))
		{
			$nr_crawled->is_soc_fb_read = 1;
			$c_data->soc_fb = $soc_fb;
			$c_data->soc_fb_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (!empty($soc_twitter))
		{
			$nr_crawled->is_soc_twitter_read = 1;
			$c_data->soc_twitter = $soc_twitter;
			$c_data->soc_twitter_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;			
		}

		if (!empty($soc_linkedin))
		{
			$nr_crawled->is_soc_linkedin_read = 1;
			$c_data->soc_linkedin = $soc_linkedin;
		}
		
		if (!empty($soc_pinterest))
		{
			$nr_crawled->is_soc_pinterest_read = 1;
			$c_data->soc_pinterest = $soc_pinterest;
			$c_data->soc_pinterest_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}	

		if (!empty($soc_youtube) && $soc_youtube !== "watch"  && $soc_youtube !== "embed")
		{
			$nr_crawled->is_soc_youtube_read = 1;
			$c_data->soc_youtube = $soc_youtube;
			$c_data->soc_youtube_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (!empty($soc_gplus))
		{
			$nr_crawled->is_soc_gplus_read = 1;
			$c_data->soc_gplus = $soc_gplus;
			$c_data->soc_gplus_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}
		

		$c_data->save();
		
		$nr_crawled->save();			

	}

	public function update_about_blurb()
	{
		$cnt = 1;
		
		$sql = "SELECT *
				FROM ac_nr_mynewsdesk_company_data
				WHERE NOT ISNULL(NULLIF(newsroom_url, ''))
				AND is_about_blurb_read = 0
				ORDER BY mynewsdesk_company_id DESC
				LIMIT 200";

		$query = $this->db->query($sql);

		$results = Model_MyNewsDesk_Company_Data::from_db_all($query);
		 
		foreach ($results as $c_data) 
		{
			$this->read_about_blurb($c_data);

			if ($cnt%20 == 0)
				sleep(2);

			$cnt++;
		}
	}

	protected function read_about_blurb($c_data)
	{

		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$html = @file_get_html($c_data->newsroom_url);

		if (empty($html))
		{
			$c_data->is_about_blurb_read = 1;
			$c_data->save();
			return;
		}		

		$about = "";

		$about = @$html->find('div[id=newsroom-summary-box] div[class=expanded]', 0)->plaintext;
		if (empty($about))
			$about = @$html->find('div[id=newsroom-summary-box] div[class=collapsed]', 0)->plaintext;

		if (empty($about))
			$about = @$html->find('div[id=newsroom-summary-box]', 0)->plaintext;
		
		if (!empty($about))
			$about = trim($about);


		if (!empty($about))
		{	
			$c_data->about_company = $about;
			$c_data->short_description = $about;
		}

		$c_data->is_about_blurb_read = 1;
		$c_data->save();
	}
	
}

?>
