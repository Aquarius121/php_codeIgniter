<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Category_Page_Controller extends Auto_Create_NR_Base { // fetching newswire_ca category page
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT * 
				FROM ac_nr_newswire_ca_category
				WHERE is_completed = 0";

		while ($cnt++ <= 10)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;			
			
			$category = Model_Newswire_CA_Category::from_db($result);
			if (!$category) break;

			$page_num = $category->pages_scanned;
			$page_num++;
			
			$url = "http://www.newswire.ca/news-releases/{$category->slug}/";
			$url = "{$url}?page={$page_num}&pagesize=90";
			//$url = "{$url}&pagesize=500";
							
			$is_a_new_rec_added = $this->get($url, $category);			

			$category->pages_scanned = $page_num;
			
			if (!$is_a_new_rec_added)
				$category->is_completed = 1;
			
			$category->save();			
			sleep (3);

		}
	}

	public function get($url, $category)
	{
		lib_autoload('simple_html_dom');

		if (!$html = file_get_html($url))
			return 0;

		$is_a_new_rec_added = 0;
		
		foreach($html->find('div[class=release-list] ul[class=list-unstyled] li') as $element)
		{
			if ($anchor = @$element->find('h4 a', 0))
			{
				$pr_title = $anchor->innertext;
				$pr_title = $this->sanitize($pr_title);
				$pr_url = $anchor->href;

				if ($pr_url && substr($pr_url, 0, 7) != "http://")
					$pr_url = "http://www.newswire.ca/{$pr_url}";

				if ($publish_date = @$element->find('div[class=col-sm-3] p', 0)->innertext);
				{
					$publish_date = str_replace("ET", "EDT", $publish_date);
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));
					$publish_date = Date::in($publish_date);
				}				
			}
			
			
			if (!empty($pr_url) && ! $pr = Model_PB_Newswire_CA_PR::find('url', $pr_url))
			{
				$is_a_new_rec_added = 1;
				$m_content = new Model_Content();
				$m_content->type = Model_Content::TYPE_PR;
				$m_content->title = $pr_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->is_excluded_from_news_center = 1;
				$m_content->is_scraped_content = 1;
				$m_content->date_publish = $publish_date;
				$m_content->is_draft = 1;
				$m_content->save();

				$newswire_ca_pr = new Model_PB_Newswire_CA_PR();
				$newswire_ca_pr->content_id = $m_content->id;
				$newswire_ca_pr->cat_id = $category->newswire_cat_id;
				$newswire_ca_pr->url = $pr_url;
				$newswire_ca_pr->newswire_ca_category_id = $category->id;
				$newswire_ca_pr->save();

				$m_pb_scraped_c = new Model_PB_Scraped_Content();
				$m_pb_scraped_c->content_id = $m_content->id;
				$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_NEWSWIRE_CA;
				$m_pb_scraped_c->source_url = $pr_url;
				$m_pb_scraped_c->save();
			}		
			
		}

		return $is_a_new_rec_added;

	}
}

?>
