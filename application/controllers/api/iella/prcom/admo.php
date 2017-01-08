<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');

class Admo_Controller extends PRCom_API_Base {
	
	public function session()
	{
		$session_id = $this->iella_in->session;
		$ras = Virtual_User_Remote_Admo_Session::load($session_id);
		if (!$ras) return;

		$this->iella_out->id = $ras->id;
		$this->iella_out->admin_user = $ras->admin_user;
		$this->iella_out->user = $ras->user;
		$this->iella_out->is_valid = true;
	}
	
}