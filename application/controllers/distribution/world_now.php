<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('distribution/generic');

class World_Now_Controller extends Generic_Controller {

	protected $view = 'distribution/world_now';
	
	public function index()
	{
		$providers = sql_in_list(array(
			Model_Content_Release_Plus::PROVIDER_WORLDNOW
		));

		$sql = "SELECT c.id FROM nr_content c 
			INNER JOIN nr_pb_pr p
			ON p.content_id = c.id
			INNER JOIN nr_content_release_plus crp
			ON crp.content_id = c.id
			AND crp.provider IN ({$providers})
			AND crp.is_confirmed = 1
			WHERE c.is_published = 1 
			AND c.type = ? 
			AND c.is_premium = 1
			AND c.date_publish < UTC_TIMESTAMP()
			AND p.is_distribution_disabled = 0
			AND p.is_external = 0
			ORDER BY c.date_updated DESC
			LIMIT 200";

		$id_list = array();
		$dt_last_modified = Date::first();
		$dbr = $this->db->query($sql, array(Model_Content::TYPE_PR));
		foreach ($dbr->result() as $result)
			$id_list[] = (int) $result->id;

		$this->_index_id_list($id_list);
	}

	protected function _text_filter($text)
	{
		return to_utf8_fix_chars($text);
	}

}