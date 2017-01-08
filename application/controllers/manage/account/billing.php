<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/order');

class Billing_Controller extends Manage_Base {

	use Order_Trait;

	public $title = 'Billing Information';
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
		$user = Auth::user();
		$billing = Model_Billing::find($user->id);
		$this->vd->cart = Cart::instance();

		// bill failures? blockable?
		if (!Auth::is_admin_online() && 
			(Model_Bill_Failure::has_bill_block_user($user) ||
			 Model_Bill_Failure::has_bill_block_addr($this->env['remote_addr'])))
			return $this->bill_blocked_error();

		$last_update = Model_User_Last_Billing_Update::find($user->id);
		if ($last_update && !$last_update->allowed_to_update() && !Auth::is_admin_online())
			$this->vd->update_blocked = true;
	
		$this->set_cc_suffix();
		$this->load_countries();
		if (!isset($this->vd->data))
			$this->vd->data = $billing;
		
		if ($billing)
		{
			$raw_data = $billing->raw_data();
			$this->vd->data->raw_data = $raw_data;
			$this->vd->data->has_remote_card = 
				!empty($raw_data->remote_card_id);

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
		else
		{
			$this->vd->data = new stdClass();
			$this->vd->data->raw_data = new stdClass();
			$this->vd->data->has_remote_card = false;
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/account/billing');
		$this->load->view('manage/footer');
	}
	
	public function submit()
	{
		$data = new stdClass();
		$post = $this->input->post();
		foreach ($post as $k => $v)
			$data->{$k} = $v;
		$this->vd->data = $data;
		$this->remove_cc_suffix_from_data($data);
		
		// don't use remote card if new cc provided
		$is_new_card = !empty($data->cc_number) || !empty($data->cc_nonce);
		$is_new_paypal = !empty($data->paypal_nonce);
		$data->use_remote_card = !$is_new_card && !$is_new_paypal;
		
		$user = Auth::user();
		$data->email = $user->email;
		$braintree = new BrainTree_Process();
		$billing = Model_Billing::find($user->id);
		// no billing information at all
		if (!$billing) $billing = $this->create_billing($data);
		$billing_data = $billing->raw_data();
		if (empty($billing_data->remote_customer_id))
			$billing_data = $this->create_billing($data)->raw_data();
		$status_success = false;

		// force use remote card if recently updated
		$last_update = Model_User_Last_Billing_Update::find($user->id);
		if ($last_update && !$last_update->allowed_to_update() && !Auth::is_admin_online())
			$data->use_remote_card = true;

		// bill failures? blockable?
		if (!Auth::is_admin_online() && 
			(Model_Bill_Failure::has_bill_block_user($user) ||
			 Model_Bill_Failure::has_bill_block_addr($this->env['remote_addr'])))
			return $this->bill_blocked_error();
		
		// use the existing card but only if it actually exists
		if ($data->use_remote_card && isset($billing_data->remote_card_id))
		{
			// use the same card but just update customer 
			// details like name and billing address
			$r_customer_id = $billing_data->remote_customer_id;
			$r_card_id = $billing_data->remote_card_id;
			if ($customer = $braintree->update_customer($r_customer_id, $data))
				$status_success = true;
			
			// update the billing address associated with card
			if (!$billing_data->is_virtual_card)
				$braintree->update_address_for_card($r_card_id, $data);
		}
		// create a new card for an existing braintree customer
		else if ($is_new_card && isset($billing_data->remote_customer_id))
		{
			// use a different card and update details
			$r_customer_id = $billing_data->remote_customer_id;
			$customer = $braintree->update_customer($r_customer_id, $data);
			$braintree->remove_cards_for_customer($customer, true);
			$result = $braintree->add_card($r_customer_id, $data);
			
			if ($result)
			{
				$billing_data->remote_card_id = $result->remote_card_id;
				$billing_data->card_details = $result->card_details;
				$billing_data->is_virtual_card = $result->is_virtual_card;
				$billing_data->virtual_card_type = @$result->virtual_card_type;
				$status_success = true;

				// save the last billing update 
				// so that we can detect users attempting
				// to use other peoples credit cards
				Model_User_Last_Billing_Update::update($user);

				// reset on_hold status for renewals
				Model_Component_Item::reset_on_hold($user);
			}
			else
			{
				if (count(($messages = $braintree->messages())))
				{
					$feedback = new Feedback('warning');
					foreach ($messages as $message)
						$feedback->add_text($message, true);
					$this->use_feedback($feedback);
				}

				$bill_failure_data = new stdClass();
				$bill_failure_data->messages = $messages;
				$bill_failure_data->amount = 0;
				$bill_failure_data->data = $this->remove_sensitive_data(clone $data);
				$bill_failure_data->cart = null;
				$bill_failure_data->type = Model_Bill_Failure::TYPE_BILLING;
				$bill_failure_data->raw_b64 = base64_encode(serialize($braintree->raw_result));
				$bill_failure = new Model_Bill_Failure();
				$bill_failure->raw_data($bill_failure_data);
				$bill_failure->remote_addr = $this->env['remote_addr'];
				$bill_failure->user_id = $user->id;
				$bill_failure->is_safe = (int) Auth::is_admin_online();
				$bill_failure->save();
				$bill_failure->notify_staff();
			}
		}
		// create a new card for an existing braintree customer
		else if ($is_new_paypal && isset($billing_data->remote_customer_id))
		{
			// use a different paypal and update details
			$r_customer_id = $billing_data->remote_customer_id;
			$customer = $braintree->update_customer($r_customer_id, $data);
			$braintree->remove_cards_for_customer($customer, true);
			$result = $braintree->add_card($r_customer_id, $data);
			
			if ($result)
			{
				$billing_data->remote_card_id = $result->remote_card_id;
				$billing_data->card_details = $result->card_details;
				$billing_data->is_virtual_card = $result->is_virtual_card;
				$billing_data->virtual_card_type = @$result->virtual_card_type;
				$status_success = true;

				// save the last billing update 
				// so that we can detect users attempting
				// to use other peoples credit cards
				Model_User_Last_Billing_Update::update($user);

				// reset on_hold status for renewals
				Model_Component_Item::reset_on_hold($user);
			}
			else
			{
				if (count(($messages = $braintree->messages())))
				{
					$feedback = new Feedback('warning');
					foreach ($messages as $message)
						$feedback->add_text($message, true);
					$this->use_feedback($feedback);
				}

				$bill_failure_data = new stdClass();
				$bill_failure_data->messages = $messages;
				$bill_failure_data->amount = 0;
				$bill_failure_data->data = $this->remove_sensitive_data(clone $data);
				$bill_failure_data->cart = null;
				$bill_failure_data->type = Model_Bill_Failure::TYPE_BILLING;
				$bill_failure_data->raw_b64 = base64_encode(serialize($braintree->raw_result));
				$bill_failure = new Model_Bill_Failure();
				$bill_failure->raw_data($bill_failure_data);
				$bill_failure->remote_addr = $this->env['remote_addr'];
				$bill_failure->user_id = $user->id;
				$bill_failure->is_safe = (int) Auth::is_admin_online();
				$bill_failure->save();
				$bill_failure->notify_staff();
			}
		}
		else
		{
			// just a save to our database
			$status_success = true;
		}

		unset($data->paypal_nonce);
		unset($data->cc_nonce);
		
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
		$billing->gateway = Model_Transaction::GATEWAY_BRAINTREE;
		$billing->raw_data = json_encode($billing_data);
		$billing->save();
		
		if ($status_success)
		{
			// schedule update event for next run
			$event = new Scheduled_Iella_Event();
			$event->data->user = $user->values();
			$event->data->billing = $billing->values();
			$event->schedule('user_update_billing');
			
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Your details have been saved.');
			$this->add_feedback($feedback);			
			$this->redirect('manage/account/billing');
		}
		else
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Error!');
			$feedback->set_text('Your details have not been saved.');
			$this->use_feedback($feedback);
			return $this->index();
		}
	}
	
