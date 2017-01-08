<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from 
// within the admin area to fetch 
// the MyNewsDesk images on demand

load_controller('cli/auto_create_nr/base');

class Pull_Images_Controller extends Auto_Create_NR_Base { 

	public function index($company_id)
	{
		set_time_limit(5000);

		if (empty($company_id))
			return false;

		$cnt = 1;

		$sql = "SELECT cd.newsroom_url, cd.mynewsdesk_company_id,
				c.company_id, c.mynewsdesk_category_id
				FROM ac_nr_mynewsdesk_company c
				INNER JOIN ac_nr_mynewsdesk_company_data cd
				ON cd.mynewsdesk_company_id = c.id
				WHERE c.company_id = '{$company_id}'
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				AND cd.is_images_list_fetched = 0
				AND cd.is_images_fetched = 0
				ORDER BY cd.mynewsdesk_company_id 
				LIMIT 1";


		$result = $this->db->query($sql);

		if ($result->num_rows())
		{
			$c_data = Model_MyNewsDesk_Company_Data::from_db($result);
			
			$cnt = 1;
			while ($cnt <= 25)
			{
				if (!$this->fetch_images_list($c_data, $cnt))
					break;

				$cnt++;
			}
			
			$c_data->is_images_list_fetched = 1;
			$c_data->save();
		}

		
		// Now fetching individual Images

		$comp = Model_MyNewsDesk_Company::find('company_id', $company_id);
		$c_data = Model_MyNewsDesk_Company_Data::find($comp->id);

		$sql = "SELECT p.* 
				FROM nr_pb_mynewsdesk_content p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				LEFT JOIN nr_pb_image pi
				ON pi.content_id = c.id
				WHERE p.mynewsdesk_company_id = '{$comp->id}'
				AND c.type = ?
				AND pi.image_id = 0
				ORDER BY content_id DESC
				LIMIT 1";

		$cnt = 1;
		while (1)
		{
			$result = $this->db->query($sql, Model_Content::TYPE_IMAGE);
			
			if (!$result->num_rows()) break;
		
			$mynewsdesk_content = Model_PB_MyNewsDesk_Content::from_db($result);
			if (!$mynewsdesk_content) break;

			$this->fetch_single_image($mynewsdesk_content, $company_id);
			
			if ($cnt%2 == 0)
				sleep(2);

			$cnt++;			
		}
		
		$c_data->is_images_fetched = 1;
		$c_data->save();		
		
	}

	public function fetch_images_list($c_data, $cnt)
	{
		set_time_limit(5000);

		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$url = $c_data->newsroom_url;
		
		if (!empty($url))
		{
			if (strlen($url) > 0 && substr($url, strlen($url) - 1, 1) != "/")
				$url = "{$url}/";

			$url = "{$url}images/page/{$cnt}";
		}

		$html = @file_get_html($url);

		if (empty($html))
			return 0;

		$is_page_exist = 0;

		$mynewsdesk_cat = Model_MyNewsDesk_Category::find($c_data->mynewsdesk_category_id);

		$newsroom_section = @$html->find('div[class=newsroom-section]', 0);

		foreach(@$newsroom_section->find('div[class=photo-holder] div[class=media-wrapper] a') as $anchor)
		{
			$img_title = $img_url = null;

			$img_title = @$anchor->find('img', 0)->alt;
			$img_url = $anchor->href;

			if ($img_url && substr($img_url, 0, 4) != "http")
					$img_url = "http://www.mynewsdesk.com{$img_url}";

			$is_page_exist = 1;

			$criteria = array();
			$criteria[] = array('url', $img_url);
			$criteria[] = array('mynewsdesk_company_id', $c_data->mynewsdesk_company_id);

			if ($img = Model_PB_MyNewsDesk_Content::find($criteria))
			{}

			elseif (!empty($img_url) && !empty($img_title))
			{
				$m_content = new Model_Content();
				$m_content->company_id = $c_data->company_id;
				$m_content->type = Model_Content::TYPE_IMAGE;
				$m_content->title = $img_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->date_publish = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->is_draft = 1;
				$m_content->title_to_slug();
				$m_content->is_scraped_content = 1;
				$m_content->save();

				$mynewsdesk_content = new Model_PB_MyNewsDesk_Content();
				$mynewsdesk_content->mynewsdesk_company_id = $c_data->mynewsdesk_company_id;
				$mynewsdesk_content->mynewsdesk_category_id = $c_data->mynewsdesk_category_id;
				$mynewsdesk_content->content_id = $m_content->id;
				$mynewsdesk_content->url = $img_url;				
				$mynewsdesk_content->save();

				$m_img = new Model_PB_Image();
				$m_img->content_id = $m_content->id;
				$m_img->save();

				$m_scraped_content = new Model_PB_Scraped_Content();
				$m_scraped_content->content_id = $m_content->id;
				$m_scraped_content->source = Model_PB_Scraped_Content::SOURCE_MYNEWSDESK;
				$m_scraped_content->source_url = $img_url;
				$m_scraped_content->save();
			}			
		}

		return $is_page_exist;

	}

