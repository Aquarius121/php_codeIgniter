<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Writing_Process extends Model {	

	const ACTOR_ADMIN = 'admin';
	const ACTOR_CUSTOMER = 'customer';
	const ACTOR_RESELLER = 'reseller';
	const ACTOR_WRITER = 'writer';

	protected static $__table = 'rw_writing_process';
	
	protected static $status_index_vector = array(
		Model_Writing_Order::STATUS_NOT_ASSIGNED,
		Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER,
		Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION,
		Model_Writing_Order::STATUS_SENT_BACK_TO_WRITER,
		Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE,
		Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS,
		Model_Writing_Order::STATUS_REVISED_DETAILS_ACCEPTED,
		Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER,
		Model_Writing_Order::STATUS_RESELLER_REJECTED,
		Model_Writing_Order::STATUS_SENT_TO_CUSTOMER,
		Model_Writing_Order::STATUS_CUSTOMER_REJECTED,
		Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED,
		Model_Writing_Order::STATUS_APPROVED,
		Model_Writing_Order::STATUS_REJECTED,
	);
	
	public static function get_max_process($writing_order_id)
	{
		$ci =& get_instance();
		$sql = "SELECT MAX(process+0)
			AS max_status_num
			FROM rw_writing_process
			WHERE writing_order_id = ?";
			
		$query = $ci->db->query($sql, array($writing_order_id));		
		return @$query->row()->max_status_num;
	}
	
	public static function get_pre_writing_conversation($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array("process IN ('writer_request_details_revision',
			'sent_back_to_writer', 'sent_to_customer_for_detail_change',
			'customer_revise_details',	'revised_details_accepted')");

		return static::find_all($criteria, array('process_date','desc'));
	}
	
	public static function get_pre_writing_conversation_with_writer($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array("process IN ('writer_request_details_revision',
			'sent_back_to_writer', 'revised_details_accepted')");
										
		return static::find_all($criteria, array('process_date', 'desc'));
	}
	
	public static function get_rejection_conversation($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array("process IN ('writer_request_details_revision',
			'sent_back_to_writer', 'sent_to_customer_for_detail_change', 'customer_rejected',
			'reseller_rejected', 'written_sent_to_reseller', 'sent_to_customer',
			'customer_revise_details')");
		
		return static::find_all($criteria, array('process_date', 'desc'));
	}
	
	public static function get_reseller_rejection_messages($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array('process', 'reseller_rejected');
		
		return static::find_all($criteria, array('process_date', 'desc'));
	}
	
	public static function how_many_times_rejected_by_customer($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array('process', 'customer_rejected');
		
		return static::count_all($criteria);
	}
	
	public static function how_many_times_rejected($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array("process IN ('customer_rejected', 'reseller_rejected')");
		
		return static::count_all($criteria);
	}
	
	public static function get_latest_customer_rejection_comments($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array('process', 'customer_rejected');
		
		$results = static::find_all($criteria, array('process_date', 'desc'), 1);
		if ($results) return $results[0]->comments;
		return null;
	}
	
	public static function get_latest_message_to_customer($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array("process IN ('sent_to_customer', 'sent_to_customer_for_detail_change')");
		
		$results = static::find_all($criteria, array('process_date', 'desc'), 1);
		if ($results) return $results[0]->comments;
		return null;
	}
	
	public static function get_latest_reseller_rejection_comments($writing_order_id)
	{
		$criteria = array();
		$criteria[] = array('writing_order_id', $writing_order_id);
		$criteria[] = array('process', 'reseller_rejected');
		
		$results = static::find_all($criteria, array('process_date', 'desc'), 1);
		if ($results) return $results[0]->comments;
		return null;
	}
	
	public static function create_and_save($writing_order_id, $process, $actor, $comments = null)
	{
		$w_process = new static();
		$w_process->writing_order_id = $writing_order_id;
		$w_process->process = $process;
		$w_process->actor = $actor;
		$w_process->comments = $comments;
		$w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$w_process->save();
		return $w_process;
	}
	
	public static function create()
	{
		$w_process = new static();
		$w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		return $w_process;
	}
	
	public static function status_to_index($status)
	{
		return array_search($status, static::$status_index_vector) + 1;
	}
	
	public static function index_to_status($index)
	{
		return @static::$status_index_vector[$index + 1];
	}
	
}

?>