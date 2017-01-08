<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Virtual_Cart extends Cart {

	protected $allow_disabled_items = true;
		
	public function __construct()
	{
		return;
	}
	
	public function save()
	{
		return;
	}
	
	public static function instance()
	{
		return new Virtual_Cart();
	}
	
	public static function create_from_order($order)
	{
		$instance = static::instance();
		$transaction = Model_Transaction::find_order_first($order);
		$instance->unserialize($transaction->virtual_cart);
		$instance->allow_expired_coupon();
		$instance->allow_deleted_coupon();
		return $instance;
	}
	
	public static function create_from_transaction($transaction)
	{
		$instance = static::instance();
		$instance->unserialize($transaction->virtual_cart);
		$instance->allow_expired_coupon();
		$instance->allow_deleted_coupon();
		return $instance;
	}
	
	public static function create_from_post_data($data)
	{
		$coupon = null;
		$instance = new static();
		foreach ((array) @$data['cart_item_hash'] as $k => $hash)
			$instance->force_add_cart_item(new Cart_Item_Hash($hash));
		if (!empty($data['cart_coupon_code']))
			$coupon = Model_Coupon::find_code($data['cart_coupon_code']);
		else if (!empty($data['cart_coupon_id']))
			$coupon = Model_Coupon::find($data['cart_coupon_id']);
		$instance->set_coupon($coupon);
		return $instance;
	}

	// always valid
	public function validate()
	{
		return true;
	}
	
}
