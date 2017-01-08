<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Crawl_Permalink_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT c.*
				FROM ac_nr_topseos_company c
				LEFT JOIN ac_nr_topseos_crawl_permalink cp
				ON cp.topseos_company_id = c.id
				WHERE cp.topseos_company_id IS NULL 
				AND NOT ISNULL(NULLIF(c.permalink, ''))
				ORDER BY c.id
				LIMIT 1";

		while ($cnt++ <= 10)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;

			$comp = Model_TopSeos_Company::from_db($result);
			if (!$comp) break;

			$this->get($comp);

			sleep(1);
		}
	}

	public function get($comp)
	{
		if (empty($comp->permalink))
			return false;

		lib_autoload('simple_html_dom');
		$url = $comp->permalink;

		$response = Unirest\Request::get($url);

		if (empty($response->raw_body))
		{
			echo "going";
			return false;
		}

		$body = $response->raw_body;
		
		$html = str_get_html($body);

		$logo_image_path = null;
		$desc = null;

		if ($logo_p = @$html->find('p.box-logo-img', 0))
			if ($logo_img = @$logo_p->find('img', 0))
				$logo_image_path = $logo_img->src;

		if ($desc_div = @$html->find('div.details-con1', 0))
		{
			if ($desc_title_span = $desc_div->find('span.detail-title1', 0))
				$desc_title_span->innertext = "";

			$desc = $desc_div->innertext;
			$desc = HTML2Text::plain($desc);
			$desc = trim($desc);
		}

		$gnews_query = null;
		if ($gnews_ul = @$html->find('ul#company-gnews', 0))
			$gnews_query = $gnews_ul->{'data-query'};

		
		$c_data = Model_TopSeos_Company_Data::find($comp->id);
		$c_data->logo_image_path = value_or_null($logo_image_path);
		if (!empty($logo_image_path))
			$c_data->is_logo_valid = 1;

		$c_data->short_description = value_or_null($desc);
		$c_data->about_company = value_or_null($desc);
		$c_data->save();

		$comp->gnews_query = $gnews_query;
		$comp->save();

		$m_p_crawl = new Model_TopSeos_Crawl_Permalink();
		$m_p_crawl->topseos_company_id = $comp->id;
		$m_p_crawl->date_crawled = Date::$now->format(Date::FORMAT_MYSQL);
		$m_p_crawl->save();
		
	}	
}

?>
