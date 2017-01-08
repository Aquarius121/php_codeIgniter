<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from 
// within the admin area to fetch 
// the MyNewsDesk news on demand

load_controller('cli/auto_create_nr/base');

class Pull_News_Controller extends Auto_Create_NR_Base { 

	public function index($company_id)
	{
		$cnt = 1;

		$sql = "SELECT cd.newsroom_url, cd.mynewsdesk_company_id,
				c.company_id, c.mynewsdesk_category_id
				FROM ac_nr_mynewsdesk_company c
				INNER JOIN ac_nr_mynewsdesk_company_data cd
				ON cd.mynewsdesk_company_id = c.id
				WHERE c.company_id = '{$company_id}'
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				AND cd.is_news_fetched = 0
				AND cd.is_news_list_fetched = 0
				ORDER BY cd.mynewsdesk_company_id 
				LIMIT 1";


		$result = $this->db->query($sql);

		if ($result->num_rows())
		{
			$c_data = Model_MyNewsDesk_Company_Data::from_db($result);
			
			$this->fetch_news_list($c_data);
			
			$c_data->is_news_list_fetched = 1;
			$c_data->save();
		}

		
		// Now fetching individual News
		
		$comp = Model_MyNewsDesk_Company::find('company_id', $company_id);
		$c_data = Model_MyNewsDesk_Company_Data::find($comp->id);

		$sql = "SELECT p.* 
				FROM nr_pb_mynewsdesk_content p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				LEFT JOIN nr_content_data cd
				ON cd.content_id = c.id
				LEFT JOIN nr_pb_news pn
				ON pn.content_id = cd.content_id
				WHERE p.mynewsdesk_company_id = '{$comp->id}'
				AND cd.content IS NULL
				AND c.type = ?
				AND pn.source_url IS NULL
				ORDER BY content_id
				LIMIT 1";

		$cnt = 1;
		while (1)
		{
			$result = $this->db->query($sql, Model_Content::TYPE_NEWS);
			
			if (!$result->num_rows()) break;
		
			$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::from_db($result);
			if (!$mynewsdesk_pr) break;

			$this->fetch_single_news($mynewsdesk_pr, $company_id);
			
			if ($cnt%20 == 0)
				sleep(2);

			$cnt++;
			
		}

		
		$c_data->is_news_fetched = 1;
		$c_data->save();		
	}

	public function fetch_news_list($c_data)
	{
		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$url = $c_data->newsroom_url;
		
		if (!empty($url))
		{
			if (strlen($url) > 0 && substr($url, strlen($url) - 1, 1) != "/")
				$url = "{$url}/";

			$url = "{$url}news";
		}

		$html = @file_get_html($url);

		if (empty($html))
			return 0;

		$is_page_exist = 0;

		$mynewsdesk_cat = Model_MyNewsDesk_Category::find($c_data->mynewsdesk_category_id);

		foreach($html->find('div[class=material-list] div[class=article]') as $element)
		{
			$is_page_exist = 1;

			$news_title = $pr_url = $cover_image_url = $publish_date = null;

			if ($anchor = @$element->find('h2[class=newsroom-list-header] a', 0))
			{
				$news_title = $anchor->innertext;
				$pr_url = $anchor->href;
			}

			if ($pr_url && substr($pr_url, 0, 4) != "http")
					$pr_url = "http://www.mynewsdesk.com{$pr_url}";


			if ($meta = @$element->find('h4[class=meta]', 0))
			{
				$dt = $meta->find('span.material-date', 0)->plaintext;
				$tm = $meta->find('span.material-time', 0)->plaintext;
				
				if (!empty($dt))
					$dt = trim($dt);

				if (!empty($tm))
					$tm = trim($tm);

				if (!empty($dt) && !empty($tm))
					$publish_date = "{$dt} {$tm}";
				
				if (!empty($publish_date))
					$publish_date = $this->make_db_date($publish_date);

			}

			if ($cover_image = @$element->find('div[class=media-wrapper] a[class=material] img', 0))
				if ($cover_image->alt != 'Media-no-image')
				$cover_image_url = @$cover_image->src;

			if (!empty($news_title))
				$news_title = $this->sanitize($news_title);

			$criteria = array();
			$criteria[] = array('title', $news_title);
			$criteria[] = array('company_id', $c_data->company_id);

			if ($pr = Model_Content::find($criteria))
			{}

			elseif (!empty($pr_url) && !empty($news_title) &&	!empty($publish_date))
			{
				$m_content = new Model_Content();
				$m_content->company_id = $c_data->company_id;
				$m_content->type = Model_Content::TYPE_NEWS;
				$m_content->title = $news_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->date_publish = $publish_date;
				$m_content->is_draft = 1;
				$m_content->title_to_slug();
				$m_content->is_scraped_content = 1;
				$m_content->save();

				$mynewsdesk_pr = new Model_PB_MyNewsDesk_Content();
				$mynewsdesk_pr->mynewsdesk_company_id = $c_data->mynewsdesk_company_id;
				$mynewsdesk_pr->mynewsdesk_category_id = $c_data->mynewsdesk_category_id;
				$mynewsdesk_pr->content_id = $m_content->id;
				$mynewsdesk_pr->url = $pr_url;
				$mynewsdesk_pr->cover_image_url = value_or_null($cover_image_url);
				
				$mynewsdesk_pr->save();

				$m_news = new Model_PB_News();
				$m_news->content_id = $m_content->id;
				$m_news->cat_1_id = $mynewsdesk_cat->newswire_cat_id;
				$m_news->save();

				$m_content->set_beats(array($mynewsdesk_cat->newswire_beat_id));

				$m_scraped_content = new Model_PB_Scraped_Content();
				$m_scraped_content->content_id = $m_content->id;
				$m_scraped_content->source = Model_PB_Scraped_Content::SOURCE_MYNEWSDESK;
				$m_scraped_content->source_url = $pr_url;
				$m_scraped_content->save();
			}
			
		}

		return $is_page_exist;

	}

