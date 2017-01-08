<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Credit {
	
	const TYPE_PREMIUM_PR            = 'PREMIUM_PR';
	const TYPE_BASIC_PR              = 'BASIC_PR';
	const TYPE_EMAIL                 = 'EMAIL';
	const TYPE_NEWSROOM              = 'NEWSROOM';
	const TYPE_WRITING               = 'WRITING';
	const TYPE_RP_PRNEWSWIRE         = 'RP_PRNEWSWIRE';
	const TYPE_PREMIUM_PLUS          = 'PREMIUM_PLUS';
	const TYPE_PREMIUM_PLUS_STATE    = 'PREMIUM_PLUS_STATE';
	const TYPE_PREMIUM_PLUS_NATIONAL = 'PREMIUM_PLUS_NATIONAL';
	const TYPE_PREMIUM_FINANCIAL     = 'PREMIUM_FINANCIAL';
	const TYPE_MEDIA_OUTREACH        = 'MEDIA_OUTREACH';
	const TYPE_PITCH_WRITING         = 'PITCH_WRITING';
	
	protected static $common = array(
		self::TYPE_RP_PRNEWSWIRE,
		self::TYPE_PREMIUM_PLUS,
		self::TYPE_PREMIUM_PLUS_STATE,
		self::TYPE_PREMIUM_PLUS_NATIONAL,
		self::TYPE_PREMIUM_FINANCIAL,
		self::TYPE_MEDIA_OUTREACH,
		self::TYPE_PITCH_WRITING,
	);

	public static function list_types()
	{
		return array(
			static::TYPE_PREMIUM_PR,
			static::TYPE_BASIC_PR,
			static::TYPE_EMAIL,
			static::TYPE_NEWSROOM,
			static::TYPE_WRITING,
			static::TYPE_PREMIUM_PLUS,
			static::TYPE_PREMIUM_PLUS_STATE,
			static::TYPE_PREMIUM_PLUS_NATIONAL,
			static::TYPE_PREMIUM_FINANCIAL,
			static::TYPE_MEDIA_OUTREACH,
			static::TYPE_PITCH_WRITING,
		);
	}

	// credits that can be bought individually
	// * this should include most of list_types()
	// * all common types are included by default
	public static function list_extra_credit_types()
	{
		return array_merge(array(
			static::TYPE_PREMIUM_PR,
			static::TYPE_EMAIL,
			static::TYPE_NEWSROOM,
			static::TYPE_WRITING,
		), static::list_common_types());
	}

	public static function list_common_types()
	{
		return static::$common;
	}
	
	public static function is_common($credit)
	{
		return in_array($credit, static::$common);
	}

	public static function full_name($credit_const)
	{
		$display = array(
			static::TYPE_PREMIUM_PR => 'Premium PR',
			static::TYPE_BASIC_PR => 'Basic PR',
			static::TYPE_EMAIL => 'Media Outreach',
			static::TYPE_NEWSROOM => 'Newsroom',
			static::TYPE_WRITING => 'Writing',
			static::TYPE_RP_PRNEWSWIRE => 'PR Newswire',
			static::TYPE_PREMIUM_PLUS => 'Premium Plus',
			static::TYPE_PREMIUM_PLUS_STATE => 'Premium Plus State Newsline',
			static::TYPE_PREMIUM_PLUS_NATIONAL => 'Premium Plus National',
			static::TYPE_PREMIUM_FINANCIAL => 'Premium Financial',
			static::TYPE_MEDIA_OUTREACH => 'Targetted Media Campaign',
			static::TYPE_PITCH_WRITING => 'Pitch Writing',
		);

		return @$display[$credit_const];
	}

	public static function has_rollover_support($credit_const)
	{
		$supported = array(
			static::TYPE_PREMIUM_PR,
			static::TYPE_BASIC_PR,
			static::TYPE_EMAIL,
			static::TYPE_WRITING,
		);

		return in_array($credit_const, $supported);
	}

	// ! do not include asi/custom prefixes 
	public static function tracking_name($credit_const)
	{
		$display = array(
			static::TYPE_PREMIUM_PR => 'premium_pr_credit',
			static::TYPE_BASIC_PR => 'basic_pr_credit',
			static::TYPE_EMAIL => 'email_credit',
			static::TYPE_NEWSROOM => 'newsroom_credit',
			static::TYPE_WRITING => 'writing_distribution_credit',
			static::TYPE_RP_PRNEWSWIRE => 'release_plus_prnewswire',
			static::TYPE_PREMIUM_PLUS => 'premium_plus',
			static::TYPE_PREMIUM_PLUS_STATE => 'premium_plus_state',
			static::TYPE_PREMIUM_PLUS_NATIONAL => 'premium_plus_national',
			static::TYPE_PREMIUM_FINANCIAL => 'premium_financial',
			static::TYPE_MEDIA_OUTREACH => 'media_outreach_credit',
			static::TYPE_PITCH_WRITING => 'pitch_writing_credit',
		);

		return @$display[$credit_const];
	}

	public static function item_slug($credit_const)
	{
		$display = array(
			static::TYPE_PREMIUM_PR => 'premium-pr-credit',
			static::TYPE_BASIC_PR => null,
			static::TYPE_EMAIL => 'email-credit',
			static::TYPE_NEWSROOM => 'newsroom-credit',
			static::TYPE_WRITING => 'writing-credit',
			static::TYPE_RP_PRNEWSWIRE => null,
			static::TYPE_PREMIUM_PLUS => 'premium-plus-credit',
			static::TYPE_PREMIUM_PLUS_STATE => 'premium-plus-state-credit',
			static::TYPE_PREMIUM_PLUS_NATIONAL => 'premium-plus-national-credit',
			static::TYPE_PREMIUM_FINANCIAL => 'premium-financial-credit',
			static::TYPE_MEDIA_OUTREACH => 'media-outreach-credit',
			static::TYPE_PITCH_WRITING => 'pitch-writing-credit',
		);

		return @$display[$credit_const];
	}

	public static function item($credit_const)
	{
		if (($slug = static::item_slug($credit_const)))
			return Model_Item::find_slug($slug);
		return null;
	}

	public static function construct_held($type, $m_user = null)
	{
		if ($type == static::TYPE_PREMIUM_PR)
		{
			$held = new Model_Limit_PR_Held();
			$held->type = Model_Content::PREMIUM;
			if ($m_user) $held->user_id = $m_user->id;
			return $held;
		} 
		
		if ($type == static::TYPE_BASIC_PR)
		{
			$held = new Model_Limit_PR_Held();
			$held->type = Model_Content::BASIC;	
			if ($m_user) $held->user_id = $m_user->id;
			return $held;
		}

		if ($type == static::TYPE_EMAIL)
		{
			$held = new Model_Limit_Email_Held();
			if ($m_user) $held->user_id = $m_user->id;
			return $held;
		}

		if ($type == static::TYPE_NEWSROOM)
		{
			$held = new Model_Limit_Newsroom_Held();
			if ($m_user) $held->user_id = $m_user->id;
			return $held;
		}

		if ($type == static::TYPE_WRITING)
		{
			$held = new Model_Limit_Writing_Held();
			if ($m_user) $held->user_id = $m_user->id;
			return $held;
		}

		if (static::is_common($type))
		{
			$held = new Model_Limit_Common_Held();
			if ($m_user) $held->user_id = $m_user->id;
			$held->type = $type;
			return $held;
		}

		return null;
	}

	public static function locate_store_items_for_user($user)
	{
		return Store::locate_store_items_for_user($user);
	}
	
}

?>