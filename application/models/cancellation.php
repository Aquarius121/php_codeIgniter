<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Cancellation extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'co_cancellation';
	
	public static function create()
	{
		$instance = new static();
		$instance->date_cancel = Date::$now->format(Date::FORMAT_MYSQL);
		return $instance;
	}
	
}

?>