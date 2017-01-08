<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');

class Industry_Distribution_Controller extends PRCom_API_Base {
	
	public function index()
	{
		$this->companies_for_beat();
	}

	public function companies_for_beat()
	{
		$beat_id = (int) $this->iella_in->beat_id;
		$results = $this->__companies_for_beat_ids(array($beat_id));
		$this->iella_out->results = $results;
	}

	public function companies_for_beat_group()
	{
		$beat_ids = array();
		$beat_group_id = (int) $this->iella_in->beat_group_id;
		$beats = Model_Beat::find_all(array('beat_group_id', $beat_group_id));
		foreach ($beats as $beat)
			$beat_ids[] = (int) $beat->id;
		$results = $this->__companies_for_beat_ids($beat_ids);
		$this->iella_out->results = $results;
	}

	public function companies_for_beat_list()
	{
		$beat_ids = $this->iella_in->beats;
		$results = $this->__companies_for_beat_ids($beat_ids);
		$this->iella_out->results = $results;
	}

	protected function __companies_for_beat_ids($beat_ids)
	{
		if (!count($beat_ids)) return array();
		$beat_ids = sql_in_list($beat_ids);
		$sql = "SELECT SQL_CALC_FOUND_ROWS
			cmt.simplified AS media_type, 
			c.company_name AS company_name
			FROM nr_contact c
			LEFT JOIN nr_contact_media_type cmt
			ON c.contact_media_type_id = cmt.id
			WHERE c.is_media_db_contact = 1
			AND (c.beat_1_id IN ({$beat_ids}) OR c.beat_2_id IN ({$beat_ids}) OR c.beat_3_id IN ({$beat_ids}))
			GROUP BY c.company_name
			ORDER BY c.rand ASC
			LIMIT 1000";

		$dbr = $this->db->query($sql);
		$results = Model_Base::from_db_all($dbr);
		$this->iella_out->found_rows = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		return $results;
	}

}

?>