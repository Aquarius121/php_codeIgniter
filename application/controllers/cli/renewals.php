<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_shared_fnc('shared/order');

class Renewals_Controller extends CLI_Base {
	
	use Order_Trait;

	// default period to use for
	// no item or no period
	const DEFAULT_PERIOD = Renewal::DEFAULT_PERIOD;
	
	// number of hours before due
	// that we attempt transaction
	const AUTO_RENEW_PRE_HOURS = Renewal::AUTO_RENEW_PRE_HOURS;
	
	// number of attempts to make transaction
	const AUTO_RENEW_ATTEMPTS = Renewal::AUTO_RENEW_ATTEMPTS;

	// maximum amount of time a renewal can be suspended
	const MAX_SUSPENSION_PERIOD = Renewal::MAX_SUSPENSION_PERIOD;

	public function index()
	{
		// allow at most 1 process
		if ($this->process_count() > 1)
			return;

		try 
		{
			$this->extend_auto_renew_enabled();
			$this->extend_renewable();
			$this->deactivate_old_plans();
		}
		catch (Exception $e)
		{
			$alert = new Critical_Alert($e);
			$alert->send();
		}
	}
	
	protected function extend_renewable()
	{
		while (true)
		{
			$dt_5m = Date::minutes(5)->format(Date::FORMAT_MYSQL);
			$sql = "SELECT * FROM co_component_item ci
				WHERE is_renewable = 1
				  AND date_expires < date_termination
				  AND date_expires < ?
				LIMIT 1";
				  
			$db_result = $this->db->query($sql, array($dt_5m));
			$c_item = Model_Component_Item::from_db($db_result);
			if (!$c_item) break;
			
			if ($c_item->period)
			     $this->extend_renewable_period($c_item, $c_item->period);
			else $this->extend_renewable_period($c_item, static::DEFAULT_PERIOD);
			$c_item->trigger();
		}
	}
	
	protected function extend_renewable_period($c_item, $period)
	{
		$dt_interval = Date::interval(0, 0, $period, 0, 0, 0);
		$dt_termination = Date::utc($c_item->date_termination);
		$dt_expires = Date::utc($c_item->date_expires);
		// if the expires date is not within 
		// an hour of the current time
		// then shift forward to compensate
		// * implies billing issue shifted date_termination
		$dt_now_diff_1h = Date::hours(-1);
		if ($dt_expires < $dt_now_diff_1h)
			$dt_expires = Date::utc();
		$dt_expires->add($dt_interval);
		if ($dt_termination < $dt_expires)
			$dt_expires = $dt_termination;
		$c_item->date_expires = $dt_expires->format(Date::FORMAT_MYSQL);
		$c_item->save();
	}
	
	protected function extend_auto_renew_enabled()
	{
		while (sleep(1) === 0)
		{
			$dt_ar_hrs = Date::hours(static::AUTO_RENEW_PRE_HOURS);
			$hold_hours = $this->conf('renewal_hold_hours');
			$dt_hold_interval = Date::interval(0, 0, 0, $hold_hours);
			
			$dt_ar_hrs_hold_1 = clone $dt_ar_hrs;
			$dt_ar_hrs_hold_1->sub($dt_hold_interval);
			$dt_ar_hrs_hold_2 = clone $dt_ar_hrs_hold_1;
			$dt_ar_hrs_hold_2->sub($dt_hold_interval);
			
			$dt_ar_hrs = $dt_ar_hrs->format(Date::FORMAT_MYSQL);
			$dt_ar_hrs_hold_1 = $dt_ar_hrs_hold_1->format(Date::FORMAT_MYSQL);
			$dt_ar_hrs_hold_2 = $dt_ar_hrs_hold_2->format(Date::FORMAT_MYSQL);
			
			// find a component that needs to be renewed
			$sql = "SELECT cs.* FROM co_component_item ci
				INNER JOIN co_component_set cs ON cs.id = ci.component_set_id
				WHERE ci.is_auto_renew_enabled = 1
				  AND ci.is_suspended = 0
				  AND cs.is_legacy = 0
				  AND ((ci.date_termination < ? AND ci.is_on_hold = 0)
				    OR (ci.date_termination < ? AND ci.is_on_hold = 1)
				    OR (ci.date_termination < ? AND ci.is_on_hold = 2))
				LIMIT 1";
				  
			$params = array($dt_ar_hrs, $dt_ar_hrs_hold_1, $dt_ar_hrs_hold_2);
			$db_result = $this->db->query($sql, $params);
			if (!$cs = Model_Component_Set::from_db($db_result)) break;
			$billing = Model_Billing::find($cs->user_id);
			
			// the default billing option is assumed to be braintree
			// when no actual billing information is available
			// * this will trigger hold_braintree() for the items
			if (!$billing || $billing->gateway === Model_Transaction::GATEWAY_BRAINTREE)
			{
				$this->extend_auto_renew_enabled_braintree($cs, $billing);
				continue;
			}
		}
	}
	
