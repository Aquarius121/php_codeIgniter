<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Stats_Commit_Base extends Iella_Base {
	
	const CHUNK_SIZE = 1000;

	protected $maxmind_reader;
	protected $stat_db;

	public function __construct()
	{
		parent::__construct();
		$this->stat_db = $this->load_db('stat');
	}

	protected function add_location_data_to_queue($queue)
	{
		$total = count($queue);
		$batch_size = 0;

		for ($i = 0; $i < $total; $i += $batch_size)
		{
			$addr_location_in = array();
			$addr_location_out = array();
			$batch_size = min(($total - $i), static::CHUNK_SIZE);
			for ($j = 0; $j < $batch_size; $j++) $addr_location_in[$j] = $queue[$i+$j]->addr;
			$addr_location_out = $this->remote_addr_to_location_iso_batch($addr_location_in);
			for ($j = 0; $j < $batch_size; $j++) $queue[$i+$j]->location = $addr_location_out[$j];
		}
	}

	protected function insert_batch($table, &$batch)
	{
		for ($offset = 0; 
			$offset < count($batch); 
			$offset += static::CHUNK_SIZE)
		{
			set_time_limit(300);
			$chunk = array_slice($batch, 
				$offset, static::CHUNK_SIZE);
			$this->insert_chunk($table, $chunk);
		}
	}

	protected function insert_chunk($table, &$chunk)
	{
		$inserts = array();
		// transform to SQL values line
		foreach ($chunk as $insert)
			$inserts[] = sql_insert_line($insert);
		$inserts = comma_separate($inserts);

		// insert into this table
		$this->stat_db->trans_start();
		$sql = "INSERT IGNORE INTO 
			{$table} VALUES {$inserts}";
		$this->stat_db->query($sql);
		$this->stat_db->trans_complete();
	}

	protected function summation_batch($table, &$batch)
	{
		for ($offset = 0; 
			$offset < count($batch); 
			$offset += static::CHUNK_SIZE)
		{
			set_time_limit(300);
			$chunk = array_slice($batch, 
				$offset, static::CHUNK_SIZE);
			$this->summation_chunk($table, $chunk);
		}
	}

	protected function summation_chunk($table, &$chunk)
	{
		$inserts = array();
		// transform to SQL values line
		foreach ($chunk as $insert)
			$inserts[] = sql_insert_line($insert);
		$inserts = comma_separate($inserts);

		// insert or increase summation
		$this->stat_db->trans_start();
		$sql = "INSERT INTO {$table} VALUES {$inserts}
			ON DUPLICATE KEY UPDATE sum = sum + VALUES(sum)";
		$this->stat_db->query($sql);
		$this->stat_db->trans_complete();
	}
	
	protected function remote_addr_to_location_iso_batch($batch)
	{
		$locations = array();
		$db_connection = $this->stat_db->conn_id;
		$sql = array();

		foreach ($batch as $remote_addr)
		{
			if (($int_addr = ip2long($remote_addr)) !== false) 
			{
				// finds the first value that has the correct
				// IP_from when sorted in descending order
				// * assumes that there is no overlap in ranges
				$sql[] = "SELECT geo_country, geo_sub FROM (
						SELECT IP_to, geo_country, geo_sub
						FROM addr_location
						WHERE IP_from <= {$int_addr}
						ORDER BY IP_from DESC LIMIT 1
					) AS mf WHERE IP_to >= {$int_addr}";
			}
			else
			{
				// need this to keep the 
				// number of queries fixed
				$sql[] = "SELECT 
					NULL as geo_country, 
					NULL as geo_sub";
			}
		}

		$query = new CIL_Multi_Query($db_connection);
		$results = $query->execute($sql);
		
		foreach ($results as $result)
		{
			$location = new stdClass();
			$location->country = @ $result[0]['geo_country'];
			$location->sub = @ $result[0]['geo_sub'];
			$locations[] = $location;
		}

		return $locations;
	}
	
}

?>