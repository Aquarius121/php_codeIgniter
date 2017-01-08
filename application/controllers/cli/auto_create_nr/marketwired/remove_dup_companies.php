<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Remove_Dup_Companies_Controller extends CLI_Base { 

//load_controller('browse/base');
//class Remove_Dup_Companies_Controller extends Browse_Base { 

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT c.name, COUNT(c.name) AS counter
				FROM ac_nr_marketwired_company c
				INNER JOIN ac_nr_marketwired_company_data cd
				ON cd.marketwired_company_id = c.id
				WHERE cd.website IS NULL
				GROUP BY c.name HAVING count(c.name) > 1
				LIMIT 1";

		while ($cnt++ <= 100)
		{			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$marketwired_c_data = Model_MarketWired_Company_Data::from_db($result);
			if (!$marketwired_c_data) break;

			
			$sql_all = "SELECT marketwired_company_id 
						FROM ac_nr_marketwired_company c
						INNER JOIN ac_nr_marketwired_company_data cd
						ON cd.marketwired_company_id = c.id
						WHERE cd.website IS NULL
						AND c.name = ?
						ORDER BY marketwired_company_id";

			$result_all = $this->db->query($sql_all, array($marketwired_c_data->name));
			$recs = Model_MarketWired_Company_Data::from_db_all($result_all);

			$ids_to_replace = array();
			$first_id = null;
			foreach ($recs as $i => $rec)
			{
				if ($i == 0)
					$first_id = $rec->marketwired_company_id;

				else
					$ids_to_replace[] = $rec->marketwired_company_id;
			}

			if (!count($ids_to_replace))
				continue;

			$str_ids_to_replace = sql_in_list($ids_to_replace);

			$sql_marketwired_pr = "	UPDATE nr_pb_marketwired_pr
									SET marketwired_company_id = {$first_id}
									WHERE marketwired_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_marketwired_pr);

			$sql_del_marketwired_comp = "DELETE FROM ac_nr_marketwired_company
										WHERE id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_marketwired_comp);

			$sql_del_marketwired_c_data = "DELETE FROM ac_nr_marketwired_company_data
										WHERE marketwired_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_marketwired_c_data);

		}

		$this->remove_dup_with_same_website();
		
	}

	protected function remove_dup_with_same_website()
	{
		$cnt = 1;
		
		$sql = "SELECT website, COUNT(website) AS counter 
				FROM ac_nr_marketwired_company_data
				GROUP BY website
				HAVING COUNT(website) > 1 
				ORDER BY COUNT(website) DESC
				LIMIT 1";

		while ($cnt++ <= 100)
		{			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$marketwired_c_data = Model_MarketWired_Company_Data::from_db($result);
			if (!$marketwired_c_data) break;

			
			$sql_all = "SELECT marketwired_company_id 
						FROM ac_nr_marketwired_company_data
						WHERE website = ?
						ORDER BY marketwired_company_id";

			$result_all = $this->db->query($sql_all, array($marketwired_c_data->website));
			$recs = Model_MarketWired_Company_Data::from_db_all($result_all);

			$ids_to_replace = array();
			$first_id = null;
			foreach ($recs as $i => $rec)
			{
				if ($i == 0)
					$first_id = $rec->marketwired_company_id;

				else
					$ids_to_replace[] = $rec->marketwired_company_id;
			}

			if (!count($ids_to_replace))
				continue;

			$str_ids_to_replace = sql_in_list($ids_to_replace);

			$sql_marketwired_pr = "UPDATE nr_pb_marketwired_pr
							SET marketwired_company_id = {$first_id}
							WHERE marketwired_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_marketwired_pr);

			$sql_del_marketwired_comp = "DELETE FROM ac_nr_marketwired_company
									WHERE id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_marketwired_comp);

			$sql_del_marketwired_c_data = "DELETE FROM ac_nr_marketwired_company_data
									WHERE marketwired_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_marketwired_c_data);

		}
	}
		
}

?>