	protected function extend_auto_renew_enabled_braintree($cs, $billing)
	{
		$dt_ar_hrs = Date::hours(static::AUTO_RENEW_PRE_HOURS);
		$hold_hours = $this->conf('renewal_hold_hours');
		$dt_hold_interval = Date::interval(0, 0, 0, $hold_hours);
		
		$dt_ar_hrs_hold_1 = clone $dt_ar_hrs;
		$dt_ar_hrs_hold_1->sub($dt_hold_interval);
		$dt_ar_hrs_hold_2 = clone $dt_ar_hrs_hold_1;
		$dt_ar_hrs_hold_2->sub($dt_hold_interval);
		
		$dt_ar_hrs = $dt_ar_hrs->format(Date::FORMAT_MYSQL);
		$dt_ar_hrs_hold_1 = $dt_ar_hrs_hold_1->format(Date::FORMAT_MYSQL);
		$dt_ar_hrs_hold_2 = $dt_ar_hrs_hold_2->format(Date::FORMAT_MYSQL);
		
		// find all components that need to be renewed 
		// that exist within the specified set
		$sql = "SELECT ci.* FROM co_component_item ci
			WHERE ci.is_auto_renew_enabled = 1
			  AND ci.component_set_id = ?
			  AND ci.is_suspended = 0
			  AND ((ci.date_termination < ? AND ci.is_on_hold = 0)
			    OR (ci.date_termination < ? AND ci.is_on_hold = 1)
			    OR (ci.date_termination < ? AND ci.is_on_hold = 2))";
			
		$params = array($cs->id, $dt_ar_hrs, $dt_ar_hrs_hold_1, $dt_ar_hrs_hold_2);
		$db_result = $this->db->query($sql, $params);
		$c_items = Model_Component_Item::from_db_all($db_result);
		$coupon = Model_Coupon::find($cs->coupon_id);
		$user = Model_User::find($cs->user_id);
		if (!$user) throw new Exception();
		
		$order_id = null;
		$order = Model_Order::find_component_set($cs);
		if ($order) $order_id = $order->id;
		
		$v_cart = Virtual_Cart::instance();
		$v_cart->allow_expired_coupon();
		$v_cart->set_coupon($coupon);
		$v_cart->remove_one_time_coupon();
		$v_cart->allow_disabled_items();
		
		foreach ($c_items as $c_item)
		{
			$item = Model_Item::find($c_item->item_id);
			$v_cart->add($item, $c_item->quantity, $c_item->price);

			// suspend the renewal during the process
			// so that if something breaks the renewals
			// won't go crazy and keep billing
			$c_item->is_suspended = 1;
			$c_item->save();
		}
		
		if (!$billing) return $this->hold_braintree($c_items, $v_cart, $user);
		$billing_data = $billing->raw_data();
		
		if (empty($billing_data->remote_card_id))
			return $this->hold_braintree($c_items, $v_cart, $user);
		if (empty($billing_data->remote_customer_id))
			return $this->hold_braintree($c_items, $v_cart, $user);
		
		$r_customer_id = $billing_data->remote_customer_id;
		$r_card_id = $billing_data->remote_card_id;
		
		// mock result as OK
		$result = new stdClass();
		
		// only use braintree if non-zero cost
		if (($amount = $v_cart->total_with_discount()) > 0)
		{
			$transaction_data = clone $billing_data;
			$transaction_data->descriptor = $this->conf('transaction_descriptor');

			if (count($c_items) === 1 && $c_items[0]->item()->descriptor)
			     $descriptor_item_name = $c_items[0]->item()->descriptor;
			else $descriptor_item_name = 'Custom';
			$transaction_data->descriptor['name'] = 
				sprintf($transaction_data->descriptor['name'], 
				$descriptor_item_name);

			$braintree = new BrainTree_Process();
			$result = $braintree->transaction_vault($transaction_data, $amount, true);
		}
		
		if (!$result)
		{
			$alert_data = new stdClass();
			$alert_data->v_cart = $v_cart;
			$alert_data->c_items = $c_items;
			$alert_data->billing = $billing_data;
			$alert_data->amount = $amount;
			$alert_data->result = $braintree->raw_result;
			$critical_alert = new Critical_Alert($alert_data);
			$critical_alert->log();

			$messages = $braintree->messages();
			$bill_failure_data = new stdClass();
			$bill_failure_data->messages = $messages;
			$bill_failure_data->amount = $amount;
			$bill_failure_data->data = $transaction_data;
			$bill_failure_data->cart = $v_cart->serialize();
			$bill_failure_data->type = Model_Bill_Failure::TYPE_RENEWAL;
			$bill_failure_data->raw_b64 = base64_encode(serialize($braintree->raw_result));

			$bill_failure = new Model_Bill_Failure();
			$bill_failure->raw_data($bill_failure_data);
			$bill_failure->user_id = $user->id;
			$bill_failure->is_safe = 1;
			$bill_failure->save();
			$bill_failure->notify_staff();
			
			return $this->hold_braintree($c_items, $v_cart, $user);
		}
		
		// store the transaction data 
		$transaction = Model_Transaction::create();
		$transaction->gateway = Model_Transaction::GATEWAY_BRAINTREE;
		if (isset($result->transaction))
			$transaction->raw_data($result->transaction);
		$transaction->virtual_cart = $v_cart->serialize();
		$transaction->order_id = $order_id;
		$transaction->user_id = $cs->user_id;
		$transaction->is_renewal = 1;
		$transaction->price = $amount;
		$transaction->save();

		// store transaction logs
		Model_Transaction_Item_Log::create($transaction, $v_cart);
		
		// extend the termination date
		foreach ($c_items as $c_item)
		{
			if ($c_item->period)
			     $period = $c_item->period;
			else $period = static::DEFAULT_PERIOD;
			
			if ($c_item->period_repeat_count)
			     $period_repeat_count = $c_item->period_repeat_count;
			else $period_repeat_count = 1;
			
			$extend_days = $period * $period_repeat_count;
			$dt_extend = Date::interval(0, 0, $extend_days);
			$dt_termination = Date::utc($c_item->date_termination);
			$date_now = Date::utc();
			if ($dt_termination < $date_now)
				$dt_termination = $date_now;
			$dt_termination->add($dt_extend);
						
			// undo renewal suspension
			$c_item->is_suspended = 0;

			$c_item->is_on_hold = 0;
			$c_item->date_termination = $dt_termination->format(Date::FORMAT_MYSQL);
			$c_item->save();
		}
		
		// record the event within KM
		$kmec = new KissMetrics_Event_Library($user);
		$kmec->event_billed($transaction);
		
		// record the order in the affiliate program
		$affiliate = new IDevAffiliate_Process();
		$affiliate->sale($transaction, $user);

		// send email receipt
		// $this->vd->cart = $v_cart;
		// $this->vd->transaction = $transaction;
		// $this->vd->order = $order;
		// $this->send_receipt($user, true);
	}
	
