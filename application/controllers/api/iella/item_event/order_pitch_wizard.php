<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_Pitch_Wizard_Controller extends Iella_Base {
	
	use Order_Attached_Trait;

	public function index()
	{
		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);		
		$user = Model_User::from_object($this->iella_in->user);
		$pitch_session_id = $cart_item->track->pitch_session_id;
		$m_pw_order = $this->convert_to_order($pitch_session_id);
		$item = $cart_item->item();

		// process any attached items (track to next comes from cart_item)
		$track_back = $this->process_attached($cart_item, clone $cart_item->track);

		if ($item->slug == Model_Pitch_Order::ITEM_PW_ORDER_SLUG)
		{
			// we need to consume 1 credit of either writing or media outreach
			$m_pw_session = Model_Pitch_Session::find($pitch_session_id);
			$pw_raw_data = $m_pw_session->raw_data();
			if ($pw_raw_data->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH)
				$held_credits = Model_Limit_Common_Held::find_user($user, Credit::TYPE_MEDIA_OUTREACH);
			else // $pw_raw_data->order_type == Model_Pitch_Session::ORDER_TYPE_WRITING
				$held_credits = Model_Limit_Common_Held::find_user($user, Credit::TYPE_PITCH_WRITING);	
			$consumer = new Common_Credit_Consumer();
			$consumer->set_held($held_credits);
			$consumer->consume(1);
		}
		
		if ($item->slug == Model_Pitch_Order::ITEM_PW_SECOND_INDUSTRY_SLUG)
		{
			$m_pw_session = Model_Pitch_Session::find($pitch_session_id);
			$pw_raw_data = $m_pw_session->raw_data();
			$m_pw_order->beat_2_id = $pw_raw_data->beat_2_id;
			$m_pw_order->save();
		}
		
		if ($item->slug == Model_Pitch_Order::ITEM_PW_NATIONAL_DIST_SLUG)
		{
			$m_pw_order->distribution = Model_Pitch_Order::DISTRIBUTION_NATIONAL;
			$m_pw_order->save();
		}
		
		if ($item->slug == Model_Pitch_Order::ITEM_PW_RUSH_DELIVERY_SLUG)
		{
			$m_pw_order->delivery = Model_Pitch_Order::DELIVERY_RUSH;
			$m_pw_order->save();
		}
	}
	
	public function convert_to_order($pitch_session_id)
	{
		$m_pw_session = Model_Pitch_Session::find($pitch_session_id);
		if ( ! empty($m_pw_session->pitch_order_id) && 
				$m_pw_order = Model_Pitch_Order::find($m_pw_session->pitch_order_id))
			return $m_pw_order;
		
		// creating the pitch order if it does not exist
		$pw_raw_data = $m_pw_session->raw_data();
		
		$date_now_str = Date::$now->format(Date::FORMAT_MYSQL);
		$m_content = Model_Content::find($m_pw_session->content_id);

		$company_id = $m_pw_session->company_id;
		$company_profile = Model_Company_Profile::find($company_id);

		$m_campaign = new Model_Campaign();
		$m_campaign->company_id = $company_id;
		$m_campaign->name = 'Pitch: ' . strtoupper($m_content->title);
		$m_campaign->sender_name = $this->newsroom->company_name;
		$m_campaign->sender_email = $company_profile->email;
		$m_campaign->is_draft = 1;
		$m_campaign->is_under_writing = 1;
		$m_campaign->content_id = $m_pw_session->content_id;
		$m_campaign->date_send = Date::days(+4)->format(Date::FORMAT_MYSQL);
		$m_campaign->save();

		$m_campaign_data = new Model_Campaign_Data();
		$m_campaign_data->campaign_id = $m_campaign->id;
		$m_campaign_data->save();

		$m_pw_order = new Model_Pitch_Order();
		$m_pw_order->campaign_id = $m_campaign->id;
		$m_pw_order->date_created = $date_now_str;
		$m_pw_order->status = Model_Pitch_Order::STATUS_NOT_ASSIGNED;
		$m_pw_order->beat_1_id = @$pw_raw_data->beat_1_id;
		$m_pw_order->keyword = $pw_raw_data->keyword;
		$m_pw_order->city = @$pw_raw_data->city;
		$m_pw_order->state_id = @$pw_raw_data->state_id;
		$m_pw_order->order_type = $pw_raw_data->order_type;

		// set the distribution (other than national as required pay)
		// * will set a distribution of NULL for writing only
		$m_pw_order->distribution = Model_Pitch_Order::DISTRIBUTION_LOCAL;
		if ($pw_raw_data->distribution != Model_Pitch_Order::DISTRIBUTION_NATIONAL)
			$m_pw_order->distribution = $pw_raw_data->distribution;

		$m_pw_order->pitch_highlight = $pw_raw_data->pitch_highlight;
		$m_pw_order->additional_comments = $pw_raw_data->additional_comments;
		$m_pw_order->delivery = Model_Pitch_Order::DELIVERY_STANDARD;
		$m_pw_order->save();

		if ($pw_raw_data->order_type === Model_Pitch_Session::ORDER_TYPE_OUTREACH)
		{
			$m_pitch_list = new Model_Pitch_List();
			$m_pitch_list->pitch_order_id = $m_pw_order->id;
			$m_pitch_list->status = Model_Pitch_List::STATUS_NOT_ASSIGNED;
			$m_pitch_list->save();		
		}
		
		$m_pw_session->pitch_order_id = $m_pw_order->id;
		$m_pw_session->save();
		
		return $m_pw_order;
	} 

	
}

?>