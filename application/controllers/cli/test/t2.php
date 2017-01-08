<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class T2_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;
	
	public function index()
	{
		$email = new Email();
		$email->set_subject('test');
		$email->set_to_email('dev-inewswire@staite.net');
		$email->set_from_email('jonathan@newswire.com');
		Mailer::send($email);
		Mailer::queue($email);
	}

}

