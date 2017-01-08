<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait Order_Trait {
	
	protected $cart_state = array();

	public function item($id, $secret = null, $quantity = 1)
	{		
		$item = Model_Item::find($id);
		if (!$item || $item->is_disabled) show_404();
		if (!$item->is_valid_secret($secret)) show_404();

		if ($set_quantity = (int) $this->input->post('quantity'))
			$quantity = $set_quantity;
		
		$cart = Cart::instance();
		$cart_item = $cart->add($item, $quantity);

		if ($callback = $this->input->get('callback'))
			$cart_item->callback = $callback;
		$cart->save();
		
		if ($this->input->is_ajax_request()) return;
		$url = "{$this->order_url_prefix}";
		$this->set_redirect($url);
	}

	public function reset_item()
	{
		Cart::instance()->reset();
		call_user_func_array(
			array($this, 'item'), 
			$this->params);
	}
	
	public function item_coupon($code)
	{
		$item_args = array_slice(func_get_args(), 1);
		call_user_func_array(array($this, 'item'), $item_args);
		$coupon = Model_Coupon::find_code($code);
		if (!$coupon) $coupon = Model_Coupon::find_secret($code);
		$cart = Cart::instance();
		$cart->set_coupon($coupon);
		$cart->save();
	}

	public function set_coupon($code)
	{
		$coupon = Model_Coupon::find_code($code);
		$cart = Cart::instance();
		$cart->set_coupon($coupon);
		$cart->save();
		
		$this->redirect(gstring('pricing/single'));
	}
	
	public function client_token()
	{
		$braintree = new BrainTree_Process();
		$token = $braintree->generate_client_token();
		$this->json($token);
	}

	public function remove()
	{
		$this->remove_id();
	}
	
	public function remove_id()
	{
		$id = $this->input->post('id');
		$cart = Cart::instance();
		$this->save_cart_state($cart);
		$cart->remove($id);
		$cart->save();
		$this->cart_update($cart);
	}

	public function remove_token()
	{
		$token = $this->input->post('token');
		$cart = Cart::instance();
		$this->save_cart_state($cart);
		$cart->remove_item($token);
		$cart->save();
		$this->cart_updated($cart);
	}

	public function reload()
	{
		$cart = Cart::instance();
		$this->save_cart_state($cart);
		$this->cart_updated($cart, true);
	}

	public function change_quantity()
	{
		$token = $this->input->post('token');
		$quantity = (int) $this->input->post('quantity');
		$cart = Cart::instance();
		$this->save_cart_state($cart);
		$cart_item = $cart->get_token($token);
		if ($cart_item->is_quantity_unlocked)
			$cart_item->quantity = $quantity;
		$cart->save();
		$this->cart_updated($cart);
	}
	
	protected function load_countries()
	{
		$order = array('name', 'asc');
		$criteria = array('is_common', 1);
		$this->vd->common_countries = Model_Country::find_all($criteria, $order);
		$this->vd->countries = Model_Country::find_all(null, $order);
	}
	
	public function apply_coupon()
	{
		$cart = Cart::instance();
		$this->save_cart_state($cart);
		$cart->set_coupon(null);
		$cart->save();
		
		$code = $this->input->post('code');
		$coupon = Model_Coupon::find_code($code);
		$cart->set_coupon($coupon);
		$cart->save();

		$this->cart_updated($cart);
	}

	protected function save_cart_state($cart)
	{
		$this->cart_state = $cart->serialize();
	}

	protected function cart_updated($cart, $render = false)
	{
		if (!$this->cart_state) 
			throw new Exception();

		$response = new stdClass();

		if ($render)
		{
			$this->vd->cart = $cart;
			$this->vd->renewal_costs = 
				$cart->renewal_costs();
			$view = 'shared/partials/cart-items';
			$response->render = $this->load->view_return($view);
		}

		$pre_cart = new Virtual_Cart();
		$pre_cart->unserialize($this->cart_state);
		$post_cart = Virtual_Cart::create_from_cart($cart);

		$response->coupon = $cart->coupon();
		$response->_discount = $cart->discount();
		$response->_price_total = $cart->total();
		$response->_total = $cart->total_with_discount();
		$response->discount = $cart->format($response->_discount);
		$response->price_total = $cart->format($response->_price_total);
		$response->total = $cart->format($response->_total);		
		$response->discount_percent = $cart->discount_as_percent();
		$response->is_one_time_discount = $cart->is_one_time_discount();
		$response->items = array();

		$process_item = function($token, $cart_item, $atd_quantity = 1) use (&$cart, &$process_item) {
			$processed = new stdClass();
			$processed->name = $cart_item->name;
			$processed->hash = $cart_item->hash();
			$processed->item_id = $cart_item->item_id;
			$processed->quantity = $cart_item->quantity;
			$processed->_discount = $cart->line_discount($cart_item);
			$processed->discount = $cart->format($processed->_discount);
			$processed->_base_price_total = $cart_item->base_price_total() * $atd_quantity;
			$processed->base_price_total = $cart->format($processed->_base_price_total);
			$processed->_price_total = $cart_item->price_total() * $atd_quantity;
			$processed->price_total = $cart->format($processed->_price_total);
			$processed->_renewal_distance = $cart_item->item()->renewal_distance();
			$processed->renewal_distance = $this->renewal_distance_text($processed->_renewal_distance);
			$processed->attached = array();
			foreach ($cart_item->attached as $token => $atd)
				$processed->attached[$token] = $process_item($token, $atd, 
					($atd_quantity * $cart_item->quantity));
			return $processed;
		};
		
		foreach ($pre_cart->items() as $token => $cart_item)
		{			
			if (!($cart_item = $post_cart->get_token($token)))
			{
				$response->items[$token] = null;
				continue;
			}

			$post_cart->remove_item($token);
			$response->items[$token] = $process_item($token, $cart_item);
		}

		foreach ($post_cart->items() as $token => $cart_item)
			$response->items[$token] = $process_item($token, $cart_item);

		$response->renewal_costs = $post_cart->renewal_costs();
		foreach ($response->renewal_costs as $k => $price)
		{
			$response->renewal_costs[$k] = new stdClass();
			$response->renewal_costs[$k]->_price = $price;
			$response->renewal_costs[$k]->price = $cart->format($price);
		}

		return $this->json($response);
	}
	
	public function cart_unlock()
	{
		$this->output->set_output('unlocked');
		$cart = Cart::instance();
		$cart->unlock();
	}

	public function cart_reset()
	{
		$this->output->set_output('reset');
		$cart = Cart::instance();
		$cart->clear();
		$cart->save();
	}
	
	// a random prefix for CC field names
	protected function set_cc_suffix()
	{
		$this->vd->cc_suffix = substr(md5(microtime(true)), 0, 8);
	}
	
	// assign cc details to true field names (remove suffix)
	protected function & remove_cc_suffix_from_data(&$data)
	{
		$suffix = $data->cc_suffix;
		foreach ($data as $name => $value)
		{
			if (str_ends_with($name, $suffix))
			{
				unset($data->{$name});
				$name = substr($name, 0, -(strlen($suffix) + 1));
				$data->{$name} = $value;
			}
		}

		return $data;
	}

	// removes sensitive fields from the data
	// such as credit card information
	protected function & remove_sensitive_data(&$data)
	{
		$sensitive = array(
			'cc_cvc',
			'cc_expires_month',
			'cc_expires_year',
			'cc_number',
			'cc_suffix',
			'cc_nonce',
			'payment_method_nonce',
			'paypal_nonce',
			'password',
		);

		foreach ($sensitive as $name)
		{
			if (isset($data->{$name}))
				unset($data->{$name});
		}

		return $data;
	}

	// send receipt to customer (and staff CC)
	// * assumes that appropriate View_Data 
	// values have been set
	protected function send_receipt($user, $is_renewal = false)
	{
		$this->vd->user = $user;
		$this->vd->is_renewal = $is_renewal;
		if ($this->vd->order instanceof Model_Order 
			&& !isset($this->vd->order_data))
			$this->vd->order_data = $this->vd->order->raw_data();

		// receipt email message to be sent to the user 
		$message = $this->load->view('email/receipt', null, true);
		
		// send receipt
		$email = new Email();
		$email->__avoid_conversation();
		$email->set_to_email($user->email);
		$email->set_from_email($this->conf('email_address'));
		$email->set_to_name($user->name());
		$email->set_from_name($this->conf('email_name'));
		$email->set_subject('Newswire Receipt');
		$email->set_message($message);
		$email->enable_html();

		if (!$user->is_mail_blocked(Model_User_Mail_Blocks::PREF_ORDER))
			Mailer::queue($email, false, Mailer::POOL_TRANSACTIONAL);

		// lookup staff who want a CC on the receipt and send to each
		$emails_block = Model_Setting::value('staff_email_order_receipts');
		$cc_emails = Model_Setting::parse_block($emails_block);
		
		// change the subject so that its unique
		$email->set_subject(sprintf('Newswire Receipt: Transaction #%s', 
			$this->vd->transaction->nice_id()));

		foreach ($cc_emails as $cc_email)
		{
			// send another copy to the staff
			$email->set_to_email($cc_email);
			Mailer::queue($email, false, Mailer::POOL_TRANSACTIONAL);
		}
	}

	public function renewal_distance_text($distance)
	{
		if (!$distance) return (string) null;
		if ($distance === 30)
		     return 'each month';
		elseif ($distance === 90)
		     return 'each quarter';
		elseif ($distance === 360)
		     return 'each year';
		elseif ($distance === 720)
		     return 'every 2 years';
		// default to exact day count
		return "every {$distance} days";
	}

	protected function bill_blocked_error()
	{
		$feedback = new Feedback('error');
		$feedback->set_text('Automated ordering system is unavailable.');
		$feedback->add_text(' Please contact us to continue.');
		$this->add_feedback($feedback);
		if (Auth::is_user_online())
		     $this->redirect('manage');
		else $this->redirect('register');
		return;
	}
	
}

?>