	protected function hold_braintree($c_items, $v_cart, $user)
	{
		foreach ($c_items as $c_item)
		{
			$c_item->is_on_hold++;
			$c_item->save();
			
			if ($c_item->is_on_hold == static::AUTO_RENEW_ATTEMPTS)
			{
				$c_item->cancel();
				
				$raw_data = new stdClass();
				$raw_data->reason = 'braintree renewal failed';
				$m_cancel = Model_Cancellation::create();
				$m_cancel->component_item_id = $c_item->id;
				$m_cancel->raw_data($raw_data);
				$m_cancel->save();
				
				$item = Model_Item::find($c_item->item_id);
				if ($item->type == Model_Item::TYPE_PLAN)
				{
					// record the events within KM
					$kmec = new KissMetrics_Event_Library($user);
					$kmec->event_cancelled();
				
					// schedule cancel task event for next run
					$event = new Scheduled_Iella_Event();
					$event->data->cancellation = $m_cancel->values();
					$event->data->user = $user->values();
					$event->data->item = $item->values();
					$event->schedule('cancellation_task');
				}
			}
		}
		
		$en = new Email_Notification();
		$en_subject = 'Account Renewal Failure';
		$en->set_from_email('patrick@newswire.com');
		$en->set_from_name('Patrick Santiago');
		$en->set_content_view('renewal_hold_braintree');
		$en->set_container_view(null);
		$en->set_data('v_cart', $v_cart);
		$en->send($user, $en_subject, true);
	}
	
	protected function deactivate_old_plans()
	{
		$active_plans = Model_User_Plan::find_all_expired();
		foreach ($active_plans as $active_plan)
			$active_plan->deactivate();
	}

	public function cancel_over_suspended()
	{
		// find a renewal that has been suspended
		// that terminated more than 60* days ago
		// * value from MAX_SUSPENSION_PERIOD

		$date_termination = escape_and_quote(Date::days(-static::MAX_SUSPENSION_PERIOD));
		$sql = "UPDATE co_component_item ci
			INNER JOIN co_component_set cs ON cs.id = ci.component_set_id
			SET ci.is_auto_renew_enabled = 0
			WHERE ci.is_auto_renew_enabled = 1
			  AND ci.is_suspended = 1
			  AND cs.is_legacy = 0
			  AND ci.date_termination < {$date_termination}";
			  
		$this->db->query($sql);
	}
	
}

?>