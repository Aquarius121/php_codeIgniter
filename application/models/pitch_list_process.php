<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Pitch_List_Process extends Model {

	const PROCESS_NOT_ASSIGNED = 'not_assigned';
	const PROCESS_ASSIGNED_TO_LIST_BUILDER = 'assigned_to_list_builder';
	const PROCESS_SENT_TO_ADMIN = 'sent_to_admin';
	const PROCESS_ADMIN_REJECTED = 'admin_rejected';
	const PROCESS_SENT_TO_CUSTOMER = 'sent_to_customer';

	protected static $__table = 'pw_pitch_list_process';

	public static function full_process($process)
	{
		$display = array(
			static::PROCESS_NOT_ASSIGNED => 'Not Yet Assigned',
			static::PROCESS_ASSIGNED_TO_LIST_BUILDER => 'Assigned to List Builder',
			static::PROCESS_SENT_TO_ADMIN => 'Submitted by List Builder',
			static::PROCESS_ADMIN_REJECTED => 'Rejected by Admin',
			static::PROCESS_SENT_TO_CUSTOMER => 'Sent to Customer'
		);

		return @$display[$process];
	}

	
	public static function create_and_save($pitch_list_id, $process, $comments = null)
	{
		$m_pl_process = new static();
		$m_pl_process->pitch_list_id = $pitch_list_id;
		$m_pl_process->process = $process;
		$m_pl_process->comments = $comments;
		$m_pl_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_pl_process->save();
		return $m_pl_process;
	}	

	public static function get_rejection_conversation($pitch_list_id)
	{
		$criteria = array();
		$criteria[] = array('pitch_list_id', $pitch_list_id);
		$process_list = sql_in_list(array(static::PROCESS_SENT_TO_ADMIN, static::PROCESS_ADMIN_REJECTED));
		$criteria[] = array("process IN ({$process_list})");

		return static::find_all($criteria, array('process_date', 'desc'));
	}


}

?>