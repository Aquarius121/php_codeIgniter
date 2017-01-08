<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Insights_Alert extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_insights_alert';

	const MIN_WAIT_TIME = 43200;
	
	public static function create()
	{
		$instance = new static();
		$instance->date_created = Date::$now;
		$instance->date_sent = Date::hours(-12);
		$instance->secret = md5(UUID::create());
		$instance->is_enabled = 1;
		return $instance;
	}
	
}
