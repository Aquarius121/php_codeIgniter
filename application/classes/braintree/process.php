<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class BrainTree_Process {
	
	public $raw_result;
	protected $config;
	
	public function __construct()
	{
		// configuration is hard coded because
		// braintree doesn't allow multiple 
		// concurrent configurations so its safer
		// to always load the same config here
		$config = get_instance()->conf('braintree');
		Braintree_Configuration::environment($config['environment']);
		Braintree_Configuration::merchantId($config['merchant_id']);
		Braintree_Configuration::publicKey($config['public_key']);
		Braintree_Configuration::privateKey($config['private_key']);
		$this->config = $config;
	}
	
	public function generate_client_token($remote_customer_id = null)
	{
		return Braintree_ClientToken::generate(array(
			'customerId' => $remote_customer_id,
		));
	}
	
	public function find_customer($remote_customer_id)
	{
		try { $result = Braintree_Customer::find($remote_customer_id); }
		catch (Braintree_Exception_NotFound $e) { return false; }
		$this->raw_result = $result;
		return $result;
	}
	
	public function find_card($remote_card_id)
	{
		try { $result = Braintree_CreditCard::find($remote_card_id); }
		catch (Braintree_Exception_NotFound $e) { return false; }
		$this->raw_result = $result;
		return $result;
	}

	public function find_payment_method($remote_card_id)
	{
		try { $result = Braintree_PaymentMethod::find($remote_card_id); }
		catch (Braintree_Exception_NotFound $e) { return false; }
		$this->raw_result = $result;
		return $result;
	}
	
	public function find_address($remote_customer_id, $remote_address_id)
	{
		try { $result = Braintree_Address::find($remote_customer_id, $remote_address_id); }
		catch (Braintree_Exception_NotFound $e) { return false; }
		$this->raw_result = $result;
		return $result;
	}
	
	public function add_customer($data)
	{
		$remote_customer_id = UUID::create();
		$result = Braintree_Customer::create(array(
			'id' => $remote_customer_id,
			'firstName' => (string) $data->first_name,
			'lastName' => (string) $data->last_name,
			'company' => (string) $data->company_name,
			'email' => (string) $data->email,
			'phone' => (string) $data->phone,
		));
		
		$this->raw_result = $result;
		if (!$result->success) return false;
		
		$res = new stdClass();
		$res->customer = $result->customer;
		$res->remote_customer_id = $result->customer->id;
		return $res;
	}
	
	public function update_customer($remote_customer_id, $data)
	{
		$result = Braintree_Customer::update(
			$remote_customer_id, array(
			'firstName' => (string) $data->first_name,
			'lastName' => (string) $data->last_name,
			'company' => (string) $data->company_name,
			'email' => (string) $data->email,
			'phone' => (string) $data->phone,
		));
		
		$this->raw_result = $result;
		if (!$result->success) return false;
		$customer = $result->customer;
		return $customer;
	}
	
	public function update_address($remote_customer_id, $remote_address_id, $data)
	{
		$result = Braintree_Address::update(
			$remote_customer_id, $remote_address_id, array(
			'firstName' => (string) $data->first_name,
			'lastName' => (string) $data->last_name,
			'company' => (string) $data->company_name,
			'streetAddress' => (string) $data->street_address,
			'extendedAddress' => (string) null,
			'locality' => (string) $data->locality,
			'region' => (string) $data->region,
			'countryName' => (string) @Model_Country::find($data->country_id)->name,
			'postalCode' => $data->country_id == Model_Country::ID_UNITED_STATES()
				? (string) $data->zip : (string) null,
		));
		
		$this->raw_result = $result;
		if (!$result->success) return false;
		$address = $result->address;
		return $address;
	}
	
	public function remove_customer($remote_customer_id)
	{
		$result = Braintree_Customer::delete($remote_customer_id);
		$this->raw_result = $result;
		if (!$result->success) return false;
		return true;
	}
	
	public function remove_card($remote_card_id)
	{
		// this will remove real cards and virtual cards
		$result = Braintree_PaymentMethod::delete($remote_card_id);
		$this->raw_result = $result;
		if (!$result->success) return false;
		return true;
	}
	
	public function remove_address($remote_customer_id, $remote_address_id)
	{
		$result = Braintree_Address::delete($remote_customer_id, $remote_address_id);
		$this->raw_result = $result;
		if (!$result->success) return false;
		return true;
	}
		
	// removes all existing cards for a customer
	// * $customer should be braintree customer object
	// * $remote_card_id the card that should not be removed
	public function remove_cards_for_customer($customer, 
		$remote_card_id = null, $remove_address = true)
	{
		// => customer is the customer id so find 
		if (!($customer instanceof Braintree_Customer))
		{
			$braintree = new static();
			$customer = $braintree->find_customer($customer);
		}
			
		if (!$customer) return false;
		
		// remove old cards as we store just 1
		// * these are the real credit/debit cards
		foreach ($customer->creditCards as $card)
		{
			// don't remove the card if it 
			// is the excluded $remote_card_id
			if ($card->token !== $remote_card_id)
			{
				$braintree = new static();
				$braintree->remove_card($card->token);
				
				if ($remove_address && isset($card->billingAddress->id))
				{
					$remote_customer_id = $customer->id;
					$remote_address_id = $card->billingAddress->id;
					$braintree->remove_address($remote_customer_id, 
						$remote_address_id);
				}
			}
		}
		
		// remove old cards as we store just 1
		// * these are the virtual cards (paypal)
		foreach ($customer->paypalAccounts as $card)
		{
			// don't remove the card if it 
			// is the excluded $remote_card_id
			if ($card->token !== $remote_card_id)
			{
				$braintree = new static();
				$braintree->remove_card($card->token);
			}
		}
	}
	
	// update the billing address associated with a card
	// * returns false when used with paypal
	public function update_address_for_card($card, $data)
	{
		// => card is the card id so find 
		if (!($card instanceof Braintree_CreditCard))
		{
			$braintree = new static();
			$card = $braintree->find_card($card);
		}
			
		if (!$card) return false;
		$remote_customer_id = $card->customerId;
		$remote_address_id = $card->billingAddress->id;
		if (!$remote_address_id) return false;
		return $this->update_address($remote_customer_id, $remote_address_id, $data);
	}
	
	public function add_card($remote_customer_id, $data, $make_default = true)
	{
		$res = new stdClass();
		$remote_card_id = UUID::create();

		$request_data = array();
		$request_data['customerId'] = $remote_customer_id;
		
		// credit or debit card (legacy)
		if (!empty($data->cc_number))
		{
			$data->cc_number = filter_var($data->cc_number, FILTER_SANITIZE_NUMBER_INT);
			$data->cc_cvc = filter_var($data->cc_cvc, FILTER_SANITIZE_NUMBER_INT);
			
			// trigger CVV provided but not correct
			if (!$data->cc_cvc) $data->cc_cvc = 999999999;
			
			$request_data['token'] = $remote_card_id;
			$request_data['expirationMonth'] = (string) $data->cc_expires_month;
			$request_data['expirationYear'] = (string) $data->cc_expires_year;
			$request_data['number'] = (string) $data->cc_number;
			$request_data['cvv'] = (string) $data->cc_cvc;
			$request_data['options'] = array(
				'makeDefault' => $make_default,
				'verifyCard' => true,
			);
			
			$request_data['billingAddress'] = array(
				'firstName' => (string) $data->first_name,
				'lastName' => (string) $data->last_name,
				'company' => (string) $data->company_name,
				'streetAddress' => (string) $data->street_address,
				'extendedAddress' => (string) null,
				'locality' => (string) $data->locality,
				'region' => (string) $data->region,
				'countryName' => (string) @Model_Country::find($data->country_id)->name,
				'postalCode' => $data->country_id == Model_Country::ID_UNITED_STATES()
					? (string) $data->zip : (string) null,
			);
			
			$result = Braintree_CreditCard::create($request_data);
			$this->raw_result = $result;
			if (!$result->success) return false;

			$res->remote_customer_id = $remote_customer_id;
			$res->remote_card_id = $result->creditCard->token;
			$res->card_details = $result->creditCard->serialize(true, 1);
			$res->virtual_card_type = null;
			$res->is_virtual_card = false;
			return $res;
		}

		// credit or debit card (nonce)
		else if (!empty($data->cc_nonce))
		{
			$request_data['token'] = $remote_card_id;			
			$request_data['paymentMethodNonce'] = $data->cc_nonce;
			$request_data['options'] = array(
				'makeDefault' => $make_default,
				'verifyCard' => true,
			);
			
			$request_data['billingAddress'] = array(
				'firstName' => (string) $data->first_name,
				'lastName' => (string) $data->last_name,
				'company' => (string) $data->company_name,
				'streetAddress' => (string) $data->street_address,
				'extendedAddress' => (string) null,
				'locality' => (string) $data->locality,
				'region' => (string) $data->region,
				'countryName' => (string) @Model_Country::find($data->country_id)->name,
				'postalCode' => $data->country_id == Model_Country::ID_UNITED_STATES()
					? (string) $data->zip : (string) null,
			);
			
			$result = Braintree_PaymentMethod::create($request_data);
			$this->raw_result = $result;
			if (!$result->success) return false;

			if (isset($result->paymentMethod->verifications[0]))
			{
				$verification_failed = false;
				$verification = $result->paymentMethod->verifications[0];

				// street address was checked and is valid 
				$streetAddressConfirmed = isset($verification['avsStreetAddressResponseCode'])
					&& $verification['avsStreetAddressResponseCode'] === 'M';

				// postal code was checked and is valid
				$postalCodeConfirmed = isset($verification['avsPostalCodeResponseCode'])
					&& $verification['avsPostalCodeResponseCode'] === 'M';

				// street address was checked
				$streetAddressChecked = isset($verification['avsStreetAddressResponseCode'])
					&& (    $verification['avsStreetAddressResponseCode'] === 'M'
						  || $verification['avsStreetAddressResponseCode'] === 'N');

				// postal code was checked
				$postalCodeChecked = isset($verification['avsPostalCodeResponseCode'])
					&& (    $verification['avsPostalCodeResponseCode'] === 'M'
						  || $verification['avsPostalCodeResponseCode'] === 'N');

				// CVV was checked and is valid
				$cvvConfirmed = isset($verification['cvvResponseCode'])
					&& $verification['cvvResponseCode'] === 'M';

				// CVV was checked (including not provided)
				$cvvCheckedOrNotProvided = isset($verification['cvvResponseCode'])
					&& (    $verification['cvvResponseCode'] === 'M'
						  || $verification['cvvResponseCode'] === 'N'
						  || $verification['cvvResponseCode'] === 'I');

				// postal code or street address
				// must be valid when checked
				if ($postalCodeChecked 
					&& !$postalCodeConfirmed
					&& !$streetAddressConfirmed)
				{
					$verification_failed = true;
					if (!isset($result->message))
						$result->message = null;
					$result->message = implode(array(
						'Postal code is not correct for this card. ',
						$result->message,
					));
				}

				// CVV was checked and is not valid
				// or no CVV was provided
				if ($cvvCheckedOrNotProvided && !$cvvConfirmed)
				{
					$verification_failed = true;
					if (!isset($result->message))
						$result->message = null;
					$result->message = implode(array(
						'CVV is missing or incorrect for this card. ',
						$result->message,
					));
				}

				if ($verification_failed) 
				{
					$remote_card_id = $result->paymentMethod->token;
					(new static())->remove_card($remote_card_id);
					return false;
				}
			}

			$res->remote_customer_id = $remote_customer_id;
			$res->remote_card_id = $result->paymentMethod->token;
			$res->card_details = $result->paymentMethod->serialize(true, 1);
			$res->virtual_card_type = null;
			$res->is_virtual_card = false;
			return $res;
		}
		
		// paypal agreement
		else if (!empty($data->paypal_nonce))
		{
			$request_data['token'] = $remote_card_id;
			$request_data['paymentMethodNonce'] = $data->paypal_nonce;
			$request_data['options'] = array(
				'makeDefault' => $make_default,
			);
			
			$result = Braintree_PaymentMethod::create($request_data);
			$this->raw_result = $result;
			if (!$result->success) return false;
			
			$res->remote_customer_id = $remote_customer_id;
			$res->card_details = $result->paymentMethod->serialize(true, 1);
			$res->remote_card_id = $result->paymentMethod->token;
			$res->virtual_card_type = Model_Billing::VIRTUAL_CARD_PAYPAL;
			$res->is_virtual_card = true;	
			return $res;
		}

		else
		{
			return false;
		}
	}
	
	public function settle_transaction($transaction_id)
	{
		$result = Braintree_Transaction::submitForSettlement($transaction_id);
		$this->raw_result = $result;
		return $result->success;
	}
	
	public function transaction_vault($data, $amount, $is_renewal = false)
	{
		$remote_customer_id = $data->remote_customer_id;
		$remote_card_id = $data->remote_card_id;
		$is_virtual_card = $data->is_virtual_card;
		
		$request_data = array(
			'amount' => static::format_amount($amount),
			'customerId' => $remote_customer_id,
			'merchantAccountId' => $this->config['merchant_account_id'],
			'paymentMethodToken' => $remote_card_id,
			'recurring' => $is_renewal,
			'options' => array(
				'submitForSettlement' => true
			),
		);

		// if (isset($data->descriptor))
		// 	$request_data['descriptor'] = $data->descriptor;
			
		$result = Braintree_Transaction::sale($request_data);
		
		$this->raw_result = $result;
		if (!$result->success) return false;
		$transaction = $result->transaction;		
		if ($transaction->status != 'submitted_for_settlement'
		 && $transaction->status != 'settling'
		 && $transaction->status != 'authorized')
				return false;
			
		if ($transaction->status == 'authorized')
			(new static())->settle_transaction($transaction->id);
		
		$res = new stdClass();
		$res->transaction = $transaction->serialize(true);
		return $res;
	}
	
	public function transaction_update($data, $amount, $use_remote_card)
	{
		$remote_customer_id = $data->remote_customer_id;
		$result = Braintree_Customer::update(
			$remote_customer_id, array(
			'firstName' => (string) $data->first_name,
			'lastName' => (string) $data->last_name,
			'company' => (string) $data->company_name,
			'email' => (string) $data->email,
			'phone' => (string) $data->phone,
		));
		
		$this->raw_result = $result;
		if (!$result->success) return false;
		$customer = $result->customer;
		
		if ($use_remote_card)
		{
			$remote_card_id = $data->remote_card_id;
			$is_virtual_card = $data->is_virtual_card;
			$request_data = array(
				'amount' => static::format_amount($amount),
				'customerId' => $remote_customer_id,
				'paymentMethodToken' => $remote_card_id,
				'merchantAccountId' => $this->config['merchant_account_id'],
				'recurring' => false,
			);
			
			if (!$is_virtual_card)
				$request_data['options'] = 
					array('submitForSettlement' => true);

			// if (isset($data->descriptor))
			// 	$request_data['descriptor'] = $data->descriptor;
				
			$result = Braintree_Transaction::sale($request_data);
			$this->raw_result = $result;
			if (!$result->success) return false;
			$transaction = $result->transaction;			
			if ($transaction->status != 'submitted_for_settlement'
			 && $transaction->status != 'settling'
			 && $transaction->status != 'authorized')
				return false;
			
			if ($transaction->status == 'authorized')
				(new static())->settle_transaction($transaction->id);
			
			$res = new stdClass();
			$res->transaction = $transaction->serialize(true);
			return $res;
		}

		// credit or debit card (legacy)
		else if (!empty($data->cc_number))
		{
			$remote_card_id = UUID::create();
			$data->cc_number = filter_var($data->cc_number, FILTER_SANITIZE_NUMBER_INT);
			$data->cc_cvc = filter_var($data->cc_cvc, FILTER_SANITIZE_NUMBER_INT);
			
			// trigger CVV provided but not correct
			if (!$data->cc_cvc) $data->cc_cvc = 999999999;
			
			$request_data = array();
			$request_data['customerId'] = $remote_customer_id;
			$request_data['amount'] = static::format_amount($amount);
			$request_data['merchantAccountId'] = $this->config['merchant_account_id'];
			$request_data['recurring'] = false;
			$request_data['options'] = array(
				'submitForSettlement' => true,
				'storeInVaultOnSuccess' => true,
				'addBillingAddressToPaymentMethod' => true,
			);
			
			$request_data['billing'] = array(
				'firstName' => (string) $data->first_name,
				'lastName' => (string) $data->last_name,
				'company' => (string) $data->company_name,
				'streetAddress' => (string) $data->street_address,
				'extendedAddress' => (string) null,
				'locality' => (string) $data->locality,
				'region' => (string) $data->region,
				'countryName' => (string) @Model_Country::find($data->country_id)->name,
				'postalCode' => $data->country_id == Model_Country::ID_UNITED_STATES()
					? (string) $data->zip : (string) null,
			);
			
			$request_data['creditCard'] = array(
				'token' => $remote_card_id,
				'expirationMonth' => (string) $data->cc_expires_month,
				'expirationYear' => (string) $data->cc_expires_year,
				'number' => (string) $data->cc_number,
				'cvv' => (string) $data->cc_cvc,
			);

			// if (isset($data->descriptor))
			// 	$request_data['descriptor'] = $data->descriptor;
			
			$result = Braintree_Transaction::sale($request_data);
			$this->raw_result = $result;
			
			if (!$result->success) return false;
			$transaction = $result->transaction;
			if ($transaction->status != 'submitted_for_settlement'
			 && $transaction->status != 'settling'
			 && $transaction->status != 'authorized')
				return false;
			
			if ($transaction->status == 'authorized')
				(new static())->settle_transaction($transaction->id);
			
			$res = new stdClass();
			$res->transaction = $transaction->serialize(true);
			$res->card_details = $transaction->creditCardDetails->serialize(true, 1);
			$res->remote_card_id = $transaction->creditCardDetails->token;
			$res->remote_customer_id = $remote_customer_id;
			$res->is_virtual_card = false;
		}

		// credit or debit card (nonce)
		// paypal agreement (nonce)
		else if (!empty($data->cc_nonce) || 
			      !empty($data->paypal_nonce))
		{
			$add_card_result = $this->add_card($remote_customer_id, $data, true);
			if (!$add_card_result) return false;
			$remote_card_id = $add_card_result->remote_card_id;
							
			$request_data = array();
			$request_data['amount'] = static::format_amount($amount);
			$request_data['recurring'] = false;
			$request_data['paymentMethodToken'] = $remote_card_id;
			$request_data['options'] = array('submitForSettlement' => true);
			$request_data['merchantAccountId'] = $this->config['merchant_account_id'];
			
			// if (isset($data->descriptor))
			// 	$request_data['descriptor'] = $data->descriptor;

			$result = Braintree_Transaction::sale($request_data);
			$this->raw_result = $result;
			
			if (!$result->success)
			{
				(new static())->remove_card($remote_card_id);
				return false;
			}
			
			$transaction = $result->transaction;
			if ($transaction->status != 'submitted_for_settlement'
			 && $transaction->status != 'settling'
			 && $transaction->status != 'authorized')
				return false;
			
			if ($transaction->status == 'authorized')
				(new static())->settle_transaction($transaction->id);
			
			$res = new stdClass();
			$res->transaction = $transaction->serialize(true);
			$res->remote_customer_id = $remote_customer_id;
			$res->card_details = $add_card_result->card_details;
			$res->remote_card_id = $remote_card_id;			
			$res->is_virtual_card = $add_card_result->is_virtual_card;
			$res->virtual_card_type = $add_card_result->virtual_card_type;
		}

		else
		{
			return false;
		}
		
		// remove all cards (except the one we purchased with)
		(new static())->remove_cards_for_customer($customer, $remote_card_id);
		
		return $res;
	}
	
	public function transaction_initial($data, $amount)
	{
		$res = new stdClass();
		
		// credit or debit card (legacy)
		if (!empty($data->cc_number))
		{
			$remote_customer_id = UUID::create();
			$remote_card_id = UUID::create();
			
			$data->cc_number = filter_var($data->cc_number, FILTER_SANITIZE_NUMBER_INT);
			$data->cc_cvc = filter_var($data->cc_cvc, FILTER_SANITIZE_NUMBER_INT);
			
			// trigger CVV provided but not correct
			if (!$data->cc_cvc) $data->cc_cvc = 999999999;
			
			$request_data = array();
			$request_data['amount'] = static::format_amount($amount);
			$request_data['merchantAccountId'] = $this->config['merchant_account_id'];
			$request_data['recurring'] = false;
			$request_data['customer'] = array(
				'id' => $remote_customer_id,
				'firstName' => (string) $data->first_name,
				'lastName' => (string) $data->last_name,
				'company' => (string) $data->company_name,
				'email' => (string) $data->email,
				'phone' => (string) $data->phone,
			);
			
			$request_data['billing'] = array(
				'firstName' => (string) $data->first_name,
				'lastName' => (string) $data->last_name,
				'company' => (string) $data->company_name,
				'streetAddress' => (string) $data->street_address,
				'extendedAddress' => (string) null,
				'locality' => (string) $data->locality,
				'region' => (string) $data->region,
				'countryName' => (string) @Model_Country::find($data->country_id)->name,
				'postalCode' => $data->country_id == Model_Country::ID_UNITED_STATES()
					? (string) $data->zip : (string) null,
			);
			
			$request_data['creditCard'] = array(
				'token' => $remote_card_id,
				'expirationMonth' => (string) $data->cc_expires_month,
				'expirationYear' => (string) $data->cc_expires_year,
				'number' => (string) $data->cc_number,
				'cvv' => (string) $data->cc_cvc,
			);
			
			$request_data['options'] = array(
				'submitForSettlement' => true,
				'storeInVaultOnSuccess' => true,
				'addBillingAddressToPaymentMethod' => true,
			);

			// if (isset($data->descriptor))
			// 	$request_data['descriptor'] = $data->descriptor;
			
			$result = Braintree_Transaction::sale($request_data);
			$this->raw_result = $result;
			
			if (!$result->success) return false;
			$transaction = $result->transaction;
			if ($transaction->status != 'submitted_for_settlement'
			 && $transaction->status != 'settling'
			 && $transaction->status != 'authorized')
				return false;
			
			if ($transaction->status == 'authorized')
				(new static())->settle_transaction($transaction->id);
			
			$res->transaction = $transaction->serialize(true);
			$res->card_details = $transaction->creditCardDetails->serialize(true, 1);
			$res->remote_card_id = $transaction->creditCardDetails->token;			
			$res->remote_customer_id = $remote_customer_id;
			$res->is_virtual_card = false;
		}
		
		// credit or debit card (nonce)
		// paypal agreement (nonce)
		else if (!empty($data->cc_nonce) ||
			      !empty($data->paypal_nonce))
		{
			$add_customer_result = $this->add_customer($data);
			if (!$add_customer_result) return false;
			$remote_customer_id = $add_customer_result->remote_customer_id;
			$add_card_result = $this->add_card($remote_customer_id, $data, true);
			
			if (!$add_card_result) 
			{
				(new static())->remove_customer($remote_customer_id);
				return false;
			}
			
			$remote_card_id = $add_card_result->remote_card_id;	
							
			$request_data = array();
			$request_data['amount'] = static::format_amount($amount);
			$request_data['recurring'] = false;			
			$request_data['paymentMethodToken'] = $remote_card_id;
			$request_data['options'] = array('submitForSettlement' => true);
			$request_data['merchantAccountId'] = $this->config['merchant_account_id'];

			// if (isset($data->descriptor))
			// 	$request_data['descriptor'] = $data->descriptor;

			$result = Braintree_Transaction::sale($request_data);
			$this->raw_result = $result;
			
			if (!$result->success)
			{
				(new static())->remove_customer($remote_customer_id);
				return false;
			}
			
			$transaction = $result->transaction;
			if ($transaction->status != 'submitted_for_settlement'
			 && $transaction->status != 'settling'
			 && $transaction->status != 'authorized')
				return false;
			
			if ($transaction->status == 'authorized')
				(new static())->settle_transaction($transaction->id);
			
			$res->transaction = $transaction->serialize(true);
			$res->remote_customer_id = $remote_customer_id;
			$res->card_details = $add_card_result->card_details;
			$res->remote_card_id = $remote_card_id;
			$res->is_virtual_card = $add_card_result->is_virtual_card;
			$res->virtual_card_type = $add_card_result->virtual_card_type;
		}

		else
		{
			return false;
		}
		
		return $res;
	}

	public function no_transaction_initial($data)
	{
		$add_customer_result = $this->add_customer($data);
		if (!$add_customer_result) return false;
		$remote_customer_id = $add_customer_result->remote_customer_id;
		$add_card_result = $this->add_card($remote_customer_id, $data, true);

		if (!$add_card_result) 
		{
			(new static())->remove_customer($remote_customer_id);
			return false;
		}
		
		$res = new stdClass();
		$res->remote_customer_id = $remote_customer_id;
		$res->card_details = $add_card_result->card_details;
		$res->remote_card_id = $add_card_result->remote_card_id;
		$res->is_virtual_card = $add_card_result->is_virtual_card;
		$res->virtual_card_type = $add_card_result->virtual_card_type;
		return $res;
	}

	public function transaction_find($id)
	{
		$result = Braintree_Transaction::find($id);
		$this->raw_result = $result;
		if (!$result) return false;
		return $result;
	}

	public function transaction_void($id)
	{
		$result = Braintree_Transaction::void($id);
		$this->raw_result = $result;
		if ($result->success)
			return true;
		return false;
	}

	public function transaction_refund($id, $amount)
	{
		$amount = static::format_amount($amount);
		$result = Braintree_Transaction::refund($id, $amount);
		$this->raw_result = $result;
		if ($result->success)
			return true;
		return false;
	}

	public function messages()
	{
		if (!isset($this->raw_result->message)) 
			return array();

		$messages = array();
		$raw_message = $this->raw_result->message;
		$messages = preg_split('#\.($|\s+)#s', $raw_message, 0, PREG_SPLIT_NO_EMPTY);
		foreach ($messages as &$message) $message = sprintf('%s.', $message);
		return $messages;
	}
	
	protected static function format_amount($value)
	{
		return sprintf('%0.2f', $value);
	}
	
}