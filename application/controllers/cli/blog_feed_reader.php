<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Blog_Feed_Reader_Controller extends CLI_Base {

	use Beanstalk_Queue_Trait;
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function __construct()
	{
		parent::__construct();

		static::$_JOB_QUEUE = 'blog-feed-reader';
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
		$sql = "SELECT * FROM nr_company_profile WHERE company_id > ?
			AND NOT ISNULL(NULLIF(soc_rss, ''))
			LIMIT {$queue_10_percent}";

		while (true)
		{
			$dbr = $this->db->query($sql, array($last_id));
			$profiles = Model_Company_Profile::from_db_all($dbr);
			if (!$profiles) break;

			foreach ($profiles as $profile)
			{
				$this->add_to_queue($profile->serialize());
				$last_id = max($profile->company_id, $last_id);
			}
		}
	}

	protected function work(Beanstalk\Job $job)
	{
		$profile = Model_Company_Profile::__unserialize($job->body);
		$blog_feed_reader = new Feed_Reader_Blog($profile);
		$blog_feed_reader->update();
	}

}