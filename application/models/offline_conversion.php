<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Offline_Conversion extends Model {
	
	protected static $__table = 'nr_offline_conversion';
	protected static $__primary = 'transaction_id';
	
	const CONVERSION_PERIOD = 30;
	
	public static function find_for_conversion($user)
	{
		$sql = "SELECT oc.* 
			FROM nr_offline_conversion oc
			WHERE oc.user_id = ?
			AND oc.is_converted = 0
			AND date_created > ?";
			
		$params = array($user->id, Date::days(-static::CONVERSION_PERIOD));
		$dbr = static::__db()->query($sql, $params);
		return static::from_db_all($dbr);
	}
	
}

?>