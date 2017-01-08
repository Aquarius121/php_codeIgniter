<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Activate_Credit_Controller extends Iella_Base {
	
	public function index()
	{
		$this->iella_out->status = false;
		$component_item = Model_Component_Item::from_object($this->iella_in->component_item);
		$item = Model_Item::from_object($this->iella_in->item);
		$item_data = $item->raw_data();
		if (!isset($item_data->type)) return;
		$credit = null;
		
		// find component set (for user id)
		$set_id = $component_item->component_set_id;
		$component_set = Model_Component_Set::find($set_id);
		if (!$component_set) return;
		
		if ($item_data->type === Credit::TYPE_PREMIUM_PR)
		{
			$credit = new Model_Limit_PR_Held();
			$credit->user_id = $component_set->user_id;
			$credit->date_expires = $component_item->date_expires;
			$credit->type = Model_Content::PREMIUM;
			$credit->amount_used = 0;
			$credit->amount_total = $component_item->quantity;
			$credit->save();
			
			// allow a credit to be ordered 
			// for a specific PR and locked in
			if (!empty($this->iella_in->track)
			 && !empty($this->iella_in->track->content_id))
			{
				if ($m_content = Model_Content::find($this->iella_in->track->content_id))
				{
					$m_user = Model_User::find($component_set->user_id);
					$m_user->consume_pr_credit_premium($m_content);
					$m_content->is_credit_locked = 1;
					$m_content->save();
				}
			}
			
			// free email credits with every PR
			$bundled = Model_Setting::value('bundled_email_credits');
			$credit = new Model_Limit_Email_Held();
			$credit->user_id = $component_set->user_id;
			$credit->date_expires = $component_item->date_expires;
			$credit->amount_total = $component_item->quantity * $bundled;
			$credit->amount_used = 0;
			$credit->save();
		}
		
		else if ($item_data->type === Credit::TYPE_BASIC_PR)
		{
			$credit = new Model_Limit_PR_Held();
			$credit->user_id = $component_set->user_id;
			$credit->date_expires = $component_item->date_expires;
			$credit->amount_total = $component_item->quantity;
			$credit->type = Model_Content::BASIC;
			$credit->amount_used = 0;			
			$credit->save();
		}
		
		else if ($item_data->type === Credit::TYPE_NEWSROOM)
		{
			$credit = new Model_Limit_Newsroom_Held();
			$credit->user_id = $component_set->user_id;
			$credit->date_expires = $component_item->date_expires;
			$credit->amount_total = $component_item->quantity;
			$credit->amount_used = 0;
			$credit->save();
		}
		
		else if ($item_data->type === Credit::TYPE_EMAIL)
		{
			$credit = new Model_Limit_Email_Held();
			$credit->user_id = $component_set->user_id;
			$credit->date_expires = $component_item->date_expires;
			$credit->amount_total = $component_item->quantity;
			$credit->amount_used = 0;
			$credit->save();
		}
		
		else if ($item_data->type === Credit::TYPE_WRITING)
		{
			$credit = new Model_Limit_Writing_Held();
			$credit->user_id = $component_set->user_id;
			$credit->date_expires = $component_item->date_expires;
			$credit->amount_total = $component_item->quantity;
			$credit->amount_used = 0;
			$credit->save();
		}

		else if (Credit::is_common($item_data->type))
		{
			$credit = new Model_Limit_Common_Held();
			$credit->type = $item_data->type;
			$credit->user_id = $component_set->user_id;
			$credit->date_expires = $component_item->date_expires;
			$credit->amount_total = $component_item->quantity;
			$credit->amount_used = 0;
			$credit->save();
		}
		
		if (!$credit) return;
		$this->iella_out->credit_id = $credit->id;
		$this->iella_out->credit_class = get_class($credit);
		$this->iella_out->status = true;
	}
	
}

?>