<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('common/order');

class Make_Controller extends Manage_Base {

	public function index($order_id)
	{
		$order = Model_Order::find($order_id);
		if (!$order || $order->user_id != Auth::user()->id)
			$this->denied();
		$this->set_redirect('manage/order');
		$cart = Cart::create_from_order($order);

		if (!$cart->validate())
		{
			$feedback = new Feedback('warning');
			$feedback->set_title('Warning!');
			$feedback->set_text('Some items could not be added to the cart.');
			$this->add_feedback($feedback);
		}

		$cart->update_prices();
		$cart->save();
	}
	
}

?>