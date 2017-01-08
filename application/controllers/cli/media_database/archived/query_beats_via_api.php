<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('cli/contact_keyword_builder/sem_rush_api');

class Query_Beats_Via_API_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index($worker_id)
	{
		$mutex = new Flock_Mutex(__FILE__);
		set_time_limit(86400);
		
		while (true)
		{
			$mutex->lock();
			
			$sql = "SELECT * FROM __beats_google_urls
				WHERE is_api_queried = 0
				AND is_locked = 0
				LIMIT 1";
				
			$dbr = $this->db->query($sql);
			$model = __beats_google_urls::from_db($dbr);
			
			if (!$model) 
			{
				$this->trace('waiting for more work...');
				sleep(60);
				continue;
			}
			
			// lock all for that beat while we check
			$sql = "UPDATE __beats_google_urls
				SET is_locked = 1 WHERE beat_id = ?";
			$this->db->query($sql, array($model->beat_id));
			
			$mutex->unlock();
			
			$links = $model->raw_data();
			
			foreach ($links as $link)
			{				
				// google redirect to website
				if (preg_match('#url\?(url|q)=([^&]+)#i', $link->href, $match))
					$link->href = $match[2];
				
				$params = new stdClass();
				$params->type = 'url_organic';
				$params->url = $link->href;
				$params->database = 'us';
				$params->display_limit = 20;
				$params->export_columns = 'Ph';
				$api = new SEM_Rush_API();
				$response = $api->query($params);
				
				if (!$response || !$response->data)
				{
					// update/unlock all for that beat
					$sql = "UPDATE __beats_google_urls
						SET is_api_queried = 0, is_locked = 0
						WHERE beat_id = ?";
					$this->db->query($sql, array($model->beat_id));
					$this->trace('API FAILURE');
					sleep(5);
					continue;
				}
				
				$tags = $this->parse_response($response->data);
				$this->trace(sprintf('%02d', $worker_id), $model->beat_id, count($tags), $link->href);
				
				foreach ($tags as $tag)
				{
					$tag = Tag::uniform(trim($tag));
					$sql = "INSERT IGNORE INTO nr_contact_linked_tags
						VALUES (?, 'beats', ?)";
					$this->db->query($sql, array($tag, $model->beat_id));
				}
			}	
			
			// update/unlock all for that beat
			$sql = "UPDATE __beats_google_urls
				SET is_api_queried = 1, is_locked = 0
				WHERE beat_id = ?";
			$this->db->query($sql, array($model->beat_id));
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

// temporary model class 
class __beats_google_urls extends Model {
	use Raw_Data_Trait;
	protected static $__table = '__beats_google_urls';
	protected static $__primary = 'beat_id';
}

?>