	public function remove()
	{
		if ($this->input->post('confirm'))
		{
			$billing = Model_Billing::find(Auth::user()->id);
			$billing_data = json_decode($billing->raw_data);
			
			if (isset($billing_data->remote_customer_id)) 
			{
				$braintree = new Braintree_Process();
				$braintree->remove_customer($billing_data->remote_customer_id);
			}
			
			$billing->delete();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Billing information removed.');
			$this->add_feedback($feedback);
			$this->redirect('manage/account/billing');
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/account/billing-remove');
		$this->load->view('manage/footer');
	}
	
	protected function create_billing($data)
	{
		$billing = new Model_Billing();
		$billing->user_id = Auth::user()->id;
		$billing->gateway = Model_Transaction::GATEWAY_BRAINTREE;
		$billing->raw_data(new stdClass());
		
		$braintree = new Braintree_Process();
		$add_customer_result = $braintree->add_customer($data);
		
		if (!$add_customer_result) 
		{
			$feedback = new Feedback('warning');
			$feedback->set_text($braintree->raw_result->message);
			$this->use_feedback($feedback);			
			return $billing;
		}
		
		$remote_customer_id = $add_customer_result->remote_customer_id;
		$billing_data = new stdClass();
		$billing_data->remote_customer_id = $remote_customer_id;
		$billing->raw_data($billing_data);
		return $billing;
	}
	
}

?>
