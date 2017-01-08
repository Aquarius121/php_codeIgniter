<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/speedlinks/base');

class Submit_NR_URLs_Controller extends SpeedLinks_Base {

	protected $fin_site_ids;

	const DRIP_FEED_DAYS = 1;
	const LIMIT = 1000;
	
	public function index()
	{
		$urls = $this->get_nr_urls();

		if (!count($urls))
			return false;

		$today_left = $this->get_remaining_limit();

		if ($today_left <= 0)
			return false;

		// Now submitting the URLs		
		$url_str = implode("\n", $urls);
		$batch_title = "nr_batch_". time();
		$submitted_urls = $this->submit($url_str, $batch_title, static::DRIP_FEED_DAYS);

		if (!is_array($submitted_urls) || !count($submitted_urls))
			return false;

		$sl_batch = new Model_SpeedLinks_Batch();
		$sl_batch->title = $batch_title;
		$sl_batch->dripfeed_days = static::DRIP_FEED_DAYS;
		$sl_batch->date_submitted = Date::$now->format(Date::FORMAT_MYSQL);
		$sl_batch->submission_type = Model_SpeedLinks_Batch::SUBMISSION_TYPE_NR;
		$sl_batch->save();

		$speedlinks_batch_id = $sl_batch->id;

		$values = array();
		foreach ($submitted_urls as $url)
			if ($idx = array_search($url, $urls))
				$values[] = "('{$idx}', '{$speedlinks_batch_id}')";

		if (!count($values))
			return false;

		$values_str = implode(", ", $values);

		$sql = "INSERT INTO 
				sl_speedlinks_nr_url (company_id, speedlinks_batch_id) 
				VALUES {$values_str}
				ON DUPLICATE KEY 
				UPDATE speedlinks_batch_id = ?;";

		$this->db->query($sql, array($speedlinks_batch_id));		
	}

	protected function get_nr_urls()
	{
		$sql = "SELECT n.*
				FROM nr_newsroom n
				LEFT JOIN nr_company_profile cp
				ON cp.company_id = n .company_id
				LEFT JOIN sl_speedlinks_nr_url su
				ON su.company_id = n.company_id
				WHERE n.is_active = 1
				AND su.company_id IS NULL
				AND n.company_id NOT IN (
					SELECT id FROM nr_company 
					WHERE newsroom REGEXP '^.*[0-9]{3}$'
					AND date_created > '2015-03-15' )
				ORDER BY cp.company_id DESC
				LIMIT ?";

		$query = $this->db->query($sql, array(static::LIMIT));
		$results = Model_Newsroom::from_db_all($query);

		$urls = array();
		foreach ($results as $result)
			$urls[$result->company_id] = $result->url();

		return $urls;
	}			
}

?>