	<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Profiler_Stats_Controller extends CLI_Base {

	const FILE = 'application/logs/profiler/request.log';

	public function index($file = self::FILE, $sort_by = 'real_time')
	{
		if (!is_file($file) || !is_readable($file))
		{
			$this->error('error: log file is not readable');
			$this->error('usage: profiler_stats <file> <real_time|user_time|system_time|marked_time|memory_usage>');
			exit(1);
		}

		if (!in_array($sort_by, array('real_time', 'user_time', 'system_time', 'marked_time', 'memory_usage')))
		{
			$this->error('error: missing sort option');
			$this->error('usage: profiler_stats <file> <real_time|user_time|system_time|marked_time|memory_usage>');
			exit(1);
		}

		$analyzed_log = array();
		$handle = fopen($file, 'r');

		while (($entry = fgetcsv($handle)) !== false)
		{
			list($real_time, $user_time, $system_time, $marked_time, $memory_usage, $uri) = $entry;
			$uri_segments = explode('/', $uri);

			$merged_uri = '';
			for ($segment_length = 0; $segment_length < count($uri_segments); $segment_length++)
			{
				$segment_id = '';
				$segment = $uri_segments[$segment_length];

				if (!$segment) continue;

				if ($segment_length == count($uri_segments) - 1)
				{
					$merged_uri .= $segment;
					$segment_id = $merged_uri;
				}
				else
				{
					$merged_uri .= sprintf('%s/', $segment);
					$segment_id = sprintf('%s*', $merged_uri);
				}

				if (array_key_exists($segment_id, $analyzed_log))
				{
					$log_entry = $analyzed_log[$segment_id];
					$log_entry->count++;
					$log_entry->real_time += $real_time;
					$log_entry->user_time += $user_time;
					$log_entry->system_time += $system_time;
					$log_entry->marked_time += $marked_time;
					$log_entry->memory_usage = intval((($log_entry->count * $log_entry->memory_usage) + $memory_usage) / ($log_entry->count));
				}
				else
				{
					$log_entry = new stdClass();
					$log_entry->real_time = $real_time;
					$log_entry->user_time = $user_time;
					$log_entry->system_time = $system_time;
					$log_entry->marked_time = $marked_time;
					$log_entry->memory_usage = $memory_usage;
					$log_entry->count = 1;
					$analyzed_log[$segment_id] = $log_entry;
				}
			}
		}
		
		$uris = array_keys($analyzed_log);
		$fields = array();
		foreach ($analyzed_log as $log) 
			$fields[] = $log->{$sort_by};
		
		array_multisort($fields, SORT_DESC, $uris, SORT_ASC, $analyzed_log);
		$this->generate($analyzed_log);
	}

	protected function generate($analyzed)
	{
		$handle = fopen('php://stdout', 'w');
		if (!$handle) return;

		foreach ($analyzed as $uri => $stats) 
		{
			$row = array(
				$stats->real_time, 
				$stats->user_time, 
				$stats->system_time, 
				$stats->marked_time, 
				$stats->memory_usage, 
				$uri
			);

			fputcsv($handle, $row);
		}
	}

}

?>