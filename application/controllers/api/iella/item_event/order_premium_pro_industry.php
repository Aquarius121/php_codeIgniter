<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/item_event/order_component');

class Order_Premium_Pro_Industry_Controller extends Order_Component_Controller {
	
	public function index()
	{
		parent::index();

		$user = Model_User::find($this->iella_in->user->id);
		$urd = $user->raw_data();
		if (!$urd) $urd = new Raw_Data();
		$urd->auto_hold_under_review = true;
		$user->raw_data($urd);
		$user->save();
	}
	
}

?>