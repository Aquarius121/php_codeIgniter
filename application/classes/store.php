<?php 

class Store {

	const ITEM_BASIC_PR               = 'BASIC_PR';
	const ITEM_EMAIL                  = 'EMAIL';
	const ITEM_PPS_EXTRA_100_WORDS    = 'PPS_EXTRA_100_WORDS';
	const ITEM_PPN_EXTRA_100_WORDS    = 'PPN_EXTRA_100_WORDS';
	const ITEM_PRN_IMAGE_DISTRIBUTION = 'PRN_IMAGE_DISTRIBUTION';
	const ITEM_PRN_VIDEO_DISTRIBUTION = 'PRN_VIDEO_DISTRIBUTION';
	const ITEM_MEDIA_DATABASE_ACCESS  = 'MEDIA_DATABASE_ACCESS';
	const ITEM_MEDIA_OUTREACH         = 'MEDIA_OUTREACH';
	const ITEM_NEWSROOM               = 'NEWSROOM';
	const ITEM_PITCH_WRITING          = 'PITCH_WRITING';
	const ITEM_PR_REVISION_WRITING    = 'PR_REVISION_WRITING';
	const ITEM_PREMIUM_FINANCIAL      = 'PREMIUM_FINANCIAL';
	const ITEM_PREMIUM_PLUS           = 'PREMIUM_PLUS';
	const ITEM_PREMIUM_PLUS_STATE     = 'PREMIUM_PLUS_STATE';
	const ITEM_PREMIUM_PLUS_NATIONAL  = 'PREMIUM_PLUS_NATIONAL';
	const ITEM_PREMIUM_PR             = 'PREMIUM_PR';
	const ITEM_RP_PRNEWSWIRE          = 'RP_PRNEWSWIRE';
	const ITEM_WRITING                = 'WRITING';

