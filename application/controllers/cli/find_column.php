<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Find_Column_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index($column = null)
	{
		if (!$column)
		{
			$this->error('usage: ... find_column <column-name>');
			exit(-1);
		}

		$tables = array();
		$query = $this->db->query('show tables');
		foreach ($query->result_array() as $result)
			$tables[] = array_values($result)[0];
		
		foreach ($tables as $table)
		{
			$query = $this->db->query(sprintf('show columns in %s', $table));

			foreach ($query->result_array() as $result)
			{
				if ($result['Field'] === $column)
				{
					$this->console($table, $column);
				}
			}
		}
	}

}

?>