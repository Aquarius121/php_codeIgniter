<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stats_Query {
	
	public function __construct()
	{
		$ci =& get_instance();
		$this->db = $ci->load_db('stat');
	}

	public function query($sql, $params = array())
	{
		$results = array();
		$dbr = $this->db->query($sql, $params);
		foreach ($dbr->result() as $result)
			$results[] = $result;
		return $results;
	}

	public function hits_daily_summation($context, $dt_min_local, $dt_max_local = null)
	{
		if ($dt_max_local === null)
			$dt_max_local = Date::local();

		$results = array();
		$dt_min = clone $dt_min_local;
		$dt_max = clone $dt_max_local;
		$dt_min->setTimezone(Date::$utc);
		$dt_max->setTimezone(Date::$utc);

		$tzo_utc = Date::$now->getTimezoneOffsetString();
		$tzo_utc = $this->db->escape($tzo_utc);
		$tzo_local = $dt_max_local->getTimezoneOffsetString();
		$tzo_local = $this->db->escape($tzo_local);		

		for ($i = clone $dt_min; $i <= $dt_max; $i = Date::days(1, $i))
			$results[$i->format('Y-m-d')] = 0;
		
		$bucket = Stats_Engine::hits_bucket($context);
		$sql = "SELECT count(1) as count, date(convert_tz(date_request, {$tzo_utc}, {$tzo_local})) as date
			from {$bucket} where context = ? and date_request >= ? and date_request <= ? 
			group by date(convert_tz(date_request, {$tzo_utc}, {$tzo_local}))";

		$params = array($context, $dt_min, $dt_max);
		$dbr = $this->db->query($sql, $params);
		foreach ($dbr->result() as $result)
			$results[$result->date] = (int) $result->count;

		ksort($results);
		return $results;
	}

	public function hits_hour_window_summation($context, $dt_local = null)
	{
		if ($dt_local === null)
			$dt_local = Date::local();

		$results = array();
		$tzo_utc = Date::$now->getTimezoneOffsetString();
		$tzo_utc = $this->db->escape($tzo_utc);
		$tzo_local = $dt_local->getTimezoneOffsetString();
		$tzo_local = $this->db->escape($tzo_local);

		for ($i = 0; $i <= 23; $i++)
			$results[$i] = 0;
		
		$bucket = Stats_Engine::hits_bucket($context);
		$sql = "SELECT count(1) as count, hour(convert_tz(date_request, {$tzo_utc}, {$tzo_local})) as hour
			from {$bucket} where context = ? group by hour(convert_tz(date_request, {$tzo_utc}, {$tzo_local}))";

		$params = array($context);
		$dbr = $this->db->query($sql, $params);
		foreach ($dbr->result() as $result)
			$results[(int) $result->hour] = (int) $result->count;

		return $results;
	}

	public function hits_over_period_summation($context, $dt_min_local, $dt_max_local = null)
	{
		if ($dt_max_local === null)
			$dt_max_local = Date::local();

		$dt_min = clone $dt_min_local;
		$dt_max = clone $dt_max_local;
		$dt_min->setTimezone(Date::$utc);
		$dt_max->setTimezone(Date::$utc);

		$bucket = Stats_Engine::hits_bucket($context);
		$sql = "SELECT count(1) as count from {$bucket} 
			where context = ? and date_request >= ? 
			and date_request <= ?";

		$params = array($context, $dt_min, $dt_max);
		$result = $this->db->query($sql, $params)->row();
		if ($result) return (int) $result->count;
		return 0;
	}
		
	public function hits_summation($context)
	{
		$sql = "SELECT sum from sx_hits_summation where context = ?";
		$result = $this->db->query($sql, array($context))->row();
		if ($result) return (int) $result->sum;
		return 0;
	}

	public function hits_summation_batch($context_arr)
	{
		$results = array();
		foreach ($context_arr as $context)
			$results[(int) $context] = 0;

		$in_context_str = sql_in_list($context_arr);
		$sql = "SELECT context, sum from sx_hits_summation 
			where context in (${in_context_str})";

		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $result)
			$results[(int) $result->context] = (int) $result->sum;
		return $results;
	}

	public function activated($context)
	{
		$sql = "SELECT context, context_set, date_request 
			from sx_activation where context = ?";
		$result = $this->db->query($sql, array($context))->row();
		if ($result) return $result;
		return false;
	}

	public function activated_set($context_set)
	{
		$results = array();
		$sql = "SELECT context, context_set, date_request 
			from sx_activation where context_set = ?";
		$dbr = $this->db->query($sql, array($context_set));
		foreach ($dbr->result() as $result) 
			$results[] = $result;
		return $results;
	}

	public function activated_set_summation($context_set)
	{
		$sql = "SELECT count(1) as count
			from sx_activation where context_set = ?";
		$result = $this->db->query($sql, array($context_set))->row();
		if ($result) return $result->count;
		return 0;
	}
	
}

?>