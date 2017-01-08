<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class Login_Controller extends Browse_Base {

	public function index()
	{
		$url = $this->website_url('manage');
		$this->redirect(gstring($url), false);
	}

}