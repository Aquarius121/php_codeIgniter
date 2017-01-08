<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

// This CLI script is called from 
// within the admin area to fetch 
// the PR.Co press releases on demand

class Pull_PRs_Controller extends Auto_Create_NR_Base { 

	public function index($company_id)
	{
		if (empty($company_id))
			return false;

		$cnt = 1;

		$sql = "SELECT cd.newsroom_url, cd.pr_co_company_id,
				c.company_id, c.pr_co_category_id
				FROM ac_nr_pr_co_company c
				INNER JOIN ac_nr_pr_co_company_data cd
				ON cd.pr_co_company_id = c.id
				WHERE c.company_id = '{$company_id}'
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				AND cd.is_prs_fetched = 0
				AND cd.is_pr_list_fetched = 0
				ORDER BY cd.pr_co_company_id 
				LIMIT 1";


		$result = $this->db->query($sql);

		if ($result->num_rows())
		{
			$c_data = Model_PR_Co_Company_Data::from_db($result);
			
			$cnt = 1;
			while ($cnt <= 25)
			{
				if (!$this->fetch_prs_list($c_data, $cnt))
					break;

				$cnt++;
			}

			$c_data->is_pr_list_fetched = 1;
			$c_data->save();
		}

		
		// Now fetching individual PRs

		$comp = Model_PR_Co_Company::find('company_id', $company_id);
		$c_data = Model_PR_Co_Company_Data::find($comp->id);

		$sql = "SELECT p.* 
				FROM nr_pb_pr_co_content p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				LEFT JOIN nr_content_data cd
				ON cd.content_id = c.id
				WHERE p.pr_co_company_id = '{$comp->id}'
				AND cd.content IS NULL
				AND c.type = ?
				AND c.is_draft = 1
				ORDER BY content_id DESC
				LIMIT 1";

		$cnt = 1;

		while (1)
		{
			$result = $this->db->query($sql, Model_Content::TYPE_PR);
			
			if (!$result->num_rows()) break;
		
			$pr_co_pr = Model_PB_PR_Co_Content::from_db($result);
			if (!$pr_co_pr) break;

			$this->fetch_single_pr($pr_co_pr, $company_id);
			
			if ($cnt%20 == 0)
				sleep(2);

			$cnt++;			
		}

		
		$c_data->is_prs_fetched = 1;
		$c_data->save();

		
	}


	public function fetch_prs_list($c_data, $cnt)
	{
		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$url = $c_data->newsroom_url;
		
		if (!empty($url))
		{
			if (strlen($url) > 0 && substr($url, strlen($url) - 1, 1) != "/")
				$url = "{$url}/";

			$url = "{$url}/?page={$cnt}";
		}

		$html = @file_get_html($url);

		if (empty($html))
			return 0;

		$is_page_exist = 0;
		$prs = array();
		$category = Model_PR_Co_Category::find($c_data->pr_co_category_id);

		foreach ($html->find('div.pressroom-items-for-month') as $i => $month_items)
		{
			$month = null;
			
			if ($m = $html->find('h4.pressroom-month', $i))
				$month = $m->innertext;
			
			foreach($month_items->find('article.press_release, article.story') as $element)
			{
				$is_page_exist = 1;

				$pr = new stdClass();

				if ($pr_day = $element->find('span.pressroom-day', 0))
				{
					$publish_date = $pr_day->plaintext;
					$publish_date = trim($publish_date);

					if (!empty($month))
					{
						$month = trim($month);
						$y = explode(" ", $month);
						$year = $y[1];
						if (!empty($year))
							$publish_date = "{$publish_date} {$year}";
					}

					$pr->publish_date = $publish_date;
				}

				if ($anchor = @$element->find('a', 0))
				{
					if ($h3 = $anchor->find('h3', 0))
						$pr_title = $h3->plaintext;

					$pr->title = $pr_title;
					$pr->url = $anchor->href;

					$prs[] = $pr;

				}
				
			}
			
		}

		foreach ($html->find('ol.pressroom-pressdocs') as $i => $month_items)
		{
			$month = null;
			
			if ($m = $html->find('h2.pressroom-month', $i))
				$month = $m->innertext;
			
			foreach($month_items->find('li.pressrelease') as $element)
			{
				$is_page_exist = 1;

				$pr = new stdClass();

				if ($pr_day = $element->find('span.release_date', 0))
				{
					$publish_date = $pr_day->plaintext;
					$publish_date = trim($publish_date);

					if (!empty($month))
					{
						$month = trim($month);
						$y = explode(" ", $month);
						$year = $y[1];
						if (!empty($year))
							$publish_date = "{$publish_date} {$year}";
					}
					
					$pr->publish_date = $publish_date;
				}

				if ($anchor = @$element->find('a', 0))
				{
					if ($h3 = $anchor->find('h3', 0))
						$pr_title = $h3->plaintext;
					else
						$pr_title = $anchor->plaintext;
					
					$pr->title = $pr_title;
					$pr->url = $anchor->href;

					$prs[] = $pr;
				}
				
			}
		}

		foreach ($prs as $pr)
		{
			$pr_url = $pr->url;
			$pr_title = $pr->title;
			$publish_d = $pr->publish_date;

			if (substr($pr_url, 0, 4) !== "http")
			{
				if (substr(strrev($c_data->newsroom_url), 0, 1) == "/")
					$c_data->newsroom_url = substr($c_data->newsroom_url, 0, strlen($c_data->newsroom_url)-1);

				if (substr($pr_url, 0, 1) == "/")
					$pr_url = substr($pr_url, 1);

				$pr_url = "{$c_data->newsroom_url}/{$pr_url}";
			}

			$pr_title = $this->sanitize($pr_title);
			

			if (!empty($publish_d))
			{
				$publish_date = trim($publish_d);
				$publish_date = str_replace("-", "", $publish_date);
				$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));
			}

