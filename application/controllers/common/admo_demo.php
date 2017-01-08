<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admo_Demo_Controller extends CIL_Controller {

	public function index()
	{
		$this->session->set('admo_demo_mode', true);
		$this->json(true);
	}

}

?>