	protected function fetch_single_news($mynewsdesk_pr, $company_id)
	{
		lib_autoload('simple_html_dom');

		if (empty($mynewsdesk_pr->url))
			return false;
		
		$html = @file_get_html($mynewsdesk_pr->url);

		if (empty($html))
		{
			$m_c_data = Model_Content_Data::find($mynewsdesk_pr->content_id);
			$m_c_data->content = "Not found";
			$m_c_data->summary = "Not found";
			$m_c_data->save();
			return;
		}

		$m_content = Model_Content::find($mynewsdesk_pr->content_id);

		if (! $m_c_data = Model_Content_Data::find($mynewsdesk_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $mynewsdesk_pr->content_id;
		}


		if (!$m_news = Model_PB_News::find($m_content->id))
		{
			$m_news = new Model_PB_News();
			$m_news->content_id = $m_content->id;
		}

	
		if ($mynewsdesk_cat = Model_MyNewsDesk_Category::find($mynewsdesk_pr->mynewsdesk_category_id))
		{
			$m_news->cat_1_id = $mynewsdesk_cat->newswire_cat_id;
			$m_news->save();
		}

		$summary = "";

		$pr_b = @$html->find('div[class=newsroom-article] div[class=markdown]', 0)->innertext;

		foreach($html->find('div[class=newsroom-article] div[class=markdown] p') as $element)
			if (empty($summary) || strlen($summary) < 100)
				$summary = "{$summary} ".$element->plaintext;


		$pr_body = "";
		$content = "";

		foreach($html->find('div[class=newsroom-article] div[class=markdown] p') as $element)
		{
			$pr_body = "{$pr_body}".$element->innertext;
			$text = $element->innertext;
			$content .= "<p>{$text}</p>";
		}

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
		

		if (empty($content))
			$content = "<p></p>";

		
		////////////////////////////////////////////////////////////////////////////

		$m_content->date_updated = $m_content->date_publish;
		$m_content->is_published = 1;
		$m_content->is_approved = 1;
		$m_content->is_draft = 0;
		
		if (is_array($tags) && count($tags))
			$m_content->set_tags($tags);

		$m_content->save();

		$m_content->set_beats(array($mynewsdesk_cat->newswire_beat_id));


		// Now saving the content data
		if (!empty($summary))
			$summary = $this->sanitize($summary);

		$m_c_data->summary = $summary;

		if (!empty($content))
			$content = $this->sanitize($content);

		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		// Updating the cover image now
		if (!empty($mynewsdesk_pr->cover_image_url))
		{
			$cover_file = "cover";
			$img_url = $mynewsdesk_pr->cover_image_url;
			@copy($img_url, $cover_file);

			if (Image::is_valid_file($cover_file))
			{
				// import the cover image into the system
				$pr_im = Quick_Image::import("cover", $cover_file);
				 
				// assign to the new company and save
				$pr_im->company_id = $company_id;
				$pr_im->save();
				 
				// set it to use the new logo image and save
				$m_content->cover_image_id = $pr_im->id;
				$m_content->save();
			}
		}
		
	}

	
}

?>
