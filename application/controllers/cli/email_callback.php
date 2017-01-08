<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Email_Callback_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index($address)
	{
		$handle = fopen('php://stdin', 'r');
		$email = null;
		while (!feof($handle))
			$email .= fgets($handle, 32768);
		fclose($handle);

		$callback = Model_Email_Callback::find_address($address);
		$callback->trigger($email);
	}

}