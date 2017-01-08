<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_Item_Controller extends Iella_Base {
	
	public function index()
	{
		$user = Model_User::from_object($this->iella_in->user);
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$item = $cart_item->item();
		
		if ($item->type == Model_Item::TYPE_PLAN) 
		{
			// record the events within KM
			$kmec = new KissMetrics_Event_Library($user);
			$kmec->event_purchased_plan($cart_item);
		}
		else
		{
			// record the events within KM
			$kmec = new KissMetrics_Event_Library($user);
			$kmec->event_purchased_item($cart_item);
		}
	}
	
}

?>