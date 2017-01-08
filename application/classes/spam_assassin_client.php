<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Spam_Assassin_Client {
	
	protected $sa_client;
	
	public function __construct()
	{
		$this->sa_client = $this->__create_client();
	}

	protected function __create_client()
	{
		$ci =& get_instance();
		$config = $ci->conf('spamassassin');
		$this->enabled = $config['enabled'];
		if (!$this->enabled) return;
		$sa_client = new Spamassassin\Client($config);
		return $sa_client;
	}

	public function spam_score($message)
	{
		if (!$this->enabled) return 0;
		$sa_scrore = $this->sa_client->getScore($message);
		return $sa_scrore;
	}

	public function spam_check($message)
	{
		if (!$this->enabled) return false;
		$s_check = $this->sa_client->check($message);
		return $s_check;
	}

	public function spam_report($message)
	{
		if (!$this->enabled) return false;
		$s_report = $this->sa_client->getSpamReport($message);
		return $s_report;
	}

}