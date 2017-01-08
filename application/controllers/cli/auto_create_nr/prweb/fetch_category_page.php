<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Category_Page_Controller extends Auto_Create_NR_Base { // fetching prweb category page
	
	public function index()
	{
		$cnt = 1;

		$sql = "SELECT * 
				FROM ac_nr_prweb_category
				WHERE pages_scanned < 30
				AND is_completed = 0
				LIMIT 1";

		while ($cnt++ <= 10)		
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$category = Model_PRWeb_Category::from_db($result);
			if (!$category) break;

			$url = "http://www.prweb.com/newsbycategory/{$category->slug}/";

			$page_num = $category->pages_scanned;
			$page_num++;

			if ($page_num > 1)
				$url = "{$url}$page_num.htm";
			
			$is_a_new_rec_added = $this->get($url, $category);			
			$category->pages_scanned = $page_num;
			if (!$is_a_new_rec_added)
				$category->is_completed = 1;
			$category->save();

			// now adding a log to prweb srape run page read
			$sql_latest_scrape = "SELECT MAX(id) AS latest_scrape_run_id
									FROM ac_nr_prweb_scrape_run";

			$prweb_scrape_run_id = (int) $this->db->query($sql_latest_scrape)->row()->latest_scrape_run_id;

			if ($prweb_scrape_run_id && $category->id)
			{
				$criteria = array();
				$criteria[] = array('prweb_scrape_run_id', $prweb_scrape_run_id);
				$criteria[] = array('prweb_category_id', $category->id);

				if (! $prweb_scrape_run_page_read = Model_PRWeb_Scrape_Run_Page_Read::find($criteria))
				{
					$prweb_scrape_run_page_read = new Model_PRWeb_Scrape_Run_Page_Read();
					$prweb_scrape_run_page_read->prweb_scrape_run_id = $prweb_scrape_run_id;
					$prweb_scrape_run_page_read->prweb_category_id = $category->id;
				}

				$prweb_scrape_run_page_read->page_num_read_last = $page_num;
				
				if ($is_a_new_rec_added)
					$prweb_scrape_run_page_read->page_num_where_last_new_rec_found = $page_num;
				$prweb_scrape_run_page_read->date_last_page_read = Date::$now->format(Date::FORMAT_MYSQL);
				$prweb_scrape_run_page_read->save();
			}

			// sleep (3);

		}
	}

	public function get($url, $category)
	{
		lib_autoload('simple_html_dom');
		$html = file_get_html($url);

		$is_a_new_rec_added = 0;

		foreach($html->find('article[class=article-box] a') as $element)
		{
			$pr_title = @$element->find('h1', 0)->plaintext;
			$pr_title = $this->sanitize($pr_title);
			$pr_url = "http://prweb.com".$element->href;
			$publish_date = @$element->find('p[class=article-box-text] span[class=article-box-date]', 0)->innertext;
			$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));

			
			$criteria = array();
			if ( ! $pr = Model_PB_PRWeb_PR::find('url', $pr_url))
			{
				$is_a_new_rec_added = 1;
				$m_content = new Model_Content();
				$m_content->type = Model_Content::TYPE_PR;
				$m_content->title = $pr_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->date_publish = $publish_date;
				$m_content->is_excluded_from_news_center = 1;
				$m_content->is_draft = 1;
				$m_content->is_scraped_content = 1;
				$m_content->save();

				if (!empty($m_content->id))
				{
					$prweb_pr = new Model_PB_PRWeb_PR();
					$prweb_pr->content_id = $m_content->id;
					$prweb_pr->cat_id = $category->newswire_cat_id;
					$prweb_pr->url = $pr_url;
					$prweb_pr->prweb_category_id = $category->id;
					$prweb_pr->save();

					$m_pb_scraped_c = new Model_PB_Scraped_Content();
					$m_pb_scraped_c->content_id = $m_content->id;
					$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_PRWEB;
					$m_pb_scraped_c->source_url = $pr_url;
					$m_pb_scraped_c->save();
				}
			}
			
			
		}

		return $is_a_new_rec_added;

	}
}

?>
