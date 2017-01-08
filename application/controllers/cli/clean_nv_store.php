<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Clean_NV_Store_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function index()
	{
		$sql = "DELETE FROM nv_store
			WHERE date_expires IS NOT NULL 
			AND date_expires < UTC_TIMESTAMP()";
		$this->db->query($sql);
	}

}