<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Remove_Dup_PRs_Controller extends Auto_Create_NR_Base { 

	public function index()
	{
		$cnt = 1;

		$sql = "SELECT title, COUNT(title) 
				FROM nr_content c
				INNER JOIN nr_pb_businesswire_pr p
				ON p.content_id = c.id
				GROUP BY title 
				Having COUNT(title) > 1 
				ORDER BY count(title) DESC 
				LIMIT 50";

		while (1)
		{
			$results = Model_Content::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $m_content)
				$this->remove_dups($m_content);
		}
	}

	protected function remove_dups($m_content)
	{			
		$sql_all = "SELECT id 
					FROM nr_content c
					WHERE title = ?
					ORDER BY id";

		$result_all = $this->db->query($sql_all, array($m_content->title));
		$recs = Model_Content::from_db_all($result_all);

		$ids_to_del = array();
		$first_id = null;
		foreach ($recs as $i => $rec)
		{
			if ($i == 0)
				$first_id = $rec->id;

			else
				$ids_to_del[] = $rec->id;
		}

		if (!count($ids_to_del))
			continue;

		$str_ids_to_del = sql_in_list($ids_to_del);

		$sql_content = "DELETE FROM nr_content
						WHERE id IN ({$str_ids_to_del})";

		$this->db->query($sql_content);
		

		$sql_pb_businesswire = "DELETE FROM nr_pb_businesswire_pr
									WHERE content_id IN ({$str_ids_to_del})";

		$this->db->query($sql_pb_businesswire);

		$sql_del_m_content_data = "DELETE FROM nr_content_data
									WHERE content_id IN ({$str_ids_to_del})";

		$this->db->query($sql_del_m_content_data);

		$sql_pb_pr = "DELETE FROM nr_pb_pr
						WHERE content_id IN ({$str_ids_to_del})";
		$this->db->query($sql_pb_pr);
	}
}

?>
