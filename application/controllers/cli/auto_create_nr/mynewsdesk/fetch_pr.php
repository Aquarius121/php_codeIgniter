<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_Controller extends Auto_Create_NR_Base { // fetching mynewsdesk single pr

	public function index()
	{
		set_time_limit(30000);

		$cnt = 1;

		$sql = "SELECT p.* 
				FROM nr_pb_mynewsdesk_content p
				INNER JOIN nr_content c 
				ON p.content_id = c.id
				WHERE c.type = ? 
				AND mynewsdesk_company_id = 0
				ORDER BY p.content_id
				LIMIT 1";

		while ($cnt++ <= 30)
		{
			$result = $this->db->query($sql, array(Model_Content::TYPE_PR));
			
			if (!$result->num_rows()) break;
		
			$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::from_db($result);
			if (!$mynewsdesk_pr) break;

			$this->get($mynewsdesk_pr);
			if ($cnt%100 == 0)
				sleep(5);
		}
	}

	protected function get($mynewsdesk_pr)
	{
		set_time_limit(3000);

		lib_autoload('simple_html_dom');

		if (empty($mynewsdesk_pr->url))
			return false;
		
		$html = @file_get_html($mynewsdesk_pr->url);

		if (empty($html))
		{
			$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::find($mynewsdesk_pr->content_id);
			$mynewsdesk_pr->mynewsdesk_company_id = -1;
			$mynewsdesk_pr->is_content_updated = 1;
			$mynewsdesk_pr->save();
			return;
		}

		$m_content = Model_Content::find($mynewsdesk_pr->content_id);

		if (! $m_c_data = Model_Content_Data::find($mynewsdesk_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $mynewsdesk_pr->content_id;
		}


		if (!$m_pr = Model_PB_PR::find($m_content->id))
		{
			$m_pr = new Model_PB_PR();
			$m_pr->content_id = $m_content->id;
		}
		
		if ($mynewsdesk_cat = Model_MyNewsDesk_Category::find($mynewsdesk_pr->mynewsdesk_category_id))
		{
			$m_pr->cat_1_id = $mynewsdesk_cat->newswire_cat_id;
			$m_pr->save();
		}

		$summary = null;

		$pr_b = @$html->find('div[class=newsroom-article] div[class=markdown]', 0)->innertext;

		$newsroom_url = null;

		$newsroom_url = @$html->find('div[class=newsroom-nav-collapse] ul[class=newsroom-nav-items] li a', 0)->href;

		if (!empty($newsroom_url) && substr($newsroom_url, 0, 7) != "http://")
			$newsroom_url = "http://www.mynewsdesk.com{$newsroom_url}";
		
		if (!empty($mynewsdesk_pr->company_name))
			$company_name = $mynewsdesk_pr->company_name;

		if (empty($company_name))
		{
			// First try to find company name 
			// if its a newsroom

			$company_name = @$html->find('img[class=logotype]', 0)->alt;

			if (empty($company_name))
			{
				@$html->find('header h2[class=org] a', 0)->innertext = "";
				@$html->find('header h2[class=org] h1', 0)->innertext = "";
				@$html->find('header h2[class=org] h4', 0)->innertext = "";
				$company_name = @$html->find("header h2", 0)->plaintext;
				$company_name = trim($company_name);
			}
		}

		$website_source = Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_NONE;
		
		$pr_body = "";
		$content = "";

		$keep_tags = true;
		foreach($html->find('div[class=newsroom-article] div[class=markdown] p') as $element)
		{
			$pr_body = "{$pr_body}".$element->innertext;
			$text = $element->innertext;
			$text = $this->sanitize($text, $keep_tags);
			$content .= "<p>{$text}</p>";
		}

		if (empty($content))
		{
			$content = @$html->find('div[class=newsroom-article] div[class=markdown]', 0)->innertext;
			if (!empty($content))
			{
				$content = $this->sanitize($content, $keep_tags);
				$pr_body = $content;
			}
		}

		// Searching website from within the content
		$web_result = $this->extract_website($pr_b, $company_name);
		
		if (is_array($web_result))
		{
			$website = @$web_result['website'];
			$website_source = @$web_result['website_source'];
		}


		if (!empty($website))
			$website = $this->get_web_address($website); 

		$email = $this->extract_email_address($pr_b);
		
		$logo_image_path = @$html->find('div[class=newsroom-header-content] img[class=logotype]', 0)->src;
		
		$phone_num = $this->extract_phone_number($pr_b);

		$anchors = array();

		foreach(@$html->find('div[class=newsroom-article] div[class=markdown] a') as $element)
			$anchors[] = $element->href;

		$socials = $this->extract_socials($anchors);
		
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

		$tags = array();
		foreach ($html->find('a[class=tag]') as $tag)
			if (!empty($tag->title))
			{
				$tg = $tag->plaintext;
				$tags[] = $this->sanitize($tg);
			}

		$topics = array();
		foreach ($html->find('a[class=clean-tag]') as $topic)
		{
			$tg = $topic->plaintext;
			$topics[] = $this->sanitize($tg);
		}

		if (is_array($topics) && count($topics) > 0)
			$this->set_topics($mynewsdesk_pr->content_id, $topics);
				
		////////////////////////////////////////////////////////////////////////////

		$m_content->date_updated = $m_content->date_publish;
		$m_content->is_published = 1;
		$m_content->is_approved = 1;
		$m_content->is_draft = 0;
		
		if (is_array($tags) && count($tags))
			$m_content->set_tags($tags);

		$m_content->save();

		$content = $this->linkify($content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));

		$m_content->set_beats(array($mynewsdesk_cat->newswire_beat_id));

		$m_c_data->summary = $summary;

		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		$is_new_comp = 0;

		if (!empty($company_name))
			$company_name = $this->sanitize($company_name);
		
		// check if the company already exists
		// with the same name
		if (!empty($company_name) &&
				$mynewsdesk_comp = Model_MyNewsDesk_Company::find('name', $company_name))
			$mynewsdesk_c_data = Model_MyNewsDesk_Company_Data::find($mynewsdesk_comp->id);

		// check if the company already exists 
		// with the same website
		elseif(!empty($website) && $mynewsdesk_c_data = Model_MyNewsDesk_Company_Data::find('website', $website))
			$mynewsdesk_comp = Model_MyNewsDesk_Company::find($mynewsdesk_c_data->mynewsdesk_company_id);
				
		else
		{
			$is_new_comp = 1;
			$mynewsdesk_comp = new Model_MyNewsDesk_Company();
			$mynewsdesk_comp->name = $company_name;
			$mynewsdesk_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
			$mynewsdesk_comp->mynewsdesk_category_id = $mynewsdesk_pr->mynewsdesk_category_id;
			$mynewsdesk_comp->save();

			$mynewsdesk_c_data = new Model_MyNewsDesk_Company_Data();
			$mynewsdesk_c_data->mynewsdesk_company_id = $mynewsdesk_comp->id;
		}

		if ($is_new_comp || empty($mynewsdesk_c_data->country))
			$mynewsdesk_c_data->country = value_or_null($mynewsdesk_pr->country);
		

		if ($is_new_comp || empty($mynewsdesk_c_data->email))
		{
			$mynewsdesk_c_data->email = value_or_null($email);
			if (!empty($email))
				$mynewsdesk_c_data->is_email_from_pr_text = 1;
		}

		if ($is_new_comp || empty($mynewsdesk_c_data->newsroom_url))
			$mynewsdesk_c_data->newsroom_url = value_or_null($newsroom_url);

		if ($is_new_comp || empty($mynewsdesk_c_data->website))
		{
			$mynewsdesk_c_data->website = value_or_null(@$website);
			$mynewsdesk_c_data->website_source = $website_source;
			if ($website_source == Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_EMAIL_DOMAIN_WORD_MATCHING
				|| $website_source == Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_DOMAIN_MATCHING)
				$mynewsdesk_c_data->is_website_valid = 0;
			elseif (!empty($website))
				$mynewsdesk_c_data->is_website_valid = 1;
		}

		if ($is_new_comp || empty($mynewsdesk_c_data->phone_num))
		{
			$mynewsdesk_c_data->phone = value_or_null($phone_num);
			if (!empty($phone_num))
				$mynewsdesk_c_data->is_phone_from_pr_text = 1;
		}

		if ($is_new_comp || empty($mynewsdesk_c_data->logo_image_path))
		{
			$mynewsdesk_c_data->logo_image_path = value_or_null($logo_image_path);
			if (!empty($logo_image_path))
				$mynewsdesk_c_data->is_logo_valid = 1;
		}

		if (($is_new_comp || empty($mynewsdesk_c_data->soc_fb)) && !empty($soc_fb))
		{
			$mynewsdesk_c_data->soc_fb = value_or_null($soc_fb);
			$mynewsdesk_c_data->soc_fb_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($mynewsdesk_c_data->soc_twitter)) && !empty($soc_twitter))
		{
			$mynewsdesk_c_data->soc_twitter = value_or_null($soc_twitter);
			$mynewsdesk_c_data->soc_twitter_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($mynewsdesk_c_data->soc_linkedin)) && !empty($soc_linkedin))
			$mynewsdesk_c_data->soc_linkedin = value_or_null($soc_linkedin);

		if (($is_new_comp || empty($mynewsdesk_c_data->soc_pinterest)) && !empty($soc_pinterest))
		{
			$mynewsdesk_c_data->soc_pinterest = value_or_null($soc_pinterest);
			$mynewsdesk_c_data->soc_pinterest_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($mynewsdesk_c_data->soc_youtube)) && !empty($soc_youtube))
		{
			$mynewsdesk_c_data->soc_youtube = value_or_null($soc_youtube);
			$mynewsdesk_c_data->soc_youtube_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($mynewsdesk_c_data->soc_gplus)) && !empty($soc_gplus))
		{
			$mynewsdesk_c_data->soc_gplus = value_or_null($soc_gplus);
			$mynewsdesk_c_data->soc_gplus_feed_status = Model_MyNewsDesk_Company_Data::SOCIAL_NOT_CHECKED;
		}

		$mynewsdesk_c_data->save();		

		$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::find($mynewsdesk_pr->content_id);
		$mynewsdesk_pr->mynewsdesk_company_id = $mynewsdesk_comp->id;
		$mynewsdesk_pr->is_content_updated = 1;
		$mynewsdesk_pr->save();
	}

	protected function extract_website($pr_body, $company_name)
	{	
		$pr_body = str_get_html($pr_body);

		foreach ($pr_body->find('img') as $img)
			$img->src = "";

		$regex = '/https?\:\/\/[^\" ]+/i';
		preg_match_all($regex, $pr_body, $results);
		
		$urls = array();

		foreach ($results as $set) 
			foreach ($set as $item) 
				$urls[] = $item;
			
		foreach ($urls as $i => $url)
		{
			$u = strtolower($url);
			if (!empty($u))
			{
				$info = parse_url($u);
				$host = $info['host'];
				$url = $info['scheme']."://".$info['host'];
				$url = str_replace("<", "", $url);
				$urls[$i] = $url;
			}
		}

		$company_name = strtolower($company_name);
		
		$stop_words = array('the', 'into', 'with', 'for', 'to', 'and', 'by', 'a', 'of', 'as', 'in', 
							'at', 'from', 'on', 'without', 'or', 'via');
		$abbr = "";
		$c_words_list = explode(" ", $company_name);
		$c_words = array();
		foreach ($c_words_list as $i => $w)
		{
			if (!in_array($w, $stop_words))				
			{
				$c_words[] = $w;
				$abbr .= substr($w, 0, 1);
			}
		}

		// if count of words is greater than 2
		// we are also adding a word based on the
		// first letters of the first three words
		// to form an abbreviation

		if (strlen($abbr) > 2)
		{
			$abbr = substr($abbr, 0, 3);
			$c_words[] = $abbr;
		}
		else
			$abbr = null;

		$website = "";
		// trying to find a URL that matches
		// any of the word in the company name

		foreach ($urls as $i => $url)
			foreach ($c_words as $j => $c_word)
				if (strstr($url, $c_word))
				{
					$website = $url;					
					
					if (!empty($abbr) && $j+1 == count($c_words))
					{
						$website_source = Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_ABBREVIATION;
						$web = array('website' => $website, 'website_source' => $website_source);
						// print_r($web);
						return $web;
					}

					$website_source = Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_WORD_MATCHING;
					$web = array('website' => $website, 'website_source' => $website_source);
					// print_r($web);
					return $web;
				}
				

		// If the website is not found in the
		// extracted URLs, we will find it in 
		// the domain names of the email addresses
		// in the text

		$emails = array();
		$pattern = '/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/i';
		$pattern_wrong = '/(.*)(\.png)|(\.gif)|(\.jpg)|(\.bmp)|(\.js)$/i'; 

		if (preg_match_all($pattern, $pr_body, $matches))
			foreach ($matches as $i => $match_array)
				foreach ($match_array as $match)
					if (! preg_match($pattern_wrong, $match, $email_matches))
						if (!in_array($match, $emails))
							$emails[] = $match;
		

		foreach ($emails as $email)
		{
			$domain = strstr($email, "@");
			$domain = substr($domain, 1);
			 
			foreach ($c_words as $c_word)
				if (strstr($domain, $c_word))
				{
					$website = "http://{$domain}";
					$website_source = Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_EMAIL_DOMAIN_WORD_MATCHING;
					$web = array('website' => $website, 'website_source' => $website_source);
					// print_r($web);
					return $web;
				}
				
			$urls[] = "http://{$domain}";
		}

		// The website is still not found 
		// we are checking the extracted
		// urls if the text in the urls
		// exists in the pr body. may be 
		// the domain name is abbreviation

		$plain_pr_body = preg_replace('#(<a.*?>).*?(</a>)#', '$1$2', $pr_body);
		$plain_pr_body = strip_tags($plain_pr_body);

		foreach ($urls as $url)
		{
			$u = parse_url($url, PHP_URL_HOST);
			$u = str_replace("www.", "", $u);
			$u = substr($u, 0, strpos($u, "."));
			
			if (stristr($plain_pr_body, $u))
			{
				$website = $url;
				$website_source = Model_MyNewsDesk_Company_Data::WEBSITE_SOURCE_DOMAIN_MATCHING;
				$web = array('website' => $website, 'website_source' => $website_source);
				return $web;
			}

		}
	}	
}

?>
