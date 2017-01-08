<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Remove_Dup_Companies_Controller extends Auto_Create_NR_Base { 

	public function index()
	{
		$cnt = 1;

		$sql = "SELECT website, COUNT(website) AS counter 
				FROM ac_nr_prweb_company_data
				WHERE is_website_updated = 1
				GROUP BY website
				HAVING COUNT(website) > 1 
				ORDER BY COUNT(website) DESC
				LIMIT 1";

		while ($cnt++ <= 100)
		{			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$prweb_c_data = Model_PRWeb_Company_Data::from_db($result);
			if (!$prweb_c_data) break;

			
			$sql_all = "SELECT prweb_company_id 
						FROM ac_nr_prweb_company_data
						WHERE website = ?
						ORDER BY prweb_company_id";

			$result_all = $this->db->query($sql_all, array($prweb_c_data->website));
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

			if (!count($ids_to_replace))
				continue;

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

	public function by_company_name()
	{
		$cnt = 1;

		$sql = "SELECT name, COUNT(name) AS counter 
				FROM ac_nr_prweb_company
				GROUP BY name
				HAVING COUNT(name) > 1 
				ORDER BY COUNT(name)
				LIMIT 1";

		while ($cnt++ <= 500)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$prweb_c = Model_PRWeb_Company::from_db($result);
			
			echo "name = ". $prweb_c->name . "\n \n";
			
			if (!$prweb_c) break;

			$sql_all = "SELECT pc.id AS id
						FROM ac_nr_prweb_company pc
						LEFT JOIN nr_company c
						ON pc.company_id = c.id
						WHERE pc.name = ?
						ORDER BY c.user_id DESC";

			$result_all = $this->db->query($sql_all, array($prweb_c->name));
			$recs = Model_PRWeb_Company::from_db_all($result_all);

			$ids_to_replace = array();
			$first_id = null;
			foreach ($recs as $i => $rec)
			{
				if ($i == 0)
				{
					$first_id = $rec->id;
					$rec->is_dup_remove_comp = 1;
					$rec->save();
				}
				else
					$ids_to_replace[] = $rec->id;
			}

			if (!count($ids_to_replace))
				continue;

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
			echo "done!!!";

		}
	}


	public function sync_remove_dup_with_live()
	{
		$cnt = 1;

		$sql = "SELECT * 
				FROM ac_nr_prweb_company
				WHERE is_dup_remove_comp = 1
				AND is_dup_updated = 0
				LIMIT 1";

		while ($cnt++ <= 500)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$prweb_c = Model_PRWeb_Company::from_db($result);
			
			echo "name = ". $prweb_c->name . "\n \n";
			
			if (!$prweb_c) break;

			$first_id = null;
			$first_id = $prweb_c->id;

			$sql_all = "SELECT pc.id AS id
						FROM ac_nr_prweb_company pc
						LEFT JOIN nr_company c
						ON pc.company_id = c.id
						WHERE pc.name = ?
						AND pc.id <> '$first_id'
						ORDER BY c.user_id DESC";

			$result_all = $this->db->query($sql_all, array($prweb_c->name));
			$recs = Model_PRWeb_Company::from_db_all($result_all);

			$ids_to_replace = array();
			foreach ($recs as $i => $rec)
				$ids_to_replace[] = $rec->id;


			if (!count($ids_to_replace))
				continue;

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

			$prweb_c->is_dup_updated = 1;
			$prweb_c->save();

		}
	}


	public function update_website_url()
	{
		$sql = "SELECT *
				FROM ac_nr_prweb_company_data
				WHERE is_website_updated = 0
				ORDER BY prweb_company_id DESC
				LIMIT 1";

		while (1)
		{
			if (!$c_data = Model_PRWeb_Company_Data::from_sql($sql))
				break;

			$c_data->website_bak = $c_data->website;
			$c_data->website = $this->get_web_address($c_data->website);
			$c_data->is_website_updated = 1;
			$c_data->save();
		}
	}
		
}

?>
