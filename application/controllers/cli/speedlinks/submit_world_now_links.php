<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/speedlinks/base');

class Submit_World_Now_Links_Controller extends SpeedLinks_Base {

	protected $world_now_site_ids;

	const DRIP_FEED_DAYS = 1;
	const LIMIT = 100;
	const START_DATE = "2015-04-01 00:00:01";	
	
	public function __construct()
	{
		parent::__construct();
		$this->load_world_now_site_ids();
	}

	public function index()
	{
		if (!is_array($this->world_now_site_ids) || !count($this->world_now_site_ids))
			return false;

		$urls = $this->get_world_now_dist_urls();

		if (!count($urls))
			return false;

		$today_left = $this->get_remaining_limit();

		if ($today_left <= 0)
			return false;

		// Now submitting the URLs		
		$url_str = implode("\n", $urls);
		$batch_title = "wn_batch_". time();
		$submitted_urls = $this->submit($url_str, $batch_title, static::DRIP_FEED_DAYS);

		if (!is_array($submitted_urls) || !count($submitted_urls))
			return false;

		$sl_batch = new Model_SpeedLinks_Batch();
		$sl_batch->title = $batch_title;
		$sl_batch->dripfeed_days = static::DRIP_FEED_DAYS;
		$sl_batch->date_submitted = Date::$now->format(Date::FORMAT_MYSQL);
		$sl_batch->submission_type = Model_SpeedLinks_Batch::SUBMISSION_TYPE_WORLD_NOW_LINK;
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
				sl_speedlinks_world_now_link (distribution_index_id, speedlinks_batch_id) 
				VALUES {$values_str}
				ON DUPLICATE KEY 
				UPDATE speedlinks_batch_id = ?;";

		$this->db->query($sql, array($speedlinks_batch_id));		
	}

	protected function load_world_now_site_ids()
	{
		$hashes = $this->get_world_now_dist_hashes();
			
		if (!$hashes)
			return false;

		$sql = "SELECT id 
				FROM nr_distribution_site
				WHERE hash IN ({$hashes})";

		$query = $this->db->query($sql);
		$this->world_now_site_ids = Model_Distribution_Site::values_from_db($query, 'id');
	}

	protected function get_world_now_dist_hashes()
	{
		$sources = (require 'raw/distribution/world_now.php');
		$hashes = sql_in_list(array_map(function($n) {
			return $n->hash;
		}, $sources));
		
		return $hashes;
	}

	protected function get_world_now_dist_urls()
	{
		if (!is_array($this->world_now_site_ids) || !count($this->world_now_site_ids))
			return false;

		$ids_str = sql_in_list($this->world_now_site_ids);		

		$sql = "SELECT i.id, i.url
				FROM nr_distribution_index i
				INNER JOIN nr_content c 
				ON i.content_id = c.id
				LEFT JOIN sl_speedlinks_world_now_link sl
				ON sl.distribution_index_id = i.id				
				WHERE c.type = ?
				AND c.is_premium = 1 
				AND c.is_published = 1
				AND c.date_publish > ?
				AND sl.distribution_index_id IS NULL
				AND i.distribution_site_id IN ({$ids_str})
				ORDER BY c.id DESC
				LIMIT ?";

		$query = $this->db->query($sql, array(Model_Content::TYPE_PR, static::START_DATE, static::LIMIT));
		$results = Model_Distribution_Index::from_db_all($query);

		$urls = array();
		foreach ($results as $result)
			$urls[$result->id] = $result->url;

		if (count($urls))
			$urls = array_unique($urls);

		return $urls;
	}
}

?>