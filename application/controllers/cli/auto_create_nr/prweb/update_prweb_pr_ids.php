<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Update_PRWeb_PR_IDs extends CLI_Base { 

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT p.*,  c.id AS new_content_id
				FROM nr_pb_prweb_pr p
				INNER JOIN nr_content c
				ON p.content_id = c.dev_server_content_id
				WHERE is_processed = 0
				ORDER by p.content_id 
				LIMIT 1 ";

		while ($cnt++ <= 10000)
		{			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$prweb_pr = Model_PB_PRWeb_PR::from_db($result);
			if (!$prweb_pr) break;

			$sql_update = "UPDATE nr_pb_prweb_pr
							SET content_id = {$prweb_pr->new_content_id},
							is_processed = 1
							WHERE content_id = {$prweb_pr->content_id}";

			$this->db->query($sql_update);

		}
	}
		
}

?>
