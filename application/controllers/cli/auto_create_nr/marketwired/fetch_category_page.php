<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Category_Page_Controller extends Auto_Create_NR_Base { // fetching marketwired industry page

	public function index()
	{
		$cnt = 1;

		$sql = "SELECT * 
				FROM ac_nr_marketwired_category
				WHERE is_read = 0";

		while ($cnt++ <= 1)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$category = Model_MarketWired_Category::from_db($result);
			if (!$category) break;

			$this->get($category);

			$category->is_read = 1;
			$category->save();
			// sleep (3);

		}
	}

	public function get($category)
	{
		lib_autoload('simple_html_dom');

		$url = $category->xml_url;

		if ( ! $sxml = simplexml_load_file($url))
			return;

		if (!count($sxml->channel->item))
			return;
		
		foreach ($sxml->channel->item as $item)
		{
			$pr_title = $item->title;
			$pr_title = $this->sanitize($pr_title);
			$pr_url = $item->link;

			if ( ! $pr = Model_PB_MarketWired_PR::find('url', $pr_url))
			{
				$is_a_new_rec_added = 1;
				$m_content = new Model_Content();
				$m_content->type = Model_Content::TYPE_PR;
				$m_content->title = $pr_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->is_excluded_from_news_center = 1;
				$m_content->is_scraped_content = 1;
				$m_content->is_draft = 1;
				$m_content->save();

				$marketwired_pr = new Model_PB_MarketWired_PR();
				$marketwired_pr->content_id = $m_content->id;
				$marketwired_pr->cat_id = $category->newswire_cat_id;
				$marketwired_pr->url = $pr_url;
				$marketwired_pr->marketwired_category_id = $category->id;
				$marketwired_pr->save();

				$m_pb_scraped_c = new Model_PB_Scraped_Content();
				$m_pb_scraped_c->content_id = $m_content->id;
				$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_MARKETWIRED;
				$m_pb_scraped_c->source_url = $pr_url;
				$m_pb_scraped_c->save();
			}
		}

		
	}
}

?>
