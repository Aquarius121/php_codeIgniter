<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Beanstalk_Queue_Trait {

	protected static $_JOB_QUEUE = null;
	protected static $_JOB_QUEUE_LENGTH = 1000;
	protected static $_NUM_WORKERS = 4;
	protected static $_WORKER_RETRIES = 5;
	protected static $_WORKER_TIMEOUT = 10;
	protected static $_WAIT_FOR_SPACE_MS = 1000000;

	protected $beanstalk;

	public function size()
	{
		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->useTube(static::$_JOB_QUEUE);
		$stats = $beanstalk->statsTube(static::$_JOB_QUEUE);
		$this->trace($stats['current-jobs-ready']);
	}

	public function clear()
	{
		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->watch(static::$_JOB_QUEUE);

		while (true)
		{
			$stats = $beanstalk->statsTube(static::$_JOB_QUEUE);
			$this->trace($stats['current-jobs-ready']);
			if (!$stats['current-jobs-ready']) break;
			$job = $beanstalk->reserve();
			$job->delete();
		}
	}

	public function worker()
	{
		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->watch(static::$_JOB_QUEUE);
		$retries = static::$_WORKER_RETRIES;

		while (true)
		{
			$job = $beanstalk->reserve(static::$_WORKER_TIMEOUT);
			
			if ($job === false)
			{
				if ($retries <= 0)
					break;
				$retries--;
				continue;
			}
			else
			{
				$job->delete();
				$retries = static::$_WORKER_RETRIES;
				$this->work($job);
			}
		}
	}

	protected function init()
	{
		if (!static::$_JOB_QUEUE)
			throw new Exception();

		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->useTube(static::$_JOB_QUEUE);
		$this->beanstalk = $beanstalk;

		$params = $this->controller_uri_parts;
		array_shift($params);
		array_pop($params);
		$params[] = 'worker';

		$task = new CI_Background_Task();
		$task->set($params);
		$task->run(static::$_NUM_WORKERS);

		return $beanstalk;
	}

	protected function wait_for_space(Beanstalk\Client $beanstalk)
	{
		$stats = $beanstalk->statsTube(static::$_JOB_QUEUE);
		if ($stats['current-jobs-ready'] < static::$_JOB_QUEUE_LENGTH)
			return false;

		usleep(static::$_WAIT_FOR_SPACE_MS);
		return true;
	}

	protected function add_to_queue($work)
	{
		while ($this->wait_for_space($this->beanstalk));
		$this->beanstalk->put($work);
	}

	protected function work(Beanstalk\Job $job)
	{
		// override this method
	}

}