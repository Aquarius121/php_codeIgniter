<?php

namespace Bad_Bots;

class Processor {
	
	protected $data_dir;
	protected $excluded;
	protected $map;

	public function __construct($opt)
	{
		$this->data_dir = $opt['data_dir'];
		$this->excluded = array();
	}

	protected function list_ip_addresses()
	{
		return array_filter(array_map(function($v) {
			return str_ends_with($v, '.bot') ? substr($v, 0, -4) : false;
		}, scandir($this->data_dir)));
	}

	public function exclude_addresses($excluded)
	{
		$this->excluded = $excluded;
	}
	
	public function build_map_file($map_file)
	{
		$bots = $this->list_ip_addresses();
		$lines = array();

		foreach ($bots as $ip) 
			if (!in_array($ip, $this->excluded))
				$lines[] = sprintf('%s 1;', $ip);

		file_put_contents($map_file, implode(PHP_EOL, $lines));
	}
	
	public function remove_old_data($max_age_in_seconds)
	{
		$date = \Date::seconds(-$max_age_in_seconds);
		$timestamp = $date->getTimestamp();
		$bots = $this->list_ip_addresses();

		foreach ($bots as $ip) 
		{
			$filename = sprintf('%s.bot', $ip);
			$file = build_path($this->data_dir, $filename);
			if (filemtime($file) < $timestamp)
				unlink($file);
		}
	}

}
