<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Process_Results_Company_Profile_Trait {

	protected function process_results_company_profile($results)
	{
		$id_list = array();
		foreach ($results as $result)
			$id_list[] = $result->id;
		if (!count($id_list)) return $results;
		$id_list_str = sql_in_list($id_list);
		
		$sql = "SELECT cp.* FROM nr_company_profile cp
			WHERE cp.company_id IN ({$id_list_str})";

		$db_result = $this->db->query($sql);
		$profile_set = Model_Company_Profile::from_db_all($db_result);

		$indexed_results = array();
		foreach ($results as $result)
			$indexed_results[$result->id] = $result;
		
		foreach ($profile_set as $profile)
		{
			$result = $indexed_results[$profile->company_id];
			$result->company_profile = $profile;
		}
		
		return $results;
	}

}