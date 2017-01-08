<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Echo_Controller extends Iella_Base {
	
	public function index()
	{
		$this->iella_out = $this->iella_in;
	}
	
}

?>