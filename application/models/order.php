<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Order extends Model {

	use Raw_Data_Trait;
	
	protected static $__table = 'co_order';
	
	public static function create($uuid = null)
	{
		$instance = new static();
		if ($uuid === null)
		     $instance->id = UUID::create();
		else $instance->id = $uuid;
		$instance->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		return $instance;
	}
	
	public function nice_id()
	{
		$short = substr($this->id, 0, 8);
		$short = strtoupper($short);
		return $short;
	}
	
	public static function find_component_set($component_set)
	{
		if ($component_set instanceof Model_Component_Set)
			$component_set = $component_set->id;
		return static::find('component_set_id', $component_set);
	}
	
}

?>