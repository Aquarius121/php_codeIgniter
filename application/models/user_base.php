<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_User_Base extends Model {

	use Raw_Data_Trait;
	
	protected static $__table = 'nr_user_base';
	protected static $__compressed = array('raw_data');
	
	public static function create_user()
	{
		$base = new static();
		$base->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		$base->is_new_user = 1;
		$base->id = Model_ID_Store::next('user');
		$base->save();

		$user = Model_User::find($base->id);
		return $user;
	}
	
}