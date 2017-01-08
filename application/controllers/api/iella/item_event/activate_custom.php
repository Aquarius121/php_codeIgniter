<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Activate_Custom_Controller extends Iella_Base {
	
	public function index()
	{
		$this->iella_out->status = true;
	}
	
}

?>