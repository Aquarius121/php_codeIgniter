<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('shared/process_results_distribution_bundle_trait');
load_controller('shared/process_results_release_plus_trait');
load_controller('shared/media_database_beats_trait');
load_controller('manage/publish/content');
load_controller('shared/upgrade');

class PR_Controller extends Content_Base { 

	protected $content_type = Model_Content::TYPE_PR;

	use Upgrade_Trait;
	use Media_Database_Beats_Trait;
	use Process_Results_Release_Plus_Trait;
	use Process_Results_Distribution_Bundle_Trait;

	const WORD_COUNT_PATTERN = '#([a-z0-9]\S*(\s+[^a-z0-9]*|$))#i';

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Distribution';
		$this->vd->title[] = 'Press Releases';
	}
	
	protected function process_results($results)
	{
		$results = parent::process_results($results);
		$results = $this->process_results_distribution_bundle($results);
		$results = $this->process_results_release_plus($results);
		return $results;
	}
	
	// this is a bit bad - is there better way?
	public function conf($name, $index = null)
	{
		// use a different conf for free users
		if ($name === 'press_release_links_basic')
			if (Auth::user()->is_free_user())
				$name = 'press_release_links_free';
			
		return parent::conf($name, $index);
	}

	public function index()
	{
		$this->redirect('manage/publish/pr/all');
	}
	
	public function edit($content_id = null)
	{		
		$vars = parent::edit($content_id);
		extract($vars, EXTR_SKIP);

		// external content is handled by a different editor
		if ($m_content && $m_content->is_external)
		{
			// Removed the redirect from here
			// because for delete operation it 
			// was redirecting to edit/external

			$this->edit_external($m_content->id);
			return;
		}

		if ($m_content && 
			 $m_content->is_premium &&
			($m_content->is_published ||
		    $m_content->is_under_review ||
		    $m_content->is_approved))
		{
			$requires_downstream_update = 
			$this->vd->requires_downstream_update = 
				   $m_content->is_approved 
				|| $m_content->is_under_review
				|| $m_content->is_published;

			$feedback = new Feedback('warning');
			$feedback->set_title('Warning!');
			if ($requires_downstream_update)
				  $feedback->set_text('This PR has been processed.');
			else $feedback->set_text('This PR is being processed.');
			$feedback->add_text('You should contact our support team to 
				confirm that changes are reflected downstream.');
			$this->use_feedback($feedback);
		}
		
		$company_profile = Model_Company_Profile::find($this->newsroom->company_id);
		$this->vd->company_profile = $company_profile;
		$this->vd->default_location = null;
		
		if (!$company_profile)
		{
			// load profile warning message
			$feedback_view = 'manage/publish/partials/feedback/profile_warning';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
		}

		$press_contact = Model_Company_Contact::find($this->newsroom->company_contact_id);
		$this->vd->press_contact = $press_contact;
		
		$items = $this->locate_credits_for_access_level();
		$this->vd->item_premium = $items->pr_credit;
		$this->vd->item_premium_plus = $items->all[Credit::TYPE_PREMIUM_PLUS];
		$this->vd->item_premium_plus_state = $items->all[Credit::TYPE_PREMIUM_PLUS_STATE];
		$this->vd->item_premium_plus_national = $items->all[Credit::TYPE_PREMIUM_PLUS_NATIONAL];
		$this->vd->item_premium_financial = $items->all[Credit::TYPE_PREMIUM_FINANCIAL];
		$this->vd->item_pps_extra_100_words = $items->all[Store::ITEM_PPS_EXTRA_100_WORDS];
		$this->vd->item_ppn_extra_100_words = $items->all[Store::ITEM_PPN_EXTRA_100_WORDS];
		$this->vd->item_prn_image_distribution = $items->all[Store::ITEM_PRN_IMAGE_DISTRIBUTION];
		$this->vd->item_prn_video_distribution = $items->all[Store::ITEM_PRN_VIDEO_DISTRIBUTION];
		
		$this->vd->credits_basic = Auth::user()->pr_credits_basic();
		$this->vd->credits_premium = Auth::user()->pr_credits_premium();
		$this->vd->credits_premium_plus = Model_Limit_Common_Held::find_user(
			Auth::user(), Credit::TYPE_PREMIUM_PLUS)->available();
		$this->vd->credits_premium_plus_state = Model_Limit_Common_Held::find_user(
			Auth::user(), Credit::TYPE_PREMIUM_PLUS_STATE)->available();
		$this->vd->credits_premium_plus_national = Model_Limit_Common_Held::find_user(
			Auth::user(), Credit::TYPE_PREMIUM_PLUS_NATIONAL)->available();
		$this->vd->credits_premium_financial = Model_Limit_Common_Held::find_user(
			Auth::user(), Credit::TYPE_PREMIUM_FINANCIAL)->available();

		if ($m_content && $m_content->id)
			$this->vd->mcd_extras = Model_Content_Distribution_Extras::find($m_content->id);
		if (!$this->vd->mcd_extras)
			$this->vd->mcd_extras = new Model_Content_Distribution_Extras();
		
		$beats = $this->list_beats_non_zero();
		$this->vd->outreach_industries = $beats;

		$m_cbc = Model_Content_Bundled_Campaign::find($content_id);
		$this->vd->m_content_bundled_campaign = $m_cbc;
		
		if ($m_cbc && $m_cbc->campaign_id)
		{
			$m_campaign = Model_Campaign::find($m_cbc->campaign_id);
			$this->vd->m_campaign = $m_campaign;
		}

		if ($m_content && Auth::is_admin_mode())
		{
			$mc = $m_content;
			$mcd = Model_Content_Data::from_object($mc);
			$mpr = Model_PB_PR::from_object($mc);
			$mb = $mc->distribution_bundle();

			if (($match = $this->cfhc_litigation__test($mc, $mcd, $mpr, $mb)) !== false)
			{
				$feedback = new Feedback('info');
				$feedback->set_html('<strong>Possible litigation detected!</strong>');
				$feedback->add_text('The following terms were found: ', true);
				$feedback->add_text(comma_separate($match, true), true);
				$this->use_feedback($feedback);
			}

			if (($match = $this->cfhc_publicly_traded__test($mc, $mcd, $mpr, $mb)) !== false)
			{
				$feedback = new Feedback('info');
				$feedback->set_html('<strong>Possible public company detected!</strong>');
				$feedback->add_text('The following terms were found: ', true);
				$feedback->add_text(comma_separate($match, true), true);
				$this->use_feedback($feedback);
			}
		}

		$this->load->view('manage/header');
		$this->load->view('manage/publish/pr-edit');
		$this->load->view('manage/footer');
	}
	
	public function edit_save()
	{
		$vars = parent::edit_save('pr');
		extract($vars, EXTR_SKIP);
		
		$location                  = value_or_null($post['location']);
		$outreach_email_country    = value_or_null($post['outreach_email_country']);
		$outreach_email_send       = value_or_null($post['outreach_email_send']);
		$source                    = value_or_null($post['source']);		
		$stored_file_id_1          = value_or_null($post['stored_file_id_1']);
		$stored_file_id_2          = value_or_null($post['stored_file_id_2']);
		$stored_file_name_1        = value_or_null($post['stored_file_name_1']);
		$stored_file_name_2        = value_or_null($post['stored_file_name_2']);
		$web_video_id              = value_or_null($post['web_video_id']);
		$web_video_provider        = value_or_null($post['web_video_provider']);
		$is_publish_date_selected  = value_or_null($post['is_publish_date_selected']);
		$press_contact_first_name  = value_or_null($post['press_contact_first_name']);
		$press_contact_last_name   = value_or_null($post['press_contact_last_name']);
		$press_contact_email       = value_or_null($post['press_contact_email']);
		$press_contact_phone       = value_or_null($post['press_contact_phone']);

		$press_contact_provided =
			$press_contact_email ||
			$press_contact_first_name ||
			$press_contact_last_name;

		if ($is_preview)
		{
			if ($press_contact_provided)
			{
				$press_contact = new Model_Company_Contact();
				$press_contact->first_name = $press_contact_first_name;
				$press_contact->last_name = $press_contact_last_name;
				$press_contact->email = $press_contact_email;
				$press_contact->phone = $press_contact_phone;
				Detached_Session::write('nr_contact', $press_contact);
			}

			// construct model so that
			// we can process the files/video
			$m_pb_pr = new Model_PB_PR();
			$m_pb_pr->stored_file_name_1 = $stored_file_name_1;
			$m_pb_pr->stored_file_name_2 = $stored_file_name_2;
			$m_pb_pr->web_video_provider = $web_video_provider;
			$m_pb_pr->web_video_id = $web_video_id;
			$m_pb_pr->outreach_email_send = $outreach_email_send;
			$m_pb_pr->clean_files();
			$m_pb_pr->clean_video();
			
			$m_content = Detached_Session::read('m_content');
			$m_content->web_video_provider = $m_pb_pr->web_video_provider;
			$m_content->web_video_id = $m_pb_pr->web_video_id;
			$m_content->stored_file_id_1 = $stored_file_id_1;
			$m_content->stored_file_id_2 = $stored_file_id_2;
			$m_content->stored_file_name_1 = $m_pb_pr->stored_file_name_1;
			$m_content->stored_file_name_2 = $m_pb_pr->stored_file_name_2;
			$m_content->location = $location;
			$m_content->source = $source;

			Detached_Session::write('m_content', $m_content);
			return;
		}

		if ($is_new_content)
		     $m_pb_pr = new Model_PB_PR();
		else $m_pb_pr = Model_PB_PR::find($m_content->id);			
		if (!$m_pb_pr) $m_pb_pr = new Model_PB_PR();
		
		$m_pb_pr->is_publish_date_selected = $is_publish_date_selected;
		$m_pb_pr->web_video_provider = $web_video_provider;
		$m_pb_pr->web_video_id = $web_video_id;
		$m_pb_pr->stored_file_id_1 = $stored_file_id_1;
		$m_pb_pr->stored_file_id_2 = $stored_file_id_2;
		$m_pb_pr->stored_file_name_1 = $stored_file_name_1;
		$m_pb_pr->stored_file_name_2 = $stored_file_name_2;
		$m_pb_pr->outreach_email_send = $outreach_email_send;
		$m_pb_pr->outreach_email_country = $outreach_email_country;
		$m_pb_pr->location = $location;
		$m_pb_pr->source = $source;
		$m_pb_pr->content_id = $m_content->id;

		if ($press_contact_provided)
		{
			$press_contact = Model_Company_Contact::find($this->newsroom->company_contact_id);

			if ($press_contact && $press_contact->email == $press_contact_email)
			{
				$press_contact->first_name = $press_contact_first_name;
				$press_contact->last_name = $press_contact_last_name;
				$press_contact->email = $press_contact_email;
				$press_contact->phone = $press_contact_phone;
				$press_contact->save();
			}
			else
			{
				$press_contact = new Model_Company_Contact();
				$press_contact->company_id = $this->newsroom->company_id;
				$press_contact->first_name = $press_contact_first_name;
				$press_contact->last_name = $press_contact_last_name;
				$press_contact->email = $press_contact_email;
				$press_contact->phone = $press_contact_phone;
				$press_contact->save();
				$press_contact->name_to_slug();
				$press_contact->save();

				$this->newsroom->company_contact_id = $press_contact->id;
				$this->newsroom->save();
			}
		}

		if (Auth::is_admin_online())
		{
			$m_pb_pr->is_nofollow_enabled = (bool) 
				$this->input->post('is_nofollow_enabled');
		}

		$m_pb_pr->clean_video();
		$m_pb_pr->clean_files();
		$m_pb_pr->save();
		
		// update the dashboard progress bar 
		Model_Bar::done('dashboard', 'pr-submission');

		// record if we cleared cart
		// so we dont do it twice
		$cart_updated = false;
		$cart = Cart::instance();
		$cart->reset();

		$m_user = Auth::user();
		$m_bundle = $m_content->distribution_bundle();
		$items = Store::locate_store_items_for_user($m_user);		

		$mcd_extras = Model_Content_Distribution_Extras::find($m_content->id);
		if (!$mcd_extras) $mcd_extras = new Model_Content_Distribution_Extras();
		$mcd_extras->content_id = $m_content->id;

		if (!Auth::is_admin_mode()
			&& !$m_content->is_draft
			&& $m_content->is_consume_locked())
		     $send_release_plus_update = true;
		else $send_release_plus_update = false;

		if ($m_content->is_consume_locked() && 
		    $this->input->post('distribution_bundle') && 
		    $this->input->post('distribution_bundle') != $m_bundle->bundle)
		{
			$feedback = new Feedback('warning');
			$feedback->set_title('Warning!');
			$feedback->set_text('Could not modify distribution as the press 
				release has already been scheduled. Please contact our staff 
				if you wish to change it.');
			$this->add_feedback($feedback);
		}
		else
		{
			if ($this->input->post('distribution_bundle') && 
			   ($this->input->post('distribution_bundle') != $m_bundle->bundle ||
			    $m_bundle->is_new()))
			{
				$m_bundle->disable();
				$m_bundle->delete();

				$m_bundle = new Model_Content_Distribution_Bundle();
				$m_bundle->content_id = $m_content->id;
				$m_bundle->bundle = $this->input->post('distribution_bundle');
				$m_bundle->save();
				$m_bundle->enable();

				$m_content->is_credit_locked = 0;
				$m_content->save();
			}
			
			if (($distcust = $this->input->post('distcust')))
			{
				$distcust = Raw_Data::from_array($distcust);
				$m_bundle->customize($distcust);
			}
		}

		if (!Auth::is_admin_mode())
		{
			// check if we need to hold 
			// for admin review
			$this->check_for_hold_criteria(
				$m_content, $m_content_data, 
				$m_pb_pr, $m_bundle);
		}

		$credits = 0;
		$credits_item = null;

		switch ($m_bundle->bundle)
		{
			case Model_Content::DIST_BASIC:
				$credits = Auth::user()->pr_credits_basic();
				$credits_item = null;
				break;
			case Model_Content::DIST_PREMIUM:
				$credits = Auth::user()->pr_credits_premium();
				$credits_item = $items->pr_credit;

				// TODO: use common/held credits globally
				// temporary fix for PR credit not being 
				// compatable with distribution bundle
				$credits_item->order_event = 'item_order_premium';
				break;
			case Model_Content::DIST_PREMIUM_PLUS:
				$credits = Model_Limit_Common_Held::find_user(
					Auth::user(), Credit::TYPE_PREMIUM_PLUS)->available();
				$credits_item = $items->other[Credit::TYPE_PREMIUM_PLUS];
				break;
			case Model_Content::DIST_PREMIUM_PLUS_STATE:
				$credits = Model_Limit_Common_Held::find_user(
					Auth::user(), Credit::TYPE_PREMIUM_PLUS_STATE)->available();
				$credits_item = $items->other[Credit::TYPE_PREMIUM_PLUS_STATE];
				break;
			case Model_Content::DIST_PREMIUM_PLUS_NATIONAL:
				$credits = Model_Limit_Common_Held::find_user(
					Auth::user(), Credit::TYPE_PREMIUM_PLUS_NATIONAL)->available();
				$credits_item = $items->other[Credit::TYPE_PREMIUM_PLUS_NATIONAL];
				break;
			case Model_Content::DIST_PREMIUM_FINANCIAL:
				$credits = Model_Limit_Common_Held::find_user(
					Auth::user(), Credit::TYPE_PREMIUM_FINANCIAL)->available();
				$credits_item = $items->other[Credit::TYPE_PREMIUM_FINANCIAL];
				break;
		}

		$admin_force_schedule = Auth::is_admin_online() && 
			$this->input->post('force_schedule');

		// extras for PR Newswire microlists
		if ($m_bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE) &&
			($mcd_extras->id || $this->input->post('microlist')))
		{
			$available_microlists = Raw_Data::from_array(PRNewswire_Distribution::microlists());
			$selected_microlists = ($this->input->post('microlist') ?: array());
			$ml_extras = $mcd_extras->filter($mcd_extras::TYPE_MICROLIST);
			
			foreach ($ml_extras as $uuid => $extra)
			{
				if (!in_array($extra->data->item_code, $selected_microlists) &&
				    !(isset($extra->data->is_confirmed) && $extra->data->is_confirmed))
					$mcd_extras->remove($uuid);
			}

			foreach ($selected_microlists as $code)
			{
				if (!$code) continue;
				$microlist = $available_microlists[$code];
				$is_selected = array_reduce($ml_extras, 
					function($carry, $extra) use ($code) {
						return $carry || $extra->data->item_code === $code;
					});

				if ($is_selected) continue;
				$mcd_extras->add($microlist, 
					$mcd_extras::TYPE_MICROLIST);
			}

			$mcd_extras->save();

			if (!$this->input->post('is_draft'))
			{
				foreach ($mcd_extras->filter($mcd_extras::TYPE_MICROLIST) as $extra)
				{
					if (isset($extra->data->is_confirmed) && 
						$extra->data->is_confirmed)
						continue;

					// we must reload from database, not saved raw data
					// in order to ensure the item is still valid
					$item = Model_Item::find($extra->data->item->id);
					$cart_item = $cart->add($item, 1);
					$cart_item->track->content_id = $m_content->id;
					$cart_item->track->extra_uuid = $extra->uuid;
					$cart_item->is_quantity_unlocked = false;
					$cart_updated = true;
				}
			}
		}

		// extras for image distribution to PR Newswire 
		if ($m_bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE) && 
			($mcd_extras->id || isset($post['image_meta_data']['prn'])))
		{
			$item_quantity = 0;
			$selected_images = array();
			foreach ($images as $k => $image)
				if ($post['image_meta_data']['prn'][$k])
					$selected_images[(int) $image->id] = $image;

			$pi_extras = $mcd_extras->filter($mcd_extras::TYPE_PRN_IMAGES);
			$pi_extra = count($pi_extras) 
				? array_values($pi_extras)[0] 
				: $mcd_extras->add(new Raw_Data(), $mcd_extras::TYPE_PRN_IMAGES);
			$pi_extra->data = Raw_Data::from_object($pi_extra->data);
			$pi_extra->data->selected = array_keys($selected_images);

			if (!$pi_extra->data->confirmed)
				  $pi_extra->data->confirmed = array();
			if (!$pi_extra->data->credits)
				  $pi_extra->data->credits = 0;

			foreach ($pi_extra->data->confirmed as $id)
			{
				if (!in_array($id, $pi_extra->data->selected))
				{
					array_remove_all($pi_extra->data->confirmed, $id);
					$pi_extra->data->credits++;
				}
			}

			foreach ($pi_extra->data->selected as $id)
			{
				if (!in_array($id, $pi_extra->data->confirmed))
				{
					if ($pi_extra->data->credits > 0)
					{
						$pi_extra->data->confirmed[] = $id;
						$pi_extra->data->credits--;
					}
					else if (!$this->input->post('is_draft'))
					{
						$item_quantity++;
					}
				}
			}

			if ($item_quantity && !$m_content->is_draft)
			{
				$item = $items->unlisted[Store::ITEM_PRN_IMAGE_DISTRIBUTION];
				$cart_item = $cart->add($item, $item_quantity);
				$cart_item->track->content_id = $m_content->id;
				$cart_item->track->extra_uuid = $pi_extra->uuid;
				$cart_item->is_quantity_unlocked = false;
				$cart_updated = true;
			}

			$mcd_extras->set($pi_extra->uuid, $pi_extra);
			$mcd_extras->save();
		}

		// extras for video distribution to PR Newswire 
		if ($m_bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE))
		{
			$pv_checked = isset($post['prn_video_distribution']) 
				              && $post['prn_video_distribution'];

			$pv_extras = $mcd_extras->filter($mcd_extras::TYPE_PRN_VIDEO);
			$pv_extra = count($pv_extras) 
				? array_values($pv_extras)[0] 
				: $mcd_extras->add(new Raw_Data(), $mcd_extras::TYPE_PRN_VIDEO);
			$pv_extra->data = Raw_Data::from_object($pv_extra->data);		
			$pv_extra->data->is_selected = $pv_checked || $pv_extra->data->is_confirmed;

			if (!$pv_extra->data->is_confirmed && !$m_content->is_draft && $pv_checked)
			{
				$pv_extra->data->is_confirmed = false;
				$item = $items->unlisted[Store::ITEM_PRN_VIDEO_DISTRIBUTION];
				$cart_item = $cart->add($item, 1);
				$cart_item->track->content_id = $m_content->id;
				$cart_item->track->extra_uuid = $pv_extra->uuid;
				$cart_item->is_quantity_unlocked = false;
				$cart_updated = true;
			}

			$mcd_extras->set($pv_extra->uuid, $pv_extra);
			$mcd_extras->save();
		}

		if ($admin_force_schedule)
		{
			$m_content->is_credit_locked = 1;
			$m_content->is_draft = 0;
			$m_content->save();
		}
		else if ($m_content->requires_credit() && !$m_content->is_draft)
		{
			$cart_item = null;
			$consume_credit = false;
			$credits_consumer = null;
			$extra_words_item = null;
			$extra_words_limit = PHP_INT_MAX;

			switch ($m_bundle->bundle)
			{
				case Model_Content::DIST_BASIC:
					$credits_consumer = new PR_Credit_Consumer();
					$credits_consumer->set_held($m_user->m_limit_pr_held_basic());
					$credits_consumer->set_plan($m_user->m_limit_pr_basic());
					break;
				case Model_Content::DIST_PREMIUM:					
					$credits_consumer = new PR_Credit_Consumer();
					$credits_consumer->set_held($m_user->m_limit_pr_held_premium());
					$credits_consumer->set_plan($m_user->m_limit_pr_premium());
					break;
				case Model_Content::DIST_PREMIUM_PLUS:
					$credits_consumer = new Common_Credit_Consumer();
					$credits_consumer->set_held(Model_Limit_Common_Held::find_collection(
						$m_user, Credit::TYPE_PREMIUM_PLUS));
					break;
				case Model_Content::DIST_PREMIUM_PLUS_STATE:
					$credits_consumer = new Common_Credit_Consumer();
					$credits_consumer->set_held(Model_Limit_Common_Held::find_collection(
						$m_user, Credit::TYPE_PREMIUM_PLUS_STATE));
					$extra_words_item = $items->unlisted[Store::ITEM_PPS_EXTRA_100_WORDS];
					$extra_words_limit = PRNewswire_Distribution::included_words(PRNewswire_Distribution::DIST_STATELINE);
					break;
				case Model_Content::DIST_PREMIUM_PLUS_NATIONAL:
					$credits_consumer = new Common_Credit_Consumer();
					$credits_consumer->set_held(Model_Limit_Common_Held::find_collection(
						$m_user, Credit::TYPE_PREMIUM_PLUS_NATIONAL));
					$extra_words_item = $items->unlisted[Store::ITEM_PPN_EXTRA_100_WORDS];
					$extra_words_limit = PRNewswire_Distribution::included_words(PRNewswire_Distribution::DIST_NATIONAL);
					break;
				case Model_Content::DIST_PREMIUM_FINANCIAL:
					$credits_consumer = new Common_Credit_Consumer();
					$credits_consumer->set_held(Model_Limit_Common_Held::find_collection(
						$m_user, Credit::TYPE_PREMIUM_FINANCIAL));
					break;
			}

			if ($credits > 0)
			{
				$consume_credit = true;				
			}
			else if ($credits_item)
			{
				$cart_updated = true;
				$cart_item = $cart->add($credits_item, 1);
				$cart_item->track->content_id = $m_content->id;
				$cart_item->is_quantity_unlocked = false;
			}

			if ($credits_item && 
				 $extra_words_item &&
				 $extra_words_limit)
			{
				$pattern = static::WORD_COUNT_PATTERN;
				$word_count = preg_match_all($pattern, implode(' ', array(
					$m_content->title,
					$m_content_data->summary,
					HTML2Text::plain($m_content_data->content),
				)));
				
				if ($word_count > $extra_words_limit)
				{
					$extra_words = $word_count - $extra_words_limit;
					$quantity = ceil($extra_words / 100);

					$cart_updated = true;
					if (!$cart_item) $cart_item = $cart->add($credits_item, 1, 0);
					$cart_item->track->use_existing_credit = $consume_credit;
					$cart_item->track->content_id = $m_content->id;
					$cart_item->is_quantity_unlocked = false;
					$cart_item->attach(Cart_Item::create($extra_words_item, $quantity));
					$consume_credit = false;
				}
			}

			if ($consume_credit)
			{
				$credits_consumer->consume(1);
				$m_bundle->confirm();
				
				$m_content->is_credit_locked = 1;
				$m_content->is_draft = 0;
				$m_content->save();
			}
			else
			{
				$m_content->is_credit_locked = 0;
				$m_content->is_draft = 1;
				$m_content->save();
			}
		}
		
		// customer wants to include outreach email
		if (   $outreach_email_send 
			&&  $m_content->is_premium
			&& !$this->input->post('is_draft'))
		{
			$contact_limit_for_beats = $this->conf('bundled_email_credits');
			if ($credits_item && isset($credits_item->raw_data_object()->bundled_email_credits))
				$contact_limit_for_beats = $credits_item->raw_data_object()->bundled_email_credits;
			$mo_factory = new Bundled_Media_Outreach_Factory($m_content);
			
			if ($outreach_email_country)
			{
				$mo_factory->set_compiler_callback_beats(function($compiler) use ($outreach_email_country) {
					$compiler->set_countries(array($outreach_email_country));
				});
			}
		
			$mo_factory->set_contact_limit_beats($contact_limit_for_beats);
			$mo_factory->set_beats($beats);
			$m_cbc = $mo_factory->create();
		}

		if ($is_new_scheduled && 
			!$m_content->is_draft &&
		    $dt_date_publish > Date::days(1))
		{
			$sch_n = new Model_Scheduled_Notification();
			$sch_n->related_id = $m_content->id;
			$sch_n->class = Model_Scheduled_Notification::CLASS_CONTENT_SCHEDULED;
			$sch_n->user_id = Auth::user()->id;
			$sch_n->save();
		}

		if (!$this->input->post('is_draft'))
		{
			$this->record_changes($m_content, array(
				'bundle' => $m_bundle,
				'extras' => $mcd_extras,
			));

			// release plus order has been updated, notify admins
			if ($send_release_plus_update)
			{
				if ($comment = $this->input->post('update_comment'))
				{
					$hold = Model_Hold_Data::find_or_create($m_content->id);
					$hold->add_comment($comment);
					$hold->save();
				}

				$releases = Model_Content_Release_Plus::find_all_content($m_content->id);
				$mailer = new Release_Plus_Mailer();
				foreach ($releases as $release)
					$mailer->send_updated($m_content, $release, $comment);
			}
		}

		if ($cart_updated)
		{
			$this->set_redirect('manage/order');
			$cart->save();
		}
	}

	public function edit_external($content_id = null)
	{		
		$vars = parent::edit($content_id);
		extract($vars, EXTR_SKIP);

		$this->vd->content_type = Model_Content::full_type(Model_Content::TYPE_PR);
		
		$this->load->view('manage/header');
		$this->load->view('manage/publish/pr-edit-external');
		$this->load->view('manage/footer');
	}

	public function edit_save_external()
	{
		$vars = parent::edit_save('pr');
		extract($vars, EXTR_SKIP);
		
		if ($is_new_content)
		     $m_pb_pr = new Model_PB_PR();
		else $m_pb_pr = Model_PB_PR::find($m_content->id);
		if (!$m_pb_pr) $m_pb_pr = new Model_PB_PR();
		
		$m_content->is_backdated = 1;
		$m_content->is_draft = 0;
		$m_content->is_under_review = 1;
		$m_content->save();

		$m_pb_pr->is_distribution_disabled = 1;
		$m_pb_pr->content_id = $m_content->id;
		$m_pb_pr->is_external = 1;
		$m_pb_pr->source_url = $this->input->post('source_url');
		$m_pb_pr->save();
	}

	protected function check_for_hold_criteria($m_content, $m_content_data, $m_pb_pr, $m_bundle)
	{
		// only apply these validation checks to those with validation enabled
		if (!Auth::user()->raw_data_object()->disable_pr_body_validation)
		{
			if ($this->cfhc_litigation($m_content, $m_content_data, $m_pb_pr, $m_bundle) ||
			    $this->cfhc_publicly_traded($m_content, $m_content_data, $m_pb_pr, $m_bundle))
				return;
		}
	}

	protected function cfhc_publicly_traded($m_content, $m_content_data, $m_pb_pr, $m_bundle)
	{
		if ($this->cfhc_publicly_traded__test($m_content, $m_content_data, $m_pb_pr, $m_bundle) !== false)
		{
			if (!$m_content->is_published)
			{
				$m_content->is_draft = 1;			
				$m_content->save();
			}

			$feedback = new Feedback('error');
			$feedback->set_title('Attention!');
			$feedback->set_html('Any release mentioning a publicly traded company with ticker symbol must be published
				through our Premium Financial distribution option. <br>If this release has been flagged in 
				error, please contact us on <a href="tel:800-713-7278"><strong>800-713-7278</strong></a> or email
				<a href="mailto:support@newswire.com"><strong>support@newswire.com</strong></a>.');
			$feedback->enable_alert();
			$feedback->enable_inline();
			$this->add_feedback($feedback);

			$url = sprintf('manage/publish/pr/edit/%d', $m_content->id);
			$this->set_redirect($url);

			return true;
		}

		return false;
	}

	protected function cfhc_litigation($m_content, $m_content_data, $m_pb_pr, $m_bundle)
	{
		if ($this->cfhc_litigation__test($m_content, $m_content_data, $m_pb_pr, $m_bundle) !== false)
		{
			if (!$m_content->is_published)
			{
				$m_content->is_draft = 1;
				$m_content->save();
			}

			$feedback = new Feedback('error');
			$feedback->set_title('Attention!');
			$feedback->set_html('Any releases discussing a litigation case will not be accepted.
				<br>If this release has been flagged in error, please contact us on 
				<a href="tel:800-713-7278"><strong>800-713-7278</strong></a> or email
				<a href="mailto:support@newswire.com"><strong>support@newswire.com</strong></a>.');
			$feedback->enable_alert();
			$feedback->enable_inline();
			$this->add_feedback($feedback);

			$url = sprintf('manage/publish/pr/edit/%d', $m_content->id);
			$this->set_redirect($url);

			return true;
		}

		return false;
	}

	protected function cfhc_publicly_traded__test($m_content, $m_content_data, $m_pb_pr, $m_bundle)
	{
		if ($m_bundle && $m_bundle->bundle === 
			 $m_bundle::DIST_PREMIUM_FINANCIAL)
			return false;

		$terms = Model_Setting::parse_block($this->conf('cfhc_publicly_traded_terms'));
		foreach ($terms as &$term) preg_quote($term);
		$terms = implode('|', $terms);
		$pattern = sprintf('#\b(%s)\b#is', $terms);

		return $this->cfhc__test($pattern, array(
			$m_content->title,
			$m_content_data->summary,
			$m_content_data->content,
		));	
	}

	protected function cfhc_litigation__test($m_content, $m_content_data, $m_pb_pr, $m_bundle)
	{
		$terms = Model_Setting::parse_block($this->conf('cfhc_litigation_terms'));
		foreach ($terms as &$term) preg_quote($term);
		$terms = implode('|', $terms);
		$pattern = sprintf('#\b(%s)\b#is', $terms);

		return $this->cfhc__test($pattern, array(
			$m_content->title,
			$m_content_data->summary,
			$m_content_data->content,
		));		
	}

	protected function cfhc__test($pattern, $tests)
	{
		$matches = array();
		foreach ($tests as $test)
			if (preg_match_all($pattern, $test, $m))
				$matches = array_unique(array_merge($matches, 
					array_map('strtolower', $m[1])));

		if (count($matches))
			return $matches;
		return false;
	}

	public function autosave_edit($id)
	{
		parent::autosave_edit($id);
	}

	public function autosave_create()
	{
		parent::autosave_create(Model_Content::TYPE_PR);
	}

	public function autosave_delete($id)
	{
		parent::autosave_delete(Model_Content::TYPE_PR, $id);
	}

	public function autosave($chunk = 1)
	{
		parent::autosave(Model_Content::TYPE_PR, $chunk);
	}

}
