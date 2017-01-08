<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Pitch_List extends Model {	

	const STATUS_NOT_ASSIGNED = 'not_assigned';
	const STATUS_ASSIGNED_TO_LIST_BUILDER = 'assigned_to_list_builder';
	const STATUS_SENT_TO_ADMIN = 'sent_to_admin';
	const STATUS_ADMIN_REJECTED = 'admin_rejected';
	const STATUS_SENT_TO_CUSTOMER = 'sent_to_customer';

	protected static $__table = 'pw_pitch_list';

	public static function full_status($status)
	{
		$display = array(
			static::STATUS_NOT_ASSIGNED => 'Not Yet Assigned',
			static::STATUS_ASSIGNED_TO_LIST_BUILDER => 'Assigned to List Builder',
			static::STATUS_SENT_TO_ADMIN => 'Review List',
			static::STATUS_ADMIN_REJECTED => 'Rejected by Admin',
			static::STATUS_SENT_TO_CUSTOMER => 'Uploaded to Customer'
		);

		return @$display[$status];
	}

	public function save()
	{
		if ($this->__source->status != $this->status || ! $this->__source)
    			$this->date_of_last_status = Date::$now->format(DATE::FORMAT_MYSQL);
		parent::save();
	}

}

?>