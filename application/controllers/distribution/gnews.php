<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('distribution/generic');

class GNews_Controller extends Generic_Controller {

	protected $view = 'distribution/gnews';
	protected $stats = false;

	public function index()
	{
		$sql = "SELECT c.id FROM nr_content c 
			INNER JOIN nr_pb_pr p
			ON p.content_id = c.id
			INNER JOIN nr_content_copies cc
			ON cc.content_id = c.id
			AND cc.copies = 0
			WHERE c.is_published = 1 
			AND c.type = ? 
			AND c.is_premium = 1 
			AND c.date_publish < UTC_TIMESTAMP()
			AND c.date_publish > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 DAY)
			AND p.is_distribution_disabled = 0
			ORDER BY c.date_updated DESC
			LIMIT 500";

		$dbr = $this->db->query($sql, array(Model_Content::TYPE_PR));
		foreach ($dbr->result() as $result)
			$id_list[] = (int) $result->id;

		$this->_index_id_list($id_list);
	}

	protected function _index_id_list($id_list)
	{
		$id_list_str = sql_in_list($id_list);
		$sql = "SELECT c.id,
			c.title,
			c.slug,
			c.date_publish,
			c.type
			FROM nr_content c 
			WHERE c.id IN ({$id_list_str})
			ORDER BY c.date_publish DESC";

		$this->_index_sql($sql);
	}

}

?>