<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Adword_Conversion extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_adword_conversion';
	protected static $__primary = 'item_id';
	
	const DEFAULT_LABEL = '4YAdCL7Q1FYQgqLX2QM';
	const DEFAULT_ID = 993382658;
	
	public static function defaults()
	{
		$data = new stdClass();
		$data->id = static::DEFAULT_ID;
		$data->label = static::DEFAULT_LABEL;
		$instance = new Model_Adword_Conversion();
		$instance->raw_data($data);
		return $instance;
	}
	
	
}

?>