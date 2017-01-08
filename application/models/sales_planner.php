<?php

class Model_Sales_Planner extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_sales_planner';

	public static function create($uuid = null)
	{
		$instance = new static();
		if ($uuid === null)
		     $instance->id = UUID::create();
		else $instance->id = $uuid;
		$instance->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		return $instance;
	}
	
}