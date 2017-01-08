<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Pitch_Session extends Model {

	use Raw_Data_Trait;

	const ORDER_TYPE_OUTREACH = 'outreach';
	const ORDER_TYPE_WRITING = 'writing';

	protected static $__table = 'pw_pitch_session';

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

}

?>