<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_PRN_Image_Controller extends Iella_Base {

	public function index()
	{
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$item = $cart_item->item();
		$item_data = $item->raw_data();
		$quantity = $cart_item->quantity;

		if ($cart_item->track->content_id)
		{
			$uuid = $cart_item->track->extra_uuid;
			$content_id = $cart_item->track->content_id;
			$cde = Model_Content_Distribution_Extras::find($content_id)
				?: new Model_Content_Distribution_Extras();

			$extra = $cde->get($uuid);
			$extra->data = $extra->data ?: new Raw_Data();
			$extra->data->confirmed = $extra->data->confirmed ?: array();
			$extra->data->selected = $extra->data->selected ?: array();
			$extra->data->credits = $extra->data->credits ?: 0;
			
			$confirm = array_diff($extra->data->selected, $extra->data->confirmed);
			$confirm = array_slice($confirm, 0, $quantity);
			$quantity = $quantity - count($confirm);

			foreach ($confirm as $id)
				$extra->data->confirmed[] = $id;
			$extra->data->credits += $quantity;

			$cde->set($uuid, $extra);
			$cde->save();
		}
	}
	
}