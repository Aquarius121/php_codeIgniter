<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Console_Session extends Session {
	
	public function __construct()
	{
		$this->start();
	}
	
	public function start()
	{
		session_id('cli');
		session_start();
	}
	
}

?>