<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Buckets_Check_Controller extends CLI_Base {
	
	public function index()
	{
		$tables = array();
		$db = $this->load_db('stat');
		$dbr = $db->query('show tables');
		foreach ($dbr->result_array() as $result)
			$tables[] = array_values((array) $result)[0];

		$dbr = $db->query('SELECT MAX(context) 
			AS max FROM context_hash');
		$max = $dbr->row()->max;

		$hits_test = $max + (Stats_Engine::HITS_BUCKET_SIZE * 0.75);
		$hits_bucket = Stats_Engine::hits_bucket($hits_test);
		if (!in_array($hits_bucket, $tables))
			$this->send_alert($hits_bucket);
	}

	protected function send_alert($table)
	{
		$alert = new Critical_Alert();
		$alert->set_subject('Stats bucket is near capacity');
		$alert->set_content(sprintf('next: %s', $table));
		$alert->send();
	}

}

?>