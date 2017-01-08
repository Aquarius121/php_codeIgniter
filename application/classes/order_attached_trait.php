<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Order_Attached_Trait {
	
	// must be called in the order event handler 
	// @param $cart_item The Cart_Item the original handler worked on
	// @param $track_to_next_item Any tracking information to be passed on
	// @return $track_to_next_item Tracking information from these sequence
	protected function process_attached($cart_item, $track_to_next_item)
	{
		$user = Model_User::from_object($this->iella_in->user);
		$transaction = Model_Transaction::from_object($this->iella_in->transaction);
		$component_set = Model_Component_Set::from_object($this->iella_in->component_set);

		foreach ($cart_item->attached as $atd)
		{
			foreach ($track_to_next_item as $k => $v)
			{
				if (!isset($atd->track->{$k}))
					$atd->track->{$k} = $v;
			}
			
			// run the item's order event to activate
			$iella_event = new Iella_Event();
			$iella_event->data->cart_item = $atd;
			$iella_event->data->user = $user;
			$iella_event->data->component_set = $component_set;
			$iella_event->data->transaction = $transaction;
			$iella_event->data->is_attached = true;
			$iella_event->data->attached_quantity = $cart_item->quantity;
			$iella_event->data->attached_cart_item = $cart_item;
			$order_event_response = $iella_event->emit($atd->item()->order_event);
			
			if (isset($order_event_response->track))
			{
				foreach ($order_event_response->track as $k => $v)
					$track_to_next_item->{$k} = $v;
			}
		}

		return $track_to_next_item;
	}
	
}

?>