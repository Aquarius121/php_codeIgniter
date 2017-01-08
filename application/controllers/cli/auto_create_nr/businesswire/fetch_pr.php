<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT * FROM nr_pb_businesswire_pr
				WHERE businesswire_company_id = 0
				ORDER BY content_id
				LIMIT 1";

		while ($cnt++ <= 5)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$businesswire_pr = Model_PB_BusinessWire_PR::from_db($result);
			if (!$businesswire_pr) break;

			$this->get($businesswire_pr);
			sleep(1);
		}
	}

	protected function get($businesswire_pr)
	{
		lib_autoload('simple_html_dom');

		if (empty($businesswire_pr->url))
			return false;

		$text = $this->fetch_url_through_phantomjs($businesswire_pr->url);
		if (empty($text))
			return false;

		$html = str_get_html($text);
		
		if (empty($html))
		{
			$businesswire_pr = Model_PB_BusinessWire_PR::find($businesswire_pr->content_id);
			$businesswire_pr->businesswire_company_id = -1;
			$businesswire_pr->save();
			return;
		}

		$m_content = Model_Content::find($businesswire_pr->content_id);

		if (! $m_c_data = Model_Content_Data::find($businesswire_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $businesswire_pr->content_id;
		}

		$date_line = @$html->find('time[itemprop=dateModified]', 0)->datetime;

		$publish_date = date(DATE::FORMAT_MYSQL, strtotime($date_line));

		$location_line = @$html->find('div[class=bw-release-story] p', 0)->innertext;
		

		preg_match("/(.*)--\((.*)/", $location_line, $match); //--(
		if (is_array($match) && count($match) > 1)
			$address = strip_tags($match[1]);

		$first_para = @$html->find('div[class=bw-release-story] p', 0)->plaintext;
		$fir = explode(")--", $first_para);		
		unset( $fir[0] );
		$summary = trim( implode(")", $fir));

		$summary = $this->sanitize($summary);

		$contact_area = @$html->find('div[class=bw-release-contact]', 0)->plaintext;
		$emails = $this->extract_email_address($contact_area);
		if (is_array($emails) && count($emails))
			$email = $emails[0];
		
		if ( ! empty($contact_area))
		{
			$lines = explode("\n", $contact_area);

			$pattern1 = '#(\+[0-9]*)#'; // format +1
			$pattern2 = '#([0-9]{3}\-[0-9]{4}\-*)#'; // format 123-1234
			$pattern3 = '#(\([0-9]{3}\)\ [0-9]{3}*)#'; // format (416) 299

			for($k=0; $k < count($lines); $k++)
			{
				if (@preg_match($pattern1, $lines[$k], $match) || 
					@preg_match($pattern2, $lines[$k], $match) || @preg_match($pattern3, $lines[$k], $match))
					$phone_num = $lines[$k];
			}

			if (!empty($phone_num))
			{
				$ph = explode(",", $phone_num);
				if (is_array($ph) && count($ph) > 1)
					foreach ($ph as $p)
						if (@preg_match($pattern1, $p, $match) || 
							@preg_match($pattern2, $p, $match) || 
							@preg_match($pattern3, $p, $match))
							$phone_num = $p;
						else
							$contact_name = $p;
				
			}
		}

		if (empty($website))
		{
			$website = @$html->find('div[class=bw-release-logos] a', 0)->href;

			if (!empty($website))
			{
				$info = parse_url($website);
				$host = $info['host'];
				$website = $info['scheme']."://".$info['host'];
			}
		}

		if (empty($website))
		{
			foreach (@$html->find('div.bw-release-companyinfo ul li') as $li)
			{
				if ($strong = $li->find('strong', 0))
				{
					$text = $strong->plaintext;
					if (trim($text) == "Website:")
					{
						$strong->innertext = "";
						$website = $li->plaintext;
						if (!empty($website))
							$website = trim($website);
					}

				}
			}
		}		

		$contact_area = @$html->find('div[class=bw-release-contact] p', 0)->innertext;
		$lines = explode("<br/>", $contact_area);
		foreach ($lines as $line)
		{
			if ($line == strip_tags($line) && empty($company_name))
				$company_name = $line;

			if (substr($line, 0, 4) == "Web:")
			{
				$website = substr($line, 5);
				$website = strip_tags($website);
			}
			
		}

		if (empty($company_name))
			if ($comp_info = @$html->find('div.bw-release-companyinfo', 0))
				if ($span_name = $comp_info->find('span[itemprop=name]', 0))
					$company_name = $span_name->innertext;
			

		if (!empty($company_name))
		{
			$company_name = trim($company_name);
			$company_name = $this->sanitize($company_name);
		}

		if (!$logo_image_path = @$html->find('div[class=bw-release-logos] a img', 0)->src)
			$logo_image_path = @$html->find('div[class=bw-release-logos] img', 0)->src;

		if (!empty($logo_image_path))
			$logo_image_path = str_replace("/2/", "/1/", $logo_image_path); // Because /1/ is the original image

				
		if (!empty($company_name))
		{
			$k = explode(" ", $company_name);
			$c_f_name = $k[0];
		}

		// the website is neither read from the logo
		// nor from the about blurb, so we need to 
		// read a link in the pr
		if (empty($website))
		{
			$anchors = @$html->find('div[class=bw-release-story] a');
			foreach ($anchors as $a) // searching all anchors now
			{
				$link = $a->href;

				if ($c_f_name && strstr(strtolower($link), strtolower($c_f_name)))
				{
					
					if (substr($link, 0, 11) == "http://cts.")
					{
						$link = urldecode($link);
						if (preg_match("/(.*)url\=(.*)/i", $link, $match))
							$link = $match[2];
					}

					$info = parse_url($link);
					$host = $info['host'];
					$website = $info['scheme']."://".$info['host'];
					break;
				}
			}

			if (empty($website))
			{
				foreach ($anchors as $a) // searching all anchors now
				{
					$link = $a->href;
					if ($a->innertext != "BUSINESS WIRE" && !strstr($link, "bit.ly"))
					{

						if (substr($link, 0, 11) == "http://cts.")
						{
							$link = urldecode($link);
							if (preg_match("/(.*)url\=(.*)/i", $link, $match))
								$link = $match[2];
						}

						$info = parse_url($link);
						$host = $info['host'];
						if (!empty($info['scheme']))
							$website = $info['scheme']."://".$info['host'];

						if (!empty($website))
							break;
					}
				}
			}
		}
		

		if (strstr($website, "&"))
		{
			$k = explode("&", $website);
			$website = $k[0];
		}

		$website = $this->url_protocol($website);

		$content = @$html->find('div[class=bw-release-story]', 0)->innertext;
		
		$m_content->date_publish = $publish_date;
		$m_content->date_created = $publish_date;
		$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_content->is_published = 1;
		$m_content->is_approved = 1;
		$m_content->is_premium = 1;
		$m_content->is_draft = 0;
		$m_content->title_to_slug();
		$m_content->save();

		// Now saving the content data
		$m_c_data->summary = $summary;

		if (!empty($content))
		{
			$e_content = @str_get_html($content);

			foreach ($e_content->find('a') as $anchor)
			{
				if ($anchor->id == "tweet-pull-quote")
					$anchor->outertext = "";
				else
					$anchor->outertext = $anchor->plaintext;
			}

			// now removing the Business Wire main link
			if ($provider = @$e_content->find('span[itemscope=itemscope]', 0))
				$provider->outertext = "";

			$content = $e_content->innertext;
			$content = str_replace("()--", "", $content);
		}
		
		$keep_tags = true;
		$content = $this->sanitize($content, $keep_tags);
		$content = $this->linkify($content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));

		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		$m_businesswire_cat = Model_BusinessWire_Category::find($businesswire_pr->businesswire_category_id);

		$m_pb_pr = new Model_PB_PR();
		$m_pb_pr->content_id = $m_content->id;
		$m_pb_pr->is_distribution_disabled = 1;
		$m_pb_pr->save();

		if (!empty($m_businesswire_cat->newswire_beat_id))
			$m_content->set_beats(array($m_businesswire_cat->newswire_beat_id));

		$is_new_comp = 0;		

		if (!empty($company_name) && $businesswire_comp = Model_BusinessWire_Company::find('name', $company_name))
			$businesswire_c_data = Model_BusinessWire_Company_Data::find($businesswire_comp->id);
		else
		{
			$is_new_comp = 1;
			$businesswire_comp = new Model_BusinessWire_Company();
			$businesswire_comp->name = $company_name;
			$businesswire_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
			$businesswire_comp->businesswire_category_id = $businesswire_pr->businesswire_category_id;
			$businesswire_comp->save();

			$businesswire_c_data = new Model_BusinessWire_Company_Data();
			$businesswire_c_data->businesswire_company_id = $businesswire_comp->id;
		}
			

		if ($is_new_comp || empty($businesswire_c_data->contact_name))
			$businesswire_c_data->contact_name = value_or_null(@$contact_name);

		if ($is_new_comp || empty($businesswire_c_data->cover_image_url))
			$businesswire_c_data->cover_image_url = value_or_null(@$cover_image_url);
			
		if ($is_new_comp || empty($businesswire_c_data->website))
			$businesswire_c_data->website = value_or_null(@$website);
		
		if ($is_new_comp || empty($businesswire_c_data->address))
			$businesswire_c_data->address = value_or_null($address);
		
		if ($is_new_comp || empty($businesswire_c_data->phone_num))
			$businesswire_c_data->phone = value_or_null(@$phone_num);

		if ($is_new_comp || empty($businesswire_c_data->logo_image_path))
		{
			$businesswire_c_data->logo_image_path = value_or_null($logo_image_path);
			if (!empty($logo_image_path))
				$businesswire_c_data->is_logo_valid = 1;
		}

			
		if ($is_new_comp || empty($businesswire_c_data->email))
			$businesswire_c_data->email = value_or_null(@$email);

		if ($is_new_comp || empty($businesswire_c_data->about_blurb))
			$businesswire_c_data->short_description = value_or_null(@$about_blurb);

		if ($is_new_comp || empty($businesswire_c_data->about_company))
			$businesswire_c_data->about_company = value_or_null(@$about_blurb);

		$businesswire_c_data->save();

		$businesswire_pr = Model_PB_BusinessWire_PR::find($businesswire_pr->content_id);
		$businesswire_pr->businesswire_company_id = $businesswire_c_data->businesswire_company_id;
		
		$businesswire_pr->save();
		
	}
}

?>
