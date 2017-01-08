<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		set_time_limit(86400);

		$cnt = 1;

		$sql = "SELECT pb.*, sc.source_url 
				FROM nr_pb_prn_pr pb
				INNER JOIN nr_pb_scraped_content sc
				ON pb.content_id = sc.content_id
				INNER JOIN nr_content c
				ON pb.content_id = c.id
				WHERE pb.is_pr_scraped = 0
				ORDER BY c.date_publish DESC
				LIMIT 1";

		while ($cnt++ <= 5)
		{
			$results = Model_PB_PRN_PR::from_sql_all($sql);
			if (!count($results))
				die();

			foreach ($results as $result)
			{
				$this->get($result);
				sleep(2);
			}

			$this->inspect($cnt);
			if ($cnt % 10 == 0)
				sleep(1);
		}
	}

	protected function get($prn_pr)
	{
		lib_autoload('simple_html_dom');

		$this->console($prn_pr->source_url);
		if (empty($prn_pr->source_url))
			return false;

		$this->console($prn_pr->content_id);

		$text = $this->fetch_url_through_phantomjs($prn_pr->source_url);
		if (empty($text))
			return false;

		$html = str_get_html($text);
		
		if (empty($html))
		{
			$prn_pr->is_pr_scraped = 1;
			$prn_pr->is_pr_revisited = 1;
			$prn_pr->save();
			$this->console("Not found");
			return;
		}

		$m_content = Model_Content::find($prn_pr->content_id);

		if (! $m_c_data = Model_Content_Data::find($prn_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $prn_pr->content_id;
		}

		$summary = null;
		$company_name = null;
		$prn_company_url = null;
		$logo_url = null;
		if ($header = $html->find("header[class=release-header]", 0))
		{
			if ($summary_p = $header->find("p[class=subtitle]", 0))
				$summary = $summary_p->innertext;

			if ($img_a = $header->find("a[data-gallery=logo]", 0))
			{
				if ($img = $img_a->find("img", 0))
				{
					$logo_url = $img->src;
					if ($logo_url)
						$logo_url = str_replace("max=200", "max=1600", $logo_url);
				}
			}

			if ($p_meta = $header->find("p[class=meta]", 0))
			{
				$p_meta_text = $p_meta->plaintext;
				$p_meta_text = trim($p_meta_text);
				if ($p_meta_text === "News provided by")
				{
					$p_div = $p_meta->parent;
					$company_name = $p_div->find("a", 0)->plaintext;
					$prn_company_url = $p_div->find("a", 0)->href;
				}
			}
		}

		if (empty($company_name))
		{
			if ($p = $html->find('p[class=release-details]', 0))
			{
				$company_name = $p->find("a", 0)->plaintext;
				$prn_company_url = $p->find("a", 0)->href;
			}
		}

		if (!empty($summary))
		{
			$summary = $this->sanitize($summary);
			$summary = str_replace("&amp;", "&", $summary);
		}

		if (!empty($company_name))
		{
			$company_name = $this->sanitize($company_name);
			$company_name = str_replace("&amp;", "&", $company_name);
		}

		$this->console("Company_name " . $company_name);
		$this->console("------------------------");

		if (!empty($company_name))
		{
			$normalized_name = String_Util::normalize($company_name);

			if (!$prn_company = Model_PRN_Company::find('normalized_name', $normalized_name))
				$prn_company = new Model_PRN_Company();

			if ($prn_company->prn_company)

			$prn_company->name = $company_name;
			$prn_company->normalized_name = $normalized_name;
			
			if (!empty($logo_url))
				$prn_company->logo_url = value_or_null($logo_url);

			if (!empty($prn_company_url))
				$prn_company->prn_url = value_or_null($prn_company_url);

			$prn_company->save();			
		}

		$content = null;
		foreach ($html->find("section[class=release-body], div[class=release-body]") as $section)
		{
			if ($purl = $section->find("p[id=PURL]", 0))
				$purl->innertext = " ";

			if ($cont_reading = $section->find("div[class=continue-reading]", 0))
				$cont_reading->innertext = " ";

			$content .= $section->innertext;
		}

		if (empty($content))
		{
			$prn_pr->is_pr_scraped = 1;
			$prn_pr->save();
			return;
		}

		$keep_tags = true;
		if (!empty($content))
		{
			$content = str_replace('<div class="col-sm-10 col-sm-offset-1">', "<div>", $content);
			$content = str_replace('<div class="row">', "<div>", $content);
			$content = preg_replace("#\<figure(.*)/figure>#iUs", "", $content);

			$content = $this->sanitize($content, $keep_tags);
			$content = $this->linkify($content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));
		}

		$content = $this->update_prnewswire_wording($content);

		$categories = array();
		if ($category_div = $html->find("div[class=links-group]", 0))
		{
			foreach ($category_div->find("a[itemprop=articleSection]") as $anchor)
			{
				$category = new stdClass();
				$category->name = trim($anchor->plaintext);
				$category->name = str_replace("&amp;", "&", $category->name);
				$category_url = $anchor->href;
				$slug = str_replace("http://www.prnewswire.com/news-releases/", "", $category_url);
				$slugs = explode("/", $slug);
				if (is_array($slugs) && count($slugs))
					$slug = $slugs[0];

				$category->group_slug = $slug;
				$categories[] = $category;
			}
		}

		$images = array();
		if ($gallery = $html->find("div[class=gallery-carousel-main], div[id=galleryContainer], figure ", 0))
		{
			foreach ($gallery->find("img[class=img-responsive], a.hidden-xs[data-toggle=modal] img, a[data-type=image] img") as $img)
			{
				$image = new stdClass();
				$image->src = $img->src;
				$image->title = $img->title;
				$images[] = $image;
			}
		}

		$raw_data = new stdClass();
		if (count($categories))
			$raw_data->categories = $categories;
		
		if (count($images))
			$raw_data->images = $images;

		if (!empty($company_name))
			$raw_data->company_name = $company_name;

		$address = null;
		if ($first_para = $html->find("p[itemprop=articleBody]", 0))
			if ($location = $first_para->find("span[itemprop=addressLocality]", 0))
				$address = $location->plaintext;

		if ($address)
			$address = trim($address);

		$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_content->is_published = 1;
		$m_content->is_approved = 1;
		$m_content->is_premium = 1;
		$m_content->title_to_slug();
		$m_content->save();
		
		// Now saving the content data
		$m_c_data->summary = $summary;
		$m_c_data->content = value_or_null($content);
		$m_c_data->save();

		if (! $m_pb_pr = Model_PB_PR::find($m_content->id))
		{
			$m_pb_pr = new Model_PB_PR();
			$m_pb_pr->content_id = $m_content->id;
		}
	
		$m_pb_pr->location = value_or_null($address);
		$m_pb_pr->is_distribution_disabled = 1;
		$m_pb_pr->save();

		
		if (!empty($company_name) && $prn_company)
			$prn_pr->prn_company_id = $prn_company->id;

		$prn_pr->raw_data($raw_data);
		$prn_pr->is_pr_scraped = 1;
		$prn_pr->is_pr_revisited = 1;
		$prn_pr->save();

		if (isset($prn_company))
		{
			$this->inspect("Old date was => {$prn_company->date_last_pr_submitted}");
			$this->recheck_for_prn_sop($prn_company);
		}
	}

	protected function recheck_for_prn_sop($prn_comp)
	{
		$this->inspect("UPDATING LATEST PR DATE for PRN Comp = {$prn_comp->id}");
		$this->update_latest_pr_date($prn_comp);

		if ($nw_ca_comp = Model_Newswire_CA_Company::find('name', $prn_comp->name))
		{
			$this->inspect("UPDATING NEWSWIRE_CA COMPANY VALID FOR Comp = {$nw_ca_comp->id}");
			$this->update_newswire_ca_prn_valid($nw_ca_comp);
		}

		if ($prweb_comp = Model_PRWeb_Company::find('name', $prn_comp->name))
		{
			$this->inspect("UPDATING PRWEB COMPANY VALID FOR Comp = {$prweb_comp->id}");
			$this->update_prweb_prn_valid($prweb_comp);
		}
	}

	protected function update_prnewswire_wording($content)
	{
		$html = str_get_html($content);
		
		foreach($html->find('p') as $p)
		{
			$p_text = $p->innertext;
			$pattern = '#[\s\S]+\/PRNewswire([A-Za-z0-9\. ]+)?\/ ?\-?(\&\#160;)?#';
			$p_text = preg_replace($pattern, "", $p_text);
			$p->innertext = $p_text;
		}

		$content = $html->innertext;
		return $content;
	}

	public function map_categories()
	{
		set_time_limit(86400);
		$sql = "SELECT pb.*
				FROM nr_pb_prn_pr pb
				WHERE pb.is_pr_scraped = 1
				AND pb.is_category_mapped = 0
				ORDER BY pb.content_id
				LIMIT 100";

		while(1)
		{
			$results = Model_PB_PRN_PR::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$result->map_categories();
				$result->is_category_mapped = 1;
				$result->save();
			}
		}
	}

	public function pull_cover_image()
	{
		set_time_limit(86400);
		$sql = "SELECT c.*,
				{{ pb.* AS prn_pr USING Model_PB_PRN_PR }}
				FROM nr_pb_prn_pr pb
				INNER JOIN nr_content c
				ON pb.content_id = c.id
				WHERE pb.is_pr_scraped = 1
				AND pb.is_cover_image_pulled = 0
				AND c.date_publish > '2016-07-26'
				ORDER BY c.date_publish DESC
				LIMIT 10";

		$cnt = 1;
		while($cnt++ <= 3)
		{
			$results = Model_Content::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $content)
			{
				$this->save_cover_image($content);
				$content->prn_pr->is_cover_image_pulled = 1;
				$content->prn_pr->save();

				$this->console($content->id . " -------" . $content->date_publish);
				$this->console($content->slug);
				$this->console("-------------");
			}

			$this->inspect($cnt);
		}
	}

	public function save_cover_image($content)
	{
		$raw_data = $content->prn_pr->raw_data();
		if (!isset($raw_data->images) && empty($content->prn_pr->cover_image_url))
			return;

		$img_file = "image";
		if (isset($raw_data->images) && is_array($raw_data->images) && count($raw_data->images))
		{
			$images = $raw_data->images;
			$image = $images[0];

			@copy($image->src, $img_file);
			if (Image::is_valid_file($img_file))
			{
				$m_image = Quick_Image::import('cover', $img_file);
				$content->cover_image_id = $m_image->id;
				$content->save();
				$this->console('FROM RELATED');
			}
		}

		if ($content->cover_image_id)
			return;

		$cover_image_url = $content->prn_pr->cover_image_url;
		if (empty($cover_image_url))
			return;

		if (!String_Util::contains($cover_image_url, "?max="))
			$cover_image_url = "{$cover_image_url}?max=1600";

		$cover_image_url = str_replace("prnthumb", "prnvar", $cover_image_url);		

		@copy($cover_image_url, $img_file);

		if (Image::is_valid_file($img_file))
		{
			$m_image = Quick_Image::import('cover', $img_file);
			$content->cover_image_id = $m_image->id;
			$content->save();
		}	

		if ($content->cover_image_id)
		{
			$this->console('from cover image');
			return;
		}
	}	

	public function update_latest_pr_dates()
	{
		$cnt = 1;
		
		$sql = "SELECT * 
				FROM ac_nr_prn_company
				WHERE is_last_pr_date_updated = 0
				ORDER BY id DESC
				LIMIT 20";

		while (1)
		{
			$results = Model_PRN_Company::from_sql_all($sql);
			if (!count($results)) break;

			foreach ($results as $prn_comp)
			{
				$this->console($prn_comp->id);
				$this->update_latest_pr_date($prn_comp);
			}
		}
	}

	protected function update_latest_pr_date($prn_comp)
	{
		$sql = "SELECT c.date_publish
				FROM nr_pb_prn_pr pb
				INNER JOIN nr_content c
				ON pb.content_id = c.id
				WHERE pb.prn_company_id = ?
				ORDER BY c.date_publish DESC
				LIMIT 1";

		$date_publish = null;		
		if ($content = Model::from_sql($sql, array($prn_comp->id)))
			$date_publish = $content->date_publish;

		$this->inspect("UPDATING date_publish = {$date_publish}");

		$prn_comp->date_last_pr_submitted = value_or_null($date_publish);
		$prn_comp->is_last_pr_date_updated = 1;
		$prn_comp->is_last_pr_date_migrated = 0;
		$prn_comp->save();
	}
}