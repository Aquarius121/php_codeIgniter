<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');

class Fetch_Controller extends PRCom_API_Base {

	public function index()
	{
		$uuids = $this->iella_in->uuids;
		$uuids_in_list = sql_in_list($uuids);

		$sql = "SELECT c.*, cvs.remote_uuid FROM nr_content c 
			INNER JOIN nr_content_virtual_source cvs
			ON cvs.content_id = c.id
			WHERE cvs.remote_uuid IN ({$uuids_in_list})";

		$results = Model_Content::from_sql_all($sql);
		$stat_hashes = array();

		foreach ($results as $result)
		{
			$result->_url = $this->website_url($result->url());

			if ($this->iella_in->stats)
			{
				$stats_hash = new Stats_Hash();
				$stats_hash->content = $result->id;
				$stat_hashes[] = $stats_hash->hash();
			}

			if ($result->is_published && Date::utc($result->date_publish) < Date::hours(-12))
			     $result->is_report_available = true;
			else $result->is_report_available = false;
		}

		if ($this->iella_in->stats && count($stat_hashes))
		{
			$stats_contexts = Stats_Hash::__context_batch($stat_hashes);
			$stats_query = new Stats_Query();
			$summation = $stats_query->hits_summation_batch($stats_contexts);
			$summation = array_values($summation);
			foreach ($results as $k => $result)
				$result->views = $summation[$k];
		}

		$this->iella_out->results = $results;
	}

}

?>