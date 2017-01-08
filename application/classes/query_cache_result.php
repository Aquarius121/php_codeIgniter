<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Query_Cache_Result {

	protected $qcd;
	protected $qc;

	public function __construct($qcd, $qc)
	{
		$this->qcd = $qcd;
		$this->qc = $qc;
	}

	public function result()
	{
		return $this->result_object();
	}

	public function result_array()
	{
		$arrays = array();
		$objects = $this->result_object();
		if ($objects === false) return false;
		foreach ($objects as $v)
			$arrays[] = (array) $v;
		return $arrays;
	}

	public function result_object()
	{
		return $this->qcd->records;
	}

	public function row($idx = 0)
	{
		return $this->row_object($idx);
	}

	public function row_array($idx = 0)
	{
		$object = $this->row_object($idx);
		if ($object === false) return false;
		return (array) $object;
	}

	public function row_object($idx = 0)
	{
		if (isset($this->qcd->records[$idx]))
			return $this->qcd->records[$idx];
		return null;
	}

	public function num_rows()
	{
		return count($this->qcd->records);
	}

	public function free_result()
	{
		$this->qcd->records = null;
	}

	public function found_rows()
	{
		if ($this->qcd->found_rows !== null)
			return $this->qcd->found_rows;

		$sql = "SELECT FOUND_ROWS() AS count";
		$count = $this->qc->db()->query($sql)->row()->count;
		$this->qcd->found_rows = (int) $count;

		$hex = Query_Cache::__hash($this->qcd);
		$qcd_string = serialize($this->qcd);
		Data_Cache_ST::write($hex, $qcd_string,
			$this->qcd->lifetime);

		return (int) $count;
	}

}