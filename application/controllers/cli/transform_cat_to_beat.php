<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Transform_Cat_To_Beat_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index()
	{
		set_time_limit(86400);
		$this->_process('pr');
		$this->_process('news');
	}

	protected function _process($type)
	{
		$offset = 0;
		Model_Beat::enable_cache();		

		while (true)
		{
			$sql = "SELECT content_id, cat_1_id, cat_2_id, cat_3_id 
				from nr_pb_{$type} order by content_id desc 
				limit {$offset}, 1000";
			
			$results = Model::from_sql_all($sql);
			if (!$results) break;
			$offset += 1000;
			usleep(500000);

			foreach ($results as $result)
			{
				$beats = array();
				$beats = array_merge($beats, Model_Cat_To_Beat::beats($result->cat_1_id));
				$beats = array_merge($beats, Model_Cat_To_Beat::beats($result->cat_2_id));
				$beats = array_merge($beats, Model_Cat_To_Beat::beats($result->cat_3_id));

				$m = new Model_Content();
				$m->id = $result->content_id;
				$m->set_beats($beats);

				$this->trace($result->content_id, count($beats));
			}
		}
	}

}