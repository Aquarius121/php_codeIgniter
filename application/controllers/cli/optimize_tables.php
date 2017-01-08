<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Optimize_Tables_Controller extends CLI_Base {

	public function index()
	{
		set_time_limit(86400);

		$tables = array();
		$dbr = $this->db->query('show tables');
		foreach ($dbr->result_array() as $result)
			$tables[] = array_values((array) $result)[0];

		foreach ($tables as $table)
		{
			$this->trace_info('started', $table);
			$sql = "OPTIMIZE NO_WRITE_TO_BINLOG TABLE {$table}";
			$this->trace_info('finished', $table);
			$this->db->query($sql);
			sleep(60);
		}
	}

}