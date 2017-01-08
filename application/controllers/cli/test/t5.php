<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class T5_Controller extends CLI_Base { 

	protected $trace_enabled = true;
	protected $trace_time = false;

	public function index()
	{
		$publish_date = "October 06, 2016 16:35 ET";
		$publish_date = str_replace("ET", "EDT", $publish_date);
		$publish_date = date(DATE::FORMAT_MYSQL, strtotime($publish_date));
		$publish_date = Date::in($publish_date);
		$this->console($publish_date);
	}

}
