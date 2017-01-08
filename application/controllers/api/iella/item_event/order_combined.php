<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_Combined_Controller extends Iella_Base {
	
	public function index()
	{
		$user = Model_User::from_object($this->iella_in->user);
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$transaction = Model_Transaction::from_object($this->iella_in->transaction);
		$component_set = Model_Component_Set::from_object($this->iella_in->component_set);
		$item = $cart_item->item();
		$item_data = $item->raw_data();
		if (!isset($item_data->items))
			throw new Exception();

		// for tracking info onto next item
		$track_to_next_item = new stdClass();
		
		foreach ($item_data->items as $new_item_definition)
		{
			if (!($new_item = Model_Item::find_slug($new_item_definition->slug))) continue;
			$new_cart_item = Cart_Item::create($new_item);
			$new_cart_item->price = 0;
			$new_cart_item->track = $cart_item->track;

			// use the same quantity as container
			// * if custom quantity defined, use as multi
			$new_cart_item->quantity = $cart_item->quantity;
			if (isset($new_item_definition->quantity))
				$new_cart_item->quantity *= $new_item_definition->quantity;
			
			// set or replace local item data
			if (isset($new_item_definition->item_data))
			{
				$new_item_data = $new_item->raw_data();
				foreach ($new_item_definition->item_data as $k => $v)
					$new_item_data->{$k} = $v;
			}

			// pass tracking info onto next item
			foreach ($track_to_next_item as $k => $v)
			{
				if (!isset($new_cart_item->track->{$k}))
					$new_cart_item->track->{$k} = $v;
			}
			
			// run the item's order event to activate
			$iella_event = new Iella_Event();
			$iella_event->data->cart_item = $new_cart_item;
			$iella_event->data->user = $user;
			$iella_event->data->component_set = $component_set;
			$iella_event->data->transaction = $transaction;
			$iella_event->data->item_data = $new_item_data;
			$order_event_response = $iella_event->emit($new_item->order_event);

			// capture tracking response for next iteration
			if (isset($order_event_response->track))
			{
				foreach ($order_event_response->track as $k => $v)
					$track_to_next_item->{$k} = $v;
			}
		}
	}
	
}

?>