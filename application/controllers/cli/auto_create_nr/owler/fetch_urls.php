<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_URLs_Controller extends Auto_Create_NR_Base { // fetching company urls and data

	public function index()
	{
		lib_autoload('simple_html_dom');
		
		$config = Model_Owler_Config::find(array('name', 'last_checked_id'));
		// $config_proxy = Model_Owler_Config::find(array('name', 'last_used_nr_proxy_id'));

		$last_id = $config->value;

		$cnt = 1;
		
		$chars = 'abcdefghijklmnopqrstuwxyz';
		$chars .= '-';

		$sql = "SELECT name 
				FROM ac_nr_owler_company
				ORDER BY RAND() LIMIT 0, 1";

		while ($cnt++ <= 2) // while ($cnt++ <= 5)
		{
			$last_id++;

			$config->value = $last_id;
			$config->save();


			// $m_proxy = $this->get_owler_proxy($config_proxy);

			// if (empty($m_proxy->id))
			//	return;

			// $proxy = $m_proxy->ip;

			// $config_proxy->value = $m_proxy->id;
			// $config_proxy->save();

		

			$result = $this->db->query($sql)->row();

			$slug = Slugger::create($result->name, Model_Content::MAX_SLUG_LENGTH);

			$url = "https://www.owler.com/iaApp/{$last_id}/{$slug}-company-profile";

			// echo "\n ------ \n" . $proxy . "\n ------ \n";

			// $this->inspect($proxy);
			
			$text = $this->fetch_page_text($url); 
			
			$html = str_get_html($text);

			if (!$html || @$html->find('div[id=error_content]', 0))
			{
				echo "\n \n 404";
				// $config->value = $last_id;
				// $config->save();
			}
			else
			{
				$owler_url = new Model_Owler_URL();
				$owler_url->url = $url;
				$owler_url->owler_id = $last_id;
				$owler_url->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
				// $owler_url->proxy_id = $m_proxy->id;
				// $owler_url->proxy = $proxy;
				// $owler_url->proxy_source = $m_proxy->source;

				$owler_url->save();

				// $config->value = $last_id;
				// $config->save();
				$this->save($html, $owler_url);
			}

			sleep(2);		
		}
	}

	public function fetch_page_text($url)
	{
		$chars = 'abcdefghijklmnopqrstuwxyz';
		$chars .= '0123456789';		
		$rand_str = "";
		$length = 20;

		for ($i = 0, $rand_str = null; $i < $length; $i++)
				$rand_str .= $chars[mt_rand(0, strlen($chars) - 1)];

		$phantom_script = "raw/phantom_fetch_page.js";
		
		// $proxy = "zproxy.luminati.io:22225";
		// $proxy_auth = "lum-customer-newswire-zone-gen-country-us-session-{$rand_str}:599728c4b19c";
		// $command = "phantomjs --load-images=false --proxy={$proxy} --proxy-auth={$proxy_auth} {$phantom_script} {$url}";

		$proxy = "127.0.0.1:9050";
		$command = "phantomjs --load-images=false --proxy={$proxy} --proxy-type=socks5 {$phantom_script} {$url}";

		$response = exec($command, $output);

		$text = "";
 		if (is_array($output))
 			foreach ($output as $o)
 				$text = "{$text} $o";
 		else
 			$text = $output;

 		// echo $text;

 		return $text;
	}

	public function save($html, $owler_url)
	{
		lib_autoload('simple_html_dom');
		
		if ($comp_span = @$html->find('span[class=company-name]', 0))
			$company_name = $comp_span->plaintext;

		if (!empty($company_name))
			$company_name = $this->sanitize($company_name);

		$this->inspect('Company_NAme = '.@$company_name);
		echo "\nCOMPANY NAME = " . @$company_name;

		if (empty($company_name) || trim($company_name) == "{{companyBasicDetails.name}}")
			return false;

		

		if ($industry_li = @$html->find('li[ng-repeat=sector in companyBasicDetails.industrySector]', 0))
			if ($industry_span = @$industry_li->find('span[class=ng-binding]', 0))
			{
				foreach ($industry_span->find('span') as $ind_span)
					$ind_span->innertext = "";
				
				$category_name = $industry_span->plaintext;
			}


		if (!empty($category_name))
			$category_name = trim($category_name);
		else
			$category_name = 'other';

		echo "\ncategory_name = " . @$category_name;


		$address1 = "";
		$address2 = "";
		$address3 = "";
		if ($street1_input = @$html->find('input[itemprop=streetAddress]', 0))
			$address1 = $street1_input->content;

		if ($street2_input = @$html->find('input[itemprop=addressLocality]', 0))
			$address2 = $street2_input->content;

		if ($street3_input = @$html->find('input[itemprop=addressRegion]', 0))
			$address3 = $street3_input->content;

		if ($zip_input = @$html->find('input[itemprop=postalCode]', 0))
			$zip = $zip_input->content;

		if ($phone_input = @$html->find('input[itemprop=telephone]', 0))
			$phone = $phone_input->content;

		if ($web_input = @$html->find('input[itemprop=url]', 0))
			$website = $web_input->content;

		if (!empty($website) && substr($website, 0, 4) != "http")
			$website = "http://{$website}";

		if ($logo_input = @$html->find('input[itemprop=logo]', 0))
			$logo_image_path = $logo_input->content;

		if ($about_input = @$html->find('input[itemprop=description]', 0))
			$about_blurb = $about_input->content;

		if (!empty($about_blurb))
			$about_blurb = $this->sanitize($about_blurb);

		$anchors = array();
		$socials = array();


		foreach(@$html->find('div.cp-description-section a') as $element)
			$anchors[] = $element->href;

		if (is_array($anchors) && count($anchors))
			$socials = $this->extract_socials($anchors);

		print_r($socials);

		$address = "{$address1} {$address2} {$address3}";
		
		echo "\nADDRESS = " . @$address;

		echo "\nPHONE = " . @$phone;

		echo "\nWEBSITE = " . @$website;

		echo "\nLOGO = " . @$logo_image_path;

		echo "\nABOUT = " . @$about_blurb;


		
		$category_id = 0;
		if (!empty($category_name))
			if (!$m_category = Model_Owler_Category::find('name', $category_name))
			{
				$m_category = new Model_Owler_Category();
				$m_category->name = $category_name;
				$m_category->save();
				$category_id = $m_category->id;
			}
			else
				$category_id = $m_category->id;

		$owler_comp = new Model_Owler_Company();
		$owler_comp->name = $company_name;
		$owler_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
		$owler_comp->owler_category_id = $category_id;
		$owler_comp->owler_url_id = $owler_url->id;
		$owler_comp->save();

		$owler_c_data = new Model_Owler_Company_Data();
		$owler_c_data->owler_company_id = $owler_comp->id;

		$owler_c_data->website = value_or_null(@$website);
		$owler_c_data->address = value_or_null($address);
		// $owler_c_data->city = value_or_null($city);
		// $owler_c_data->state = value_or_null($state);
		$owler_c_data->zip = value_or_null($zip);
		$owler_c_data->phone = value_or_null(@$phone);
		$owler_c_data->logo_image_path = value_or_null($logo_image_path);
		
		if (!empty($logo_image_path))
			$owler_c_data->is_logo_valid = 1;

		$owler_c_data->short_description = value_or_null(@$about_blurb);
		$owler_c_data->about_company = value_or_null(@$about_blurb);

		
		if (!empty($socials['soc_fb']))
			$soc_fb = $socials['soc_fb'];

		if (!empty($socials['soc_twitter']))
			$soc_twitter = $socials['soc_twitter'];
			
		if (!empty($socials['soc_linkedin']))
			$soc_linkedin = $socials['soc_linkedin'];

		if (!empty($socials['soc_pinterest']))
			$soc_pinterest = $socials['soc_pinterest'];
			
		if (!empty($socials['soc_youtube']))
			$soc_youtube = $socials['soc_youtube'];
			
		if (!empty($socials['soc_gplus']))		
			$soc_gplus = $socials['soc_gplus'];
		
		$owler_c_data->soc_fb = value_or_null(@$soc_fb);
		$owler_c_data->soc_twitter = value_or_null(@$soc_twitter);
		$owler_c_data->soc_gplus = value_or_null(@$soc_gplus);
		$owler_c_data->soc_linkedin = value_or_null(@$soc_linkedin);
		$owler_c_data->soc_youtube = value_or_null(@$soc_youtube);
		$owler_c_data->soc_pinterest = value_or_null(@$soc_pinterest);

		$owler_c_data->save();

		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

		foreach (@$html->find('ul.cp-news-feed') as $news_section)
		{
			foreach (@$news_section->find('li') as $li)
			{
				$title = $summary = $link = $date_time = $publish_date = $news_image_path = null;
				
				if ($a = @$li->find('div.cp-news-txt a', 0))
				{
					$link = $a->href;
					$title = $a->innertext;
					if ($news_img = @$li->find('img', 0))
						$news_image_path = @$news_img->src;

					

					if ($summary_span = @$li->find('span.cp-news-desc', 0))
						$summary = @$summary_span->innertext;
					
					if (empty($summary))
						$summary = $title;

					if (!empty($title))
						$title = $this->sanitize($title);

					if (!empty($summary))
						$summary = $this->sanitize($summary);

					echo "\n News Title: " . @$title;
					echo "\n Summary: " . @$summary . "\n \n";					

					if ($date_time_span = @$li->find('span.cp-news-posted-time', 0))
					{
						$date_time = $date_time_span->plaintext;
						$date_time = trim($date_time);

						if (!empty($date_time) && (substr(strrev($date_time), 0, 1) == "m"
							|| substr(strrev($date_time), 0, 1) == "h")) // e.g. 7 h or 28 m
						{
							$date_time = Date::$now->format('M d, Y');
							$publish_date = Date::$now->format(DATE::FORMAT_MYSQL);
						}
						
						elseif (strlen($date_time) > 0 && in_array(substr($date_time, strlen($date_time)-3, 3), $months)) // e.g. May 07
						{
							$date_time = $date_time.", ".Date::$now->format('Y');
							$date_time = date('M d, Y', strtotime($date_time));
							$publish_date = date(DATE::FORMAT_MYSQL, strtotime($date_time));
						}	
						else // its like May 14
							$publish_date = date(DATE::FORMAT_MYSQL, strtotime($date_time));
					}

					echo "\n Publish Date: " . @$publish_date;

					if (!empty($title))
					{
						$m_content = new Model_Content();
						$m_content->type = Model_Content::TYPE_OWLER_NEWS;
						$m_content->title = $title;
						$m_content->date_publish = $publish_date;
						$m_content->date_created = $publish_date;
						$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
						$m_content->is_published = 1;
						$m_content->is_approved = 1;
						$m_content->is_excluded_from_news_center = 1;
						$m_content->is_scraped_content = 1;
						$m_content->save();

						// Now saving the content data
						$m_c_data = new Model_Content_Data();
						$m_c_data->content_id = $m_content->id;
						$m_c_data->summary = $title;
						$m_c_data->content = value_or_null($title);
						$m_c_data->save();

						$owler_pr = new Model_PB_Owler_News();
						$owler_pr->content_id = $m_content->id;
						$owler_pr->url = $link;
						$owler_pr->owler_company_id = $owler_comp->id;
						$owler_pr->news_image_path = value_or_null($news_image_path);
						$owler_pr->date_from_owler = $date_time;
						$owler_pr->save();
					}
					
				}
			}
		}

		echo "\n";
	}


	public function save__($html, $owler_url)
	{
		lib_autoload('simple_html_dom');
		
		//$html = str_get_html($owler_url_data->html);

		$company_name = @$html->find('input[name=company_short_name]', 0)->value;
		echo "COMPANY NAME = {$company_name}";

		$category_name = @$html->find('div[id=industryList] span', 0)->plaintext;
		if (!empty($category_name))
			$category_name = trim($category_name);
		else
			$category_name = 'other';
		// echo "<hr>category_name = {$category_name}";

		$street1 = @$html->find('span[id=street1]', 0)->plaintext;
		$street2 = @$html->find('span[id=street2]', 0)->plaintext;
		$address = @$street1. " ".@$street2;
		// echo "<hr>address = {$address}";

		$city = @$html->find('span[id=city]', 0)->plaintext;
		// echo "<hr>city = {$city}";

		$state = @$html->find('span[itemprop=addressRegion] span[id=state]', 0)->plaintext;
		// echo "<hr>state = {$state}";

		$zip = @$html->find('span[id=zipcode]', 0)->plaintext;
		// echo "<hr>zip = {$zip}";

		$country = @$html->find('span[id=country]', 0)->plaintext;
		// echo "<hr>country = {$country}";

		$phone = @$html->find('span[id=phone]', 0)->plaintext;
		// echo "<hr>phone = {$phone}";


		$website = @$html->find('p[class=names_url section_block]', 0)->content;
		// echo "<hr>Website = {$website}";

		$logo_image_path = @$html->find('div[class=logo] img', 0)->src;
		// echo "<hr>logo = {$logo_image_path}";

		$about_blurb = @$html->find('span[id=description]', 0)->innertext;
		// echo "<hr>about_blurb = {$about_blurb}";

		$links = str_get_html(@$html->find('div[id=linksTile]', 0)->innertext);

		// Finding social data
		foreach(@$links->find('a') as $element)
		{
			$href = $element->href;
			$pattern_fb = '/facebook.com/';
			$pattern_twitter = '/twitter.com/';
			$pattern_linkedin = '/linkedin.com/';
			$pattern_pinterest = '/pinterest.com/';
			$pattern_youtube = '/youtube.com/';
			$pattern_gplus = '/plus.google.com/';
			
			if (preg_match($pattern_fb, $href, $match)) 
			{
				$soc_fb = $href;
				$soc_fb = Social_Facebook_Profile::parse_id($soc_fb);
				// echo "<hr>soc_fb = {$soc_fb}";
			}

			if (preg_match($pattern_twitter, $href, $match)) 
			{
				$href = str_replace('/#!', '', $href);
				$soc_twitter = $href;
				$soc_twitter = Social_Twitter_Profile::parse_id($soc_twitter);
				// echo "<hr>soc_twitter = {$soc_twitter}";
			}

			if (preg_match($pattern_linkedin, $href, $match)) 
			{
				$soc_linkedin = $href;
				$soc_linkedin = Social_Linkedin_Profile::parse_id($soc_linkedin);
				// echo "<hr>soc_linkedin = {$soc_linkedin}";
			}

			if (preg_match($pattern_pinterest, $href, $match)) 
			{
				$soc_pinterest = $href;
				$soc_pinterest = Social_Pinterest_Profile::parse_id($soc_pinterest);
				// echo "<hr>soc_pinterest = {$soc_pinterest}";
			}

			if (preg_match($pattern_youtube, $href, $match)) 
			{
				$soc_youtube = $href;
				$soc_youtube = Social_Youtube_Profile::parse_id($soc_youtube);
				
				// echo "<hr>soc_youtube = {$soc_youtube}";

			}

			if (preg_match($pattern_gplus, $href, $match)) 
			{
				$soc_gplus = $href;
				$soc_gplus = Social_GPlus_Profile::parse_id($soc_gplus);
				// echo "<hr>soc_gplus = {$soc_gplus}";
			}
		}

		$category_id = 0;
		if (!empty($category_name))
			if (!$m_category = Model_Owler_Category::find('name', $category_name))
			{
				$m_category = new Model_Owler_Category();
				$m_category->name = $category_name;
				$m_category->save();
				$category_id = $m_category->id;
			}
			else
				$category_id = $m_category->id;

		$owler_comp = new Model_Owler_Company();
		$owler_comp->name = $company_name;
		$owler_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
		$owler_comp->owler_category_id = $category_id;
		$owler_comp->owler_url_id = $owler_url->id;
		$owler_comp->save();

		$owler_c_data = new Model_Owler_Company_Data();
		$owler_c_data->owler_company_id = $owler_comp->id;
		//$owler_c_data->contact_name = value_or_null(@$contact_name);
		$owler_c_data->website = value_or_null(@$website);
		$owler_c_data->address = value_or_null($address);
		$owler_c_data->city = value_or_null($city);
		$owler_c_data->state = value_or_null($state);
		$owler_c_data->zip = value_or_null($zip);
		$owler_c_data->phone = value_or_null(@$phone);
		$owler_c_data->logo_image_path = value_or_null($logo_image_path);
		
		if (!empty($logo_image_path))
			$owler_c_data->is_logo_valid = 1;

		$owler_c_data->short_description = value_or_null(@$about_blurb);
		$owler_c_data->about_company = value_or_null(@$about_blurb);

		$owler_c_data->soc_fb = value_or_null(@$soc_fb);
		$owler_c_data->soc_twitter = value_or_null(@$soc_twitter);
		$owler_c_data->soc_gplus = value_or_null(@$soc_gplus);
		$owler_c_data->soc_linkedin = value_or_null(@$soc_linkedin);
		$owler_c_data->soc_youtube = value_or_null(@$soc_youtube);
		$owler_c_data->soc_pinterest = value_or_null(@$soc_pinterest);

		$owler_c_data->save();

		$all_news = @$html->find('div[class=top_news_row]');
		$news_list = array();
		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

		if (is_array($all_news) && count($all_news) > 0)
			foreach ($all_news as $news)
			{
				$title = "";
				$date_time = "";
				$link = "";

				$single = str_get_html($news->innertext);
				$n = array();
				$source = @$single->find('a[class=link source ellipsis fb_ne_list_lowlink]', 0)->title;

				$title = @$single->find('a[class=feedTitle]', 0)->innertext; 

				if (!empty($source) && !empty($title))
					$title = str_ireplace($source, '', $title);
				// echo "<br>". $title;
				$link = @$single->find('a[class=feedTitle]', 0)->href; 
				// echo "<br>" . $link;
				$date_time = @$single->find('span[class=duration]', 0)->innertext;
				// echo "<br>" . $date_time;


				if (strlen($date_time) > 0 && (substr($date_time, strlen($date_time)-2, 2) == " h"
						|| substr($date_time, strlen($date_time)-2, 2) == " m")) // e.g. 7 h or 28 m
				{
					$date_time = Date::$now->format('M d, Y');
					// echo "<br>" . $date_time;
					$publish_date = Date::$now->format(DATE::FORMAT_MYSQL);
				}
				
				elseif (strlen($date_time) > 0 && in_array(substr($date_time, strlen($date_time)-3, 3), $months)) // e.g. May 07
				{
					$date_time = $date_time.", ".Date::$now->format('Y');
					$date_time = date('M d, Y', strtotime($date_time));
					// echo "<br>" . $date_time;
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($date_time));
				}	
				else // its like May 14
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($date_time));
					
				// echo "<br>" . $publish_date;
				//// echo htmlspecialchars($single);
				// echo "<hr>";

				$m_content = new Model_Content();
				$m_content->type = Model_Content::TYPE_OWLER_NEWS;
				$m_content->title = $title;
				$m_content->date_publish = $publish_date;
				$m_content->date_created = $publish_date;
				$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
				$m_content->is_published = 1;
				$m_content->is_approved = 1;
				$m_content->save();

				// Now saving the content data
				$m_c_data = new Model_Content_Data();
				$m_c_data->content_id = $m_content->id;
				$m_c_data->summary = $title;
				$m_c_data->content = value_or_null($title);
				$m_c_data->save();

				$owler_pr = new Model_PB_Owler_News();
				$owler_pr->content_id = $m_content->id;
				$owler_pr->url = $link;
				$owler_pr->owler_company_id = $owler_comp->id;
				$owler_pr->date_from_owler = $date_time;
				$owler_pr->save();

			}
	}
}

?>
