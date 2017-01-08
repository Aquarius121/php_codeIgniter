<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Process_Results_Distribution_Bundle_Trait {

	protected function process_results_distribution_bundle($results)
	{
		$id_list = array();
		foreach ($results as $result)
			$id_list[] = $result->id;
		if (!count($id_list)) return $results;
		$id_list_str = sql_in_list($id_list);
		
		$sql = "SELECT cdb.* FROM nr_content_distribution_bundle cdb
			WHERE cdb.content_id IN ({$id_list_str})";
			
		$db_result = $this->db->query($sql);
		$distribution_set = Model_Content_Distribution_Bundle::from_db_all($db_result);
		
		$indexed_results = array();
		foreach ($results as $result)
			$indexed_results[$result->id] = $result;
		
		foreach ($distribution_set as $bundle)
		{
			$result = $indexed_results[$bundle->content_id];
			$result->distribution_bundle = $bundle;
		}
		
		return $results;
	}

}