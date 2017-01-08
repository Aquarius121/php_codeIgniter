<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from 
// within the admin area to fetch 
// the MyNewsDesk events on demand

load_controller('cli/auto_create_nr/base');

class Pull_Events_Controller extends Auto_Create_NR_Base { 

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
				AND cd.is_events_fetched = 0
				AND cd.is_events_list_fetched = 0
				ORDER BY cd.mynewsdesk_company_id 
				LIMIT 1";


		$result = $this->db->query($sql);

		if ($result->num_rows())
		{
			$c_data = Model_MyNewsDesk_Company_Data::from_db($result);
			
			$this->fetch_events_list($c_data);

			$c_data->is_events_list_fetched = 1;
			$c_data->save();
		}

		
		// Now fetching individual Events
		
		$comp = Model_MyNewsDesk_Company::find('company_id', $company_id);
		$c_data = Model_MyNewsDesk_Company_Data::find($comp->id);

		$sql = "SELECT p.* 
				FROM nr_pb_mynewsdesk_content p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				LEFT JOIN nr_content_data cd
				ON cd.content_id = c.id
				LEFT JOIN nr_pb_event pn
				ON pn.content_id = cd.content_id
				WHERE p.mynewsdesk_company_id = '{$comp->id}'
				AND cd.content IS NULL
				AND c.type = ?
				ORDER BY content_id
				LIMIT 1";

		$cnt = 1;
		while (1)
		{
			$result = $this->db->query($sql, Model_Content::TYPE_EVENT);
			
			if (!$result->num_rows()) break;
		
			$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::from_db($result);
			if (!$mynewsdesk_pr) break;

			$this->fetch_single_event($mynewsdesk_pr, $company_id);
			
			if ($cnt%20 == 0)
				sleep(2);

			$cnt++;
			
		}

		
		$c_data->is_events_fetched = 1;
		$c_data->save();
		
	}

	public function fetch_events_list($c_data)
	{
		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$url = $c_data->newsroom_url;
		
		if (!empty($url))
		{
			if (strlen($url) > 0 && substr($url, strlen($url) - 1, 1) != "/")
				$url = "{$url}/";

			$url = "{$url}events";
		}

		$html = @file_get_html($url);

		if (empty($html))
			return 0;

		$mynewsdesk_cat = Model_MyNewsDesk_Category::find($c_data->mynewsdesk_category_id);

		$newsroom_section = @$html->find('div[class=newsroom-section]', 0);

		foreach($html->find('div[class=with-articles] div[class=article]') as $element)
		{
			$event_title = $event_url = $date_start = $date_finish = $address = null;
			$is_all_day = 0;

			if ($anchor = @$element->find('h3[class=newsroom-list-header] a', 0))
			{
				$event_title = $anchor->innertext;
				$event_url = $anchor->href;

				if ($event_url && substr($event_url, 0, 4) != "http")
					$event_url = "http://www.mynewsdesk.com{$event_url}";
				
			}

			$date_start = $element->find('span[class=dtstart]', 0)->title;
			$date_finish = $element->find('span[class=dtend]', 0)->title;
			$address = $element->find('span[class=location]', 0)->innertext;

			$start_time = @$element->find('span[class=dtstart] span[class=time]', 0)->innertext;

			if (!empty($start_time) && trim($start_time) == 'all day')
				$is_all_day = 1;

			if (!empty($date_start))
				$date_start = date(DATE::FORMAT_MYSQL, strtotime($date_start));

			if (!empty($date_finish))
				$date_finish = date(DATE::FORMAT_MYSQL, strtotime($date_finish));

			$criteria = array();
			$criteria[] = array('title', $event_title);
			$criteria[] = array('company_id', $c_data->company_id);
			$criteria[] = array('type', Model_Content::TYPE_EVENT);

			if ($event = Model_Content::find($criteria))
			{}

			elseif (!empty($event_url) && !empty($event_title))
			{
				$event_title = $this->sanitize($event_title);
				
				$m_content = new Model_Content();
				$m_content->company_id = $c_data->company_id;
				$m_content->type = Model_Content::TYPE_EVENT;
				$m_content->title = $event_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->is_draft = 1;
				$m_content->title_to_slug();
				$m_content->is_scraped_content = 1;
				$m_content->save();

				$mynewsdesk_pr = new Model_PB_MyNewsDesk_Content();
				$mynewsdesk_pr->mynewsdesk_company_id = $c_data->mynewsdesk_company_id;
				$mynewsdesk_pr->mynewsdesk_category_id = $c_data->mynewsdesk_category_id;
				$mynewsdesk_pr->content_id = $m_content->id;
				$mynewsdesk_pr->url = $event_url;
				$mynewsdesk_pr->save();

				$m_pb_event = new Model_PB_EVENT();
				$m_pb_event->content_id = $m_content->id;
				$m_pb_event->date_start = $date_start;
				$m_pb_event->date_finish = $date_finish;
				$m_pb_event->is_all_day = $is_all_day;
				$m_pb_event->address = $address;
				$m_pb_event->save();

				$m_content->set_beats(array($mynewsdesk_cat->newswire_beat_id));

				$m_scraped_content = new Model_PB_Scraped_Content();
				$m_scraped_content->content_id = $m_content->id;
				$m_scraped_content->source = Model_PB_Scraped_Content::SOURCE_MYNEWSDESK;
				$m_scraped_content->source_url = $event_url;
				$m_scraped_content->save();
			}
		}

	}

	protected function fetch_single_event($mynewsdesk_pr, $company_id)
	{
		lib_autoload('simple_html_dom');

		if (empty($mynewsdesk_pr->url))
			return false;
		
		$html = @file_get_html($mynewsdesk_pr->url);

		if (empty($html))
		{
			$m_c_data = Model_Content_Data::find($mynewsdesk_pr->content_id);
			$m_c_data->content = "<p></p>";
			$m_c_data->summary = " ";
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

		$summary = null;

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

		if ($vevent = @$html->find('div[class=newsroom-article] div[class=vevent]', 0))
		{
			$vevent->find('div[class=event-info]', 0)->innertext = "";
			$content = @$vevent->innertext;	
		}

		
		$src = "data-src";
		if ($article = @$html->find('article', 0))
			$cover_image_url = @$article->find('div[class=media-wrapper] figure img', 0)->{$src};

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

		////////////////////////////////////////////////////////////////////////////

		$m_content->date_publish = $publish_date;
		$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_content->is_published = 1;
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
		if (!empty($cover_image_url))
		{
			$cover_file = "cover";
			$img_url = $cover_image_url;
			@copy($img_url, $cover_file);

			if (Image::is_valid_file($cover_file))
			{
				$im = Quick_Image::import("cover", $cover_file);
				 
				$im->company_id = $company_id;
				$im->save();

				$m_content->cover_image_id = $im->id;
				$m_content->save();

				$mynewsdesk_pr->cover_image_url = $cover_image_url;
				$mynewsdesk_pr->save();
			}
		}
		
	}	
}

?>
