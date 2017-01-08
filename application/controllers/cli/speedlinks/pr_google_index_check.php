<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class PR_Google_Index_Check_Controller extends CLI_Base {
	
	const NUM_CONTENT_SAMPLE = 20;

	public function index()
	{
		// First checking total number of 
		// prs checked for indexing yet.

		$sql = "SELECT COUNT(content_id) AS count
				FROM sl_speedlinks_pr_google_index_check";

		$count = Model_SpeedLinks_PR_Google_Index_Check::from_sql($sql)->count;
		
		if ($count < static::NUM_CONTENT_SAMPLE)
		{
			$this->google_index_count_1();
			return;
		}

		$this->google_index_count_2();
	}

	protected function google_index_count_1()
	{
		$sql = "SELECT DISTINCT(c.id) AS content_id
				FROM nr_distribution_index i
				INNER JOIN nr_content c 
				ON i.content_id = c.id
				INNER JOIN sl_speedlinks_fc_link sl
				ON sl.distribution_index_id = i.id
				LEFT JOIN sl_speedlinks_pr_google_index_check gi
				ON gi.content_id = i.content_id 
				WHERE gi.content_id IS NULL
				ORDER BY c.id DESC
				LIMIT 1";

		$result = Model_Content::from_sql($sql);

		if (!$result)
			return false;

		$content_id = $result->content_id;
		$m_content = Model_Content::find($content_id);
		$google_index_count = $this->get_search_results($m_content->title);

		$gic = new Model_SpeedLinks_PR_Google_Index_Check();
		$gic->content_id = $content_id;
		$gic->date_check_1 = Date::$now->format(Date::FORMAT_MYSQL);
		$gic->google_result_count_check_1 = $google_index_count;
		$gic->save();
	}

	// checking index count again after 5 days
	protected function google_index_count_2()
	{
		$date_5_days_ago = Date::days(-5)->format(Date::FORMAT_MYSQL);

		$sql = "SELECT gi.*, c.title
				FROM sl_speedlinks_pr_google_index_check gi
				INNER JOIN nr_content c
				ON gi.content_id = c.id
				WHERE gi.date_check_2 IS NULL
				AND gi.date_check_1 <= '{$date_5_days_ago}'
				LIMIT 1";

		$result = Model_SpeedLinks_PR_Google_Index_Check::from_sql($sql);

		if (!$result)
			return false;

		$google_index_count = $this->get_search_results($result->title);
		$result->date_check_2 = Date::$now->format(Date::FORMAT_MYSQL);
		$result->google_result_count_check_2 = $google_index_count;
		$result->save();
	}

	protected function get_search_results($title)
	{
		$url = Google_Search_Result_Count::url($title);
		$user_agent = User_Agent::random();
		$request = new HTTP_Request($url);
		$request->set_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
		$request->set_header('Accept-Language', 'en-us,en;q=0.5');
		$request->set_header('User-Agent', $user_agent);
		$response = @$request->get();

		if (!$response)
			return 0;
		
		// extract the approximate number of results
		$pattern = '#(about\s+)?([0-9,\.]+)\s+results#is';

		if (!preg_match($pattern, $response->data, $match)) 
			return 0;

		$count = (int) preg_replace('#[^0-9]#', null, $match[2]);

		return $count;
	}
}

?>