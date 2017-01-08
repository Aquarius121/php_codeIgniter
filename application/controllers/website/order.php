<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');
load_controller('shared/order');

class Order_Controller extends Website_Base {

	use Order_Trait {
		Order_Trait::item as order_trait_item;
	}
	
	public $title = 'Order';
	protected $ssl_required = true;
	protected $order_url_prefix = 'order';
	protected $force_website_checkout = false;

	public function __construct()
	{			
		parent::__construct();

		if ($this->force_website_checkout)
		{
			// force the user to use website 
			// checkout so that they can 
			// use custom order page features
			Auth::logout();
		}
		else if (Auth::is_user_online())
		{
			// redirect to control panel version
			$uri = "manage/{$this->uri->uri_string}";
			$this->redirect($uri);
		}

		$this->vd->inject_before_rule = array();
		$this->vd->inject_after_rule = array();
		$this->vd->order_url_prefix = 
			$this->order_url_prefix;
	}

	public function item($id, $secret = null, $quantity = 1)
	{
		// Cart::instance()->reset();
		$this->order_trait_item($id, $secret, $quantity);
	}

	protected function landing($cart)
	{
		$data = $this->vd->data = new Raw_Data();
		$post = (array) $this->input->post();
		foreach ($post as $k => $v)
			$data->{$k} = $v;

		if ($data->first_name && !$data->last_name && 
			// attempt to extract last name from the full name
			preg_match('#^([^\s]+\s+)+([^\s]+)$#i', $data->first_name, $ex))
		{
			$data->first_name = trim($ex[1]);
			$data->last_name = trim($ex[2]);
		}

		$item = null;
		$coupon = null;
		$item_slug = $this->input->post('item_slug');
		$item_secret = $this->input->post('item_secret');
		$callback = $this->input->post('callback');
		$coupon_code = $this->input->post('coupon_code');
		if ($item_slug) $item = Model_Item::find_slug($item_slug);
		if ($coupon_code) $coupon = Model_Coupon::find_code($coupon_code);

		if ($item && $item->is_valid_secret($item_secret) && !$item->is_disabled)
		{
			$cart->reset();
			$cart_item = $cart->add($item, 1);
			$cart_item->callback = $callback;
		}

		if ($coupon)
		{
			$cart->set_coupon($coupon);
		}
		
		if (isset($cart_item))
			return $cart_item;
	}

	// prevent issues when 
	// using child classes
	public function index()
	{
		$this->_index();
	}
	
	public function _index()
	{ 
		$this->ssl_required_post = true;
		$this->_check_ssl_status();

		// already has a recent failure? blockable?
		if (Model_Bill_Failure::has_bill_block_addr($this->env['remote_addr']))
			return $this->bill_blocked_error();

		$this->set_cc_suffix();
		$this->load_countries();
		$this->vd->cart = Cart::instance();
		$this->vd->auth_to_redirect = 'manage/order';
		$this->vd->cart->remove_zeros();
		$this->vd->renewal_costs = 
			$this->vd->cart->renewal_costs();
		
		if (!isset($this->vd->data))
		{
			$this->vd->data = new stdClass();
			$this->vd->data->country_id = Model_Country::ID_UNITED_STATES;

			// default email to fill in
			$this->vd->data->email = $this->session->get('suggested_email');
			$this->vd->data->company_name = $this->session->get('suggested_company');
			$this->vd->data->first_name = $this->session->get('suggested_first_name');
			$this->vd->data->last_name = $this->session->get('suggested_last_name');
		}
		
		$this->load->view('website/header');
		$this->load->view('website/order');
		$this->load->view('website/footer');
	}
	
