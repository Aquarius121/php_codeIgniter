<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Auto_Create_NR_Base extends CLI_Base { 
	
	protected function get_web_url($url) 
	{ 
		$options = array( 
			CURLOPT_RETURNTRANSFER => true,     // return web page 
			CURLOPT_HEADER         => true,    // return headers 
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
			CURLOPT_ENCODING       => "",       // handle all encodings 
			CURLOPT_USERAGENT      => "spider", // who am i 
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect 
			CURLOPT_TIMEOUT        => 120,      // timeout on response 
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects 
		); 

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		$err = curl_errno($ch);
		$errmsg = curl_error($ch);
		$header = curl_getinfo($ch);
		curl_close($ch);		
		return $header; 
	}


	protected function extract_email_address($html) 
	{
		$email = "";
		$pattern = '/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/i';
		$pattern_wrong = '/(.*)(\.png)|(\.gif)|(\.jpg)|(\.bmp)|(\.js)$/i'; // make sure its not an image
		if (preg_match_all($pattern, $html, $matches))
		{
			foreach ($matches as $i => $match_array)
				foreach ($match_array as $match)
					if (! preg_match($pattern_wrong, $match, $email_matches))
					{
						$email = $match; // . "<br>";
						break;
					}
		} 

		return $email;
	}


	protected function extract_phone_number($text)
	{
		$pattern = '#\(?[0-9]{3}\)?[\s|\-|.]\s?[0-9]{3}\s?-?\s?.?[0-9]{4}#';
		
		preg_match($pattern, $text, $match);

		$phone_num = "";

		if (is_array($match) && count($match))
		{
			$phone_num = $match[0];
		}

		if (!empty($phone_num))
			return $phone_num;

		// if phone number not found 
		// why not try another regex

		$pattern = "/(\d|\+|\()(\+|\d|\(|\)|-| |\/){7,}(\d|\))+\b/i";
		
		preg_match($pattern, $text, $match);

		if (is_array($match) && count($match))
			$phone_num = $match[0];

		if (!empty($phone_num))
			return $phone_num;

		// if phone number not found 
		// why not try another regex

		$pattern = '#\+?[0-9]{1,3}?[\ |\s|\–|\-|.]\(?[0-9]{1,3}?\)?[\ |\s|\–|\-|.]?[0-9]{2,4}[\s|\–|\-|.]\s?[0-9]{2,4}[\s|\–|\-|.]\s?[0-9]{2,4}[\s|\–|\-|.]\s?[0-9]{2,4}#';

		preg_match($pattern, $text, $match);

		if (is_array($match) && count($match))
			$phone_num = $match[0];

		if (!empty($phone_num))
			return $phone_num;

		// if phone number not found
		// we will try one more pattern 
		// e.g. +39. 011 88 10 111

		$pattern = '#\+?[0-9]{1,3}?[\ |\s|\–|\-|.][\ |\s|\–|\-|.]\(?[0-9]{1,3}?\)?[\ |\s|\–|\-|.]?[0-9]{2,3}[\s|\–|\-|.]\s?[0-9]{2,4}[\s|\–|\-|.]\s?[0-9]{2,4}[\s|\–|\-|.]\s?[0-9]{2,4}#';

		preg_match($pattern, $text, $match);

		if (is_array($match) && count($match))
			$phone_num = $match[0];

		if (!empty($phone_num))
			return $phone_num;

		// if phone number not found
		// we will try one more pattern 
		// e.g. +31 35 6299 211

		$pattern = '#\+[0-9]{1,3}[\ |\s|\–|\-|.]\(?[0-9]{1,3}?\)?[\ |\s|\–|\-|.]?[0-9]{3,4}[\s|\–|\-|.]\s?[0-9]{2,4}#';

		preg_match($pattern, $text, $match);
		
		if (is_array($match) && count($match))
			$phone_num = $match[0];

		return $phone_num;
	}

	public function extract_socials($urls = null)
	{
		$socials = array();

		if (!is_array($urls) || !count($urls))
			return $socials;

		$pattern_fb = '/facebook.com/';
		$pattern_twitter = '/twitter.com/';
		$pattern_linkedin = '/linkedin.com/';
		$pattern_pinterest = '/pinterest.com/';
		$pattern_youtube = '/youtube.com/';
		$pattern_gplus = '/plus.google.com/';
		$pattern_vimeo = '/vimeo.com/';
		$pattern_instagram = '/instagram.com/';

		$socials = array();
		foreach($urls as $url)
		{
			if (preg_match($pattern_fb, $url, $match)) 
			{
				$soc_fb = $url;
				$soc_fb = Social_Facebook_Profile::parse_id($soc_fb);
				if (!empty($soc_fb))
					$socials['soc_fb'] = $soc_fb;
			}

			if (preg_match($pattern_twitter, $url, $match)) 
			{
				$soc_twitter = $url;
				$soc_twitter = Social_Twitter_Profile::parse_id($soc_twitter);
				if (!empty($soc_twitter))
					$socials['soc_twitter'] = $soc_twitter;
			}

			if (preg_match($pattern_linkedin, $url, $match)) 
			{
				$soc_linkedin = $url;
				$soc_linkedin = Social_Linkedin_Profile::parse_id($soc_linkedin);
				if (!empty($soc_linkedin))
					$socials['soc_linkedin'] = $soc_linkedin;
			}

			if (preg_match($pattern_pinterest, $url, $match)) 
			{
				$soc_pinterest = $url;
				$soc_pinterest = Social_Pinterest_Profile::parse_id($soc_pinterest);
				if (!empty($soc_pinterest))
					$socials['soc_pinterest'] = $soc_pinterest;
			}

			if (preg_match($pattern_youtube, $url, $match)) 
			{
				$soc_youtube = $url;
				$soc_youtube = Social_Youtube_Profile::parse_id($soc_youtube);
				if (!empty($soc_youtube) && $soc_youtube !== "watch"  && $soc_youtube !== "embed")
					$socials['soc_youtube'] = $soc_youtube;
			}

			if (preg_match($pattern_gplus, $url, $match)) 
			{
				$soc_gplus = $url;
				$soc_gplus = Social_GPlus_Profile::parse_id($soc_gplus);
				if (!empty($soc_gplus))
					$socials['soc_gplus'] = $soc_gplus;
			}

			if (preg_match($pattern_vimeo, $url, $match)) 
			{
				$soc_vimeo = $url;
				$soc_vimeo = Social_Vimeo_Profile::parse_id($soc_vimeo);
				if (!empty($soc_vimeo))
					$socials['soc_vimeo'] = $soc_vimeo;
			}

			
			// TODO: Instagram is not on this branch
			// Enable when the instagram branch is merged.
			if (preg_match($pattern_instagram, $url, $match)) 
			{
				$soc_instagram = $url;
				$soc_instagram = Social_Instagram_Profile::parse_id($soc_instagram);
				if (!empty($soc_instagram))
					$socials['soc_instagram'] = $soc_instagram;
			}
			
		}

		return $socials;

	}

	protected function extract_logo($html, $url)
	{
		$logo = null;

		foreach($html->find('img') as $element)
		{
			$src = $element->src;
			$alt = $element->alt;
			if ( ! empty($src))
			{
				$pattern = "/logo/i";
				if (preg_match($pattern, $src, $match) || preg_match($pattern, $alt, $match))				
				{
					$logo = $src;
					break;
				}
			}	
		}
		

		if ( ! empty($logo))
		{
			if (substr(trim($logo), 0, 4) !== "http")
			{
				if (substr($logo, 0, 1) == '/')
					$logo = substr($logo, 1);
				
				if (substr($url, strlen($url)-1, 1) == "/" )
					$logo = "{$url}{$logo}";
				else
					$logo = "{$url}/{$logo}";
			}
		}

		return $logo;

	}

	protected function get_web_address($website = null)
	{
		if (empty($website))
			return null;

		$arr = array(",", "-", "'");
		$website = str_replace($arr, "", $website);

		$info = parse_url($website);
		$host = $info['host'];
		$website = $info['scheme']."://".$info['host'];
		
		return $website;
	}

	public function sanitize($text, $keep_tags = false)
	{
		if (empty($text))
			return null;

		$allowed_tags = array();
		if ($keep_tags)
		{
			$allowed_tags = array('<p>', '<br>', '<hr>', '<ul>', '<ol>', '<li>', '<b>', '<strong>', '<em>', '<i>',
				'<u>', '<sup>', '<sub>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>',
				'<span>', '<header>', '<footer>', '<section>', '<article>', '<summary>',
				'<blockquote>', '<pre>', '<small>', '<big>', '<dl>', '<dd>', '<dt>', '<menu>', '<caption>', 
				'<table>', '<thead>', '<tbody>', '<tr>', '<th>', '<td>', '<tfoot>', '<col>', '<colgroup>' 
				 );
		}

		$text = HTML2Text::plain($text, true, $allowed_tags);
		$text = trim($text);
		$text = html_entity_decode($text);
		$text = htmlspecialchars_decode($text, ENT_QUOTES);
		$text = str_replace('&nbsp;', ' ', $text);
		$text = str_replace('&amp;', '&', $text);

		// One of the sites had meta like: 
		// <meta name="description" content="&lt;meta name=&quot;p:domain_verify&quot; 
		//  content=&quot;f8399d90c86d53d6ce39d43ca796b3ad&quot;/&gt;" />
		$text = HTML2Text::plain($text, true, $allowed_tags);

		// purifying the HTML if formatting is maintained
		$text = $this->vd->pure($text);

		return $text;
	}

	public function set_topics($content_id, $topics)
	{
		if (empty($content_id) || !is_array($topics))
			return;

		$this->db->query("DELETE FROM ac_nr_mynewsdesk_content_topic 
			WHERE content_id = ?", array($content_id));
		
		foreach ($topics as $topic)
		{
			if (!($topic = trim($topic))) continue;
			$this->db->query("INSERT IGNORE INTO ac_nr_mynewsdesk_content_topic (content_id, 
				value) VALUES (?, ?)", array($content_id, $topic));
		}
	}

	public function find_complete_url($website, $contact_page_slug)
	{
		if (substr($contact_page_slug, 0, 4) == "http")
			return $contact_page_slug;

		if (substr($contact_page_slug, 0, 1) == "/")
			$contact_page_slug = substr($contact_page_slug, 1);

		$m = parse_url($website);
		$website = "http://".$m['host'];

		$complete_url = "{$website}/{$contact_page_slug}";
		return $complete_url;
	}

	protected function make_db_date($publish_date = null)
	{
		if (empty($publish_date))
			return null;

		$publish_date = str_replace("&nbsp;", "", $publish_date);
		$publish_date = trim($publish_date);
		$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));

		return $publish_date;
	}


	public function notify_migration_failure($nr_source)
	{
		$date_4_days_ago = Date::days(-4, Date::$now->format(Date::FORMAT_MYSQL));
		$date_1_day_ago = Date::days(-1, Date::$now->format(Date::FORMAT_MYSQL));

		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration
				WHERE data_source = ?
				AND date_migrated_to_newswire > '$date_4_days_ago' ";

		$counter = $this->db->query($sql, array($nr_source))->row()->count;
		
		if ($counter)
			return;

		// If no data has been migrated for the last
		// 4 days, we are going to notify the admin
		
		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration_failure_notify
				WHERE data_source = ?
				AND date_notified > '$date_1_day_ago' ";

		$counter = $this->db->query($sql, array($nr_source))->row()->count;
		
		if ($counter)
			return;

		// The admin has not been notified 
		// in last 1 day; notify.

		$alert = new Critical_Alert();
		$nr_source_proper = ucfirst($nr_source);

		$alert->set_subject("{$nr_source_proper} Data Migration Failed");
		$text = "Data migration has failed for {$nr_source_proper} scrapper. ";
		$text .= ' No data has been transferred for the last 4 days from dev site to newswire. ';
		$text .= ' Please take corrective action';
		$alert->set_content($text);
		$alert->send();

		$notify = new Model_Scrapped_Data_Migration_Failure_Notify();
		$notify->data_source = $nr_source;
		$notify->date_notified = Date::$now->format(Date::FORMAT_MYSQL);
		$notify->save();
	}

	public function youtube_id_from_url($url) {
		$pattern = 
			'%^# Match any youtube URL
			(?:https?://)?  # Optional scheme. Either http or https
			(?:www\.)?      # Optional www subdomain
			(?:             # Group host alternatives
			  youtu\.be/    # Either youtu.be,
			| youtube\.com  # or youtube.com
			  (?:           # Group path alternatives
				/embed/     # Either /embed/
			  | /v/         # or /v/
			  | /watch\?v=  # or /watch\?v=
			  )             # End path alternatives.
			)               # End host alternatives.
			([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
			$%x'
			;
		$result = preg_match($pattern, $url, $matches);
		if (false !== $result) {
			return $matches[1];
		}

		return false;
	}

	// get a proxy for owler scrapping
	protected function get_owler_proxy($config_proxy)
	{
		$sql = "SELECT * 
				FROM ac_nr_proxy 
				WHERE id > ?
				AND is_enabled = 1
				AND type = 'socks5'
				ORDER BY id";

		$query = $this->db->query($sql, array($config_proxy->value));

		if (!$query->num_rows())
			$query = $this->db->query($sql, array(0));

		$proxy = $query->row();
		return $proxy;
	}	

	protected function process_domainiq_whois($results, $source = null)
	{
		if (!count($results) || !$source)
			return false;

		foreach ($results as $c_data)
		{
			$domain = $this->get_domain($c_data->website);

			$url = "http://www.domainiq.com/api?key=i1ds9f9wnkh9cphgigr3emgy6n68fwfe&service=domain_report";
			$url = "{$url}&domain={$domain}&output_mode=json";

			$response = Unirest\Request::get($url);

			if (empty($response->raw_body))
				return false;

			$js_response = json_decode($response->raw_body);

			// domainiq does not return any specific 
			// error code, just error status
			if (!empty($js_response->error) && 
				!String_Util::contains($js_response->error, 'is not currently supported') &&
				!String_Util::contains($js_response->error, 'Invalid domain name'))
			{
				var_dump($js_response);
				die();
			}			

			$email = $js_response->data->whois->emails[0];

			if (!empty($email))
			{
				$c_data->email = $email;
				$c_data->save();
			}

			$diq = new Model_Whois_Check_Domainiq();
			$diq->source_company_id = $c_data->source_company_id;
			$diq->source = $source;
			$diq->is_api_read_success = 1;
			if (!empty($email))
				$diq->is_email_fetched = 1;

			$diq->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
			$diq->save();
			sleep(1);
		}
	}

	protected function process_domainindex_whois($results, $source = null)
	{
		$domains = array();
		foreach ($results as $result)
		{
			if ($domain = $this->get_domain($result->website))
				$domains[] = $domain;
		}

		if (is_array($domains) && count($domains))
			$domain_string = implode(",", $domains);
		else
			exit;

		$url = "http://domainindex.com/api.php?action=whois";
		$url = "{$url}&domain={$domain_string}";
		$url = "{$url}&key=6afb79e2-2015-0514-0446-0521b050fb15&mode=xml";

		$xml_results = new SimpleXMLElement($url, null, true);

		// we are not terminating on invalid domain error
		// as there are many TLDs not handled by domainindex
		if (!empty($xml_results->error) && trim($xml_results->error) !== "Please enter one or more valid domains")
		{
			$this->log($xml_results);
			exit;
		}

		foreach ($results as $result)
		{
			if ($email = $this->find_email($result->website, $xml_results))
			{
				if (!empty($email))
				{
					$email = str_replace("(", "", $email);
					$email = str_replace(")", "", $email);
					$result->email = $email;
					$result->save();
				}
			}

			$di = new Model_Whois_Check_Domainindex();

			$di->source_company_id = $result->source_company_id;
			$di->source = $source;
			$di->is_api_read_success = 1;
			if (!empty($email))
				$di->is_email_fetched = 1;

			$di->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
			$di->save();
		}
	}

	protected function find_email($website, $xml_results)
	{
		$website = $this->get_domain($website);
		
		foreach($xml_results as $xml_result) // loop through results
		{
			if (trim(strtolower($xml_result->domain)) == trim(strtolower($website))
				&& !empty($xml_result->admin_email))
				return $xml_result->admin_email;
		}

		return false;
	}

	public function fetch_contact_us_url($c_data, $source = null)
	{
		if (empty($c_data->website))
			return false;

		$url = $c_data->website;
		
		$request = new HTTP_Request($url);
		$request->enable_redirects();
		$response = $request->get();

		$html = str_get_html($response->data);

		$m_f_contact = new Model_Fetch_Contact_Us_URL();
		$m_f_contact->source_company_id = $c_data->source_company_id;
		$m_f_contact->source = $source;
		$m_f_contact->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$m_f_contact->is_website_read_success = 0;
			$m_f_contact->save();
			return;
		}

		$m_f_contact->is_website_read_success = 1;

		// now reading the contact page url

		$contact_pattern = '/contact/i';
		$contact_page_slug = null;

		foreach($html->find('a') as $element)
		{
			$href = $element->href;
			
			if (preg_match($contact_pattern, $href, $match) && !$this->extract_email_address($href))
				$contact_page_slug = $href;
		}


		if (!empty($contact_page_slug))
		{
			$contact_page_web_url = $this->find_complete_url($c_data->website, $contact_page_slug);
			$m_f_contact->is_contact_page_url_found = 1;
			
			$c_data->contact_page_url = $contact_page_web_url;
			$c_data->save();
		}

		$m_f_contact->save();
		
	}

	protected function get_domain($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,20})$/i', $domain, $regs)) 
			return $regs['domain'];
		
		return false;
	}

	// linkify() taken from
	// https://gist.github.com/jasny/2000705
	protected function linkify($value, $protocols = array('http', 'mail'), array $attributes = array())
	{
		// Link attributes
		$attr = '';
		foreach ($attributes as $key => $val) {
			$attr = ' ' . $key . '="' . htmlentities($val) . '"';
		}
		
		$links = array();
		
		// Extract existing links and tags
		$value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', 
			function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $value);
		
		// Extract text links for each protocol
		foreach ((array)$protocols as $protocol) {
			switch ($protocol) {
				case 'http':
				case 'https':   $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', 
					function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; 
						return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>'; }, $value); break;
				
				case 'twitter': $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', 
					function ($match) use (&$links, $attr) { return '<' . array_push($links, 
						"<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . 
						"\">{$match[0]}</a>") . '>'; }, $value); break;
				
				default: $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', 
					function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr 
						href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
			}
		}
		
		// Insert all link
		$text = preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $value);

		// The mail part of this function 
		// doesn't seem to work correctly, 
		// so used another function call 
		if (in_array('mail', $protocols))
			$text = $this->linkify_emails($text);

		return $text;
	}

	// linkifyEmails() taken from
	// https://www.versioneye.com/php/nahid:linkify/1.0.0
	protected function linkify_emails($text, $options = array('attr' => ''))
    {
        $pattern = '~(?xi)
                \b
                (?<!=)           # Not part of a query string
                [A-Z0-9._\'%+-]+ # Username
                @                # At
                [A-Z0-9.-]+      # Domain
                \.               # Dot
                [A-Z]{2,4}       # Something
        ~';

        $callback = function ($match) use ($options) {
            if (isset($options['callback'])) {
                $cb = $options['callback']($match[0], $match[0], true);
                if (!is_null($cb)) {
                    return $cb;
                }
            }

            return '<a href="mailto:' . $match[0] . '"' . $options['attr'] . '>' . $match[0] . '</a>';
        };

        return preg_replace_callback($pattern, $callback, $text);
    }


	protected function fetch_url_through_phantomjs($url)
	{
		$phantom_script = "raw/phantom_fetch_page.js";
		
		$command = "phantomjs --load-images=false {$phantom_script} {$url}";

		$response = exec($command, $output);

		$text = "";
 		if (is_array($output))
 			foreach ($output as $o)
 				$text = "{$text} $o";
 		else
 			$text = $output;

 		return $text;
	}

	protected function url_protocol($url = null)
	{
		if (!$url)
			return null;

		$url = trim($url);

		if (substr($url, 0, 4) == "http")
			return $url;

		if (substr($url, 0, 2) == "//")
			return "http:{$url}";

		return "http://{$url}";
	}

	protected function nr_build_socials(&$company_profile, &$c_data)
	{
		$social_wire_settings = new stdClass();
		if (!empty($c_data->soc_fb) && $c_data->soc_fb_feed_status == Model_BusinessWire_Company_Data::SOCIAL_VALID)
		{
			$company_profile->soc_facebook = $c_data->soc_fb;
			$social_wire_settings->soc_facebook_is_feed_valid = 1;
			$social_wire_settings->is_inc_facebook_in_soc_wire = 1;
		}
		elseif (!empty($c_data->soc_fb))
			$company_profile->soc_facebook = $c_data->soc_fb;
		
		if (!empty($c_data->soc_twitter) && $c_data->soc_twitter_feed_status == Model_BusinessWire_Company_Data::SOCIAL_VALID)
		{
			$company_profile->soc_twitter = $c_data->soc_twitter;
			$social_wire_settings->soc_twitter_is_feed_valid = 1;
			$social_wire_settings->is_inc_twitter_in_soc_wire = 1;
		}
		elseif (!empty($c_data->soc_twitter))
			$company_profile->soc_twitter = $c_data->soc_twitter;

		if (!empty($c_data->soc_gplus) && $c_data->soc_gplus_feed_status == Model_BusinessWire_Company_Data::SOCIAL_VALID)
		{
			$company_profile->soc_gplus = $c_data->soc_gplus;
			$social_wire_settings->soc_gplus_is_feed_valid = 1;
			$social_wire_settings->is_inc_gplus_in_soc_wire = 1;
		}
		elseif (!empty($c_data->soc_gplus))
			$company_profile->soc_gplus = $c_data->soc_gplus;

		if (!empty($c_data->soc_youtube) && $c_data->soc_youtube_feed_status == Model_BusinessWire_Company_Data::SOCIAL_VALID)
		{
			$company_profile->soc_youtube = $c_data->soc_youtube;
			$social_wire_settings->soc_youtube_is_feed_valid = 1;
			$social_wire_settings->is_inc_youtube_in_soc_wire = 1;
		}
		elseif (!empty($c_data->soc_youtube))
			$company_profile->soc_youtube = $c_data->soc_youtube;

		if (!empty($c_data->soc_pinterest) && $c_data->soc_pinterest_feed_status == Model_BusinessWire_Company_Data::SOCIAL_VALID)
		{
			$company_profile->soc_pinterest = $c_data->soc_pinterest;
			$social_wire_settings->soc_pinterest_is_feed_valid = 1;
			$social_wire_settings->is_inc_pinterest_in_soc_wire = 1;
		}
		elseif (!empty($c_data->soc_pinterest))
			$company_profile->soc_pinterest = $c_data->soc_pinterest;
		
		$company_profile->soc_linkedin = $c_data->soc_linkedin;
		$social_wire_settings->soc_linkedin_is_feed_valid = 1;

		$company_profile->raw_data_write('social_wire_settings', $social_wire_settings);
	}

	protected function update_company_source($company_id, $source)
	{
		$inactive_sources = Model_Company::scraping_sources_inactive_by_default();

		$comp = Model_Company::find($company_id);
		
		$comp->newsroom_is_active = 1;
		if (in_array($source, $inactive_sources))
			$comp->newsroom_is_active = 0;

		$comp->source = $source;
		$comp->save();
	}

	protected function generate_token($company_id)
	{
		$token = new Model_Newsroom_Claim_Token();
		$token->company_id = $company_id;
		$token->generate();
		$token->save();
	}

	protected function extract_contact_name($contact_info = null, $company_name = null)
	{
		if (empty($contact_info))
			return null;

		$ignore_words = array(trim(strtolower($company_name)), "media", "contact", "investor", "marketing", 
			"connect", "information", "email", "inc.", "inquiry", "inquiries", "enquiry", "enquiries", 
			"headquarter", "communication", "customer", "service", "consult", "website", "www.", "twitter", 
			"facebook", "instagram", "financial", "corporation", "specialist", "executive", "query", "queries",
			"http", "linkedin");

		$honorifics = array('ms', 'miss', 'mrs', 'mr', 'fr', 'dr', 'atty', 'prof', 'hon', 'pres', 'ofc', 
			'sr', 'br', 'supt', 'maj', 'capt', 'cmdr', 'lt', 'col', 'gen', 'ceo');

		$contact_name = null;

		$lines = explode("\n", $contact_info);

		foreach ($lines as $line)
		{
			$line = trim($line);
			if (empty($line) || strlen($line) < 3)
				continue;

			$line_low = strtolower($line);			

			foreach ($ignore_words as $ignore_word)
			{
				if (String_Util::contains($line_low, $ignore_word))
					continue 2;
			}

			if ($this->extract_email_address($line))
				continue;

			if ($this->extract_phone_number($line))
				continue;

			$words = explode(" ", $line_low);
			foreach ($words as $word)
			{
				if ((int) $word > 0)
					continue 2;

				foreach ($honorifics as $honorific)
					if ($word == $honorific || $word == "{$honorific}.")
					{
						$contact_name = $line;
						break 3;
					}
			}

			if (count($words) == 1 || count($words) > 3)
				continue;

			$contact_name = $line;
			break;
		}

		return $contact_name;
	}

	protected function remove_prnewswire_dup_prs()
	{
		$cnt = 1;

		$sql = "SELECT title, COUNT(title)
				FROM nr_content c
				INNER JOIN nr_pb_prn_pr p
				ON p.content_id = c.id
				GROUP BY title 
				Having COUNT(title) > 1 
				ORDER BY count(title) DESC 
				LIMIT 50";

		while (1)
		{
			$results = Model_Content::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $m_content)
				$this->remove_prnewswire_dup_pr($m_content);
		}
	}

	protected function remove_prnewswire_dup_pr($m_content)
	{
		$sql_all = "SELECT id 
					FROM nr_content c
					WHERE title = ?
					ORDER BY id";

		$result_all = $this->db->query($sql_all, array($m_content->title));
		$recs = Model_Content::from_db_all($result_all);

		$ids_to_del = array();
		$first_id = null;
		foreach ($recs as $i => $rec)
		{
			if ($i == 0)
				$first_id = $rec->id;

			else
				$ids_to_del[] = $rec->id;
		}

		if (!count($ids_to_del))
			continue;

		$str_ids_to_del = sql_in_list($ids_to_del);

		$sql_content = "DELETE FROM nr_content
						WHERE id IN ({$str_ids_to_del})";
		$this->db->query($sql_content);

		$sql_c_data = "DELETE FROM nr_content_data
						WHERE content_id IN ({$str_ids_to_del})";
		$this->db->query($sql_c_data);
		

		$sql_pb_pr = "DELETE FROM nr_pb_pr
						WHERE content_id IN ({$str_ids_to_del})";
		$this->db->query($sql_pb_pr);

		$sql_pb_sc = "DELETE FROM nr_pb_scraped_content
						WHERE content_id IN ({$str_ids_to_del})";
		$this->db->query($sql_pb_sc);

		$sql_pb_prn = "DELETE FROM nr_pb_prn_pr
						WHERE content_id IN ({$str_ids_to_del})";
		$this->db->query($sql_pb_prn);
	}

	protected function update_prweb_prn_valid($prweb_comp)
	{
		$criteria = array();
		$criteria[] = array('source_company_id', $prweb_comp->id);
		$criteria[] = array('source', Model_PRN_Valid_Company::SOURCE_PRWEB);
		
		if (!$prn_v_comp = Model_PRN_Valid_Company::find($criteria))
			$prn_v_comp = new Model_PRN_Valid_Company();

		$prn_v_comp->source_company_id = $prweb_comp->id;
		$prn_v_comp->source = Model_PRN_Valid_Company::SOURCE_PRWEB;
		$prn_v_comp->date_checked = Date::$now->format(DATE::FORMAT_MYSQL);

		$date_6_mon_ago = Date::months(-6)->format(Date::FORMAT_MYSQL);
		$sql = "SELECT pc.id
				FROM ac_nr_prweb_company pc
				LEFT JOIN ac_nr_newswire_ca_company nc
				ON pc.name = nc.name
				LEFT JOIN ac_nr_prn_company prnc
				ON pc.name = prnc.name
				WHERE pc.id = '{$prweb_comp->id}'
				AND (pc.date_last_pr_submitted >= '{$date_6_mon_ago}'
					OR (nc.id IS NOT NULL AND nc.date_last_pr_submitted >= '{$date_6_mon_ago}')
					OR (prnc.id IS NOT NULL AND prnc.date_last_pr_submitted >= '{$date_6_mon_ago}'))";

		
		if ($result = Model::from_sql($sql)) {}
		else
		{
			$prn_v_comp->is_prn_valid_lead = 1;
			$prn_v_comp->date_till_lead_valid = Date::days(+14)->format(Date::FORMAT_MYSQL);
		}

		$sql = "SELECT pc.id,
				pc.date_last_pr_submitted AS date_last_prweb_pr,
				nc.date_last_pr_submitted AS date_last_newswire_ca_pr,
				prnc.date_last_pr_submitted AS date_last_prn_pr
				FROM ac_nr_prweb_company pc
				LEFT JOIN ac_nr_newswire_ca_company nc
				ON pc.name = nc.name
				LEFT JOIN ac_nr_prn_company prnc
				ON pc.name = prnc.name
				WHERE pc.id = '{$prweb_comp->id}'";

		$d_rec = Model::from_sql($sql);
		
		$prn_v_comp->date_last_prweb_pr = value_or_null($d_rec->date_last_prweb_pr);
		$prn_v_comp->date_last_newswire_ca_pr = value_or_null($d_rec->date_last_newswire_ca_pr);
		$prn_v_comp->date_last_prn_pr = value_or_null($d_rec->date_last_prn_pr);
		$prn_v_comp->is_migrated_to_live_site = 0;
		$prn_v_comp->save();
	}

	protected function update_newswire_ca_prn_valid($nw_ca_comp)
	{
		$criteria = array();
		$criteria[] = array('source_company_id', $nw_ca_comp->id);
		$criteria[] = array('source', Model_PRN_Valid_Company::SOURCE_NEWSWIRE_CA);
		
		if (!$prn_v_comp = Model_PRN_Valid_Company::find($criteria))
			$prn_v_comp = new Model_PRN_Valid_Company();

		$prn_v_comp->source_company_id = $nw_ca_comp->id;
		$prn_v_comp->source = Model_PRN_Valid_Company::SOURCE_NEWSWIRE_CA;
		$prn_v_comp->date_checked = Date::$now->format(DATE::FORMAT_MYSQL);

		$date_6_mon_ago = Date::months(-6)->format(Date::FORMAT_MYSQL);
		$sql = "SELECT pc.id
				FROM ac_nr_newswire_ca_company nc
				LEFT JOIN ac_nr_prweb_company pc
				ON nc.name = pc.name
				LEFT JOIN ac_nr_prn_company prnc
				ON nc.name = prnc.name
				WHERE nc.id = '{$nw_ca_comp->id}'
				AND (nc.date_last_pr_submitted >= '{$date_6_mon_ago}'
					OR (pc.id IS NOT NULL AND nc.date_last_pr_submitted >= '{$date_6_mon_ago}')
					OR (prnc.id IS NOT NULL AND prnc.date_last_pr_submitted >= '{$date_6_mon_ago}'))";

		
		if ($result = Model::from_sql($sql)) {}
		else
		{
			$prn_v_comp->is_prn_valid_lead = 1;
			$prn_v_comp->date_till_lead_valid = Date::days(+14)->format(Date::FORMAT_MYSQL);
		}

		$sql = "SELECT pc.id,
				pc.date_last_pr_submitted AS date_last_prweb_pr,
				nc.date_last_pr_submitted AS date_last_newswire_ca_pr,
				prnc.date_last_pr_submitted AS date_last_prn_pr
				FROM ac_nr_newswire_ca_company nc
				LEFT JOIN ac_nr_prweb_company pc
				ON nc.name = pc.name
				LEFT JOIN ac_nr_prn_company prnc
				ON nc.name = prnc.name
				WHERE nc.id = '{$nw_ca_comp->id}'";

		$d_rec = Model::from_sql($sql);
		
		$prn_v_comp->date_last_prweb_pr = value_or_null($d_rec->date_last_prweb_pr);
		$prn_v_comp->date_last_newswire_ca_pr = value_or_null($d_rec->date_last_newswire_ca_pr);
		$prn_v_comp->date_last_prn_pr = value_or_null($d_rec->date_last_prn_pr);
		$prn_v_comp->is_migrated_to_live_site = 0;
		$prn_v_comp->save();
	}
}