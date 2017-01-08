<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/api/base');

class Test_Controller extends API_Base {
	
	public function index()
	{
		// Auth::user() works as normal 
		$this->iella_out->email = Auth::user()->email;
	}
	
}

?>
