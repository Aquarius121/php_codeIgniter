<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/order');

class Order_Controller extends Manage_Base {

	use Order_Trait;
	
	public $title = 'Order';
	protected $ssl_required = true;
	protected $order_url_prefix = 'manage/order';
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->order_url_prefix =
			$this->order_url_prefix;
	}
	
	public function index()
	{
		$this->ssl_required_post = false;
		$this->_check_ssl_status();

		$this->set_cc_suffix();
		$this->load_countries();
		$this->vd->cart = Cart::instance();
		$this->vd->cart->remove_zeros();
		$this->vd->renewal_costs = 
			$this->vd->cart->renewal_costs();
		$user = Auth::user();

		// bill failures? blockable?
		if (!Auth::is_admin_online() && 
			(Model_Bill_Failure::has_bill_block_user($user) ||
			 Model_Bill_Failure::has_bill_block_addr($this->env['remote_addr'])))
			return $this->bill_blocked_error();

		$last_update = Model_User_Last_Billing_Update::find($user->id);
		if ($last_update && !$last_update->allowed_to_update() && !Auth::is_admin_online())
			$this->vd->update_blocked = true;
		
		$billing = Model_Billing::find($user->id);
		if (!$billing) $billing = new stdClass();
		
		if (!isset($this->vd->data))
		{
			$this->vd->data = $billing;
			if (empty($billing->first_name))
				$billing->first_name = $user->first_name;
			if (empty($billing->last_name))
				$billing->last_name = $user->last_name;
			if (empty($billing->company_name) && !$this->is_common_host)
				$billing->company_name = $this->newsroom->company_name;
		}
		
		$this->vd->data->has_remote_card = false;
		if ($billing instanceof Model_Billing 
		    && $raw_data = $billing->raw_data())
		{
			$this->vd->data->raw_data = $raw_data;
			$this->vd->data->has_remote_card = 
				!empty($raw_data->remote_card_id);
			if (!$this->input->post())
				$this->vd->data->use_remote_card = 
					$this->vd->data->has_remote_card;

			if ($this->vd->data->has_remote_card)
			{
				// check the payment method still exists before suggesting it be used
				if (!(new Braintree_Process())->find_payment_method($raw_data->remote_card_id))
				{
					$raw_data->remote_card_id = null;
					$this->vd->data->use_remote_card = false;
					$this->vd->data->has_remote_card = false;
				}
			}
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/order/index');
		$this->load->view('manage/footer');
	}
	
	public function checkout()
	{
		$this->vd->disable_menu = true;
		$this->index();
	}
	
	public function submit()
	{
		$this->ssl_required_post = true;
		$this->_check_ssl_status();
		
		if (!$this->input->post())
			$this->redirect('manage/order');
		
		$data = new stdClass();
		$data->use_remote_card = false;
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
			return $this->index();
		}
		
		if ($cart->is_locked()) 
		{
			$feedback = new Feedback('error');
			$feedback->set_text('Please contact support.');
			$feedback->set_title('Error!');
			$this->use_feedback($feedback);
			return $this->index();
		}
		
		$vcart = Virtual_Cart::create_from_post_data($post);

		if (!$cart->is_equal_to($vcart))
		{
			$feedback = new Feedback('info');
			$feedback->set_text('Your cart has been modified.');
			$feedback->set_title('Alert!');
			$this->use_feedback($feedback);
			return $this->index();
		}	
		
		$user = Auth::user();
		$data->email = $user->email;
		$braintree = new BrainTree_Process();
		$amount = $cart->total_with_discount();
		$billing = Model_Billing::find($user->id);
		if ($billing)
		     $billing_data = $billing->raw_data();
		else $billing_data = new stdClass();

		// bill failures? blockable?
		if (!Auth::is_admin_online() && 
			(Model_Bill_Failure::has_bill_block_user($user) ||
			 Model_Bill_Failure::has_bill_block_addr($this->env['remote_addr'])))
			return $this->bill_blocked_error();

		$cart->lock();
		
		// combine existing billing data
		foreach ($billing_data as $k => $v)
			if (!isset($data->{$k}))
				$data->{$k} = $v;

		// force use remote card if recently updated
		$last_update = Model_User_Last_Billing_Update::find($user->id);
		if ($last_update && !$last_update->allowed_to_update() && !Auth::is_admin_online())
			$data->use_remote_card = true;

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
			$gateway = null;
			$data->use_remote_card = true;
			$result = new stdClass();
		}
		else if ($data->use_remote_card && isset($billing_data->remote_card_id))
		{
			// use the same card but just update customer
			// details like name and billing address
			$result = $braintree->transaction_update($data, $amount, true);
			$gateway = Gateway::BRAINTREE;
		}
		else if (!empty($billing_data->remote_customer_id))
		{
			if (!empty($data->cc_number) || 
				 !empty($data->paypal_nonce) ||
				 !empty($data->cc_nonce))
			     // use a different card and update details
			     $result = $braintree->transaction_update($data, $amount, false);
			else $result = false;		
			$gateway = Gateway::BRAINTREE;	
		}
		else
		{
			// create a new braintree customer
			$result = $braintree->transaction_initial($data, $amount);
			$gateway = Gateway::BRAINTREE;
		}
		
		if (!$result)
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
			$bill_failure->raw_data($bill_failure_data);
			$bill_failure->remote_addr = $this->env['remote_addr'];
			$bill_failure->user_id = $user->id;
			$bill_failure->is_safe = (int) Auth::is_admin_online();
			$bill_failure->save();
			$bill_failure->notify_staff();
			
			if (isset($data->paypal_nonce))
				unset($data->paypal_nonce);

			if (isset($data->cc_nonce))
				unset($data->cc_nonce);

			return $this->index();
		}
		
		if (!$data->use_remote_card)
		{
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
		
		if (!$billing)
		{
			$billing = new Model_Billing();
			$billing->user_id = $user->id;
		}
		
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
		$order_raw_data = new stdClass();
		if (!empty($data->client_name))
			$order_raw_data->client_name = $data->client_name;
		$order->raw_data($order_raw_data);
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
		
		if (Auth::is_admin_mode())
		{
			$moc = new Model_Offline_Conversion();
			$moc->user_id = $user->id;
			$moc->transaction_id = $transaction->id;
			$moc->date_created = Date::$now;
			$moc->is_converted = 0;
			$moc->save();
		}
		else
		{
			// add order tracking feedback for thanks page
			$feedback = new Feedback_View('partials/track-order');
			$this->add_feedback($feedback);
		}

		// record the event within KM
		$kmec = new KissMetrics_Event_Library($user);
		$kmec->event_billed($transaction);
		
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
		
		// should we callback to another url?
		$callback = $cart->callback($transaction);
		
		$cart->reset();
		$cart->save();
		$cart->unlock();
		
		// custom callback url => redirect to that
		if ($callback) $this->redirect($callback);
		
		// redirect to view order details
		$url = "manage/account/order/view/thanks/{$order->id}";
		$this->redirect($url);
	}
	
}

?>
