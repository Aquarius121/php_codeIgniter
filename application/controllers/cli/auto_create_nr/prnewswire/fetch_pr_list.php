<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_List_Controller extends Auto_Create_NR_Base {
	
	protected $prn_scrape_id;

	public function index($num_pages_to_scan = 5)	
	{
		set_time_limit(86400);
		$cnt = 1;

		$prn_scrape = new Model_PRN_Scrape();
		$prn_scrape->date_run = Date::$now->format(Date::FORMAT_MYSQL);
		$prn_scrape->save();

		$this->prn_scrape_id = $prn_scrape->id;
		
		while ($cnt <= $num_pages_to_scan)
		{
			$url = "http://www.prnewswire.com/news-releases/news-releases-list/?page={$cnt}&pagesize=25";

			$is_a_new_rec_added = $this->get($url);

			if (!$is_a_new_rec_added)
			{
				$this->console("no more recs found");
				// break;
			}

			if ($cnt%10 == 0)
				sleep(1);

			$this->inspect($cnt);

			$cnt++;
		}

		$this->del_non_prnewswire_url_prs();
	}

	protected function get($url)
	{
		lib_autoload('simple_html_dom');
		// $html = file_get_html($url);

		$request = new HTTP_Request($url);
		$response = $request->get();

		if (!$response || !$response->data)
		{
			$this->console('fetch failed');
			die();
		}

		$html = str_get_html($response->data);

		$is_a_new_rec_added = 0;

		if (! $card_list = $html->find("div[class=card-list]", 0))
			return false;

		foreach($card_list->find("div[class=row]") as $card)
		{
			$cover_image_url = $publish_date = $title = $link = null;

			if ($col_lg_3 = $card->find("div[class=col-lg-3]", 0))
			{
				$cover_image_url = $col_lg_3->find('img', 0)->src;
				if ($cover_image_url)
					$cover_image_url = trim($cover_image_url);
			}
			
			if ($h3_date = $card->find('h3 small', 0))
				$publish_date = $h3_date->plaintext;

			if ($h3_a = $card->find('h3 a', 0))
			{
				$title = $h3_a->title;
				$link = $h3_a->href;
				$title = $this->sanitize($title);
				$title = str_replace("&amp;", "&", $title);
			}

			if (!$publish_date || !$title || !$link)
				continue;

			$publish_date = trim($publish_date);
			$publish_date = str_replace("ET", "EDT", $publish_date);
			$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));
			$publish_date = Date::in($publish_date);

			$criteria = array();
			$criteria[] = array('source_url', $link);
			$criteria[] = array('source', Model_PB_Scraped_Content::SOURCE_PRNEWSWIRE);

			if ($m_scraped_content = Model_PB_Scraped_Content::find($criteria))
				continue;

			$m_content = new Model_Content();
			$m_content->type = Model_Content::TYPE_PR;
			$m_content->title = $title;
			$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
			$m_content->date_publish = $publish_date;
			$m_content->is_excluded_from_news_center = 1;
			$m_content->is_draft = 1;
			$m_content->is_scraped_content = 1;
			$m_content->save();

			if (!empty($m_content->id))
			{
				$prn_pr = new Model_PB_PRN_PR();
				$prn_pr->content_id = $m_content->id;
				$prn_pr->prn_scrape_id = $this->prn_scrape_id;
				$prn_pr->cover_image_url = $cover_image_url;
				$prn_pr->save();

				$m_pb_scraped_c = new Model_PB_Scraped_Content();
				$m_pb_scraped_c->content_id = $m_content->id;
				$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_PRNEWSWIRE;
				$m_pb_scraped_c->source_url = $link;
				$m_pb_scraped_c->save();
			}

			$is_a_new_rec_added = 1;
		}		

		return $is_a_new_rec_added;

	}

	protected function del_non_prnewswire_url_prs()
	{
		$sql = "SELECT c.*,
				{{ pb.* AS prn_pr USING Model_PB_PRN_PR }},
				{{ sc.* AS scraped_content USING Model_PB_Scraped_Content }}
				FROM nr_pb_prn_pr pb
				INNER JOIN nr_content c
				ON pb.content_id = c.id
				INNER JOIN nr_pb_scraped_content sc
				ON sc.content_id = pb.content_id
				WHERE sc.source_url NOT LIKE '%prnewswire.com%'";

		$results = Model_PB_PRN_PR::from_sql_all($sql);
		foreach ($results as $result)
		{
			$this->console($result->scraped_content->content_id);
			$result->scraped_content->delete();
			$result->prn_pr->delete();
			$result->delete();
		}
	}
}
