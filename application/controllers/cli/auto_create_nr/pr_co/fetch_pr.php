<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_Controller extends Auto_Create_NR_Base { // fetching pr_co single pr

	public function index()
	{
		set_time_limit(30000);

		$cnt = 1;

		$sql = "SELECT p.* 
				FROM nr_pb_pr_co_content p
				INNER JOIN nr_content c 
				ON p.content_id = c.id
				WHERE c.type = ? 
				AND pr_co_company_id = 0
				ORDER BY p.content_id
				LIMIT 1";

		while ($cnt++ <= 30)
		{
			$result = $this->db->query($sql, array(Model_Content::TYPE_PR));
			
			if (!$result->num_rows()) break;
		
			$pr_co_pr = Model_PB_PR_Co_Content::from_db($result);
			if (!$pr_co_pr) break;

			$this->get($pr_co_pr);
			if ($cnt%100 == 0)
				sleep(5);
		}
	}

	protected function get($pr_co_pr)
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
			$summary = HTML2Text::plain($summary);
			
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
		{
			$about = $this->sanitize($about);
			$short_description = $about;
		}		

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

		if (count($images))
			$raw_data->images = $images;

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
			$raw_data->files = $files;

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

		$content = $this->linkify($content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));

		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		$m_pr->location = value_or_null($location);
		$m_pr->save();

		$is_new_comp = 0;

		if (!empty($company_name))
			$company_name = $this->sanitize($company_name);
		
		// check if the company already 
		// exists with the same name
		if (!empty($company_name) &&
				$pr_co_comp = Model_PR_Co_Company::find('name', $company_name))
			$pr_co_c_data = Model_PR_Co_Company_Data::find($pr_co_comp->id);

		// check if the company already exists 
		// with the same website
		elseif (!empty($website) && $pr_co_c_data = Model_PR_Co_Company_Data::find('website', $website))
			$pr_co_comp = Model_PR_Co_Company::find($pr_co_c_data->pr_co_company_id);
				
		else
		{
			$is_new_comp = 1;
			$pr_co_comp = new Model_PR_Co_Company();
			$pr_co_comp->name = $company_name;
			$pr_co_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
			$pr_co_comp->pr_co_category_id = $pr_co_pr->pr_co_category_id;
			$pr_co_comp->save();

			$pr_co_c_data = new Model_PR_Co_Company_Data();
			$pr_co_c_data->pr_co_company_id = $pr_co_comp->id;
		}

		if ($is_new_comp || empty($pr_co_c_data->email))
			$pr_co_c_data->email = value_or_null($email);

		if ($is_new_comp || empty($pr_co_c_data->newsroom_url))
			$pr_co_c_data->newsroom_url = value_or_null($newsroom_url);

		if ($is_new_comp || empty($pr_co_c_data->short_description))
			$pr_co_c_data->short_description = value_or_null($short_description);

		if ($is_new_comp || empty($pr_co_c_data->about_company))
			$pr_co_c_data->about_company = value_or_null($about);

		if ($is_new_comp || empty($pr_co_c_data->website))
		{
			$pr_co_c_data->website = value_or_null(@$website);
			$pr_co_c_data->is_website_valid = 1;
		}
		
		if ($is_new_comp || empty($pr_co_c_data->phone_num))
			$pr_co_c_data->phone = value_or_null($phone_num);

		if ($is_new_comp || empty($pr_co_c_data->address))
			$pr_co_c_data->address = value_or_null($location);
			
		if ($is_new_comp || empty($pr_co_c_data->logo_image_path))
		{
			$pr_co_c_data->logo_image_path = value_or_null($logo_image_path);
			if (!empty($logo_image_path))
				$pr_co_c_data->is_logo_valid = 1;
		}

		if (($is_new_comp || empty($pr_co_c_data->soc_fb)) && !empty($soc_fb))
		{
			$pr_co_c_data->soc_fb = value_or_null($soc_fb);
			$pr_co_c_data->soc_fb_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($pr_co_c_data->soc_twitter)) && !empty($soc_twitter))
		{
			$pr_co_c_data->soc_twitter = value_or_null($soc_twitter);
			$pr_co_c_data->soc_twitter_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($pr_co_c_data->soc_linkedin)) && !empty($soc_linkedin))
			$pr_co_c_data->soc_linkedin = value_or_null($soc_linkedin);

		if (($is_new_comp || empty($pr_co_c_data->soc_pinterest)) && !empty($soc_pinterest))
		{
			$pr_co_c_data->soc_pinterest = value_or_null($soc_pinterest);
			$pr_co_c_data->soc_pinterest_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($pr_co_c_data->soc_youtube)) && !empty($soc_youtube))
		{
			$pr_co_c_data->soc_youtube = value_or_null($soc_youtube);
			$pr_co_c_data->soc_youtube_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}

		if (($is_new_comp || empty($pr_co_c_data->soc_gplus)) && !empty($soc_gplus))
		{
			$pr_co_c_data->soc_gplus = value_or_null($soc_gplus);
			$pr_co_c_data->soc_gplus_feed_status = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		}
		
		$pr_co_c_data->save();				

		$pr_co_pr = Model_PB_PR_Co_Content::find($pr_co_pr->content_id);
		$pr_co_pr->pr_co_company_id = @$pr_co_comp->id;
		$pr_co_pr->raw_data($raw_data);
		$pr_co_pr->cover_image_url = value_or_null($cover_image_url);
		$pr_co_pr->video_url = value_or_null($video_path);
		$pr_co_pr->save();
	}	
}

?>
