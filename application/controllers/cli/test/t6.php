<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class T6_Controller extends CLI_Base {

	use Beanstalk_Queue_Trait;
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function __construct()
	{
		parent::__construct();	

		static::$_JOB_QUEUE = 't6-reader';
		static::$_JOB_QUEUE_LENGTH = 10;
		static::$_NUM_WORKERS = 2;
		static::$_WORKER_RETRIES = 3;
		static::$_WORKER_TIMEOUT = 2;

		set_time_limit(0);
	}
	
	public function index()
	{
		if ($this->process_count() > 1) return;
		if (!$this->init()) return;
		
		for ($i = 0; $i < 100; $i++)
		{
			$this->add_to_queue(sprintf('job %d', $i));
			$this->trace(sprintf('queued %d', $i));
		}
	}

	protected function work(Beanstalk\Job $job)
	{
		$this->console($job->body);
		usleep(250000);
	}

}