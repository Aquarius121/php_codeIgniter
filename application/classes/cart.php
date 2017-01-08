<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cart {
	
	protected static $__instance;
	
	protected $id;
	protected $items = array();
	protected $coupon = null;
	protected $locked = false;
	protected $allow_expired_coupon = false;
	protected $allow_deleted_coupon = false;
	protected $allow_disabled_items = false;
	
	private function __construct()
	{
		$this->id = static::cart_id();
		$cart_data_str = Data_Cache_LT::read($this->id);
		if ($cart_data_str)
			$this->unserialize($cart_data_str);
	}
	
	public static function create_from_order($order)
	{
		$instance = static::instance();
		$instance->reset();
		$transaction = Model_Transaction::find_order_first($order);
		$instance->unserialize($transaction->virtual_cart);
		return $instance;
	}
	
	public static function create_from_cart($cart)
	{
		$instance = static::instance();
		$instance->items = $cart->items;
		$instance->coupon = $cart->coupon;
		$instance->locked = false;
		return $instance;
	}
	
	public static function instance()
	{
		if (!static::$__instance)
			static::$__instance = new Cart();
		return static::$__instance;
	}

	public function allow_disabled_items($value = true)
	{
		$this->allow_disabled_items = $value;
	}

	// allows expired coupons to be used still 
	// => renewals, receipts, view order, view transaction
	public function allow_expired_coupon($allow = true)
	{
		$this->allow_expired_coupon = $allow;
	}
	
	// allows deleted coupons to be used still 
	// => receipts, view order, view transaction
	public function allow_deleted_coupon($allow = true)
	{
		$this->allow_deleted_coupon = $allow;
	}
	
	// prevent one time coupons from being used (renewals)
	public function remove_one_time_coupon()
	{
		if ($this->coupon && $this->coupon->is_one_time)
			$this->coupon = null;
	}
	
	public function format($cost, $allow_more_precision = false)
	{
		$decimal_places = 2;
		
		if ($allow_more_precision)
		{
			$cost_whole = $cost * 100;
			$cost_remain = $cost_whole - floor($cost_whole);
			if ($cost_remain > 0.01)
				$decimal_places = 4;
		}
		
		if ($cost < 0)
		     return sprintf('$ - %s', number_format(-$cost, $decimal_places));
		else return sprintf('$ %s', number_format($cost, $decimal_places));
	}
	
	protected static function cart_id()
	{
		$ci =& get_instance();
		$session_id = $ci->session->id();
		return sprintf('dc_cart_%s', $session_id);
	}
	
	protected static function cart_lock_token()
	{
		return sprintf('lock_%s', static::cart_id());
	}
	
	public function save()
	{
		$cart_data_str = $this->serialize(false);
		Data_Cache_LT::write($this->id, $cart_data_str, 86400);
	}
	
	// $as_object will return a basic object
	// representation of the cart instead of string
	public function serialize($as_object = false)
	{
		$cart_data = new stdClass();
		$cart_data->items = array();
		foreach ($this->items as $token => $cart_item)
			$cart_data->items[$token] = $cart_item->to_object();
		$cart_data->coupon = (int) @$this->coupon->id;
		if ($as_object) return $cart_data;
		$cart_data_str = json_encode($cart_data);
		return $cart_data_str;
	}
	
	// $from_object will assume a basic object
	// representation of the cart instead of string
	public function unserialize($cart_data_str, $from_object = false)
	{
		if ($from_object)
			  $cart_data = Raw_Data::from_object($cart_data_str);
		else $cart_data = Raw_Data::from_object(json_decode($cart_data_str));
		foreach ($cart_data->items as $token => $item)
			$this->items[$token] = Cart_Item::from_object($item);
		$this->coupon = value_or_null(Model_Coupon::find($cart_data->coupon));
	}
	
	public function items()
	{
		return $this->items;
	}

	public function items_array()
	{
		return array_values($this->items);
	}

	public function items_flatten()
	{
		$items = array();
		foreach ($this->items as $item)
			$items = array_merge($items, $item->flatten());
		return $items;
	}

	public function item_models()
	{
		$items = array();
		foreach ($this->items as $item)
			$items[] = $item->item();
		return $items;
	}

	public function item_models_set()
	{
		$items = array();
		
		foreach ($this->items as $cart_item)
		{
			$item = $cart_item->item();
			$items[(int) $item->id] = $item;
		}

		return $items;
	}
	
	public function discount()
	{
		if (!$this->check_coupon_is_valid()) return 0;
		return $this->coupon->discount($this);
	}

	public function discount_as_percent()
	{
		$discount = $this->discount();
		if (!$discount) return 0;
		$total = $this->total();
		return 100 * ($discount / $total);
	}

	public function coupon()
	{
		return $this->coupon;
	}
	
	public function lock()
	{
		Data_Cache_LT::write(static::cart_lock_token(), 1, 300);
	}
	
	public function unlock()
	{
		Data_Cache_LT::write(static::cart_lock_token(), 0, 300);
	}
	
	public function is_locked()
	{
		return (bool) Data_Cache_LT::read(static::cart_lock_token());
	}
	
	public function set_coupon($coupon)
	{
		return $this->coupon = value_or_null($coupon);
	}
	
	public function total()
	{
		$total = 0;
		foreach ($this->items as $item)
			$total += $item->price_total();
		return $total;
	}
	
	public function total_with_discount()
	{
		$total  = $this->total();
		$total -= $this->discount();
		return max(0, $total);
	}
	
	public function reset()
	{
		$this->items = array();
		$this->coupon = null;
	}
	
	public function clear()
	{
		$this->reset();
	}
	
	public function is_clear()
	{
		return !count($this->items);
	}
	
	public function update_prices()
	{
		// update latest prices for the items
		foreach ($this->items as $k => $item)
			$item->price = $item->item()->price;
	}
	
	public function has_item($cart_item)
	{
		if (isset($this->items[$cart_item->token()]))
			return $this->items[$cart_item->token()];
		return false;
	}
	
	public function has_equal_cart_item($cart_item)
	{
		foreach ($this->items as $test_cart_item)
			if ($test_cart_item->hash() == $cart_item->hash())
				return $test_cart_item;
		return false;
	}
	
	// note: this will force an item into the cart
	// where other checks have failed
	// * must be an instance of Cart_Item
	public function force_add_cart_item($cart_item)
	{
		$this->items[$cart_item->token()] = $cart_item;
		return $cart_item;
	}
	
	public function add($item, $quantity = 1, $price = null)
	{
		if ($item instanceof Cart_Item)
			throw new Exception();
		
		if (!($item instanceof Model_Item))
			$item = Model_Item::find($item);
		if (!$item) return false;
		if (!$this->allow_disabled_items && 
			$item->is_disabled) return false;
		if ($item->is_exclusive())
			$this->remove_exclusive($item->type);
		
		$cart_item = Cart_Item::create($item, $quantity, $price);
		return $this->add_cart_item($cart_item);
	}

	public function add_cart_item($cart_item)
	{
		if (!($cart_item instanceof Cart_Item))
			throw new Exception();

		if ($has_item = $this->has_equal_cart_item($cart_item))
		{
			if (!$has_item->is_quantity_unlocked) return $has_item;
			$has_item->quantity += $cart_item->quantity;
			$this->validate();
			return $has_item;
		}

		$this->items[$cart_item->token()] = $cart_item;
		$this->validate();
		return $cart_item;
	}
	
	public function validate()
	{
		// copy the items array 
		// for checking later
		$initial_items = $this->items;

		// validate the chain of items
		while (!$this->validate_item_chain());

		// remove any disabled items
		if (!$this->allow_disabled_items)
			foreach ($this->items as $token => $item)
				if ($item->item()->is_disabled)
					unset($this->items[$token]);

		// return true if no changes were made
		return $this->items == $initial_items;
	}
	
	// validate that each item in the chain 
	// has it's required item within the cart
	public function validate_item_chain()
	{
		foreach ($this->items as $token => $cart_item)
		{
			$item = $cart_item->item();
			$item_data = $item->raw_data();

			if (isset($item_data->requires_all))
			{
				foreach ($item_data->requires_all as $required_id) 
				{
					if (!$this->get_id($required_id))
					{
						unset($this->items[$token]);
						return false;
					}
				}
			}

			if (isset($item_data->requires_one))
			{
				$one_found = false;

				foreach ($item_data->requires_one as $required_id) 
				{
					if ($this->get_id($required_id))
					{
						$one_found = true;
						break;
					}
				}

				if (!$one_found)
				{
					unset($this->items[$token]);
					return false;
				}
			}
		}
		
		return true;
	}
	
	protected function remove_exclusive($type)
	{
		// max 1 of each exclusive type
		foreach ($this->items as $token => $item)
			if ($item->item()->type == $type)
				unset($this->items[$token]);
	}

	public function remove_item($token)
	{
		if ($token instanceof Cart_Item)
			$token = $token->token();
		if (isset($this->items[$token]))
			unset($this->items[$token]);
		$this->validate();
	}

	public function get_token($token)
	{
		// already the cart item and not token?
		if ($token instanceof Cart_Item)
			return $token;

		if (isset($this->items[$token]))
			return $this->items[$token];
		return null;
	}
	
	public function get_hash($hash)
	{
		// already the cart item and not hash?
		if ($hash instanceof Cart_Item)
			return $hash;
		
		// fetch Cart_Item from cart 
		foreach ($this->items as $item)
			if ($item->hash() == $hash)
				return $item;
	}
	
	public function remove_id($id)
	{
		// remove item with id from cart
		foreach ($this->items as $token => $item)
			if ($item->item()->id == $id)
				unset($this->items[$token]);
		$this->validate();
	}
	
	public function get_id($id)
	{
		$items = array();
		// fetch Cart_Item(s) from cart 
		foreach ($this->items_flatten() as $item)
			if ($item->item()->id == $id)
				$items[] = $item;
		return $items;
	}
	
	// confirms that one cart matches another	
	public function is_equal_to($vcart)
	{
		// different coupon on this cart
		if (@$this->coupon->id != @$vcart->coupon->id)
			return false;
		
		// find missing item
		foreach ($this->items as $cart_item)
		{
			// attempt to find with same hash
			$this_hash = $cart_item->hash();
			$match = $vcart->get_hash($this_hash);
			if (!$match) return false;

			// remove match so remove from vcart
			unset($vcart->items[$match->token()]);
		}
		
		// find extra item
		if (count($vcart->items))
			return false;
		return true;
	}
	
	public function callback($transaction = null)
	{
		$callback = null;
		foreach ($this->items as $k => $item)
			if ($item->callback && !$callback)
				$callback = $item->callback;
			else if ($item->callback)
				return null;

		// add the transaction id
		if ($callback && $transaction)
		{
			if ($transaction instanceof Model_Transaction)
				$transaction = $transaction->id;
			$callback = insert_into_query_string($callback, array(
				'transaction' => $transaction,
			));
	   }

		return $callback;
	}
	
	public function item_cost($token)
	{
		$cart_item = $this->get_token($token);
		if (!$cart_item) return null;
		if (!$this->check_coupon_is_valid()) return $cart_item->price;
		return $this->coupon->calculate_cost($this, $cart_item);
	}
	
	public function item_discount($token)
	{
		$cart_item = $this->get_token($token);
		if (!$cart_item) return null;
		if (!$this->check_coupon_is_valid()) return 0;
		$new_cost = $this->coupon->calculate_cost($this, $cart_item);
		$discount = $cart_item->price - $new_cost;
		return max(0, $discount);
	}
	
	public function line_cost($token)
	{
		$cart_item = $this->get_token($token);
		if (!$cart_item) return null;
		$item_cost = $this->item_cost($cart_item);
		return $item_cost * $cart_item->quantity;
	}
	
	public function line_discount($token)
	{
		$cart_item = $this->get_token($token);
		if (!$cart_item) return null;
		$item_discount = $this->item_discount($cart_item);
		return $item_discount * $cart_item->quantity;
	}

	public function line_discount_as_percent($token)
	{
		$cart_item = $this->get_token($token);
		if (!$cart_item) return null;
		$discount = $this->line_discount($cart_item);
		if (!$discount) return 0;
		$total = $cart_item->price_total();
		return 100 * ($discount / $total);
	}

	public function renewal_costs()
	{
		$distances = array();

		foreach ($this->items_flatten() as $cart_item)
		{
			$data = $cart_item->item()->raw_data();
			if (!isset($data->is_auto_renew_enabled)) continue;
			$distance = $cart_item->item()->renewal_distance();
			if (!isset($distances[$distance]))
				$distances[$distance] = array();
			$distances[$distance][] = $cart_item;
		}

		foreach ($distances as $distance => $items)
		{
			$v_cart = new Virtual_Cart();
			$v_cart->items = $items;
			$v_cart->coupon = $this->coupon;
			$v_cart->remove_one_time_coupon();
			$price = $v_cart->total_with_discount();

			// combine renewal costs
			$distances[$distance] = $price;
		}

		return $distances;
	}

	public function check_coupon_is_valid()
	{
		if (!$this->coupon) return false;
		
		// check if coupon has expired
		if (!$this->allow_expired_coupon
			&& $this->coupon->has_expired()) 
			return false;
		
		// check if coupon has been deleted
		if (!$this->allow_deleted_coupon
			&& $this->coupon->is_deleted) 
			return false;
		
		return true;
	}

	public function remove_zeros()
	{
		// minimum quantity is 1
		foreach ($this->items as $token => $item)
			if ($item->quantity <= 0)
				unset($this->items[$token]);
	}

	public function is_one_time_discount()
	{
		return $this->coupon && $this->coupon->is_one_time;
	}

}

?>