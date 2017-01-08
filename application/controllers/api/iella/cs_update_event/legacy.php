<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Legacy_Controller extends Iella_Base {
	
	public function index()
	{
		$this->iella_out->status = false;
		if ($this->iella_in->update_action == Model_Component_Set::UPDATE_CANCEL)
			return $this->cancel_action();
	}
	
	public function cancel_action()
	{
		$component_set = Model_Component_Set::from_object($this->iella_in->component_set);
		$user = Model_User::find($component_set->user_id);
		$terminator = new LEGACY_Subscription_Terminator();
		$terminator->cancel_all($user);
		$this->iella_out->status = true;
	}
	
}

?>