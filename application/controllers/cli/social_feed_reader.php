<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Social_Feed_Reader_Controller extends CLI_Base {

	use Beanstalk_Queue_Trait;
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function __construct()
	{
		parent::__construct();		

		static::$_JOB_QUEUE = 'social-feed-reader';
		static::$_JOB_QUEUE_LENGTH = 1000;
		static::$_NUM_WORKERS = 4;

		// only check the queue every 60 seconds
		// to see if it's no longer full
		static::$_WAIT_FOR_SPACE_MS = 60 * 1000000;

		set_time_limit(0);
	}

	public function index()
	{
		if ($this->process_count() > 1) return;
		if (!$this->init()) return;

		$last_id = 0;
		$queue_10_percent = ceil(static::$_JOB_QUEUE_LENGTH / 10);

		$ci =& get_instance();
		$soc_cache_time = $ci->conf('social_post_cache_time');
		$date_cache_mins_ago = Date::minutes(-1 * $soc_cache_time)->format(Date::FORMAT_MYSQL);
		$dt_24_hours_ago = Date::hours(-24)->format(Date::FORMAT_MYSQL);
		$company_ids = array();

		$sql = "SELECT company_id 
			FROM nr_social_wire_update 
			WHERE company_id > ?
			AND date_last_manual_request >= '$dt_24_hours_ago'
			AND (date_last_auto_update IS NULL
				OR date_last_auto_update <= '$date_cache_mins_ago')
			ORDER BY company_id ASC
			LIMIT {$queue_10_percent}";
				
		while (true)
		{
			$dbr = $this->db->query($sql, array($last_id));
			$wire_update = Model_Social_Wire_Update::from_db_all($dbr);
			if (!$wire_update) break;

			foreach ($wire_update as $rec)
			{
				$this->add_to_queue($rec->serialize());
				$last_id = max($rec->company_id, $last_id);
			}
		}
	}

	protected function work(Beanstalk\Job $job)
	{
		$wire_update = Model_Social_Wire_Update::__unserialize($job->body);
		Social_Wire::update($wire_update->company_id, Social_Wire::UPDATE_AUTO);
	}

}