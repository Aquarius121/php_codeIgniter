<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Plan extends Model {
	
	protected static $__table = 'co_plan';

	const PACKAGE_SILVER = Package::PACKAGE_SILVER;
	const PACKAGE_GOLD = Package::PACKAGE_GOLD;
	const PACKAGE_PLATINUM = Package::PACKAGE_PLATINUM;
	const PACKAGE_BASIC = Package::PACKAGE_BASIC;
	
	public static function from_item($item)
	{
		if (!($item instanceof Model_Item))
			$item = Model_Item::find($item);
		
		$raw_data = json_decode($item->raw_data);
		if (!isset($raw_data->plan_id)) return false;
		return static::find($raw_data->plan_id);
	}
	
}

?>