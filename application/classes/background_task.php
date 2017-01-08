<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Background_Task {

	protected $command;

	public function __construct($command = null)
	{	
		$this->set($command);
	}

	public function set($command)
	{
		$this->command = $command;
	}

	public function run($workers = 1)
	{
		$this->execute($workers);
	}

	public function execute($workers = 1)
	{
		if (!strlen($this->command)) return;

		for ($iw = 0; $iw < $workers; $iw++)
		{
			$uuid = escapeshellarg(UUID::create());
			$command = escapeshellarg($this->command);
			$execback = 'screen -dmS %s bash -c %s';
			$execback = sprintf($execback, $uuid, $command);
			exec($execback);
		}
	}
	
}

?>
