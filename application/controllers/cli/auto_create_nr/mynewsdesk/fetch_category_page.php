<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Category_Page_Controller extends Auto_Create_NR_Base { // fetching mynewsdesk category page
	
	public function index()
	{
		$cnt = 1;

		$sql = "SELECT * 
				FROM ac_nr_mynewsdesk_category
				WHERE is_completed = 0";

		while ($cnt++ <= 30)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;			
			
			$category = Model_MyNewsDesk_Category::from_db($result);
			if (!$category) break;

			$page_num = $category->pages_scanned;
			$page_num++;
			
			$url = "http://www.mynewsdesk.com/us/stories/search/pressreleases?all_sites=on";
			$url = "{$url}&commit=Filter+search+result&current_site=us&date_end=&date_mode=between";
			$url = "{$url}&date_on=&date_start=&g_region=&locales%5B%5D=en&page={$page_num}&query=";
			$url = "{$url}&subject={$category->subject}&subjects={$category->subject}&utf8=%E2%9C%93";

			$is_a_new_rec_added = $this->get($url, $category);

			$category->pages_scanned = $page_num;
			
			if (!$is_a_new_rec_added)
				$category->is_completed = 1;
			
			$category->save();			
			if ($cnt%10 == 0)
				sleep (3);

		}
	}

	public function get($url, $category)
	{
		lib_autoload('simple_html_dom');

		if (!$html = file_get_html($url))
			return 0;

		$is_a_new_rec_added = 0;

		foreach($html->find('div[class=pressrelease]') as $element)
		{
			if ($anchor = @$element->find('div[class=article-text] div[class=header] h3 a', 0))
			{
				$pr_title = $anchor->plaintext;
				$pr_title = HTML2Text::plain($pr_title);
				$pr_url = $anchor->href;
				$country = @$anchor->find('img[class=flag]', 0)->title;

				$time_string = @$element->find('small[class=time]', 0)->plaintext;
				$parts = explode('•', $time_string);
				if (is_array($parts) && count($parts) > 2)
				{
					$company_name = trim($parts[1]);
					$publish_date = trim($parts[2]);
				}

				if (!empty($publish_date))
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));

				if ($pr_url && substr($pr_url, 0, 7) != "http://")
					$pr_url = "http://www.mynewsdesk.com{$pr_url}";

				$cover_image_url = @$element->find('div[class=preview] a[class=material] img', 0)->src;
				
			}
			
			if (!empty($pr_url) && !empty($pr_title) &&
				!empty($publish_date) && ! $pr = Model_PB_MyNewsDesk_Content::find('url', $pr_url))
			{
				$is_a_new_rec_added = 1;
				$m_content = new Model_Content();
				$m_content->type = Model_Content::TYPE_PR;
				$m_content->title = $this->sanitize($pr_title);
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->date_publish = $publish_date;
				$m_content->is_draft = 1;
				$m_content->is_excluded_from_news_center = 1;
				$m_content->is_scraped_content = 1;
				$m_content->title_to_slug();
				
				if (!empty($m_content->slug))
				{
					$full_slug = $slug_64_chars = $m_content->slug;
					
					// the dev db had 64 length slug 
					// so the slug was truncated
					if (strlen($full_slug) > 64)
						$slug_64_chars = substr($full_slug, 0, 64);
					
					$slug_q = "SELECT id FROM nr_content
							WHERE slug='{$full_slug}' 
							OR slug='{$slug_64_chars}'";

					if (Model_Content::from_sql($slug_q)->id)
						continue;

				}


				$m_content->save();

				$mynewsdesk_pr = new Model_PB_MyNewsDesk_Content();
				$mynewsdesk_pr->content_id = $m_content->id;
				$mynewsdesk_pr->url = $pr_url;
				$mynewsdesk_pr->country = value_or_null($country);
				$mynewsdesk_pr->mynewsdesk_category_id = $category->id;
				$mynewsdesk_pr->cover_image_url = value_or_null($cover_image_url);
				$mynewsdesk_pr->time_string = value_or_null($time_string);
				
				if (!empty($company_name))
					$company_name = $this->sanitize($company_name);
					

				$mynewsdesk_pr->company_name = value_or_null($company_name);
				$mynewsdesk_pr->save();

				$m_pb_scraped_c = new Model_PB_Scraped_Content();
				$m_pb_scraped_c->content_id = $m_content->id;
				$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_MYNEWSDESK;
				$m_pb_scraped_c->source_url = $pr_url;
				$m_pb_scraped_c->save();

				$m_pr = new Model_PB_PR();
				$m_pr->content_id = $m_content->id;
				$m_pr->cat_1_id = $category->newswire_cat_id;
				$m_pr->is_distribution_disabled = 1;
				$m_pr->save();

				$m_content->set_beats(array($category->newswire_beat_id));
			}
		}

		return $is_a_new_rec_added;

	}
}

?>
