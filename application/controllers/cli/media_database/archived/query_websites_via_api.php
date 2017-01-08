<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('cli/contact_keyword_builder/sem_rush_api');

class Query_Websites_Via_API_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index($worker_id)
	{
		$mutex = new Flock_Mutex(__FILE__);
		set_time_limit(86400);
		
		while (true)
		{
			$mutex->lock();
			
			$sql = "SELECT * FROM nr_contact_keyword_builder
				WHERE 
				    is_api_queried = 0
				AND is_company_found = 1
				AND is_excluded = 0
				AND is_locked = 0
				AND domain IS NOT NULL
				LIMIT 1";
				
			$dbr = $this->db->query($sql);
			$model = Model_Contact_Keyword_Builder::from_db($dbr);
			
			if (!$model) 
			{
				$this->trace('waiting for more work...');
				sleep(60);
				continue;
			}
			
			// lock all for that domain while we check
			$sql = "UPDATE nr_contact_keyword_builder
				SET is_locked = 1 WHERE domain = ?";
			$this->db->query($sql, array($model->domain));
			
			$mutex->unlock();
			
			$params = new stdClass();
			$params->type = 'url_organic';
			$params->url = $model->website;
			$params->database = 'us';
			$params->display_limit = 20;
			$params->export_columns = 'Ph';
			$api = new SEM_Rush_API();
			$response = $api->query($params);
			
			if (!$response || !$response->data)
			{
				// update/unlock all for that domain
				$sql = "UPDATE nr_contact_keyword_builder
					SET is_api_queried = 0, is_locked = 0
					WHERE domain = ?";
				$this->db->query($sql, array($model->domain));
				$this->trace('API FAILURE');
				sleep(5);
				continue;
			}
			
			$tags = $this->parse_response($response->data);
			$json = json_encode($tags);
			
			$this->trace(sprintf('%02d', $worker_id), $model->domain, count($tags));
			
			if (!count($tags) && !preg_match('#www\.#i', $model->website))
			{
				$website = preg_replace('#(https?://)(.*)$#i', 
					'${1}www.${2}', $model->website);
				// update/unlock all for that domain
				$sql = "UPDATE nr_contact_keyword_builder
					SET is_api_queried = 0, is_locked = 0,
					website = ?	WHERE domain = ?";
				$this->db->query($sql, array($website, $model->domain));
			}
			else
			{
				// update/unlock all for that domain
				$sql = "UPDATE nr_contact_keyword_builder
					SET is_api_queried = 1, is_locked = 0,
					tags = ? WHERE domain = ?";
				$this->db->query($sql, array($json, $model->domain));
			}			
		}
		
		$mutex->unlock();
	}
	
	protected function parse_response($response)
	{
		if (preg_match('#error.+nothing found#i', $response))
			return array();
		
		$tags = array();
		$lines = preg_split('#\r?\n#', $response);
		
		if (!preg_match('#keyword#i', @$lines[0]))
		{
			$this->trace('API failure: credits?');
			die();
		}
		
		array_shift($lines);		
		foreach ($lines as $line)
		{
			$words = preg_split('#\s+#', $line);
			foreach ($words as $word)
			{
				$tag = Tag::uniform($word);
				if ($tag && !in_array($tag, $tags))
					$tags[] = $tag;
			}			
		}
		
		return $tags;
	}

}

?>