<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Scheduled_Iella_Request extends Model {
	
	protected static $__table = 'nr_scheduled_iella_request';
	
	public static function find_due()
	{
		$sql = "SELECT * FROM nr_scheduled_iella_request
			WHERE date_execute <= UTC_TIMESTAMP() AND is_active = 0 
			ORDER BY date_execute ASC, id ASC LIMIT 1";
		$db_result = get_instance()->db->query($sql);
		return static::from_db($db_result);
	}

	public static function find_active()
	{
		$sql = "SELECT * FROM nr_scheduled_iella_request
			WHERE date_execute <= UTC_TIMESTAMP() AND is_active = 1 
			ORDER BY date_execute ASC, id ASC LIMIT 1";
		$db_result = get_instance()->db->query($sql);
		return static::from_db($db_result);
	}
	
}