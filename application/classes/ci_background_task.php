<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Background_Task extends Background_Task {

	protected $params;

	public function __construct($params = array())
	{	
		$this->set($params);
	}

	public function set($params)
	{
		$this->params = $params;
	}

	public function run($workers = 1)
	{
		$this->execute($workers);
	}

	public function execute($workers = 1)
	{
		if (!count($this->params)) return;
		foreach ($this->params as &$param)
			$param = escapeshellarg($param);
		$params = implode(' ', $this->params);
		$command = sprintf('php-cli %s --background-logger cli %s', 
			get_instance()->conf('cli_php_file'), $params);
		$command = escapeshellarg($command);

		for ($iw = 0; $iw < $workers; $iw++)
		{
			$uuid = escapeshellarg(UUID::create());
			$execback = 'screen -dmS %s bash -c %s';
			$execback = sprintf($execback, $uuid, $command);
			exec($execback);
		}
	}
	
}

