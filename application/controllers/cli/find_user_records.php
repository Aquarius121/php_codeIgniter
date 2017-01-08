<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Find_User_Records_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index($source_user_id = false, $dest_user_id = null, $confirm = false)
	{
		if ($dest_user_id === null)
			return $this->error('usage: <source_user_id> <dest_user_id> <commit?>');

		$tables = array();
		$query = $this->db->query('show tables');
		foreach ($query->result_array() as $result)
			$tables[] = array_values($result)[0];
		
		foreach ($tables as $table)
		{
			$query = $this->db->query(sprintf('show columns in %s', $table));
			foreach ($query->result_array() as $result)
			{
				if ($result['Field'] === 'user_id')
				{
					$sql = 'select 1 from %s where user_id = %d';
					$sql = sprintf($sql, $table, $source_user_id);
					$query = $this->db->query($sql);
					$count = $query->num_rows();

					if ($count) 
					{
						// column is primary or unique field 
						// so we should remove exiting values
						$column_is_unique = $result['Key'] === 'PRI' 
							|| $result['Key'] === 'UNI';

						if ($dest_user_id)
						{
							if ($column_is_unique)
							{
								$sql = 'delete from %s where user_id = %d;';
								$sql = sprintf($sql, $table, $dest_user_id);
								if ($confirm) $this->db->query($sql);
								$this->console($sql);
							}

							$sql = 'update %s set user_id = %d where user_id = %d;';
							$sql = sprintf($sql, $table, $dest_user_id, $source_user_id);
							if ($confirm) $this->db->query($sql);
							$this->console($sql);
						}
						else
						{
							$this->trace($table, $count, sprintf('unique: %d',
								$column_is_unique));
						}
					}
				}
			}
		}
	}

}

?>