<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Remove_Dup_Companies_Controller extends Auto_Create_NR_Base { 

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT website, COUNT(website) AS counter 
				FROM ac_nr_mynewsdesk_company_data
				WHERE is_website_valid = 1
				GROUP BY website
				HAVING COUNT(website) > 1 
				ORDER BY COUNT(website) DESC
				LIMIT 1";

		while ($cnt++ <= 100)
		{			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$mynewsdesk_c_data = Model_MyNewsDesk_Company_Data::from_db($result);
			if (!$mynewsdesk_c_data) break;

			
			$sql_all = "SELECT mynewsdesk_company_id 
						FROM ac_nr_mynewsdesk_company_data
						WHERE website = ?
						ORDER BY mynewsdesk_company_id";

			$result_all = $this->db->query($sql_all, array($mynewsdesk_c_data->website));
			$recs = Model_MyNewsDesk_Company_Data::from_db_all($result_all);

			$ids_to_replace = array();
			$first_id = null;
			foreach ($recs as $i => $rec)
			{
				if ($i == 0)
					$first_id = $rec->mynewsdesk_company_id;

				else
					$ids_to_replace[] = $rec->mynewsdesk_company_id;
			}

			if (!count($ids_to_replace))
				continue;

			$str_ids_to_replace = sql_in_list($ids_to_replace);

			$sql_mynewsdesk_pr = "UPDATE nr_pb_mynewsdesk_content
							SET mynewsdesk_company_id = {$first_id}
							WHERE mynewsdesk_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_mynewsdesk_pr);

			$sql_del_mynewsdesk_comp = "DELETE FROM ac_nr_mynewsdesk_company
									WHERE id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_mynewsdesk_comp);

			$sql_del_mynewsdesk_c_data = "DELETE FROM ac_nr_mynewsdesk_company_data
									WHERE mynewsdesk_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_mynewsdesk_c_data);

		}
	}
		
}

?>