	public function submit()
	{
		$this->ssl_required_post = true;
		$this->_check_ssl_status();
		
		// already has a recent failure? blockable?
		if (Model_Bill_Failure::has_bill_block_addr($this->env['remote_addr']))
			return $this->bill_blocked_error();
		
		if (!$this->input->post())
			$this->redirect('order');
		
		$data = new stdClass();
		$post = $this->input->post();
		foreach ($post as $k => $v)
			$data->{$k} = $v;
		$this->vd->data = $data;
		$this->remove_cc_suffix_from_data($data);
		
		$cart = Cart::instance();
		$cart->remove_zeros();

		if ($cart->is_clear())
		{
			$feedback = new Feedback('error');
			$feedback->set_text('Your cart is empty.');
			$feedback->set_title('Error!');
			$this->use_feedback($feedback);
			return $this->_index();
		}
		
		if ($cart->is_locked()) 
		{
			$feedback = new Feedback('error');
			$feedback->set_text('Please contact support.');
			$feedback->set_title('Error!');
			$this->use_feedback($feedback);
			return $this->_index();
		}
		
		$vcart = Virtual_Cart::create_from_post_data($post);
		
		if (!$cart->is_equal_to($vcart))
		{
			$feedback = new Feedback('info');
			$feedback->set_text('Your cart has been modified.');
			$feedback->set_title('Alert!');
			$this->use_feedback($feedback);
			return $this->_index();
		}
		
		if (!($data->email = filter_var($data->email, FILTER_VALIDATE_EMAIL))) 
		{
			$feedback = new Feedback('error');
			$feedback->set_text('A valid email address is required.');
			$feedback->set_title('Error!');
			$this->use_feedback($feedback);
			return $this->_index();
		}
			
		if (Model_User::find_email($data->email)) 
		{
			if ($user = Model_User::authenticate($data->email, $data->password))
			{
				// cannot use this checkout if billing info stored
				// cannot use this checkout if recently updated billing 
				$billing = Model_Billing::find($user->id);
				$last_update = Model_User_Last_Billing_Update::find($user->id);
				if ($billing || ($last_update && !$last_update->allowed_to_update()))
					$user = null;
			}

			if (!$user)
			{				
				$feedback = new Feedback('error');
				$feedback->set_text('The email address is already in use.');
				$feedback->set_title('Error!');
				$this->use_feedback($feedback);
				return $this->_index();
			}
		}
		
		$data->use_remote_card = false;
		$billing_data = new stdClass();
		$braintree = new BrainTree_Process();
		$amount = $cart->total_with_discount();
		$cart->lock();

		$c_items = array_values($cart->items());
		$data->descriptor = $this->conf('transaction_descriptor');
		if (count($c_items) === 1 && $c_items[0]->item()->descriptor)
		     $descriptor_item_name = $c_items[0]->item()->descriptor;
		else $descriptor_item_name = 'Service';
		$data->descriptor['name'] = 
			sprintf($data->descriptor['name'], 
			$descriptor_item_name);
		
		if ($cart->total_with_discount() <= 0)
		{
			$data->use_remote_card = true;
			$result = new stdClass();
			$gateway = null;
		}
		else if (($result = $braintree->transaction_initial($data, $amount)))
		{			
			$gateway = Gateway::BRAINTREE;
		}
		else
		{
			$cart->unlock();
			$feedback = new Feedback('error');
			$feedback->add_text('Your order was denied. ');
			$feedback->add_text('Check the payment details are correct.');
			$feedback->set_title('Error!');
			$this->use_feedback($feedback);
			$messages = $braintree->messages();

			if (count($messages))
			{
				$feedback = new Feedback('warning');
				foreach ($messages as $message)
					$feedback->add_text($message, true);
				$this->use_feedback($feedback);
			}

			$bill_failure_data = new stdClass();
			$bill_failure_data->messages = $messages;
			$bill_failure_data->amount = $amount;
			$bill_failure_data->data = $this->remove_sensitive_data(clone $data);
			$bill_failure_data->cart = $cart->serialize();
			$bill_failure_data->type = Model_Bill_Failure::TYPE_ORDER;
			$bill_failure_data->raw_b64 = base64_encode(serialize($braintree->raw_result));
			$bill_failure = new Model_Bill_Failure();
			$bill_failure->remote_addr = $this->env['remote_addr'];
			$bill_failure->raw_data($bill_failure_data);
			$bill_failure->save();
			$bill_failure->notify_staff();
			
			if (isset($data->paypal_nonce))
				unset($data->paypal_nonce);

			if (isset($data->cc_nonce))
				unset($data->cc_nonce);
			
			return $this->_index();
		}
		
		if (!isset($user) || !$user)
		{
			$is_new_user = true;
			$user = Model_User::create();
			$user->email = $data->email;
			$user->is_enabled = 1;
			$user->first_name = $data->first_name;
			$user->last_name = $data->last_name;
			$user->remote_addr = $this->env['remote_addr'];
			$password = $data->password;
			// no password provided => generate one to avoid blank
			if (!$password) $password = Model_User::generate_password();
			$user->set_password($password);
		}
		else
		{
			$is_new_user = false;
		}
		
		$user->is_verified = 1;
		$user->save();

		// login the user
		Auth::login($user);
		
		if ($data->company_name && !$this->session->get('skip_create_newsroom')
			&& !Model_Newsroom::find('user_id', $user->id))
			// create company with given name for user
			$newsroom = Model_Newsroom::create($user, $data->company_name);

		if (!$data->use_remote_card)
		{
			// store remote billing data
			$billing_data = new stdClass();
			$billing_data->remote_customer_id = $result->remote_customer_id;
			$billing_data->remote_card_id = $result->remote_card_id;
			$billing_data->card_details = $result->card_details;
			$billing_data->is_virtual_card = $result->is_virtual_card;
			$billing_data->virtual_card_type = @$result->virtual_card_type;

			// save the last billing update 
			// so that we can detect users attempting
			// to use other peoples credit cards
			Model_User_Last_Billing_Update::update($user);
		}
		
		// store user billing
		$billing = new Model_Billing();
		$billing->user_id = $user->id;
		$billing->first_name = $data->first_name;
		$billing->last_name = $data->last_name;
		$billing->company_name = $data->company_name;
		$billing->street_address = $data->street_address;
		$billing->extended_address = null;
		$billing->locality = $data->locality;
		$billing->region = $data->region;
		$billing->country_id = $data->country_id;
		$billing->zip = $data->zip;
		$billing->phone = $data->phone;
		$billing->gateway = $gateway;
		$billing->raw_data($billing_data);
		$billing->save();
		
		if ($is_new_user)
		{
			// schedule register event for next run
			$event = new Scheduled_Iella_Event();
			$event->data->user = $user->values();
			if (isset($newsroom))
			     $event->data->newsroom = $newsroom->values();
			else $event->data->newsroom = null;
			$event->schedule('user_register');
		}
		
		// set of component items (detached from order)
		$component_set = new Model_Component_set();
		$component_set->user_id = $user->id;
		$component_set->is_legacy = 0;
		$component_set->coupon_id = @$cart->coupon()->id;
		$component_set->save();
		
		// basic order record
		$order = Model_Order::create();
		$order->user_id = $user->id;
		$order->component_set_id = $component_set->id;
		$order->remote_addr = $this->env['remote_addr'];
		$order->price_total = $amount;
		$order->save();
		
		// store the transaction data 
		$transaction = Model_Transaction::create();
		$transaction->gateway = $gateway;
		if (isset($result->transaction))
			$transaction->raw_data($result->transaction);
		$transaction->virtual_cart = $cart->serialize();
		$transaction->order_id = $order->id;
		$transaction->user_id = $user->id;
		$transaction->price = $amount;
		$transaction->save();

		// store transaction logs
		Model_Transaction_Item_Log::create($transaction, $cart);

		// schedule order event for next run
		$event = new Scheduled_Iella_Event();
		$event->data->user = $user->values();
		$event->data->order = $order->values();
		$event->data->billing = $billing->values();
		$event->data->component_set = $component_set->values();
		$event->data->transaction = $transaction->values();
		$event->schedule('user_order');
		
		// invoke order events for items
		foreach ($cart->items() as $cart_item)
		{			
			// run the item's order event to activate
			$iella_event = new Iella_Event();
			$iella_event->data->cart_item = $cart_item;
			$iella_event->data->user = $user;
			$iella_event->data->component_set = $component_set;
			$iella_event->data->transaction = $transaction;
			$iella_event->emit($cart_item->order_event);
			
			// schedule user_order_item event for next run
			$event = new Scheduled_Iella_Event();
			$event->data->cart_item = $cart_item;
			$event->data->user = $user;
			$event->data->transaction = $transaction;
			$event->schedule('user_order_item');
		}
		
		$this->vd->user = $user;
		$this->vd->cart = $cart;
		$this->vd->transaction = $transaction;
		$this->vd->order = $order;

		// record the events within KM
		$kmec = new KissMetrics_Event_Library($user);
		$kmec->event_billed($transaction);
		if ($is_new_user) $kmec->event_signed_up();
		$kmec->event_signed_in();
		
		// record the order in the affiliate program
		$affiliate = new IDevAffiliate_Process();
		$affiliate->sale($transaction, $user);
		
		// send email receipt
		// * assumes View_Data set
		// with appropriate objects
		$this->send_receipt($user);
		
		// thank you messages
		$feedback = new Feedback('success');
		$feedback->set_title('Thanks!');
		$feedback->set_text('Your order has been confirmed.');
		$this->add_feedback($feedback);
		
		// custom callback url?
		$this->vd->callback = $cart->callback($transaction);
		
		// thank you page
		$this->render_website('website/order-thanks');
		
		$cart->reset();
		$cart->save();
		$cart->unlock();
	}
	
}
