<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Pitch_Writing_Process extends Model {

	const PROCESS_NOT_ASSIGNED = 'not_assigned';
	const PROCESS_ASSIGNED_TO_WRITER = 'assigned_to_writer';
	const PROCESS_WRITER_REQUEST_DETAILS_REVISION = 'writer_request_details_revision';
	const PROCESS_SENT_BACK_TO_WRITER = 'sent_back_to_writer';
	const PROCESS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE = 'sent_to_customer_for_detail_change';
	const PROCESS_CUSTOMER_REVISE_DETAILS = 'customer_revise_details';
	const PROCESS_WRITTEN_SENT_TO_ADMIN = 'written_sent_to_admin';
	const PROCESS_ADMIN_REJECTED = 'admin_rejected';
	const PROCESS_SENT_TO_CUSTOMER = 'sent_to_customer';
	const PROCESS_CUSTOMER_REJECTED = 'customer_rejected';
	const PROCESS_CUSTOMER_ACCEPTED = 'customer_accepted';
	
	const COMMENTS_CUSTOMER_EDITED = 'Customer Edited Pitch';
	const COMMENTS_ADMIN_PURGED_CHANGES = 'Customer Edited Pitch - Admin Purged Changes';

	protected static $__table = 'pw_pitch_writing_process';

	public static function full_process($process)
	{
		$display = array(
			static::PROCESS_NOT_ASSIGNED => 'Not Yet Assigned',
			static::PROCESS_ASSIGNED_TO_WRITER => 'Assigned to Writer',
			static::PROCESS_WRITER_REQUEST_DETAILS_REVISION => 'Writer Requested to Revise Details', 
			static::PROCESS_SENT_BACK_TO_WRITER => 'Sent Back to Writer', 
			static::PROCESS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE => 'Sent to Customer for Revising Details',
			static::PROCESS_CUSTOMER_REVISE_DETAILS => 'Customer Revised Details', 
			static::PROCESS_WRITTEN_SENT_TO_ADMIN => 'Submitted by Writer',
			static::PROCESS_ADMIN_REJECTED => 'Rejected by Admin',
			static::PROCESS_SENT_TO_CUSTOMER => 'Sent to Customer',
			static::PROCESS_CUSTOMER_REJECTED => 'Rejected by Customer',
			static::PROCESS_CUSTOMER_ACCEPTED => 'Approved by Customer'
		);

		return @$display[$process];
	}

	public static function create_and_save($pitch_order_id, $process, $comments = null)
	{
		$m_pw_process = new static();
		$m_pw_process->pitch_order_id = $pitch_order_id;
		$m_pw_process->process = $process;
		$m_pw_process->comments = $comments;
		$m_pw_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_pw_process->save();
		return $m_pw_process;
	}

	public static function get_pre_writing_conversation($pitch_order_id)
	{
		$criteria = array();
		$criteria[] = array('pitch_order_id', $pitch_order_id);
		$process_list = sql_in_list(array(static::PROCESS_WRITER_REQUEST_DETAILS_REVISION, 
						static::PROCESS_SENT_BACK_TO_WRITER,
						static::PROCESS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE,
						static::PROCESS_CUSTOMER_REVISE_DETAILS));
		$criteria[] = array("process IN ({$process_list})");

		return static::find_all($criteria, array('process_date', 'desc'));
	}

	public static function get_rejection_conversation($pitch_order_id)
	{
		$criteria = array();
		$criteria[] = array('pitch_order_id', $pitch_order_id);
		$process_list = sql_in_list(array(static::PROCESS_WRITTEN_SENT_TO_ADMIN, 
										static::PROCESS_ADMIN_REJECTED,
										static::PROCESS_SENT_TO_CUSTOMER,
										static::PROCESS_CUSTOMER_REJECTED));
		$criteria[] = array("process IN ({$process_list})");

		return static::find_all($criteria, array('process_date', 'desc'));
	}
	
	public static function get_last_customer_rejection_comments($pitch_order_id)
	{
		$criteria = array();
		$criteria[] = array('pitch_order_id', $pitch_order_id);
		$criteria[] = array('process', static::PROCESS_CUSTOMER_REJECTED);
		$comments = null;
		if ($result = static::find_all($criteria, array('process_date', 'desc'), 1))		
			$comments = $result[0]->comments;
		return $comments;
	}
}

?>