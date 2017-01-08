<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Google_News_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT c.*
				FROM ac_nr_topseos_company c
				LEFT JOIN ac_nr_topseos_fetch_google_news fg
				ON fg.topseos_company_id = c.id
				WHERE fg.topseos_company_id IS NULL 
				AND NOT ISNULL(NULLIF(c.gnews_query, ''))
				ORDER BY c.id DESC
				LIMIT 1";

		while ($cnt++ <= 2)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;

			$comp = Model_TopSeos_Company::from_db($result);
			if (!$comp) break;

			$this->get($comp);

			sleep(3);
		}
	}

	public function get($comp)
	{
		if (empty($comp->permalink))
			return false;

		lib_autoload('simple_html_dom');
		
		$url = "http://news.google.com/news?output=rss&q='{$comp->gnews_query}'";

		$response = Unirest\Request::get($url);
		if (empty($response->raw_body))
		{
			echo "going";
			return false;
		}
		
		$rss = new RSS_Reader();
		$rss_items = $rss->read_string($response->raw_body);
		if (!$rss_items) return array();
		$results = array();
		
		foreach ($rss_items as $item)
		{
			$link = $item->link;
			if (String_Util::contains($link, '&url='))
				$link = substr($link, strpos($link, '&url=')+5);

			$title = $item->title;
			$date_publish = $item->date;
			
			$content = $item->content;
			if (empty($content))
				$content = $item->summary;

			if (empty($content))
				$content = $title;

			$content = HTML2Text::plain($content);

			$m_content = new Model_Content();
			$m_content->type = Model_Content::TYPE_NEWS;
			$m_content->title = $title;
			$m_content->title_to_slug();
			$m_content->date_publish = $date_publish;
			$m_content->date_created = $date_publish;
			$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
			$m_content->is_published = 1;
			$m_content->is_approved = 1;
			$m_content->is_excluded_from_news_center = 1;
			$m_content->save();

			// Now saving the content data
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $m_content->id;
			$m_c_data->summary = $content;
			$m_c_data->save();

			$m_pb_news = new Model_PB_News();
			$m_pb_news->content_id = $m_content->id;
			$m_pb_news->source_url = $link;
			$m_pb_news->is_external = 1;
			$m_pb_news->save();

			$topseos_news = new Model_PB_TopSeos_News();
			$topseos_news->content_id = $m_content->id;
			$topseos_news->topseos_company_id = $comp->id;
			$topseos_news->save();
		}		
		
		$m_p_crawl = new Model_TopSeos_Fetch_Google_News();
		$m_p_crawl->topseos_company_id = $comp->id;
		$m_p_crawl->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
		$m_p_crawl->save();		
	}
}

?>
