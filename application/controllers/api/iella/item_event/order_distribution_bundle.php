<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

abstract class Order_Distribution_Bundle_Controller extends Iella_Base {

	protected $credit_class = null;
	
	use Order_Attached_Trait;

	public function index()
	{
		$m_user = Model_User::from_object($this->iella_in->user);
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$item = $cart_item->item();
		$item_data = $item->raw_data();

		if ($this->iella_in->attached_quantity)
		     $quantity_multi = $this->iella_in->attached_quantity;
		else $quantity_multi = 1;

		if (!$cart_item->track->use_existing_credit)
		{
			$credit = Model_Limit_Common_Held::create($m_user, $this->credit_class);
			$credit->amount_total = $quantity_multi * $cart_item->quantity;
			$credit->amount_used = 0;
			$credit->save();
		}
		
		// if this is for some specific content
		// then we consume 1 credit immediately
		if ($cart_item->track->content_id)
		{
			$held = Model_Limit_Common_Held::find_collection($m_user, $this->credit_class);
			$consumer = new Common_Credit_Consumer();
			$consumer->set_held($held);
			$consumer->consume(1);
			
			$m_content = Model_Content::find($cart_item->track->content_id);
			if (!$m_content) throw new Exception();
			$m_content->is_credit_locked = 1;
			$m_content->is_draft = 0;
			$m_content->save();

			$m_bundle = $m_content->distribution_bundle();
			$m_bundle->confirm();
		}

		// add some bundled email credits 
		$held_credit_period = Model_Setting::value('held_credit_period');
		$held_date_expires = Date::days($held_credit_period);
		$em_credits = new Model_Limit_Email_Held();
		$em_credits->user_id = $m_user->id;
		$em_credits->amount_total = $quantity_multi * 
			$cart_item->quantity * $item_data->bundled_email_credits;
		$em_credits->date_expires = $held_date_expires;
		$em_credits->amount_used = 0;
		$em_credits->save();

		// process any attached items (track to next comes from cart_item)
		$track_back = $this->process_attached($cart_item, clone $cart_item->track);
	}
	
}