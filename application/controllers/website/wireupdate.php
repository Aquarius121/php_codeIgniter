<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class WireUpdate_Controller extends Website_Base {

	public function index()
	{
		$this->redirect(preg_replace('#^wireupdate#', 
			'journalists', $this->uri->uri_string));
	}
	
}

?>