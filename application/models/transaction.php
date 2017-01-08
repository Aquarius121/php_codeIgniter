<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Transaction extends Model {
	
	use Raw_Data_Trait;
	
	const GATEWAY_BRAINTREE = Gateway::BRAINTREE;
	
	protected static $__table = 'co_transaction';
	
	public static function create($uuid = null)
	{
		$instance = new static();
		if ($uuid === null)
		     $instance->id = UUID::create();
		else $instance->id = $uuid;
		$instance->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		return $instance;
	}
	
	public static function find_order($order)
	{
		if ($order instanceof Model_Order)
			$order = $order->id;
		$criteria = array('order_id', $order);
		return static::find_all($criteria);
	}
	
	public static function find_order_first($order)
	{
		if ($order instanceof Model_Order)
			$order = $order->id;
		$criteria = array('order_id', $order);
		$sort_order = array('date_created', 'asc');
		$results = static::find_all($criteria, $sort_order, 1);
		if (!$results) return null;
		return $results[0];
	}
	
	public function nice_id()
	{
		$short = substr($this->id, 0, 8);
		$short = strtoupper($short);
		return $short;
	}

	public function gateway_transaction()
	{
		$rdo = $this->raw_data_object();
		if ($this->gateway === static::GATEWAY_BRAINTREE && $rdo->id)
			return new Braintree_Transaction_Container($rdo->id);
		return null;
	}

	public function gateway_transaction_id()
	{
		if ($this->gateway === static::GATEWAY_BRAINTREE)
			return $this->raw_data_object()->id;
		return false;
	}
	
}