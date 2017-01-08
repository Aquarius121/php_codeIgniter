<?php 

class Braintree_Transaction_Container {

	const STATUS_AUTHORIZED               = 'authorized';
	const STATUS_SETTLED                  = 'settled';
	const STATUS_SETTLING                 = 'settling';
	const STATUS_SUBMITTED_FOR_SETTLEMENT = 'submitted_for_settlement';
	const STATUS_VOIDED                   = 'voided';

	protected $api;
	protected $id;
	protected $transaction;

	public function __construct($id)
	{
		$this->id = $id;
		$this->api = new BrainTree_Process();
		$this->transaction = $this->api->transaction_find($id);
	}

	public function id()
	{
		return $this->id;
	}

	public function transaction()
	{
		return $this->transaction;
	}

	public function void()
	{
		return $this->api->transaction_void($this->id);
	}

	public function refund($amount)
	{
		return $this->api->transaction_refund($this->id, $amount);
	}

	public function status()
	{
		return $this->transaction->status;
	}

	public function status_text()
	{
		return $this->normalize_status($this->status());
	}

	public function is_voided()
	{
		$status = $this->status();
		if ($status === static::STATUS_VOIDED)
			return true;
		return false;
	}

	public function refunds()
	{
		if (!$this->transaction->refundIds)
			return array();

		$refunds = array();
		foreach ($this->transaction->refundIds as $id)
			$refunds[] = new static($id);
		return $refunds;
	}

	public function is_voidable()
	{
		$status = $this->status();
		if ($status === static::STATUS_SUBMITTED_FOR_SETTLEMENT ||
			 $status === static::STATUS_AUTHORIZED)
			return true;
		return false;
	}

	public function is_refundable()
	{
		if (!$this->is_voidable())
		{
			$status = $this->status();
			if ($status === static::STATUS_SETTLING ||
				 $status === static::STATUS_SETTLED)
				return $this->refund_available() > 0;
			return false;
		}
	}

	public function refund_available()
	{
		$refunded = 0;
		$paid = $this->transaction->amount;
		$refunds = $this->refunds();
		foreach ($refunds as $refund)
			$refunded += $refund->amount();
		return max(0, ($paid - $refunded));
	}

	public function amount()
	{
		return $this->transaction->amount;
	}

	public function date()
	{
		$datetime = clone $this->transaction->createdAt;
		$datetime->setTimezone(Date::$utc);
		return $datetime->format(Date::FORMAT_ISO8601);
	}

	protected function normalize_status($status)
	{
		return ucwords(str_replace('_', ' ', $status));
	}

} 

