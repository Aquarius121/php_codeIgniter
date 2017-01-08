<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_shared_fnc('shared/auth');
load_controller('website/base');

class Logout_Controller extends Website_Base {

	public function index()
	{
		Auth_Shared::do_logout($this);
		$this->redirect('login');
	}

}

?>