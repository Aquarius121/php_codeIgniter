<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Pitch_Controller extends Manage_Base { 

	const LISTING_SIZE = 10;

	protected $steps = array(
		1 => 'step_1', // content selection
		2 => 'step_2', // industry selection
		3 => 'step_3', // local/national reach
		4 => 'step_4', // pitch
		5 => 'step_5' // review
	);

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Pitch Wizard';
	}

	public function is_editable($status)
	{
		if ($status == Model_Pitch_Order::STATUS_NOT_ASSIGNED ||
			$status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE) return true;
		if ( ! $status) return true;
		return false;
	}

	public function index()
	{
		$this->redirect('manage/contact/pitch/process');
	}

	public function process($pw_session_id = null, $step = 1, $is_review = false)
	{		
		if ( ! ($m_pw_session = Model_Pitch_Session::find($pw_session_id)))
		{			
			$m_pw_session = Model_Pitch_Session::create();
			$m_pw_session->company_id = $this->newsroom->company_id;
			$m_pw_session->save();
			
			$url = "manage/contact/pitch/process/{$m_pw_session->id}/1";
			$this->redirect(gstring($url));
		}

		if ($m_pw_session->company_id != $this->newsroom->company_id)
			$this->denied();

		$this->vd->is_review = $is_review;
		$this->vd->m_pw_session = $m_pw_session;
		$this->vd->pw_raw_data = $m_pw_session->raw_data();
		if ( ! $this->vd->pw_raw_data)
			$this->vd->pw_raw_data = new stdClass();

		$this->vd->m_pw_order = null;

		if ($m_pw_order = Model_Pitch_Order::find($m_pw_session->pitch_order_id))
		{
			$this->vd->m_pw_order = $m_pw_order;
			$is_editable = $this->is_editable($m_pw_order->status);
			$this->vd->can_submit = 1;

			if ( ! $is_editable)
			{
				$feedback = new Feedback('warning');
				$feedback->set_title('Locked!');
				$feedback->set_text('Order details have been sent to the writer.');
				$feedback->add_text('You cannot submit further details at this time.');
				$this->add_feedback($feedback);
				$this->redirect('manage/contact/campaign/all');
			}
		}

		$step = (int) $step;
		$step = $this->steps[$step];
		$this->$step($m_pw_session);
	}

	protected function step_1()
	{
		if ($this->input->post('is_continue'))
		{
			$m_pw_session = $this->vd->m_pw_session;
			$pw_raw_data = $this->vd->pw_raw_data;
			$m_pw_session->content_id = $pw_raw_data->content_id 
				= $this->input->post('content');
			$m_pw_session->raw_data($pw_raw_data);
			$m_pw_session->save();

			// continue to step 2
			$url = "manage/contact/pitch/process/{$m_pw_session->id}/2";
			$this->redirect(gstring($url));
		}

		$sql = "SELECT * FROM nr_content 
			WHERE company_id = ?
			AND type in (?, ?, ?)
			AND is_published = 1
			ORDER BY date_publish DESC";
				
		$db_result = $this->db->query($sql, array($this->newsroom->company_id,
			Model_Content::TYPE_PR, Model_Content::TYPE_EVENT,
			Model_Content::TYPE_NEWS));

		$results = Model_Content::from_db_all($db_result);
		$this->vd->content = $results;

		$this->vd->step = 1;
		$this->load->view('manage/header');
		$this->load->view('manage/contact/pitch/step_1');
		$this->load->view('manage/footer');
	}

	protected function step_2()
	{
		$m_pw_session = $this->vd->m_pw_session;

		if ($this->input->post('is_continue'))
		{
			$pw_raw_data = $this->vd->pw_raw_data;
			$pw_raw_data->order_type = $this->input->post('order_type');
			$pw_raw_data->beat_1_id = $this->input->post('beat_1_id');
			$pw_raw_data->beat_2_id = $this->input->post('beat_2_id');
			$pw_raw_data->city = $this->input->post('city');
			$pw_raw_data->state_id = $this->input->post('state_id');
			$pw_raw_data->distribution = $this->input->post('distribution');
			$m_pw_session->raw_data($pw_raw_data);
			$m_pw_session->save();

			// continue to step 3
			$url = "manage/contact/pitch/process/{$m_pw_session->id}/3";
			$this->redirect($url);
		}

		if ($m_pw_session->pitch_order_id)
			$this->vd->is_already_submitted = 1;

		$credit_items = Credit::locate_store_items_for_user(Auth::user());
		$this->vd->media_outreach_item = $credit_items->common_credits[Credit::TYPE_MEDIA_OUTREACH];
		$this->vd->pitch_writing_item = $credit_items->common_credits[Credit::TYPE_PITCH_WRITING];
		$this->vd->media_outreach_credits = Model_Limit_Common_Held::find_user(Auth::user(), Credit::TYPE_MEDIA_OUTREACH);
		$this->vd->pitch_writing_credits = Model_Limit_Common_Held::find_user(Auth::user(), Credit::TYPE_PITCH_WRITING);
		
		$second_cat_item = Model_Item::find_slug(Model_Pitch_Order::ITEM_PW_SECOND_INDUSTRY_SLUG);
		$this->vd->second_cat_item = $second_cat_item;
		
		$beats = Model_Beat::list_all_beats_by_group();
		$this->vd->beats = $beats;
		$states = Model_State::find_all();
		$this->vd->states = $states;
		
		$nation_dist_item = Model_Item::find_slug(Model_Pitch_Order::ITEM_PW_NATIONAL_DIST_SLUG);
		$this->vd->nation_dist_item = $nation_dist_item;

		$this->vd->step = 2;
		$this->load->view('manage/header');
		$this->load->view('manage/contact/pitch/step_2');
		$this->load->view('manage/footer');
	}

	protected function step_3()
	{
		if ($this->input->post('is_continue'))
		{
			$m_pw_session = $this->vd->m_pw_session;
			$pw_raw_data = $this->vd->pw_raw_data;			
			$pw_raw_data->keyword = $this->input->post('keyword');
			$pw_raw_data->pitch_highlight = $this->input->post('pitch_highlight');
			$pw_raw_data->additional_comments = $this->input->post('additional_comments');
			$m_pw_session->raw_data($pw_raw_data);
			$m_pw_session->save();

			// continue to step 4
			$url = "manage/contact/pitch/process/{$m_pw_session->id}/4";
			$this->redirect($url);
		}

		$this->vd->step = 3;
		$this->load->view('manage/header');
		$this->load->view('manage/contact/pitch/step_3');
		$this->load->view('manage/footer');
	}

	protected function step_4()
	{
		$m_pw_session = $this->vd->m_pw_session;
		$pw_raw_data = $this->vd->pw_raw_data;

		$pw_item = Model_Item::find('slug', Model_Pitch_Order::ITEM_PW_ORDER_SLUG);
		$rush_item = Model_Item::find('slug', Model_Pitch_Order::ITEM_PW_RUSH_DELIVERY_SLUG);
		$second_cat_item = Model_Item::find('slug', Model_Pitch_Order::ITEM_PW_SECOND_INDUSTRY_SLUG);
		$nation_dist_item = Model_Item::find('slug', Model_Pitch_Order::ITEM_PW_NATIONAL_DIST_SLUG);

		if ($pw_raw_data->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH)
		{
			$credit_items = Credit::locate_store_items_for_user(Auth::user());
			$credit_item = $credit_items->common_credits[Credit::TYPE_MEDIA_OUTREACH];
			$held_credits = Model_Limit_Common_Held::find_user(Auth::user(), Credit::TYPE_MEDIA_OUTREACH);
			$this->vd->held_credits = $held_credits;
		}
		else // Model_Pitch_Session::ORDER_TYPE_WRITING
		{
			$credit_items = Credit::locate_store_items_for_user(Auth::user());
			$credit_item = $credit_items->common_credits[Credit::TYPE_PITCH_WRITING];
			$held_credits = Model_Limit_Common_Held::find_user(Auth::user(), Credit::TYPE_PITCH_WRITING);
			$this->vd->held_credits = $held_credits;
		}
		
		// checkout button pressed. take to checkout
		if ($this->input->post('is_continue')) 
		{
			$pw_raw_data->delivery = $this->input->post('delivery');
			$m_pw_session->raw_data($pw_raw_data);
			$m_pw_session->save();
			
			$track = new stdClass();
			$track->pitch_session_id = $m_pw_session->id;

			$cart = Cart::instance();
			$cart->reset();

			$pitch_cart_entry = $cart->add($pw_item);
			$pitch_cart_entry->track = $track;
			$pitch_cart_entry->callback = sprintf(
				'manage/contact/pitch/process/%s/5', 
				$m_pw_session->id);

			// * the pitch item is just used as a placeholder
			// and the real item is then attached (hidden) to it
			// * the price/name is updated from the real item
			$pitch_cart_entry->price = 0;
			$pitch_cart_entry->name = $credit_item->name;
			
			// only need to attach the credit
			// if not already available
			if (!$held_credits->available())
			{
				$credit_atd = $pitch_cart_entry->attach($credit_item);
				$credit_atd->hidden = true;
			}
			
			if ( ! empty($pw_raw_data->beat_2_id))
			{		
				$pitch_cart_entry1 = $cart->add($second_cat_item);	
				$pitch_cart_entry1->track = $track;
			}
			
			if ($pw_raw_data->distribution == Model_Pitch_Order::DISTRIBUTION_NATIONAL)
			{
				$pitch_cart_entry = $cart->add($nation_dist_item);
				$pitch_cart_entry->track = $track;
			}
			
			if ($pw_raw_data->delivery == Model_Pitch_Order::DELIVERY_RUSH)
			{
				$pitch_cart_entry = $cart->add($rush_item);
				$pitch_cart_entry->track = $track;
			}

			// nothing to order so activate now
			if ($cart->total_with_discount() == 0)
			{
				// invoke order events for items
				foreach ($cart->items() as $cart_item)
				{
					// run the item's order event to activate
					$iella_event = new Iella_Event();
					$iella_event->data->cart_item = $cart_item;
					$iella_event->data->user = Auth::user();
					$iella_event->data->component_set = null;
					$iella_event->data->transaction = null;
					$iella_event->emit($cart_item->item()->order_event);
				}

				// thank you messages
				$feedback = new Feedback('success');
				$feedback->set_title('Thanks!');
				$feedback->set_text('Your order has been confirmed.');
				$this->add_feedback($feedback);

				// redirect back to review order page
				$this->redirect($pitch_cart_entry->callback);
			}
			
			$cart->save();
			// order/checkout is order without menu
			$this->redirect('manage/order/checkout/auto');
			return;
		}

		// edit-save 
		if ($this->input->post('is_save')) 
		{
			$pw_raw_data->delivery = $this->input->post('delivery');
			$m_pw_session->raw_data($pw_raw_data);
			$m_pw_session->save();
			$this->update_order();

			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Details updated successfully.');
			$this->add_feedback($feedback);

			// continue to the next steps
			$url = "manage/contact/campaign/all";
			$this->redirect($url);
			return;
		}

		if ($m_pw_session->pitch_order_id)
			$this->vd->is_already_submitted = 1;

		if ( ! empty($pw_raw_data->content_id))
		{
			$m_content = Model_Content::find($pw_raw_data->content_id);
			$this->vd->m_content = $m_content;
		}

		if ( ! empty($pw_raw_data->beat_1_id))
		{
			$beat_1 = Model_Beat::find($pw_raw_data->beat_1_id);
			$this->vd->beat_1_name = $beat_1->name;
		}

		if ( ! empty($pw_raw_data->beat_2_id))
		{
			$beat_2 = Model_Beat::find($pw_raw_data->beat_2_id);
			$this->vd->beat_2_name = $beat_2->name;
		}

		if ( ! empty($pw_raw_data->state_id))
		{
			$state = Model_State::find($pw_raw_data->state_id);
			$this->vd->state_name = $state->name;
		}

		if ( ! empty($pw_raw_data->distribution))
			$this->vd->distribution_title = Model_Pitch_Order::distribution_title($pw_raw_data->distribution);

		if ($held_credits->available())
		{
			$credit_available = $this->vd->credit_available = true;
			$pw_item->name = $credit_item->name;
			$pw_item->price = 0;
		}
		else
		{
			$credit_available = $this->vd->credit_available = false;
			$pw_item->price = $credit_item->price;
			$pw_item->name = $credit_item->name;
		}

		$this->vd->pw_item = $pw_item;
		$this->vd->rush_item = $rush_item;
		$this->vd->second_cat_item = $second_cat_item;
		$this->vd->nation_dist_item = $nation_dist_item;

		$this->vd->date_after_3_days = Date::days(+3);
		$this->vd->date_after_24_hours = Date::hours(+24);

		$sub_total = $pw_item->price;

		if ( ! empty($pw_raw_data->beat_2_id))
			$sub_total += $second_cat_item->price;
		
		if (isset($pw_raw_data->distribution) && $pw_raw_data->distribution == 
			Model_Pitch_Order::DISTRIBUTION_NATIONAL)
			$sub_total += $nation_dist_item->price;
			
		if (isset($pw_raw_data->delivery) && $pw_raw_data->delivery == 
			Model_Pitch_Order::DELIVERY_RUSH)
			$sub_total += $rush_item->item;
			
		$this->vd->sub_total = $sub_total;

		$this->vd->step = 4;
		$this->load->view('manage/header');
		$this->load->view('manage/contact/pitch/step_4');
		$this->load->view('manage/footer');
	}

	protected function step_5()
	{
		$m_pw_session = $this->vd->m_pw_session;
		$pw_raw_data = $this->vd->pw_raw_data;
		$m_pw_order = $this->vd->m_pw_order;
		
		if ( ! empty($pw_raw_data->content_id))
		{
			$m_content = Model_Content::find($pw_raw_data->content_id);
			$this->vd->m_content = $m_content;
		}

		if ( ! empty($pw_raw_data->beat_1_id))
		{
			$beat_1 = Model_Beat::find($pw_raw_data->beat_1_id);
			$this->vd->beat_1_name = $beat_1->name;
		}

		if ( ! empty($pw_raw_data->beat_2_id))
		{
			$beat_2 = Model_Beat::find($pw_raw_data->beat_2_id);
			$this->vd->beat_2_name = $beat_2->name;
		}

		if ( ! empty($pw_raw_data->state_id))
		{
			$state = Model_State::find($pw_raw_data->state_id);
			$this->vd->state_name = $state->name;
		}

		if ( ! empty($pw_raw_data->distribution))
			$this->vd->distribution_title = Model_Pitch_Order::distribution_title
				($pw_raw_data->distribution);

		$this->vd->date_after_3_days = Date::days(+3, Date::utc($m_pw_order->date_created));
		$this->vd->date_after_24_hours = Date::hours(+24, Date::utc($m_pw_order->date_created));

		$this->load->view('manage/header');
		$this->load->view('manage/contact/pitch/step_5');
		$this->load->view('manage/footer');
	}

	protected function update_order()
	{
		$m_pw_session = $this->vd->m_pw_session;
		$pw_raw_data = $this->vd->pw_raw_data;
		$m_pw_order = $this->vd->m_pw_order;

		$m_content = Model_Content::find($m_pw_session->content_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_campaign->name = 'Pitch: ' . strtoupper($m_content->title);
		$m_campaign->content_id = $m_content->id;
		$m_campaign->date_send = Date::days(+4)->format(Date::FORMAT_MYSQL);
		$m_campaign->save();

		$m_pw_order->beat_1_id = $pw_raw_data->beat_1_id;
		$m_pw_order->beat_2_id = value_or_null($pw_raw_data->beat_2_id);
		$m_pw_order->keyword = $pw_raw_data->keyword;
		$m_pw_order->city = $pw_raw_data->city;
		$m_pw_order->state_id = $pw_raw_data->state_id;
		$m_pw_order->distribution = $pw_raw_data->distribution;
		$m_pw_order->pitch_highlight = $pw_raw_data->pitch_highlight;
		$m_pw_order->additional_comments = $pw_raw_data->additional_comments;
		$m_pw_order->delivery = $pw_raw_data->delivery;
		
		if ($m_pw_order->status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE)
		{
			$m_pw_order->status = Model_Pitch_Order::STATUS_CUSTOMER_REVISE_DETAILS;
			$process = Model_Pitch_Writing_Process::PROCESS_CUSTOMER_REVISE_DETAILS;
			Model_Pitch_Writing_Process::create_and_save($m_pw_session->pitch_order_id, $process);
			$pw_mailer = new Pitch_Wizard_Mailer();
			$pw_mailer->customer_revised_details($m_pw_session->pitch_order_id);
		}

		$m_pw_order->save();
	}

}

?>