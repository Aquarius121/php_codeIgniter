<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_PRN_Video_Controller extends Iella_Base {

	public function index()
	{
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$item = $cart_item->item();
		$item_data = $item->raw_data();

		if ($cart_item->track->content_id)
		{
			$uuid = $cart_item->track->extra_uuid;
			$content_id = $cart_item->track->content_id;
			$cde = Model_Content_Distribution_Extras::find($content_id)
				?: new Model_Content_Distribution_Extras();

			$extra = $cde->get($uuid);
			$extra->data = $extra->data ?: new Raw_Data();
			$extra->data->is_confirmed = true;
			$cde->set($uuid, $extra);
			$cde->save();
		}
	}
	
}