<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/stats/commit/base');

class Activate_Controller extends Stats_Commit_Base {
	
	protected $buckets;

	public function index()
	{
		set_time_limit(900);

		$queue = &$this->iella_in;
		$total = count($queue);
		$this->iella_out->count = $total;
		$this->iella_out->success = true;
		$batch = array();

		for ($i = 0; $i < $total; $i++)
		{
			if (!isset($queue[$i]->rec)) continue;
			if (!isset($queue[$i]->set)) continue;
			
			// context list must be a valid array or we assume its bad data
			$context_list = Stats_Engine::data_decode($queue[$i]->rec);
			if (!is_array($context_list)) continue;

			$context_set = Stats_Engine::data_decode($queue[$i]->set);
			$context_set = Stats_Engine::context_decode($context_set);
			$date_request = Date::utc($queue[$i]->ts);

			foreach ($context_list as $context)
			{
				$insert = array();
				$insert[] = Stats_Engine::context_decode($context);
				$insert[] = $context_set;
				$insert[] = $date_request->__toString();
				$batch[] = $insert;
			}
		}

		// free resources
		unset($this->iella_in);
		unset($queue);

		$this->insert_batch('sx_activation', $batch);
	}
	
}

?>