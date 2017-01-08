<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Content_Change_Deleted extends Model {
	
	use Raw_Data_Trait;
	
	// the table name in the database
	protected static $__table = 'nr_content_change_deleted';

	// @string    the primary key field
	// @string[]  the primary key fields
	protected static $__primary = 'id';

	// should in-memory caching be enabled by default
	protected static $__cache_enabled = false;

	// how long data should be cached in-memory
	protected static $__cache_duration = 60;

	// the CI database class to use
	protected static $__db_class = 'default';

	// list of BLOB fields that use compression 
	protected static $__compressed = array('raw_data');

	public static function create()
	{
		$instance = new static();
		$instance->date_deleted = Date::$now;
		return $instance;
	}
	
}