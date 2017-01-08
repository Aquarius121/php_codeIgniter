<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Remove_Dup_Companies_Controller extends Auto_Create_NR_Base { 

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT c.name, 
				COUNT(c.name) AS counter
				FROM ac_nr_owler_company c
				GROUP BY c.name 
				HAVING count(c.name) > 1
				LIMIT 1";

		while ($cnt++ <= 500)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$owler_c = Model_Owler_Company::from_db($result);
			if (!$owler_c) break;

			
			$sql_all = "SELECT owler_company_id 
						FROM ac_nr_owler_company c
						INNER JOIN ac_nr_owler_company_data cd
						ON cd.owler_company_id = c.id
						WHERE c.name = ?
						ORDER BY owler_company_id";

			$result_all = $this->db->query($sql_all, array($owler_c->name));
			$recs = Model_Owler_Company::from_db_all($result_all);

			$ids_to_replace = array();
			$first_id = null;
			foreach ($recs as $i => $rec)
			{
				if ($i == 0)
					$first_id = $rec->owler_company_id;

				else
					$ids_to_replace[] = $rec->owler_company_id;
			}

			if (!count($ids_to_replace))
				continue;

			$str_ids_to_replace = sql_in_list($ids_to_replace);

			$sql_del_owler_comp = "DELETE FROM ac_nr_owler_company
										WHERE id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_owler_comp);

			$sql_del_owler_c_data = "DELETE FROM ac_nr_owler_company_data
										WHERE owler_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_owler_c_data);

		}
		
	}

}

?>
