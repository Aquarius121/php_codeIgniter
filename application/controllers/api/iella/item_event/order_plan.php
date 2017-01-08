<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_Plan_Controller extends Iella_Base {

	public function index()
	{
		$this->iella_out->status = false;
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$user = Model_User::from_object($this->iella_in->user);
		$item = $cart_item->item();
		if ($item->type != Model_Item::TYPE_PLAN)
			throw new Exception();

		// use item data from iella_in if provided
		if (isset($this->iella_in->item_data))
		     $item_data = $this->iella_in->item_data;
		else $item_data = $item->raw_data();

		$plan = Model_Plan::find($item_data->plan_id);

		if (isset($item_data->period_repeat_count))
		     $period_repeat_count = $item_data->period_repeat_count;
		else $period_repeat_count = 1;

		if (isset($item_data->period))
		     $period = $item_data->period;
		else $period = $plan->period;

		// terminate any existing subscription
		$terminator = new Subscription_Terminator();
		$terminator->cancel_all($user->id);

		$component_item = Model_Component_Item::create();
		$component_item->component_set_id = $this->iella_in->component_set->id;
		$component_item->item_id = $item->id;
		$component_item->date_expires = Date::days($period)->format(Date::FORMAT_MYSQL);
		$component_item->date_termination = Date::days($period_repeat_count * $period)->format(Date::FORMAT_MYSQL);
		$component_item->period_repeat_count = $period_repeat_count;
		$component_item->period = $period;
		$component_item->price = $cart_item->price;
		$component_item->is_auto_renew_enabled = (int) ((bool) @$item_data->is_auto_renew_enabled);
		// if is_auto_review_enabled => is_renewable must be true too
		$component_item->is_renewable = $component_item->is_auto_renew_enabled;
		if (@$item_data->is_renewable) $component_item->is_renewable = 1;
		$component_item->save();

		// this should always be 1 as we cannot have more than 1 plan
		$component_item->quantity = $cart_item->quantity;
		$component_item->save();

		// activate plan immediately
		$activation_event_result = $component_item->trigger();

		// used for debugging only => do not rely on it!
		$this->iella_out->activation_event_result = $activation_event_result;
		$this->iella_out->component_item_id = $component_item->id;
		$this->iella_out->status = true;
	}

}

?>