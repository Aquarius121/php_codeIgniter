<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class User_Controller extends Iella_Base {
	
	public function find()
	{
		$user_id = $this->iella_in->user_id;
		$this->iella_out->user = Model_User::find($user_id);
	}
	
}

?>