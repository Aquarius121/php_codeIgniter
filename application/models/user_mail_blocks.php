<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_User_Mail_Blocks extends Model {

	use Raw_Data_Trait;
	
	protected static $__table = 'nr_user_mail_blocks';
	protected static $__primary = 'user_id';

	const PREF_CONTENT_APPROVED = 'PREF_CONTENT_APPROVED';
	const PREF_CONTENT_PUBLISHED = 'PREF_CONTENT_PUBLISHED';
	const PREF_CONTENT_REJECTED = 'PREF_CONTENT_REJECTED';
	const PREF_CONTENT_UNDER_REVIEW = 'PREF_CONTENT_UNDER_REVIEW';
	const PREF_CONTENT_UPGRADE = 'PREF_CONTENT_UPGRADE';
	const PREF_LOW_CREDITS = 'PREF_LOW_CREDITS';
	const PREF_NO_CREDITS = 'PREF_NO_CREDITS';
	const PREF_ORDER = 'PREF_ORDER';

	protected static $descriptions = array(
		self::PREF_CONTENT_PUBLISHED => 'Your content has been published.',
		self::PREF_CONTENT_APPROVED => 'Your content has been approved by our staff.',
		self::PREF_CONTENT_REJECTED => 'Your content has been rejected by our staff.',
		self::PREF_CONTENT_UNDER_REVIEW => 'Your content has been sent for review by our staff.',
		self::PREF_CONTENT_UPGRADE => 'Newswire special offers and promotions.',
		self::PREF_LOW_CREDITS => 'Your account is running low on credits.',
		self::PREF_NO_CREDITS => 'You do not have enough credits to complete the requested action.',
		self::PREF_ORDER => 'Order confirmation, cancellation and receipts.',
	);

	public static function collection()
	{
		return array(
			static::PREF_CONTENT_APPROVED,
			static::PREF_CONTENT_PUBLISHED,
			static::PREF_CONTENT_REJECTED,
			static::PREF_CONTENT_UNDER_REVIEW,
			static::PREF_CONTENT_UPGRADE,
			static::PREF_LOW_CREDITS,
			static::PREF_NO_CREDITS,
			static::PREF_ORDER,
		);
	}

	public static function find_user($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		$model = parent::find_id($user);
		if ($model) return $model;
		$model = new static();
		$model->user_id = $user;
		return $model;
	}

	public static function describe($pref)
	{
		return static::$descriptions[$pref];
	}

	public function add($pref)
	{
		$rd = $this->raw_data();
		if (!$rd) $rd = new stdClass;
		$rd->{$pref} = true;
		$this->raw_data($rd);
	}

	public function remove($pref)
	{
		$rd = $this->raw_data();
		if (!$rd) $rd = new stdClass;
		$rd->{$pref} = false;
		$this->raw_data($rd);
	}

	public function clear()
	{
		$this->raw_data(null);
	}

	public function has($pref)
	{
		$rd = $this->raw_data();
		return isset($rd->{$pref}) && 
			$rd->{$pref};
	}

}

?>