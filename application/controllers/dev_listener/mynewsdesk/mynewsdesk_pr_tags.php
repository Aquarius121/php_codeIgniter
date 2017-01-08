<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class MyNewsDesk_PR_Tags_Controller extends Iella_Base {
	
	public function save()
	{
		$recs = $this->iella_in->recs;

		$content_ids = array();

		foreach ($recs as $rec)
		{
			$sql = "SELECT c.*
					FROM nr_content c
					INNER JOIN nr_pb_mynewsdesk_content p
					ON p.content_id = c.id
					WHERE p.dev_site_content_id = ?";

			if ($m_content = Model_Content::from_db($this->db->query($sql, array($rec->content_id))))
			{
				$m_content->set_tags($rec->tags);
				$m_content->save();
				$content_ids[] = $rec->content_id;
			}			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}	
}

?>

