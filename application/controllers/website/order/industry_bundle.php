<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/order');

class Industry_Bundle_Controller extends Order_Controller {

	protected $force_website_checkout = true;

	public function index()
	{
		if (!$this->input->post())
			$this->redirect('order');

		$cart = Cart::instance();
		$cart_item = $this->landing($cart);

		$item = Model_Item::find_slug('email-credit');
		$atd = Cart_Item::create($item, 1000, 0);
		$atd->name = 'Media Outreach Credits';
		$cart_item->attach($atd);
		// $atd->hidden = true;

		$item = Model_Item::find_slug('release-plus-prnewswire');
		$atd = Cart_Item::create($item, 1, 0);
		$cart_item->attach($atd);
		// $atd->hidden = true;

		$cart->save();
		$this->_index();
	}
	
}