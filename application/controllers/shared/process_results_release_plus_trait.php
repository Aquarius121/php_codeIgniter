<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Process_Results_Release_Plus_Trait {

	protected function process_results_release_plus($results)
	{
		$id_list = array();
		foreach ($results as $result)
			if ($result->is_premium)
				$id_list[] = $result->id;
		if (!count($id_list)) return $results;
		$id_list_str = sql_in_list($id_list);
		
		$sql = "SELECT crp.* FROM nr_content_release_plus crp
			WHERE crp.content_id IN ({$id_list_str})";
			
		$db_result = $this->db->query($sql);
		$release_plus_set = Model_Content_Release_Plus::from_db_all($db_result);
		
		$indexed_results = array();
		foreach ($results as $result)
			$indexed_results[$result->id] = $result;
		
		foreach ($release_plus_set as $release_plus)
		{
			$result = $indexed_results[$release_plus->content_id];
			if (!isset($result->release_plus_set))
				$result->release_plus_set = array();
			if (!isset($result->release_plus_providers))
				$result->release_plus_providers = array();
			$result->release_plus_providers[] = $release_plus->provider;
			$result->release_plus_set[] = $release_plus;
		}
		
		return $results;
	}

}