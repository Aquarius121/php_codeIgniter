<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait CB_API_Trait {
	
	protected function select_api_key()
	{
		$dt = Date::$now->format('Y-m-d');
		$sql = "SELECT * FROM 
				ac_nr_cb_api_key k 
				LEFT JOIN ac_nr_cb_api_key_usage u
				ON u.cb_api_key_id = k.id
				AND date_used = '{$dt}'
				ORDER BY k.id";

		$dbr = $this->db->query($sql);
		$api_key = "";

		foreach ($dbr->result() as $result)
		{
			$num_calls = (int) $result->num_calls;
			if ($num_calls < 2500)
			{
				$api_key = $result->api_key;
				break;
			}
		}

		return $api_key;
	}

	protected function log_key_usage($key)
	{
		$dt = Date::$now->format('Y-m-d');

		if (!$key)
			return 0;

		$sql = "SELECT * FROM 
				ac_nr_cb_api_key k 
				LEFT JOIN ac_nr_cb_api_key_usage u
				ON u.cb_api_key_id = k.id
				AND date_used = '{$dt}'
				WHERE k.api_key = '{$key}'";				

		if ( ! $dbr = $this->db->query($sql))
			return 0;

		$result = Model_CB_API_Key_Usage::from_db($dbr);
		
		$num_calls = (int) $result->num_calls;
		$num_calls++;
		$cb_api_key_id = $result->id;

		if (!$result->date_used)
		{
			$api_usage = new Model_CB_API_Key_Usage();
			$api_usage->cb_api_key_id = $cb_api_key_id;
			$api_usage->date_used = $dt;
		}		
		else
		{
			$criteria = array();
			$criteria[] = array('cb_api_key_id', $cb_api_key_id);
			$criteria[] = array('date_used', $dt);
			$api_usage = Model_CB_API_Key_Usage::find($criteria);
		}

		$api_usage->num_calls = $num_calls;
		$api_usage->save();
		return 1;
	}	
}

?>