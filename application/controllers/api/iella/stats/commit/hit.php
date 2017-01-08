<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/stats/commit/base');

class Hit_Controller extends Stats_Commit_Base {
	
	protected $buckets;

	public function index()
	{
		set_time_limit(900);

		$queue = &$this->iella_in;
		$total = count($queue);
		$this->iella_out->count = $total;
		$this->iella_out->success = true;
		$this->buckets = array();
		$summation_batch = array();
		$referer_batch = array();

		// batch process all remote addr and 
		// attach the location property to each request
		$this->add_location_data_to_queue($queue);

		for ($i = 0; $i < $total; $i++)
		{
			$request = $queue[$i];

			if (isset($request->rec))
			{
				// context list must be a valid array or we assume its bad data
				$context_list = Stats_Engine::data_decode($request->rec);
				if (!is_array($context_list)) continue;
				$date_request = Date::utc($request->ts);
				
				foreach ($context_list as $context)
				{
					$context = Stats_Engine::context_decode($context);
					$bucket = Stats_Engine::hits_bucket($context);
					$location = $request->location;

					if (!isset($this->buckets[$bucket]))
						$this->buckets[$bucket] = array();

					$insert = array();
					$insert[] = $context;
					$insert[] = $date_request->__toString();
					$insert[] = $request->addr;
					$insert[] = $location->country;
					$insert[] = $location->sub;

					$this->buckets[$bucket][] = $insert;
				}
			}

			if (isset($request->sum))
			{
				$context_list = Stats_Engine::data_decode($request->sum);
				if (!is_array($context_list)) continue;

				foreach ($context_list as $context)
				{
					$context = Stats_Engine::context_decode($context);
					if (isset($summation_batch[$context]))
					     $summation_batch[$context][1]++;
					else $summation_batch[$context] = array($context, 1);
				}
			}

			if (isset($request->ref))
			{
				// context list must be a valid array or we assume its bad data
				$context_list = Stats_Engine::data_decode($request->ref);
				if (!is_array($context_list)) continue;
				
				foreach ($context_list as $context)
				{
					$context = Stats_Engine::context_decode($context);
					$referer_url = $request->referer;
					if (!$referer_url) continue;

					$referer_dh = new Data_Hash();
					$referer_dh->url = $referer_url;
					$referer_hash = $referer_dh->hash('md5');

					$insert = array();
					$insert[] = $context;
					$insert[] = $referer_hash;
					$insert[] = $referer_url;

					$referer_batch[] = $insert;
				}
			}
		}

		// free resources
		unset($this->iella_in);
		unset($queue);

		foreach ($this->buckets as $bucket => &$bucket_queue)
		 	$this->insert_batch($bucket, $bucket_queue);
		$this->summation_batch('sx_hits_summation', $summation_batch);
		$this->insert_batch('sx_hits_referer', $referer_batch);
	}
	
}

?>