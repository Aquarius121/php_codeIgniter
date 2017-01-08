<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Process_Results_Company_Name_Trait {

	protected function process_results_company_name($results)
	{
		$id_list = array();
		foreach ($results as $result)
			$id_list[] = $result->company_id;
		if (!count($id_list)) return $results;
		$id_list_str = sql_in_list($id_list);
		
		$sql = "SELECT cm.id, cm.name FROM nr_company cm
			WHERE cm.id IN ({$id_list_str})";

		$db_result = $this->db->query($sql);
		$company_set = Model_Company::from_db_all($db_result);		
		$indexed_results = array();

		foreach ($results as $result)
		{
			if (isset($indexed_results[$result->company_id]))
			     $indexed_results[$result->company_id][] = $result;
			else $indexed_results[$result->company_id] = array($result);
		}
		
		foreach ($company_set as $company)
			foreach ($indexed_results[$company->id] as $result)
				$result->company_name = $company->name;
		
		return $results;
	}

}