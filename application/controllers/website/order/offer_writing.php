<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/order');

class Offer_Writing_Controller extends Order_Controller {

	protected $force_website_checkout = true;

	public function index()
	{
		if (!$this->input->post())
			$this->redirect('order');

		$this->vd->inject_before_rule[] = 'website/order/offer_writing';
		$this->vd->offer_writing_item = Model_Item::find_slug('writing-credit');

		$cart = Cart::instance();
		$cart_item = $this->landing($cart);
		$cart_item->track->offer_writing = true;
		$cart->save();

		$this->_index();
	}

	public function add()
	{
		$cart = Cart::instance();

		foreach ($cart->items() as $cart_item)
		{
			if (isset($cart_item->track->offer_writing) &&
				$cart_item->track->offer_writing)
			{
				$item = Model_Item::find_slug('writing-credit');
				$cart_item->callback = 'manage/writing/process';
				$cart_item->attach($item);
				$cart->save();
				break;
			}
		}
	}
	
}