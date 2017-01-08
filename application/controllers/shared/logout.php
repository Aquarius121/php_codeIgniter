<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_shared_fnc('shared/auth');

class Logout_Controller extends CIL_Controller {

	public function index()
	{
		Auth_Shared::do_logout($this);
		$this->redirect('shared/login');
	}

}

?>