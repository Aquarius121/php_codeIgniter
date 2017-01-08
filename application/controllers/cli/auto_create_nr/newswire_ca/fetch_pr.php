<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		
		$sql = "SELECT * FROM nr_pb_newswire_ca_pr
				WHERE newswire_ca_company_id = 0
				ORDER BY content_id
				LIMIT 1";

		$cnt = 1;

		while ($cnt++ <= 30)
		{
			$this->inspect("----------------------------");
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$newswire_ca_pr = Model_PB_Newswire_CA_PR::from_db($result);
			if (!$newswire_ca_pr) break;

			$this->get($newswire_ca_pr);
			sleep(1);
		}
	}

	protected function get($newswire_ca_pr)
	{
		lib_autoload('simple_html_dom');

		if (empty($newswire_ca_pr->url))
			return false;
		
		$html = @file_get_html($newswire_ca_pr->url);

		if (empty($html))
		{
			$newswire_ca_pr = Model_PB_Newswire_CA_PR::find($newswire_ca_pr->content_id);
			$newswire_ca_pr->newswire_ca_company_id = -1;
			$newswire_ca_pr->save();
			return;
		}

		// checking if this is a french PR
		// need to remove this
		$link = @$html->find('div[id=sidebar] a[class=alt_release]', 0)->innertext;
		if (!empty($link) && trim($link) == "English version" && $newswire_ca_pr->content_id <= 3594967)
		{
			$m_content = Model_Content::find($newswire_ca_pr->content_id);
			$m_content->load_local_data();
			$m_content->load_content_data();
			$m_content->delete();
			return;
		}

		$m_content = Model_Content::find($newswire_ca_pr->content_id);


		if (! $m_c_data = Model_Content_Data::find($newswire_ca_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $newswire_ca_pr->content_id;
		}

		$summary = "";

		$pr_b = @$html->find('div[class=news-release-detail] div[class=large-bottom-margin]', 0)->innertext;
		
		foreach($html->find('p[itemprop=articleBody]') as $element)
		{				
			$str = strstr($element->plaintext, "/CNW");

			if (!empty($summary) && strlen($summary) < 100)
				$summary = "{$summary} ".$element->plaintext;

			if ($str)
			{
				$summary = $str;
				$address = @$element->find('span[class=xn-location]', 0)->innertext;

				if (empty($address))
				{
					$add = explode(",", $element->plaintext);
					$address = $add[0];
				}
			}
		}

		if (!empty($address))
			$address = strip_tags($address);

		$summary = str_replace("--", "-", $summary);
		$fir = explode("-", $summary);
		unset( $fir[0] );
		$summary = trim( implode("-", $fir));

		$summary_first_char = substr($summary, 0, 1);

		$summary = null;
		
		if (!empty($address))
			$address = trim($address);

		$company_name = @$html->find("div[id=org_profile_content] h4", 0)->innertext;
		
		if (empty($company_name))
			$company_name = @$html->find("p[class=org-name]", 0)->innertext;

		if (!empty($company_name))
		{
			$company_name = trim($company_name);
			$company_name = $this->sanitize($company_name);
		}

		$website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_NONE;
		
		foreach ($html->find('div[class=news-release-detail] a') as $a)
			if (!empty($a->style) && $a->style == "word-wrap:break-word;")
				$website = $a->href;

		if (!empty($website))
			$website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_ORG_PROFILE;
		
		$pr_body = "";
		$content = null;
		$keep_tags = true;

		if ($c_div = $html->find("div[class=news-release-detail] div[class=large-bottom-margin]", 0))
		{
			$content = $c_div->innertext;
			
			// Just to make sure the inside content of <script> & style tags are not left			
			$content = preg_replace("#\<script(.*)/script>#iUs", "", $content);
			$content = preg_replace("#\<style(.*)/style>#iUs", "", $content);

			$content = $this->sanitize($content, $keep_tags);
		}

		if (!$content)
		{
			foreach($html->find("p[itemprop=articleBody], ul[type=disc], div[class=divOverflow]") as $element)
			{
				$pr_body = "{$pr_body}".$element->innertext;
				$text = $element->innertext;
				$text = "<{$element->tag}>{$text}</{$element->tag}>";
				$text = $this->sanitize($text, $keep_tags);
				$content .= $text;
			}
		}

		// Website not found from organization detail area
		// Now searching within the content
		if (empty($website) && !empty($company_name)) 
		{	
			$web_result = $this->extract_website($pr_b, $company_name);

			if (is_array($web_result))
			{
				$website = @$web_result['website'];
				$website_source = @$web_result['website_source'];
			}
			else
				$website = $web_result;
		}

		if (!empty($website))
		{
			$arr = array(",", "-", "'");
			$website = str_replace($arr, "", $website);

			$info = parse_url($website);
			$host = $info['host'];
			$website = $info['scheme']."://".$info['host'];
		}
		
		$email = $this->extract_email_address($pr_b);
	
		// Finding the organization URL of 
		// this organization on Newswire.ca
		foreach (@$html->find('div[class=org-profile] a') as $a)
			if (!empty($a->plaintext) && trim($a->plaintext) == "More on this organization")
				$newswire_ca_org_link = $a->href;
		
		$img_src = @$html->find('div[class=org-profile] img', 0)->src;

		$is_logo_from_pr_text = 0;
		$cover_image_url = null;

		if (empty($img_src))
		{
			foreach($html->find('div[id=galleryContainer] img') as $i => $element)
			{
				if ($i == 0)
					$cover_image_url = $element->src;

				if ($str = strstr($element->alt, "Logo"))
				{
					$img_src = $element->src;
					if (!strstr($img_src, "LOGO"))
						$is_logo_from_pr_text = 1;
					break;
				}				
			}
		}

		if (empty($cover_image_url) && !empty($img_src))
			$cover_image_url = $img_src;

		$pattern = '#\(?[0-9]{3}\)?[\s|-]\s?[0-9]{3}\s?-?\s?[0-9]{4}#';		
		
		$phone_num = $this->extract_phone_number($pr_b);

		$pattern_fb = '/facebook.com/';
		$pattern_twitter = '/twitter.com/';
		$pattern_linkedin = '/linkedin.com/';
		$pattern_pinterest = '/pinterest.com/';
		$pattern_youtube = '/youtube.com/';
		$pattern_gplus = '/plus.google.com/';
			
		foreach(@$html->find('div[class=news-release-detail] div[class=large-bottom-margin] a') as $element)
		{
			$href = $element->href;
			
			if (preg_match($pattern_fb, $href, $match)) 
			{
				$soc_fb = $href;
				$soc_fb = Social_Facebook_Profile::parse_id($soc_fb);
			}

			if (preg_match($pattern_twitter, $href, $match)) 
			{
				$soc_twitter = $href;
				$soc_twitter = Social_Twitter_Profile::parse_id($soc_twitter);
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
			
		////////////////////////////////////////////////////////////////////////////

		$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_content->is_published = 1;
		$m_content->is_approved = 1;
		$m_content->is_premium = 1;
		$m_content->is_draft = 0;
		$m_content->title_to_slug();
		$m_content->save();

		$content = $this->linkify($content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));
		
		// Now saving the content data
		$summary = $this->sanitize($summary);
		$m_c_data->summary = $summary;
		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		$m_newswire_ca_cat = Model_BusinessWire_Category::find($newswire_ca_pr->newswire_ca_category_id);

		if (! $m_pb_pr = Model_PB_PR::find($newswire_ca_pr->content_id))
		{
			$m_pb_pr = new Model_PB_PR();
			$m_pb_pr->content_id = $m_content->id;
		}

		$m_pb_pr->is_distribution_disabled = 1;
		$m_pb_pr->save();

		if (!empty($m_newswire_ca_cat->newswire_beat_id))
			$m_content->set_beats(array($m_newswire_ca_cat->newswire_beat_id));

		$is_new_comp = 0;
		
		// check if the company already exists
		// with the same name
		if (!empty($company_name) &&
				$newswire_ca_comp = Model_Newswire_CA_Company::find('name', $company_name))
			$newswire_ca_c_data = Model_Newswire_CA_Company_Data::find($newswire_ca_comp->id);

		// check if the company already exists 
		// with the same website
		elseif($newswire_ca_c_data = Model_Newswire_CA_Company_Data::find('website', $website))
			$newswire_ca_comp = Model_Newswire_CA_Company::find($newswire_ca_c_data->newswire_ca_company_id);
				
		else
		{
			$is_new_comp = 1;
			$newswire_ca_comp = new Model_Newswire_CA_Company();
			$newswire_ca_comp->name = $company_name;
			$newswire_ca_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
			$newswire_ca_comp->newswire_ca_category_id = $newswire_ca_pr->newswire_ca_category_id;
			$newswire_ca_comp->save();

			$newswire_ca_c_data = new Model_Newswire_CA_Company_Data();
			$newswire_ca_c_data->newswire_ca_company_id = $newswire_ca_comp->id;
		}		

		if ($is_new_comp || empty($newswire_ca_c_data->email))
		{
			$newswire_ca_c_data->email = value_or_null($email);
			if (!empty($email))
				$newswire_ca_c_data->is_email_from_pr_text = 1;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->newswire_ca_org_link))
			$newswire_ca_c_data->newswire_ca_org_link = value_or_null($newswire_ca_org_link);

		if ($is_new_comp || empty($newswire_ca_c_data->address) && !empty($address))
			$newswire_ca_c_data->address = value_or_null($address);

		if ($is_new_comp || empty($newswire_ca_c_data->website))
		{
			$newswire_ca_c_data->website = value_or_null(@$website);
			$newswire_ca_c_data->website_source = $website_source;
			if ($website_source == Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_EMAIL_DOMAIN_WORD_MATCHING
				|| $website_source == Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_DOMAIN_MATCHING)
				$newswire_ca_c_data->is_website_valid = 0;
			else
				$newswire_ca_c_data->is_website_valid = 1;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->phone_num))
		{
			$newswire_ca_c_data->phone = value_or_null($phone_num);
			if (!empty($phone_num))
				$newswire_ca_c_data->is_phone_from_pr_text = 1;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->logo_image_path))
		{
			$newswire_ca_c_data->logo_image_path = value_or_null($img_src);
			if (!empty($img_src) && !@$is_logo_from_pr_text)
				$newswire_ca_c_data->is_logo_valid = 1;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->soc_fb) && !empty($soc_fb))
		{
			$newswire_ca_c_data->soc_fb = value_or_null($soc_fb);
			$newswire_ca_c_data->soc_fb_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->soc_twitter) && !empty($soc_twitter))
		{
			$newswire_ca_c_data->soc_twitter = value_or_null($soc_twitter);
			$newswire_ca_c_data->soc_twitter_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->soc_linkedin) && !empty($soc_linkedin))
			$newswire_ca_c_data->soc_linkedin = value_or_null($soc_linkedin);

		if ($is_new_comp || empty($newswire_ca_c_data->soc_pinterest) && !empty($soc_pinterest))
		{
			$newswire_ca_c_data->soc_pinterest = value_or_null($soc_pinterest);
			$newswire_ca_c_data->soc_pinterest_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->soc_youtube) && !empty($soc_youtube))
		{
			$newswire_ca_c_data->soc_youtube = value_or_null($soc_youtube);
			$newswire_ca_c_data->soc_youtube_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if ($is_new_comp || empty($newswire_ca_c_data->soc_gplus) && !empty($soc_gplus))
		{
			$newswire_ca_c_data->soc_gplus = value_or_null($soc_gplus);
			$newswire_ca_c_data->soc_gplus_feed_status = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		}

		$newswire_ca_c_data->save();		

		$newswire_ca_pr = Model_PB_Newswire_CA_PR::find($newswire_ca_pr->content_id);
		$newswire_ca_pr->newswire_ca_company_id = $newswire_ca_comp->id;

		$newswire_ca_pr->cover_image_url = value_or_null($cover_image_url);		
		$newswire_ca_pr->save();

		$this->inspect("Old date was => {$newswire_ca_comp->date_last_pr_submitted}");
		$this->recheck_for_prn_sop($newswire_ca_comp);
	}

	protected function recheck_for_prn_sop($newswire_ca_comp)
	{
		$this->inspect("UPDATING LATEST PR DATE for Newswire.CA Comp = {$newswire_ca_comp->id}");

		$this->update_latest_pr_date($newswire_ca_comp);
		$this->update_newswire_ca_prn_valid($newswire_ca_comp);

		if ($prweb_comp = Model_PRWeb_Company::find('name', $newswire_ca_comp->name))
		{
			$this->inspect("UPDATING PRWEB COMPANY VALID FOR Comp = {$prweb_comp->id}");
			$this->update_prweb_prn_valid($prweb_comp);
		}

	}

	protected function extract_website($pr_body, $company_name)
	{
		$pr_body = preg_replace('/<\/?img(.|\s)*?>/', '', $pr_body); // stripping the img tag
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
						$website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_ABBREVIATION;
						$web = array('website' => $website, 'website_source' => $website_source);
						// print_r($web);
						return $web;
					}

					$website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_WORD_MATCHING;
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
			 //echo "<hr>".$domain;

			foreach ($c_words as $c_word)
				if (strstr($domain, $c_word))
				{
					$website = "http://{$domain}";
					$website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_EMAIL_DOMAIN_WORD_MATCHING;
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
				$website_source = Model_Newswire_CA_Company_Data::WEBSITE_SOURCE_DOMAIN_MATCHING;
				$web = array('website' => $website, 'website_source' => $website_source);
				return $web;
			}
		}
	}

	public function update_latest_pr_dates()
	{
		$cnt = 1;
		
		$sql = "SELECT * 
				FROM ac_nr_newswire_ca_company
				WHERE is_last_pr_date_updated = 0
				ORDER BY id DESC
				LIMIT 20";

		while (1)
		{
			$results = Model_Newswire_CA_Company::from_sql_all($sql);
			if (!count($results)) break;

			foreach ($results as $nw_ca_comp)
			{
				$this->console($nw_ca_comp->id);
				$this->update_latest_pr_date($nw_ca_comp);
			}
		}
	}

	protected function update_latest_pr_date($nw_ca_comp)
	{
		$sql = "SELECT c.date_publish
				FROM nr_pb_newswire_ca_pr pb
				INNER JOIN nr_content c
				ON pb.content_id = c.id
				WHERE pb.newswire_ca_company_id = ?
				ORDER BY c.date_publish DESC
				LIMIT 1";

		$date_publish = null;
		if ($content = Model::from_sql($sql, array($nw_ca_comp->id)))		
			$date_publish = $content->date_publish;

		$nw_ca_comp->date_last_pr_submitted = value_or_null($date_publish);
		$nw_ca_comp->is_last_pr_date_updated = 1;
		$nw_ca_comp->is_last_pr_date_migrated = 0;
		$nw_ca_comp->save();
	}


	public function check_prn_valid_leads()
	{
		$cnt = 1;

		$sql = "SELECT c.*
				FROM ac_nr_newswire_ca_company c
				LEFT JOIN ac_nr_prn_valid_company pvc
				ON pvc.source_company_id = c.id
				AND pvc.source = ?
				WHERE pvc.source_company_id IS NULL
				AND c.is_last_pr_date_updated = 1
				ORDER BY c.id
				LIMIT 50";

		while (1)
		{
			$results = Model_Newswire_CA_Company::from_sql_all($sql, array(Model_PRN_Valid_Company::SOURCE_NEWSWIRE_CA));
			if (!count($results)) break;

			foreach ($results as $nw_ca_comp)
			{
				$this->console($nw_ca_comp->id);
				$this->update_newswire_ca_prn_valid($nw_ca_comp);			
			}
		}
	}	
}