			if (!empty($pr_url) && !empty($pr_title) &&
				!empty($publish_d) && ! $pr = Model_PB_PR_Co_Content::find('url', $pr_url))
			{
				$is_a_new_rec_added = 1;
				$m_content = new Model_Content();
				$m_content->company_id = $c_data->company_id;
				$m_content->type = Model_Content::TYPE_PR;
				$m_content->title = $pr_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->date_publish = $publish_date;
				$m_content->is_draft = 1;
				$m_content->title_to_slug();
				$m_content->save();

				$pr_co_pr = new Model_PB_PR_Co_Content();
				$pr_co_pr->content_id = $m_content->id;
				$pr_co_pr->url = $pr_url;
				$pr_co_pr->pr_co_company_id = $c_data->pr_co_company_id;				
				$pr_co_pr->pr_co_category_id = $category->id;

				$pr_co_pr->save();

				$m_pr = new Model_PB_PR();
				$m_pr->content_id = $m_content->id;
				$m_pr->cat_1_id = $category->newswire_cat_id;
				$m_pr->is_distribution_disabled = 1;
				$m_pr->save();

				$m_content->set_beats(array($category->newswire_beat_id));
				
			}
			
		}

		return $is_page_exist;

	}

	
	protected function fetch_single_pr($pr_co_pr, $company_id)
	{
		set_time_limit(3000);

		lib_autoload('simple_html_dom');

		if (empty($pr_co_pr->url))
			return false;

		$html = @file_get_html($pr_co_pr->url);

		if (empty($html))
		{
			$pr_co_pr = Model_PB_PR_Co_Content::find($pr_co_pr->content_id);
			$pr_co_pr->pr_co_company_id = -1;
			$pr_co_pr->save();
			return;
		}

		$m_content = Model_Content::find($pr_co_pr->content_id);

		$m_content->title = ucwords($m_content->title);

		if (! $m_c_data = Model_Content_Data::find($pr_co_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $pr_co_pr->content_id;
			$m_c_data->save();
		}


		if (!$m_pr = Model_PB_PR::find($m_content->id))
		{
			$m_pr = new Model_PB_PR();
			$m_pr->content_id = $m_content->id;
		}
		
		if ($pr_co_cat = Model_PR_Co_Category::find($pr_co_pr->pr_co_category_id))
		{
			$m_pr->cat_1_id = $pr_co_cat->newswire_cat_id;
			$m_pr->save();
		}

		$summary = "";

		$summary = @$html->find('article[id=press_release] section[id=summary] i', 0)->plaintext;

		if (empty($summary))
			$summary = @$html->find('div[id=large_description_text] p', 0)->plaintext;

		if (empty($summary))
			$summary = @$html->find('div[id=medium_description_text]', 0)->plaintext;

		if ($header = @$html->find('div[data-type=Header]', 0))
		{
			@$header->find('h1', 0)->innertext = "";
			$summary = $header->plaintext;
		}

		if (empty($summary) && $sub_title = @$html->find('section#subtitle', 0))
			$summary = @$sub_title->plaintext;

		if (!empty($summary))
			$summary = trim($summary);

		$release_info = @$html->find('section[id=release_info]', 0)->plaintext;

		if (!empty($release_info))
		{
			$r = explode(",", $release_info);
			unset($r[0]);
			$location = implode(",", $r);
		}
		else // try the second method used in some of the other PRs
		{
			$release_info_text = @$html->find('div[id=release_info_text]', 0)->plaintext;
			if (!empty($release_info_text))
			{
				$r = explode("|", $release_info_text);
				if (is_array($r) && count($r) > 1)
					$location = $r[0];
			}
		}

		if (!empty($location))
			$location = trim($location);

		$newsroom_url = @$html->find('section[id=logo] a', 0)->href;

		if (empty($newsroom_url))
			$newsroom_url = @$html->find('div[id=logo] a', 0)->href;

		if (!empty($pr_co_pr->company_name))
			$company_name = $pr_co_pr->company_name;

		if (empty($company_name))
			$company_name = @$html->find('p[id=pressroom_name]', 0)->plaintext;
		
		if (empty($company_name)) // lets try the second method
			$company_name = @$html->find('h4[id=company_name]', 0)->plaintext;
			

		foreach (@$html->find('ul[id=pressroom_links] li a') as $link)
			if (@$link->innertext == "Main website")
				$website = $link->href;

		if (empty($website))
			foreach (@$html->find('ul[id=company_links] li a') as $link)
				if (@$link->innertext == "Main website")
					$website = $link->href;
			
		$content = "";
		if ($body_text = @$html->find('section[id=bodytext] div.default_content_block', 0))
		{
			foreach ($body_text->find('p') as $i => $p) 
				if ($i > 0 && !empty($p->innertext))
					$content = "{$content}<p>".$p->innertext."</p>";
		}

		if ((empty($content) || strlen($content) <= 20) && !empty($body_text))
			$content = @$body_text->innertext;


		if (empty($content))
			if ($body_text2 = @$html->find('div[id=large_description_text]', 0))
				foreach ($body_text2->find('p') as $i => $p) 
					$content = "{$content}<p>".$p->innertext."</p>";

		if (empty($content))
			if ($text_area = @$html->find('div[data-type=Textarea]', 0))
				foreach (@$html->find('div[data-type=Textarea]') as $text_area)
					$content .= $text_area->innertext;
		

		if ((empty($content) || strlen($content) <= 20) && !empty($body_text2))
			$content = @$body_text2->innertext;

		if (empty($summary) && !empty($content) && strlen($content) > 150)
		{
			$text = strip_tags($content);
			$summary = $this->vd->cut($text, 150);
			
		}
		
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

		$phone_num = $this->extract_phone_number($pressroom_contact_info);
		
		
		
		$logo_image_path = @$html->find('section[id=logo] a img', 0)->src;
		if (empty($logo_image_path))
			$logo_image_path = @$html->find('div[id=logo] a img', 0)->src;

		
		$anchors = array();

		foreach(@$html->find('div[id=panel_social] a') as $element)
			$anchors[] = $element->href;

		if (!count($anchors))
			foreach(@$html->find('div[id=social_circles] a') as $element)
				$anchors[] = $element->href;
			

		$socials = $this->extract_socials($anchors);

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

		$images = array();
		if ($images_area = @$html->find('section[id=images]', 0))
			foreach (@$images_area->find('ul li a img') as $img)
				$images[] = $img->src;

		if (!count($images))
			if ($images_area = @$html->find('div[id=images_field]', 0))
				foreach (@$images_area->find('a img') as $img)
					$images[] = $img->src;
			
		if (!count($images))
		{
			foreach (@$html->find('div[data-type=Media]') as $media)
			{
				if ($thumb = @$media->find('div.media_thumbs', 0))
					if ($img = @$thumb->find('img', 0))
						$images[] = @$img->src;
			}
		}
		

		$raw_data = new stdClass();

		$stored_images = array();

		if (count($images))
		{
			$raw_data->images = $images;
			
			foreach ($images as $image_url)
			{
				if (!empty($image_url))
				{
					$related_file = "related";
					@copy($image_url, $related_file);

					if (Image::is_valid_file($related_file))
					{
						$pr_im = Quick_Image::import("related", $related_file);
						$pr_im->company_id = $company_id;
						$pr_im->save();
						
						$stored_images[] = $pr_im->id;
					}
				}
			}
			
			if (count($stored_images))
				$m_content->set_images($stored_images);
		}

		$links = array();
		if ($links_area = @$html->find('section[id=links]', 0));
		else
			$links_area = @$html->find('div[id=links_field]', 0); 
		
		if (!empty($links_area->innertext))
			foreach (@$links_area->find('a.link') as $a)
			{
				$link = new stdClass();
				$link->url = $a->href;
				$link->text = @$a->find('span', 0)->innertext;
				if (empty($link->text))
					$link->text = $link->url;
				$links[] = $link;
			}

		if (count($links))
		{
			foreach ($links as $i => $link)
			{
				if (!empty($link->text) && $i == 0)
				{
					$m_c_data->rel_res_pri_title = $link->text;
					$m_c_data->rel_res_pri_link = $link->url;
				}

				if (!empty($link->text) && $i == 1)
				{
					$m_c_data->rel_res_sec_title = $link->text;
					$m_c_data->rel_res_sec_link = $link->url;
				}
			}

			$raw_data->links = $links;
		}

		$cover_image_t = @$html->find('div[id=header-image]', 0)->style;

		$regex = '/https?\:\/\/[^\" ]+/i';
		if (preg_match($regex, $cover_image_t, $url))
			$cover_image_url = $url[0];

		if (!empty($cover_image_url))
		{
			$cover_image_url = str_replace("'", "", $cover_image_url);
			$cover_image_url = str_replace(")", "", $cover_image_url);
			$cover_image_url = str_replace(";", "", $cover_image_url);
		}


		if (!empty($cover_image_url))
		{
			$cover_file = "cover";
			$img_url = $cover_image_url;
			@copy($img_url, $cover_file);

			if (Image::is_valid_file($cover_file))
			{
				$pr_im = Quick_Image::import("cover", $cover_file);
				$pr_im->company_id = $company_id;
				$pr_im->save();
				 
				$m_content->cover_image_id = $pr_im->id;
				$m_content->save();
			}
		}
		
		if ($pdf_area = @$html->find('section[id=pdf]', 0));
		else
			$pdf_area = @$html->find('div[id=pdf_field]', 0);

		$files = array();

		if (!empty($pdf_area))
			$pdf = $pdf_area->find('a', 0)->href;

		if (!empty($pdf))
			$files[] = $pdf;

		if ($doc_area = @$html->find('div[id=documents_field]', 0))
			$doc = @$doc_area->find('a.document', 0)->href;

		if (!empty($doc))
			$files[] = $doc;

		
		if (count($files))
		{
			$raw_data->files = $files;

			$file_num = 0;
			foreach ($files as $file)
			{
				if (!empty($file))
				{
					$file_name = "Download";
					$s_file = basename($file);
					@copy($file, $s_file);
					$stored_file = LEGACY_File::import($s_file);
				}
			}


			foreach ($files as $file)
			{
				if (!empty($file))
				{
					$file_name = "Download";
					$s_file = basename($file);
					@copy($file, $s_file);
					$stored_file = LEGACY_File::import($s_file);
					
					if ($file_num == 0)
					{								
						$m_pr->stored_file_id_1 = $stored_file->id;
						$m_pr->stored_file_name_1 = $file_name;
						$m_pr->save();
						$file_num++;
					}
					else
					{
						$m_pr->stored_file_id_2 = $stored_file->id;
						$m_pr->stored_file_name_2 = $file_name;
						$m_pr->save();
					}
											
				}
			}
		}


		$pr_co_pr->raw_data($raw_data);
		$pr_co_pr->save();

		if ($video_area = @$html->find('section[id=videos]', 0));
		else
			$video_area = @$html->find('div[id=videos_field]', 0);

		if (!empty($video_area))
			$video_url = $video_area->find('a.video', 0)->href;

		if (!empty($video_url))
			$pr_co_video_url = "{$newsroom_url}{$video_url}";

		if (!empty($pr_co_video_url))
		{
			$video_page_html = @file_get_html($pr_co_video_url);

			if (!empty($video_page_html))
			{
				$iframe_src = @$video_page_html->find('iframe', 0)->src;
				if (!empty($iframe_src))
				{
					$iframe_src = urldecode($iframe_src);
					$params = explode("&url=", $iframe_src);
					if (count($params) > 1)
					{
						$vid_part = $params[1];
						$v = explode("&", $vid_part);
						$video_path = $v[0];
					}
				}				
			}
		}

		if (!empty($video_path))
		{
			if ($web_video_id = $this->youtube_id_from_url($video_path))
			{
				$m_pr->web_video_id = $web_video_id;
				$m_pr->web_video_provider = Video::PROVIDER_YOUTUBE;
				$m_pr->clean_video();
			}
		}


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

		////////////////////////////////////////////////////////////////////////////

		$m_content->company_id = $company_id;
		$m_content->date_updated = $m_content->date_publish;
		$m_content->is_published = 1;
		$m_content->is_approved = 1;
		$m_content->is_draft = 0;
		$m_content->is_premium = 1;
		$m_content->save();

		$m_content->set_beats(array($pr_co_cat->newswire_beat_id));

		// Now saving the content data
		if (!empty($summary))
			$summary = $this->sanitize($summary);

		$m_c_data->summary = $summary;

		if (!empty($content))
			$content = $this->sanitize($content);

		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		$m_pr->location = value_or_null($location);
		$m_pr->save();		
	}
}

?>
