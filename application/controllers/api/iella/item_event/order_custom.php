<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_Custom_Controller extends Iella_Base {
	
	use Order_Attached_Trait;

	public function index()
	{
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$user = Model_User::from_object($this->iella_in->user);
		$item = $cart_item->item();		
		$item_data = $item->raw_data();
		
		// only bother to create renewal data if this
		// item actually needs to have such data
		if ((isset($item_data->is_auto_renew_enabled) &&
			       $item_data->is_auto_renew_enabled)  ||
			 (isset($item_data->is_renewable) &&
			       $item_data->is_renewable))
		{
			if (isset($item_data->period_repeat_count))
			     $period_repeat_count = $item_data->period_repeat_count;
			else $period_repeat_count = 1;

			if (isset($item_data->period))
			     $period = $item_data->period;
			else $period = Model_Setting::value('held_credit_period');

			// validate the values a bit because from admin
			if ($period_repeat_count < 1) $period_repeat_count = 1;
			if ($period < 1) $period = Model_Setting::value('held_credit_period');

			// allows this item to be attached to another
			if ($this->iella_in->attached_quantity)
			     $quantity_multi = $this->iella_in->attached_quantity;
			else $quantity_multi = 1;
			
			$component_item = Model_Component_Item::create();
			$component_item->component_set_id = $this->iella_in->component_set->id;
			$component_item->item_id = $item->id;
			$component_item->date_expires = Date::days($period)->format(Date::FORMAT_MYSQL);
			$component_item->date_termination = Date::days($period_repeat_count * $period)->format(Date::FORMAT_MYSQL);
			$component_item->period_repeat_count = $period_repeat_count;
			$component_item->period = $period;
			$component_item->price = $cart_item->price;
			$component_item->quantity = $quantity_multi * $cart_item->quantity;
			$component_item->is_auto_renew_enabled = (int) ((bool) @$item_data->is_auto_renew_enabled);
			// if is_auto_review_enabled => is_renewable must be true too
			$component_item->is_renewable = $component_item->is_auto_renew_enabled;
			if (@$item_data->is_renewable) $component_item->is_renewable = 1;
			$component_item->save();
			
			// activate immediately (not used ATOW)
			$component_item->trigger($cart_item->track);
		}

		// process any attached items (track to next comes from cart_item)
		$track_back = $this->process_attached($cart_item, clone $cart_item->track);
	}
	
}

?>