<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Country extends Model {
	
	const ID_UNITED_STATES = 474;
	const ID_UNITED_KINGDOM = 473;
	const ID_CANADA = 287;
	
	protected static $__table = 'nr_country';
	
	public static function ID_UNITED_STATES()
	{
		return static::ID_UNITED_STATES;
	}

	public static function ID_UNITED_KINGDOM()
	{
		return static::ID_UNITED_KINGDOM;
	}

	public static function ID_CANADA()
	{
		return static::ID_CANADA;
	}

}

?>