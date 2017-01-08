<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CIL_Multi_Query {
	
	protected $db_connection;
	protected $multi_results;

	public function __construct($db_connection)
	{
		if ($db_connection instanceof CI_DB_mysqli_driver &&
			 isset($db_connection->conn_id))
			$db_connection = $db_connection->conn_id;
		$this->db_connection = $db_connection;
	}

	public function error()
	{
		return mysqli_error($this->db_connection);
	}

	public function & execute($sql)
	{
		$this->multi_results = false;
		if (is_array($sql)) $sql = implode(';', $sql);
		if (!($res = mysqli_multi_query($this->db_connection, $sql))) 
			return $this->multi_results;
		$this->multi_results = array();
		do $this->fetch_records();
		while (mysqli_more_results($this->db_connection) &&
			mysqli_next_result($this->db_connection));
		return $this->multi_results;
	}

	protected function fetch_records()
	{
		if (!$result = mysqli_store_result($this->db_connection))
		{
			$this->multi_results[] = null;
			return;
		}

		$records = array();
		$this->multi_results[] = &$records;
		while ($record = mysqli_fetch_assoc($result))
			$records[] = $record;
		mysqli_free_result($result);
	}
	
}

?>