	protected function fetch_single_image($mynewsdesk_content, $company_id)
	{
		set_time_limit(300);
		set_memory_limit('2048M');

		lib_autoload('simple_html_dom');

		if (empty($mynewsdesk_content->url))
			return false;
		
		$html = @file_get_html($mynewsdesk_content->url);
		
		if (empty($html))
		{
			$m_pb_image = Model_PB_Image::find($mynewsdesk_content->content_id);
			$m_pb_image->image_id = -1; 
			$m_pb_image->save();
			return;
		}

		$m_content = Model_Content::find($mynewsdesk_content->content_id);

		if (! $m_c_data = Model_Content_Data::find($mynewsdesk_content->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $mynewsdesk_content->content_id;
			$m_c_data->save();
		}


		if (!$m_pb_image = Model_PB_Image::find($m_content->id))
		{
			$m_pb_image = new Model_PB_Image();
			$m_pb_image->content_id = $m_content->id;
		}

		$content_div = @$html->find('div[id=content]', 0);
		$title = @$content_div->find('header h1', 0)->plaintext;
		
		if (!empty($title))
			$title = $this->sanitize($title);

		$meta = @$html->find('header h4[class=meta]', 0);		

		$dt = $meta->find('span.material-date', 0)->plaintext;
		$tm = $meta->find('span.material-time', 0)->plaintext;
		
		if (!empty($dt))
			$dt = trim($dt);

		if (!empty($tm))
			$tm = trim($tm);

		if (!empty($dt) && !empty($tm))
			$publish_date = "{$dt} {$tm}";

		if (empty($publish_date))
		{		
			$meta_text = $meta->plaintext;
			$parts = explode('•', $meta_text);

			if (is_array($parts) && count($parts) > 1)
				$publish_date = trim($parts[1]);
		}

		if (!empty($publish_date))
			$publish_date = $this->make_db_date($publish_date);


		$license = @$content_div->find('a[class=sp-license]', 0)->plaintext;

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
			$this->set_topics($m_content->id, $topics);

		$summary = @$content_div->find('div[class=newsroom-article] table tbody tr td[colspan=2]', 0)->plaintext;

		if (!empty($summary))
		{
			$m_c_data->summary = $summary;
			$m_c_data->save();
		}

		$src = "data-src";
		$image_url = @$content_div->find('figure[class=web-image] img', 0)->{$src};

		$image_size = $this->curl_get_file_size($image_url);

		// If file size is greater than 
		// max allowed size or the file
		// doesn't exist, we should 
		// remove this content.
		if ($image_size == -1 || $image_size > Image::MAX_VALID_FILE_SIZE)
		{
			$m_content->delete();
			$mynewsdesk_content->delete();
			return;
		}

		
		if (!empty($image_url))
		{
			$img_file = "image";
			@copy($image_url, $img_file);
				
			if (Image::is_valid_file($img_file))
			{
				// import the cover image into the system
				$im = Quick_Image::import("image", $img_file);				 
				$im->company_id = $company_id;
				$im->save();

				$m_pb_image->image_id = $im->id;
				$m_pb_image->license = value_or_null($license);
				$m_pb_image->save();
			}
			else
			{
				$m_content->delete();
				$mynewsdesk_content->delete();
				return;
			}
		}	

		////////////////////////////////////////////////////////////////////////////

		$m_content->title = $title;
		$m_content->title_to_slug();
		$m_content->date_updated = $publish_date;
		$m_content->date_publish = $publish_date;
		$m_content->is_published = 1;
		$m_content->is_draft = 0;
		
		if (is_array($tags) && count($tags))
			$m_content->set_tags($tags);

		$m_content->save();
		
	}


	protected function curl_get_file_size( $url ) 
	{
		$result = -1;
		$curl = curl_init( $url );

		// Issue a HEAD request and follow any redirects.
		curl_setopt( $curl, CURLOPT_NOBODY, true );
		curl_setopt( $curl, CURLOPT_HEADER, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		//curl_setopt( $curl, CURLOPT_USERAGENT, get_user_agent_string() );

		$data = curl_exec( $curl );
		curl_close( $curl );

		if( $data ) 
		{
			$content_length = "unknown";
			$status = "unknown";

			if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) 
			{
				$status = (int)$matches[1];
			}

			if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) 
			{
				$content_length = (int)$matches[1];
			}

			// http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
			if( $status == 200 || ($status > 300 && $status <= 308) ) 
			{
				$result = $content_length;
				
			}

			
		}

		return $result;

	}
}

?>
