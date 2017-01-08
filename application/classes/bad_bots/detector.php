<?php

namespace Bad_Bots;

class Detector {
	
	protected $data_dir;
	protected $remote_addr;
	protected $user_agent;

	protected $bad_user_agents = array(

		'#AhrefsBot#',
		'#Apache-HttpClient/UNAVAILABLE#',
		'#bhcBot#',
		'#bluemasterbot#',
		'#Go-http-client#',
		'#looksystems\.net#',
		'#NetShelter ContentScan#',
		'#Owlin bot#',
		'#Salesforce\.com RSS Aggregator#',
		'#SimplePie#',
		'#UniversalFeedParser#',
		'#www\.salesforce\.com#',
		'#Tiny Tiny RSS#',

	);

	public function __construct($opt)
	{
		$this->data_dir    = $opt['data_dir'];
		$this->remote_addr = $opt['remote_addr'];
		$this->user_agent  = $opt['user_agent'];
	}
	
	public function detect()
	{
		foreach ($this->bad_user_agents as $regex)
		{
			if (preg_match($regex, $this->user_agent)) 
			{
				$this->add_to_bad_bots();
				return;
			}
		}
	}
	
	protected function add_to_bad_bots()
	{			
		if ($this->is_valid_remote_addr($this->remote_addr))
		{
			$filename = sprintf('%s.bot', $this->remote_addr);
			$filepath = build_path($this->data_dir, $filename);
			file_put_contents($filepath, $this->user_agent);
		}
	}

	protected function is_valid_remote_addr($remote_addr)
	{
		return filter_var($remote_addr, FILTER_VALIDATE_IP) !== false;
	}

}