	public static function locate_store_items_for_user($user)
	{
		Model_Item::enable_cache();

		$items = new stdClass();		
		$items->pr_credit = null;
		$items->email_credit = null;
		$items->newsroom_credit = null;
		$items->writing_credit = null;

		$items->other = array();
		$items->common_credits =& $items->other;
		$items->unlisted = array();
		$common_credits = array_intersect(Credit::list_common_types(), 
			Credit::list_extra_credit_types());
		foreach ($common_credits as $extra)
			$items->other[$extra] = null;
		$user_raw_data = $user->raw_data();
		
		if ($user->has_platinum_access())
		{
			$items->writing_credit = Model_Item::find_slug('writing-credit-platinum');	
			$items->pr_credit = Model_Item::find_slug('premium-pr-credit-platinum');
			$items->email_credit = Model_Item::find_slug('email-credit-platinum');
			$items->newsroom_credit = Model_Item::find_slug('newsroom-credit-platinum');
			
			// other items (credits)
			$items->other[Store::ITEM_PREMIUM_PLUS] = 
				Model_Item::find_slug('premium-plus-credit-platinum');
			$items->other[Store::ITEM_PREMIUM_PLUS_STATE] = 
				Model_Item::find_slug('premium-plus-state-credit-platinum');
			$items->other[Store::ITEM_PREMIUM_PLUS_NATIONAL] = 
				Model_Item::find_slug('premium-plus-national-credit-platinum');
			$items->other[Store::ITEM_PREMIUM_FINANCIAL] = 
				Model_Item::find_slug('premium-financial-credit-platinum');
			$items->other[Store::ITEM_MEDIA_OUTREACH] = 
				Model_Item::find_slug('media-outreach-credit-platinum');
			$items->other[Store::ITEM_PITCH_WRITING] = 
				Model_Item::find_slug('pitch-writing-credit-platinum');

			// other items (non-credit)
			$items->other[static::ITEM_PR_REVISION_WRITING] = 
				Model_Item::find_slug('pr-revision-writing-platinum');
		}
		else if ($user->has_gold_access())
		{
			$items->writing_credit = Model_Item::find_slug('writing-credit-gold');	
			$items->pr_credit = Model_Item::find_slug('premium-pr-credit-gold');
			$items->email_credit = Model_Item::find_slug('email-credit-gold');
			$items->newsroom_credit = Model_Item::find_slug('newsroom-credit-gold');
			
			// other items (credits)
			$items->other[Store::ITEM_PREMIUM_PLUS] = 
				Model_Item::find_slug('premium-plus-credit-gold');
			$items->other[Store::ITEM_PREMIUM_PLUS_STATE] = 
				Model_Item::find_slug('premium-plus-state-credit-gold');
			$items->other[Store::ITEM_PREMIUM_PLUS_NATIONAL] = 
				Model_Item::find_slug('premium-plus-national-credit-gold');
			$items->other[Store::ITEM_PREMIUM_FINANCIAL] = 
				Model_Item::find_slug('premium-financial-credit-gold');
			$items->other[Store::ITEM_MEDIA_OUTREACH] = 
				Model_Item::find_slug('media-outreach-credit-gold');
			$items->other[Store::ITEM_PITCH_WRITING] = 
				Model_Item::find_slug('pitch-writing-credit-gold');

			// other items (non-credit)
			$items->other[static::ITEM_MEDIA_DATABASE_ACCESS] = 
				Model_Item::find_slug('media-database-access-gold');
			$items->other[static::ITEM_PR_REVISION_WRITING] = 
				Model_Item::find_slug('pr-revision-writing-gold');
		}
		else if ($user->has_silver_access())
		{
			$items->writing_credit = Model_Item::find_slug('writing-credit-silver');	
			$items->pr_credit = Model_Item::find_slug('premium-pr-credit-silver');
			$items->email_credit = Model_Item::find_slug('email-credit-silver');
			$items->newsroom_credit = Model_Item::find_slug('newsroom-credit-silver');
			
			// other items (credits)
			$items->other[Store::ITEM_PREMIUM_PLUS] = 
				Model_Item::find_slug('premium-plus-credit-silver');
			$items->other[Store::ITEM_PREMIUM_PLUS_STATE] = 
				Model_Item::find_slug('premium-plus-state-credit-silver');
			$items->other[Store::ITEM_PREMIUM_PLUS_NATIONAL] = 
				Model_Item::find_slug('premium-plus-national-credit-silver');
			$items->other[Store::ITEM_PREMIUM_FINANCIAL] = 
				Model_Item::find_slug('premium-financial-credit-silver');
			$items->other[Store::ITEM_MEDIA_OUTREACH] = 
				Model_Item::find_slug('media-outreach-credit-silver');
			$items->other[Store::ITEM_PITCH_WRITING] = 
				Model_Item::find_slug('pitch-writing-credit-silver');

			// other items (non-credit)
			$items->other[static::ITEM_MEDIA_DATABASE_ACCESS] = 
				Model_Item::find_slug('media-database-access-silver');
			$items->other[static::ITEM_PR_REVISION_WRITING] = 
				Model_Item::find_slug('pr-revision-writing-silver');
		}
		else // free or basic
		{
			$items->writing_credit = Model_Item::find_slug('writing-credit');	
			$items->pr_credit = Model_Item::find_slug('premium-pr-credit');
			$items->email_credit = Model_Item::find_slug('email-credit');
			$items->newsroom_credit = Model_Item::find_slug('newsroom-credit');
			
			// other items (credits)
			$items->other[Store::ITEM_PREMIUM_PLUS] = 
				Model_Item::find_slug('premium-plus-credit');
			$items->other[Store::ITEM_PREMIUM_PLUS_STATE] = 
				Model_Item::find_slug('premium-plus-state-credit');
			$items->other[Store::ITEM_PREMIUM_PLUS_NATIONAL] = 
				Model_Item::find_slug('premium-plus-national-credit');
			$items->other[Store::ITEM_PREMIUM_FINANCIAL] = 
				Model_Item::find_slug('premium-financial-credit');
			$items->other[Store::ITEM_MEDIA_OUTREACH] = 
				Model_Item::find_slug('media-outreach-credit');
			$items->other[Store::ITEM_PITCH_WRITING] = 
				Model_Item::find_slug('pitch-writing-credit');

			// other items (non-credit)
			$items->other[static::ITEM_MEDIA_DATABASE_ACCESS] = 
				Model_Item::find_slug('media-database-access');
			$items->other[static::ITEM_PR_REVISION_WRITING] = 
				Model_Item::find_slug('pr-revision-writing');
		}

		$items->unlisted[static::ITEM_PPS_EXTRA_100_WORDS] = 
			Model_Item::find_slug('pps-extra-100-words');
		$items->unlisted[static::ITEM_PPN_EXTRA_100_WORDS] = 
			Model_Item::find_slug('ppn-extra-100-words');
		$items->unlisted[static::ITEM_PRN_IMAGE_DISTRIBUTION] = 
			Model_Item::find_slug('prn-image-distribution');
		$items->unlisted[static::ITEM_PRN_VIDEO_DISTRIBUTION] = 
			Model_Item::find_slug('prn-video-distribution');
		
		// grandfather items for older members
		if (isset($user_raw_data->grandfather))
		{
			$gf_data = $user_raw_data->grandfather;

			if (isset($gf_data->writing_credit))
				$items->writing_credit = Model_Item::find_slug($gf_data->writing_credit);
			if (isset($gf_data->pr_credit))
				$items->pr_credit = Model_Item::find_slug($gf_data->pr_credit);
			if (isset($gf_data->email_credit))
				$items->email_credit = Model_Item::find_slug($gf_data->email_credit);
			if (isset($gf_data->newsroom_credit))
				$items->newsroom_credit = Model_Item::find_slug($gf_data->newsroom_credit);

			if (isset($gf_data->common_credits))
			{
				foreach ($gf_data->common_credits as $k => $slug)
					$items->other[$k] = Model_Item::find_slug($slug);
			}
		}
		
		// if user has plan => check for override
		if ($user_plan = $user->m_user_plan())
		{
			// find replacement items for this user's plan (usually applies to custom plans)
			$replace_pr_credit = Model_Plan_Extra_Credit::find_id(array($user_plan->plan_id, Store::ITEM_PREMIUM_PR));
			$replace_email_credit = Model_Plan_Extra_Credit::find_id(array($user_plan->plan_id, Store::ITEM_EMAIL));
			$replace_newsroom_credit = Model_Plan_Extra_Credit::find_id(array($user_plan->plan_id, Store::ITEM_NEWSROOM));
			$replace_writing_credit = Model_Plan_Extra_Credit::find_id(array($user_plan->plan_id, Store::ITEM_WRITING));			
			if ($replace_pr_credit && !$replace_pr_credit->item()->is_disabled)
				$items->pr_credit = $replace_pr_credit->item();
			if ($replace_email_credit && !$replace_email_credit->item()->is_disabled)
				$items->email_credit = $replace_email_credit->item();
			if ($replace_newsroom_credit && !$replace_newsroom_credit->item()->is_disabled)
				$items->newsroom_credit = $replace_newsroom_credit->item();
			if ($replace_writing_credit && !$replace_writing_credit->item()->is_disabled)
				$items->writing_credit = $replace_writing_credit->item();

			foreach ($common_credits as $extra)
			{
				$replace_credit = Model_Plan_Extra_Credit::find_id(array($user_plan->plan_id, $extra));
				if ($replace_credit && !$replace_credit->item()->is_disabled)
					$items->other[$extra] = $replace_credit->item();
			}
		}

		$items->all = array();
		$items->all = array_merge($items->all, $items->other);
		$items->all = array_merge($items->all, $items->unlisted);
		$items->all[static::ITEM_PREMIUM_PR] = $items->pr_credit;
		$items->all[static::ITEM_WRITING] = $items->writing_credit;
		$items->all[static::ITEM_EMAIL] = $items->email_credit;
		$items->all[static::ITEM_NEWSROOM] = $items->newsroom_credit;

		Model_Item::disable_cache();

		return $items;
	}

	// TODO: this needs to be optimized so as to not load everything
	public static function locate_store_item_for_user($user, $code)
	{
		$items = static::locate_store_items_for_user($user, $code);
		if (isset($items->all[$code]))
			return $items->all[$code];
		return null;
	}

}