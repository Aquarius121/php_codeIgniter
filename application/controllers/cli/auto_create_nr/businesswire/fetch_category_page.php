<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Category_Page_Controller extends Auto_Create_NR_Base { // fetching businesswire category page
	
	public function index()
	{
		$cnt = 1;

		$sql = "SELECT * 
				FROM ac_nr_businesswire_category
				WHERE pages_scanned < total_pages
				AND is_completed = 0";

		while ($cnt++ <= 10)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$category = Model_BusinessWire_Category::from_db($result);
			if (!$category) break;

			$url = "http://www.businesswire.com/portal/site/home/news/industry/?vnsId={$category->vnsId}";
			
			$page_num = $category->pages_scanned;
			$page_num++;

			if ($page_num > 1)
			{
				$url = "http://www.businesswire.com/portal/site/home/template.PAGE/news/industry";
				$url = "{$url}/?javax.portlet.tpst=08c2aa13f2fe3d4dc1b6751ae1de75dd&javax.portlet.";
				$url = "{$url}prp_08c2aa13f2fe3d4dc1b6751ae1de75dd_vnsId={$category->vnsId}&javax.portlet.";
				$url = "{$url}prp_08c2aa13f2fe3d4dc1b6751ae1de75dd_viewID=MY_PORTAL_VIEW&javax.portlet.";
				$url = "{$url}prp_08c2aa13f2fe3d4dc1b6751ae1de75dd_ndmHsc=v2*A1428490800000*{$category->c1}";
				$url = "{$url}*DgroupByDate*G{$page_num}*M{$category->vnsId}*{$category->c2}&javax.portlet";
				$url = "{$url}begCacheTok=com.vignette.cachetoken&javax.portlet.endCacheTok=com.vignette.cachetoken";
			}
			
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
		$html = file_get_html($url);

		$is_a_new_rec_added = 0;

		foreach($html->find('ul[class=bwNewsList] li') as $element)
		{
			$pr_title = null;
			$pr_url = null;
			$cover_image_url = null;

			$el = @$element->find('a[class=bwTitleLink]', 0);
			$pr_title = $el->plaintext;
			$pr_title = $this->sanitize($pr_title);
			$pr_url = $el->href;
			
			if (!empty($pr_url))
				$pr_url = "http://businesswire.com{$pr_url}";

			if ($thumb_a = @$element->find('div div[class=bwThumbs] a', 1))
			{
				$cover_image_url = @$thumb_a->find('img', 0)->src;
				if (!empty($cover_image_url))
					$cover_image_url = str_replace("/21/", "/4/", $cover_image_url);
			}

			$criteria = array();
			$pr = Model_PB_BusinessWire_PR::find('url', $pr_url);
			$m_content = Model_Content::find('title', $pr_title);
			if (!$pr  && !$m_content)
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

				$businesswire_pr = new Model_PB_BusinessWire_PR();
				$businesswire_pr->content_id = $m_content->id;
				$businesswire_pr->cat_id = $category->newswire_cat_id;
				$businesswire_pr->url = $pr_url;
				$businesswire_pr->cover_image_url = value_or_null($cover_image_url);
				$businesswire_pr->businesswire_category_id = $category->id;
				$businesswire_pr->save();

				$m_pb_scraped_c = new Model_PB_Scraped_Content();
				$m_pb_scraped_c->content_id = $m_content->id;
				$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_BUSINESSWIRE;
				$m_pb_scraped_c->source_url = $pr_url;
				$m_pb_scraped_c->save();
			}
			
			
		}

		return $is_a_new_rec_added;

	}
}

?>
