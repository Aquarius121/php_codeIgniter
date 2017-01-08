<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Content_Change extends Model {
	
	protected static $__table = 'nr_content_change';
	protected static $__compressed = array('raw_data');

	use Raw_Data_Trait;

	public static function create()
	{
		$instance = new static();
		$instance->date_saved = Date::$now;
		return $instance;
	}

	public static function find_last($content_id, $amount = 1)
	{
		$criteria = array('content_id', $content_id);
		$order = array('date_saved', 'desc');
		$results = static::find_all($criteria, $order, $amount);
		$results = array_reverse($results);
		return $results;
	}
	
}