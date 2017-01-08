<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Search_Controller extends CLI_Base {
	
	const FILES_DIR = 'raw/mmi_contacts';
	const MAX_LINES = 5000;
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	protected $character_set = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
		'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
		'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
	);
		
	protected $coverage_set = array(
		1 => 'National',
		2 => 'Regional',
		3 => 'International',
		4 => 'Local',
	);
	
	protected $ex_params;
	protected $version;
	
	public function index($version = 'basic')
	{
		set_memory_limit('1024M');
		set_time_limit(0);
		
		$this->version = $version;
		
		foreach ($this->coverage_set as $coverage_id => $coverage_name)
		{
			$this->ex_params = array();
			$this->ex_params['coverageTypeIDs'] = $coverage_id;
			$this->ex_params['coverageTypes'] = $coverage_name;
			$this->process();
		}
	}
	
	protected function process($characters = array())
	{
		for ($ci = 0; $ci < count($this->character_set); $ci++)
		{
			$characters[] = $this->character_set[$ci];
			if (!$this->do_search($characters))
				$this->process($characters);
			array_pop($characters);
		}
	}
	
	protected function do_search(&$characters)
	{
		$ch_str = implode($characters);
		$query = $this->generate_query($characters);
		$ex_params = http_build_query($this->ex_params);
		$ex_params_enc = base64_encode($ex_params);
		
		$sh_ex_params = escapeshellarg($ex_params);
		$sh_query = escapeshellarg($query);
		
		$base_dir = static::FILES_DIR;
		$filename = "{$this->version}_{$ch_str}_{$ex_params_enc}";
		$command = "{$base_dir}/download.sh search/{$filename} {$sh_query} {$sh_ex_params}";
		$filepath = "{$base_dir}/search/{$filename}";
		
		if (is_file($filepath))
		{
			$this->trace($filename, 'skipped');
			return true;
		}

		sleep(1);		
		echo shell_exec($command);
		
		if (!is_file($filepath))
		{
			$this->trace($filename, 'failed');
			return true;
		}
		
		$all_lines = file($filepath);
		$lines = preg_grep('#table_row#', $all_lines);
		$line_count = count($lines);
		unset($all_lines);
		unset($lines);
		
		if ($line_count >= static::MAX_LINES)
		{
			unlink($filepath);
			return false;
		}
		else
		{
			$this->trace($filename, $line_count);
			return true;
		}
	}
	
	protected function generate_query($ch)
	{
		if (!isset($ch[0])) $ch[0] = null;
		if (!isset($ch[1])) $ch[1] = null;
		if (!isset($ch[2])) $ch[2] = null;
		if (!isset($ch[3])) $ch[3] = null;
		
		// construct a query string based on email address
		// and the set of characters provided that 
		// will provide the best result spread
		return "{$ch[1]}*{$ch[3]}@{$ch[0]}*{$ch[2]}.*";
	}

}

?>