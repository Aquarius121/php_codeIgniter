<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Content_Auto_Save extends Model {
	
	protected static $__table = 'nr_content_auto_save';
	protected static $__primary = 'id';
	protected static $__compressed = array('raw_data');

	use Raw_Data_Trait;

	public static function create()
	{
		$instance = new static();
		$instance->date_created = Date::$now;
		return $instance;
	}
	
}

?>