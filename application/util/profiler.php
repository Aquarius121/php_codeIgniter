<?php

class CIL_Profiler {

	CONST LOG_FILE = 'application/logs/profiler/request.log';

	protected static $instance;

	protected $file;
	protected $time_real = 0;
	protected $time_sys = 0;
	protected $time_user = 0;
	protected $time_mark_started = 0;
	protected $time_mark = 0;
	protected $uri;

	public static function instance()
	{
		if (!static::$instance)
			static::$instance = new static();
		return static::$instance;
	}

	public function __construct()
	{
		touch(static::LOG_FILE);
		
		$this->file = realpath(static::LOG_FILE);
		$this->time_real = $this->real_time();
		$this->time_sys = $this->sys_time();
		$this->time_user = $this->user_time();
		$this->uri = null;
	}

	public function set_uri($uri)
	{
		if (substr($uri, 0, 1) === '/')
			$uri = substr($uri, 1);
		$uri = str_replace('//', '/', $uri);
		$this->uri = $uri;
	}

	protected function real_time()
	{
		$time  = microtime(true);
		$time -= $this->time_real;
		return round($time, 3);
	}

	protected function sys_time()
	{
		$ru = getrusage();
		$time  = $ru['ru_stime.tv_sec'];
		$time += $ru['ru_stime.tv_usec'] / 1e6;
		$time -= $this->time_sys;
		return round($time, 3);
	}

	protected function user_time()
	{
		$ru = getrusage();
		$time  = $ru['ru_utime.tv_sec'];
		$time += $ru['ru_utime.tv_usec'] / 1e6;
		$time -= $this->time_user;
		return round($time, 3);
	}

	protected function mark_time()
	{
		$time = $this->time_mark;
		return round($time, 3);
	}

	protected function memory()
	{
		return memory_get_peak_usage();
	}

	public function mark_start()
	{
		$this->time_mark_started = microtime(true);
	}

	public function mark_stop()
	{
		$time  = microtime(true);
		$time -= $this->time_mark_started;
		$this->time_mark += $time;
	}

	public function save()
	{
		if (!defined('CIL_PROFILER_ENABLED') || 
			 !CIL_PROFILER_ENABLED)
			return;
			
		$fhandle = fopen($this->file, 'a+');

		fputcsv($fhandle, array(
			$this->real_time(),
			$this->user_time(),
			$this->sys_time(),
			$this->mark_time(),
			$this->memory(),
			$this->uri
		));

		fclose($fhandle);
	}

}

register_shutdown_function(function() {
	CIL_Profiler::instance()->save();
});


