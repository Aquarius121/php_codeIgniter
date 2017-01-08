<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('distribution/generic');

class Fin_Content_Controller extends Generic_Controller {

	public function __on_execution_start()
	{
		parent::__on_execution_start();

		$this->vd->feed_url = 'fincontent.xml';
		$post_item_view = 'distribution/partials/fin_content_tags';
		$this->vd->injects->post_item = $post_item_view;
	}

	public function index()
	{
		$sql = "SELECT c.id FROM nr_content c 
			INNER JOIN nr_pb_pr p
			ON p.content_id = c.id
			AND c.is_published = 1
			AND c.type = ? 
			AND c.is_premium = 1
			AND c.date_publish < UTC_TIMESTAMP()
			AND p.is_distribution_disabled = 0
			AND p.is_external = 0
			ORDER BY c.date_updated DESC
			LIMIT 100";

		$id_list = array();
		$dt_last_modified = Date::first();
		$dbr = $this->db->query($sql, array(Model_Content::TYPE_PR));
		foreach ($dbr->result() as $result)
			$id_list[] = (int) $result->id;

		$this->_index_id_list($id_list);
	}

}
