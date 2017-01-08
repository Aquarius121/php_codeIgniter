<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Process_Mailer_Queue_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	protected $mutex;

	const MAX_PROCESS_COUNT = 4;

	public function index()
	{
		set_time_limit(0);

		// prevent more than MAX process from running at a time
		if ($this->process_count() > static::MAX_PROCESS_COUNT) 
			return;

		// mutex is used for prevent sending of 
		// the same email by 2 or more processes
		$this->mutex = new Flock_Mutex(get_called_class());

		// keep at least 1 running here
		while ($this->process_count() == 1)
		{
			$this->process();
			sleep(5);
		}

		// run at least once
		$this->process();
	}

	protected function process()
	{
		while ($this->_process())
			usleep(500000);
	}

	protected function _process()
	{
		$qdir = $this->conf('mailer_queue_dir');
		$bdir = $this->conf('mailer_buffer_dir');

		// find all .mail files in the queue
		// * glob() is sorted by default to 
		// maintain the correct order
		$files = glob(sprintf('%s/*.mail', $qdir));

		foreach ($files as $k => $file)
		{
			// check every 100
			if ($k % 100 === 0)
			{
				// scan the queue again so we can check
				// for any new files with higher priority
				$_files = glob(sprintf('%s/*.mail', $qdir));

				// look for a file with higher priority 
				// and restart the proces if one is found
				if (isset($_files[0]) && !in_array($_files[0], $files))
					return true;
			}

			$file  = basename($file);
			$qpath = build_path($qdir, $file);
			$bpath = build_path($bdir, $file);
			$this->mutex->lock();
			
			if (is_file($qpath))
			{
				rename($qpath, $bpath);
			}
			else
			{
				$this->mutex->unlock();
				continue;
			}

			$this->mutex->unlock();
			$mtime = filemtime($bpath);

			// last attempt was more than an hour ago 
			// * new files have this set by default
			if ($mtime <= Date::seconds(-Mailer::QUEUE_RETRY_PERIOD)->getTimestamp())
			{
				// read the queue data file
				$qdata = Mailer::__read_queue($bpath);

				// successfully sent, nothing more to do
				if (Mailer::__send($qdata->email)) 
				{
					unlink($bpath);
					continue;
				}

				// file is older than 48 hours, assume permanent failure
				if ($qdata->date_created < Date::hours(-48))
				{
					unlink($bpath);
					continue;
				}
				
				// update TS
				// => try again
				touch($bpath);
			}

			// finished work on this file
			// so it can be returned to queue
			rename($bpath, $qpath);
		}

		// exit loop
		return false;
	}

}
