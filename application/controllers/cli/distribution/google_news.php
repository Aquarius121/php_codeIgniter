<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Google_News_Controller extends CLI_Base {

	public function index()
	{
		$this->trace('started');

		$dt2ha = escape_and_quote(Date::hours(-2));
		$dt6ha = escape_and_quote(Date::hours(-6));
		$dt8ha = escape_and_quote(Date::hours(-8));

		$sql = "SELECT c.* FROM nr_content c 
			LEFT JOIN nr_distribution_status_google_news dsgn
			ON dsgn.content_id = c.id 
			WHERE c.is_published = 1
			AND c.is_premium = 1
			AND   c.date_publish > {$dt8ha} AND (
				  (c.date_publish < {$dt2ha} AND (dsgn.requests = 0 OR dsgn.requests IS NULL))
			  OR (c.date_publish < {$dt6ha} AND (dsgn.requests = 1))
			) ORDER BY c.id ASC LIMIT 1";

		while (true)
		{
			$dbr = $this->db->query($sql);
			$m_content = Model_Content::from_db($dbr);
			if (!$m_content) break;
			$m_dsgn = Model_Distribution_Status_Google_News::find($m_content->id);
			if (!$m_dsgn) $m_dsgn = new Model_Distribution_Status_Google_News();
			$m_dsgn->content_id = $m_content->id;
			$m_dsgn->requests++;
			$m_dsgn->save();

			$this->trace('started', $m_content->id);
			$results = Google_News_Search_Results::find($m_content->title);

			foreach ($results as $result)
			{
				$d_url = Google_News_Search_Results::extract_content_url($result->link);

				// verify that the news item is actually
				// a copy of the press release
				$d_source = @file_get_contents($d_url);
				if (!str_contains($d_source, $this->website_url()))
					continue;

				$d_site = Model_Distribution_Site::find_site_from_url($d_url);
				if (!$d_site) $d_site = Model_Distribution_Site::create_site_from_url($d_url);

				// sourced from google news
				// ==> quality should be good
				$d_site->quality = 1;
				$d_site->save();

				$d_index = Model_Distribution_Index::find_index_from_source($d_site, $m_content);
				if (!$d_index) $d_index = Model_Distribution_Index::create_index_from_source($d_site, $m_content);
				$d_index->url = $d_url;
				$d_index->save();
				$this->trace($d_url);
			}

			$this->trace('finished', $m_content->id);
			$this->trace_sleep(30);
		}

		$this->trace('finished');
	}
	
}

?>