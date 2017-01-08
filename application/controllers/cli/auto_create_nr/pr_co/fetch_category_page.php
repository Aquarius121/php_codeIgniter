<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Category_Page_Controller extends Auto_Create_NR_Base { 
	
	public function index()
	{
		$cnt = 1;

		$sql = "SELECT * 
				FROM ac_nr_pr_co_category
				WHERE is_completed = 0";

		while ($cnt++ <= 30)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$category = Model_PR_Co_Category::from_db($result);
			if (!$category) break;

			$page_num = $category->pages_scanned;
			$page_num++;
			
			$url = "http://pr.co/category/{$category->slug}/en?page={$page_num}";


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


		if ($pressdocs = @$html->find('div[id=pressdocs]', 0))
			$div = 'pressdocs';
		else
			$div = 'campaigns';

		// foreach($html->find('div[id=pressdocs] ol li') as $element)
		foreach($html->find("div[id={$div}] ol li") as $element)
		{
			if ($anchor = @$element->find('span[class=release_title] a', 0))
			{
				$pr_title = $anchor->plaintext;
				$pr_title = $this->sanitize($pr_title);
				$pr_title = HTML2Text::plain($pr_title);
				
				$pr_url = $anchor->href;

				if ($cname_span = @$element->find('span[class=company_name]', 0))
				{
					$company_name = @$cname_span->find('a', 0)->plaintext;

					if (!empty($company_name))
						$company_name = trim($company_name);

					@$cname_span->find('a', 0)->innertext = "";
					$publish_date = @$cname_span->plaintext;
				}
				

				if (!empty($publish_date))
				{
					$publish_date = trim($publish_date);
					$publish_date = str_replace("-", "", $publish_date);
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));
				}

			}
			
			if (!empty($pr_url) && !empty($pr_title) &&
				!empty($publish_date) && ! $pr = Model_PB_PR_Co_Content::find('url', $pr_url))
			{
				$is_a_new_rec_added = 1;
				$m_content = new Model_Content();
				$m_content->type = Model_Content::TYPE_PR;
				$m_content->title = $pr_title;
				$m_content->date_created = Date::$now->format(Date::FORMAT_MYSQL);
				$m_content->date_publish = $publish_date;
				$m_content->is_draft = 1;
				$m_content->is_excluded_from_news_center = 1;
				$m_content->is_scraped_content = 1;
				$m_content->title_to_slug();
				$m_content->save();

				$pr_co_pr = new Model_PB_PR_Co_Content();
				$pr_co_pr->content_id = $m_content->id;
				$pr_co_pr->url = $pr_url;
				$pr_co_pr->pr_co_category_id = $category->id;
				
				if (!empty($company_name))
					$company_name = $this->sanitize($company_name);					

				$pr_co_pr->company_name = value_or_null($company_name);
				$pr_co_pr->save();

				$m_pb_scraped_c = new Model_PB_Scraped_Content();
				$m_pb_scraped_c->content_id = $m_content->id;
				$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_PR_CO;
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
