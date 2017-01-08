<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class MyNewsDesk_PR_Topics_Controller extends Iella_Base {
	
	public function save()
	{
		$recs = $this->iella_in->recs;

		$content_ids = array();

		foreach ($recs as $rec)
		{
			$sql = "SELECT *
					FROM nr_pb_mynewsdesk_content p
					WHERE p.dev_site_content_id = ?";

			if ($m_pb = Model_PB_MyNewsDesk_Content::from_db($this->db->query($sql, array($rec->content_id))))
			{
				$topics = $rec->topics;
				if (!empty($topics))
				{
					$this->db->query("DELETE FROM ac_nr_mynewsdesk_content_topic 
						WHERE content_id = ?", array($m_pb->content_id));
					
					foreach ($topics as $topic)
					{
						if (!($topic = trim($topic))) continue;
						$this->db->query("INSERT IGNORE INTO ac_nr_mynewsdesk_content_topic (content_id, 
							value) VALUES (?, ?)", array($m_pb->content_id, $topic));
					}
				}


				$content_ids[] = $rec->content_id;
			}			
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}	
}

?>

