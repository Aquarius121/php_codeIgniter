<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_User_Last_Billing_Update extends Model {

	const UPDATE_PERIOD = 24;

	protected static $__table = 'nr_user_last_billing_update';
	protected static $__primary = 'user_id';

	public static function update($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		$ob = static::find($user);
		if (!$ob) $ob = new static();
		$ob->user_id = $user;
		$ob->date_updated = Date::$now;
		$ob->save();
	}
	
	public function allowed_to_update()
	{
		return Date::utc($this->date_updated) < Date::hours(-static::UPDATE_PERIOD);
	}

}

?>