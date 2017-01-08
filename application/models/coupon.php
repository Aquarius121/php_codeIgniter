<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Coupon extends Model {
	
	use Raw_Data_Trait;

	protected static $__table = 'co_coupon';
	
	public static function find_code($code)
	{
		$code = static::normalize($code);
		$criteria = array('code', $code);
		return static::find($criteria);
	}

	public static function find_secret($secret)
	{
		$criteria = array('secret', $secret);
		return static::find($criteria);
	}
	
	public static function normalize($code)
	{
		$code = strtoupper($code);
		$code = preg_replace('/[^A-Z0-9]/', '-', $code);
		$code = preg_replace('/(^-|-$)/', '', $code);
		return $code;
	}

	// returns the coupon code
	// or previous value when 
	// the coupon has expired 
	public function code()
	{
		if ($this->code) return $this->code;
		$rd = $this->raw_data();
		if (!$rd) return null;
		return $rd->code;
	}
	
	public function has_expired()
	{
		$dt = new DateTime($this->date_expires);
		if ($dt < Date::$now) return true;
		return false;
	}
	
	public function discount($cart)
	{
		$old_total_cost = $cart->total();
		$new_total_cost = $this->calculate_cost($cart);
		return max(0, $old_total_cost - $new_total_cost);
	}
	
	public function calculate_cost($cart, $__cart_item = null)
	{
		if ($__cart_item)
		{
			$calculated_cost = 0;
			$cart_items = $__cart_item->flatten();

			foreach ($cart_items as $cart_item)
			{
				$cost = $this->__calculate_cost_sub($cart, $cart_item);
				if ($cost < 0) $cost = 0;
				$calculated_cost += $cost;
			}

			return $calculated_cost;
		}
		else
		{
			$cost = $this->__calculate_cost_sub($cart);
			if ($cost < 0) return 0;
			return $cost;
		}
	}
	
	protected function __calculate_cost_sub($cart, $__cart_item = null)
	{
		if ($__cart_item !== null)
		{	
			// we want to calculate the cost
			// of one item within cart context
			
			$__item_id = $__cart_item->item_id;
			$__item_hash = $__cart_item->hash();
			$__item_token = $__cart_item->token();
			$__item_price = $__cart_item->base_price();
		}
		
		$discount = 0;
		$discountable_cost = 0;
		$static_items_discount = 0;
		$data = $this->raw_data();
		$total_cost = $cart->total();
		
		// discount for set of items
		if (isset($data->item_restriction))
		{
			// add items cost to total cost 
			foreach ($data->item_restriction as $item_id)
				if ($item_a = $cart->get_id($item_id))
					foreach ($item_a as $item)
						$discountable_cost += $item->base_price_total();
		}
		else
		{
			// discount entire total value
			$discountable_cost = $total_cost;
		}
		
		// items have a cost reduction
		if (isset($data->item_static_cost))
		{
			// find items with a fixed price and reduce
			// adjust the total cost accordingly
			foreach ($data->item_static_cost as $item_id => $cost)
			{
				if ($item_a = $cart->get_id($item_id))
				{
					foreach ($item_a as $item)
					{
						// found updated price information for the __cart_item
						if ($__cart_item !== null && $item->token() == $__item_token)
							return min($cost, $__item_price);

						$item_discount = max(0, $item->base_price() - $cost);
						$line_discount = $item->quantity * $item_discount;
						$total_cost -= $line_discount;
						
						// reduces the discountable cost because
						// not restricted or appears within restriction
						if (!isset($data->item_restriction) || 
						     in_array($item_id, $data->item_restriction))
							$discountable_cost -= $item->base_price_total();
					}
				}
			}
		}
		
		if ($__cart_item !== null)
		{
			// discount does not apply to this 
			// item so just return the price
			// (with static cost applied)
			if (isset($data->item_restriction))
				if (!in_array($__item_id, $data->item_restriction))
					return $__item_price;
		
			// did not meet the minimum spend
			if (isset($data->minimum_cost) && 
			    $discountable_cost < $data->minimum_cost)
				return $__item_price;
			
			// percentage based discount
			if (isset($data->percentage_discount))
				return $__item_price - ($__item_price * 
					$data->percentage_discount * 0.01);
			
			// fixed discount as a percentage
			if (isset($data->fixed_discount))
				return $__item_price - ($__item_price * 
					($data->fixed_discount / $total_cost));
			
			return $__item_price;
		}

		// did not meet the minimum spend
		if (isset($data->minimum_cost) && 
		    $discountable_cost < $data->minimum_cost)
			return $total_cost;
		
		// percentage based discount
		if (isset($data->percentage_discount))
			return $total_cost - ($discountable_cost * 
				$data->percentage_discount * 0.01);
				
		// fixed price discount
		if (isset($data->fixed_discount))
			return $total_cost - $data->fixed_discount;
		
		return $total_cost;
	}
	
}

?>