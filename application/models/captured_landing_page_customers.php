<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Captured_Landing_Page_Customers extends Model {
	
	protected static $__table = 'nr_captured_landing_page_customers';
	protected static $__primary = 'id';

	public static function create()
	{
		$instance = new static();
		$instance->date_created = Date::utc();
		return $instance;
	}

}
