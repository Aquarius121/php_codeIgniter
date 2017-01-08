<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT * FROM nr_pb_marketwired_pr
				WHERE marketwired_company_id = 0
				ORDER BY content_id
				LIMIT 1";

		while ($cnt++ <= 20)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$marketwired_pr = Model_PB_MarketWired_PR::from_db($result);
			if (!$marketwired_pr) break;

			$this->get($marketwired_pr);
			sleep(1);
		}
	}

	protected function get($marketwired_pr)
	{
		lib_autoload('simple_html_dom');

		if (empty($marketwired_pr->url))
			return false;

		$html = @file_get_html($marketwired_pr->url);
		
		if (empty($html))
		{
			$marketwired_pr = Model_PB_MarketWired_PR::find($marketwired_pr->content_id);
			$marketwired_pr->marketwired_company_id = -1;
			$marketwired_pr->save();
			return;
		}

		$m_content = Model_Content::find($marketwired_pr->content_id);

		if (! $m_c_data = Model_Content_Data::find($marketwired_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $marketwired_pr->content_id;
		}	

		$publish_date = null;
		$date_line = @$html->find('p[id=news-date]', 0)->innertext;
		if (!empty($date_line))
		{
			$publish_date = str_replace("ET", "EDT", $date_line);
			$publish_date = str_replace(".", ",", $date_line);
			$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));
			$publish_date = Date::in($publish_date);
		}

		$location_line = @$html->find('div[class=mw_release] p', 0)->innertext;
		preg_match("/(.*)--\(Marketwired\ -\ (.*)/", $location_line, $match); //--(Marketwired
		if (is_array($match) && count($match) > 1)
		{
			$address = strip_tags($match[1]);
			$text = $match[2];
			
			$m = explode(")", $text);
			$p_date = $m[0];
			if (empty($publish_date))
				$publish_date = date(DATE::FORMAT_MYSQL, strtotime($p_date));
		}	

		$contact_area = @$html->find('div[id=newsroom-contact-middle] ul li', 0)->plaintext;
		$contact_area = trim($contact_area);
		$email = $this->extract_email_address($contact_area);
		
		if ( ! empty($contact_area))
			$phone_num = $this->extract_phone_number($contact_area);

		
		$logo_image_path = @$html->find('table[class=news-table] tr td a img', 0)->src;
		if (empty($logo_image_path))
			$logo_image_path = @$html->find('table[class=news-table] tr td p img', 0)->src;

		// finding the company url
		if ($logo_image = @$html->find('table[class=news-table] tr td a img', 0)) // if the logo is linked to url
		{
			$anchor = $logo_image->parent;
			$website = @$anchor->href;

			if (!empty($website))
			{
				$info = parse_url($website);
				$host = $info['host'];
				$website = $info['scheme']."://".$info['host'];
			}
		}
		
		if (!$company_name = @$html->find('table[class=news-table] tr td span[class=b]', 0)->innertext)
			$company_name = @$html->find('table[class=news-table] tr td p', 0)->plaintext; 	
		
		if (!empty($company_name))
			$company_name = str_replace('SOURCE: ', '', $company_name);

		// now finding the about company blurb 
		// and the company url if not found yet
		if (!empty($company_name))
		{
			$k = explode(" ", $company_name);
			$c_f_name = $k[0];
			$c_f_name = str_replace(",", "", $c_f_name);
			$search_text = "About {$c_f_name}";
			$paragraphs = @$html->find('div[class=mw_release] p');

			foreach ($paragraphs as $para) // searching each paragraph for "About <company first name>" in start
			{
				$p_text = $para->plaintext;
				if (@preg_match("/{$search_text}(.*)/i", $p_text, $match) || 
						@preg_match("/(.*)About\ The\ Company(.*)/i", $p_text, $match))
					
				{
					$m = $para->innertext();						
					
					if (@preg_match("/(.*)\<br\ \/>(.*)/i", $m, $match))
					{
						$about_blurb = $match[2];
						$about_blurb = str_get_html($about_blurb);
						if ($next_para = @$para->next_sibling())
						{
							if (strlen($about_blurb) < 50)
							{
								$about_blurb .= $next_para;
								$about_blurb = str_get_html($about_blurb);
							}
						}
					}

					if (empty($about_blurb))
					{
						$about_blurb = @$para->next_sibling();
						$about_blurb = str_get_html($about_blurb);
					}
					
					if (!empty($about_blurb))
					{						
						if (empty($website))
						{
							$anchors = @$about_blurb->find('a');
							foreach ($anchors as $a)
							{
								$link = @$a->href;								
								if ($link = $a->href)
								{
									if (substr($link, 0, 11) == "http://ctt.")
									{
										$link = @urldecode($link);
										if (@preg_match("/(.*)url\=(.*)/i", $link, $match))
											$link = $match[2];

										if (!empty($link))
										{
											$info = parse_url($link);
											$host = $info['host'];
											$website = $info['scheme']."://".$info['host'];
											break;
										}
									}		
								}
							}
						}						

						if (!empty($about_blurb))
						{
							$about_blurb = $this->sanitize($about_blurb);
							break;
						}


					}
				}

			}

		}

		$contact_name= null;
		if (!empty($contact_area))
			$contact_name = $this->extract_contact_name($contact_area, $company_name);

		// the website is neither read from the logo
		// nor from the about blurb, so we need to 
		// read a link in the pr
		if (empty($website))
		{
			$anchors = @$html->find('div[class=mw_release] a');
			foreach ($anchors as $a) // searching all anchors now
			{
				$link = $a->href;

				if ($c_f_name && strstr(strtolower($link), strtolower($c_f_name)))
				{
					
					if (substr($link, 0, 11) == "http://ctt.")
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

				if (substr($link, 0, 11) == "http://ctt.")
				{
					$link = urldecode($link);
					if (preg_match("/(.*)url\=(.*)/i", $link, $match))
						$link = $match[2];

					$info = parse_url($link);
					$host = $info['host'];
					$website = $info['scheme']."://".$info['host'];
				}
			}

			if (empty($website))
			{
				foreach ($anchors as $a) // searching all anchors now
				{
					$link = $a->href;
					$info = parse_url($link);
					$host = $info['host'];
					if (!empty($info['scheme']))
						$website = $info['scheme']."://".$info['host'];
					
				}
			}
		}

		$content = @$html->find('div[class=mw_release]', 0)->innertext;

		

		$summary = null;
		if ($supposed_summary_p = @$html->find('div[id=newsroom-copy] p', 3))
		{
			if (!empty($supposed_summary_p->innertext))
			{
				if ($supposed_summary_p->find('strong', 0))
				{
					$in_text = $supposed_summary_p->innertext;

					if (preg_match('#\-\-\(Marketwired[a-z0-9\_\-\.\,\ ]+\)#is', $in_text))
					{
						$summary = null;
					}
					else
					{
						$summary = $supposed_summary_p->plaintext;
						$summary = trim($summary);
					}
				}
			}
		}

		if ($summary)
			$summary = $this->sanitize($summary);

		$e_content = @str_get_html($content);

		foreach ($e_content->find('a') as $anchor)
			$anchor->outertext = $anchor->plaintext;
		
		foreach ($e_content->find('span') as $span)
			if (!empty($span->innertext) && String_Util::contains($span->innertext, '--(Marketwired'))
			{
				$span->outertext = " - ";
				break;
			}

		$keep_tags = true;
		$content = $e_content->innertext;
		$content = preg_replace('#\-\-\(Marketwired[a-z0-9\_\-\.\,\ ]+\)#is', "", $content);
		$content = $this->sanitize($content, $keep_tags);

		$content = $this->linkify($content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));

		$cover_image_url = @$html->find('div[class=newsroom-right-content] ul[id=newsroom-right-item] li a img', 0)->src;

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
		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		$m_marketwired_cat = Model_MarketWired_Category::find($marketwired_pr->marketwired_category_id);

		$m_pb_pr = new Model_PB_PR();
		$m_pb_pr->content_id = $m_content->id;
		$m_pb_pr->is_distribution_disabled = 1;
		$m_pb_pr->save();

		if (!empty($m_marketwired_cat->newswire_beat_id))
			$m_content->set_beats(array($m_marketwired_cat->newswire_beat_id));

		if (!empty($company_name))
			$company_name = $this->sanitize($company_name);

		// check if the company already exists
		if (empty($website) || 
				! $marketwired_c_data = Model_MarketWired_Company_Data::find('website', $website))
		{
			$marketwired_comp = new Model_MarketWired_Company();
			$marketwired_comp->name = $company_name;
			$marketwired_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
			$marketwired_comp->marketwired_category_id = $marketwired_pr->marketwired_category_id;
			$marketwired_comp->save();

			$marketwired_c_data = new Model_MarketWired_Company_Data();
			$marketwired_c_data->marketwired_company_id = $marketwired_comp->id;
			$marketwired_c_data->contact_name = value_or_null($contact_name);

			$marketwired_c_data->cover_image_url = value_or_null($cover_image_url);
			
			$marketwired_c_data->website = value_or_null(@$website);
			$marketwired_c_data->address = value_or_null($address);
			$marketwired_c_data->phone = value_or_null($phone_num);
			$marketwired_c_data->contact_info = value_or_null($contact_area);
			$marketwired_c_data->logo_image_path = value_or_null($logo_image_path);
			if (!empty($logo_image_path))
				$marketwired_c_data->is_logo_valid = 1;
			$marketwired_c_data->email = value_or_null($email);

			$marketwired_c_data->short_description = value_or_null($about_blurb);
			$marketwired_c_data->about_company = value_or_null($about_blurb);

			$marketwired_c_data->save();
		}

		$marketwired_pr = Model_PB_MarketWired_PR::find($marketwired_pr->content_id);
		$marketwired_pr->marketwired_company_id = $marketwired_c_data->marketwired_company_id;
		$marketwired_pr->cover_image_url = value_or_null($cover_image_url);

		$marketwired_pr->save();
	}
}

?>
