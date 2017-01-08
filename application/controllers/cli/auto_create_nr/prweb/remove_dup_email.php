<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Remove_Dup_Email_Controller extends CLI_Base { 

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT email, COUNT(email) AS counter 
				FROM ac_nr_prweb_company_data 
				GROUP BY email 
				HAVING email = 'info@xlibris.com' 
				ORDER BY COUNT(email) DESC 
				LIMIT 1 ";

		while ($cnt++ <= 1)
		{			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$prweb_c_data = Model_PRWeb_Company_Data::from_db($result);
			if (!$prweb_c_data) break;

			
			$sql_all = "SELECT prweb_company_id 
						FROM ac_nr_prweb_company_data
						WHERE email = ?
						ORDER BY prweb_company_id";

			$result_all = $this->db->query($sql_all, array('info@xlibris.com'));
			$recs = Model_PRWeb_Company_Data::from_db_all($result_all);

			$ids_to_replace = array();
			$first_id = null;
			foreach ($recs as $i => $rec)
			{
				if ($i == 0)
					$first_id = $rec->prweb_company_id;

				else
					$ids_to_replace[] = $rec->prweb_company_id;
			}

			$str_ids_to_replace = sql_in_list($ids_to_replace);

			$sql_prweb_pr = "UPDATE nr_pb_prweb_pr
							SET prweb_company_id = {$first_id}
							WHERE prweb_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_prweb_pr);

			$sql_del_prweb_comp = "DELETE FROM ac_nr_prweb_company
									WHERE id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_prweb_comp);

			$sql_del_prweb_c_data = "DELETE FROM ac_nr_prweb_company_data
									WHERE prweb_company_id IN ({$str_ids_to_replace})";

			$this->db->query($sql_del_prweb_c_data);

		}
	}
		
}

?>
