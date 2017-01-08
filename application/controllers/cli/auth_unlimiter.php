<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Auth_Unlimiter_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
	
	public function index()
	{
		// reduce the authentication attempt count for all those above 0
		$this->db->query("UPDATE nr_auth_limiter SET count = count - 1");
		$this->db->query("DELETE FROM nr_auth_limiter WHERE count <= 0");
	}

}