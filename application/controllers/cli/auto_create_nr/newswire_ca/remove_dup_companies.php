<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Remove_Dup_Companies_Controller extends CLI_Base { 

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT website, COUNT(website) AS counter 
				FROM ac_nr_newswire_ca_company_data
				WHERE is_website_valid = 1
				GROUP BY website
				HAVING COUNT(website) > 1 
				ORDER BY COUNT(website) DESC
				LIMIT 1";

		while ($cnt++ <= 100)
		{			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$newswire_ca_c_data = Model_Newswire_CA_Company_Data::from_db($result);
			if (!$newswire_ca_c_data) break;

			
			$sql_all = "SELECT newswire_ca_company_id 
						FROM ac_nr_newswire_ca_company_data
						WHERE website = ?
						ORDER BY newswire_ca_company_id";

			$result_all = $this->db->query($sql_all, array($newswire_ca_c_data->website));
			$recs = Model_Newswire_CA_Company_Data::from_db_all($result_all);

			$ids_to_replace = array();
			$first_id = null;
			foreach ($recs as $i => $rec)
			{
				if ($i == 0)
					$first_id = $rec->newswire_ca_company_id;

				else
					$ids_to_replace[] = $rec->newswire_ca_company_id;
			}

			if (!count($ids_to_replace))
				continue;

			$str_ids_to_replace = sql_in_list($ids_to_replace);

			$sql_newswire_ca_pr = "UPDATE nr_pb_newswire_ca_pr
							SET newswire_ca_company_id = {$first_id}
							WHERE newswire_ca_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_newswire_ca_pr);

			$sql_del_newswire_ca_comp = "DELETE FROM ac_nr_newswire_ca_company
									WHERE id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_newswire_ca_comp);

			$sql_del_newswire_ca_c_data = "DELETE FROM ac_nr_newswire_ca_company_data
									WHERE newswire_ca_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_newswire_ca_c_data);

		}
	}
		
}

?>
