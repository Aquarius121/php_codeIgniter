<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Writing_Order extends Model {

	const ANGLE_PROBLEM = 'problem';
	const ANGLE_DISCOUNT = 'discount';
	const ANGLE_WEBSITE = 'website';
	const ANGLE_ANNOUNCEMENT = 'announcement';
	const ANGLE_OTHER = 'other';

	const STATUS_NOT_ASSIGNED = 'not_assigned';
	const STATUS_ASSIGNED_TO_WRITER = 'assigned_to_writer';
	const STATUS_WRITER_REQUEST_DETAILS_REVISION = 'writer_request_details_revision';
	const STATUS_SENT_BACK_TO_WRITER = 'sent_back_to_writer';
	const STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE = 'sent_to_customer_for_detail_change';
	const STATUS_CUSTOMER_REVISE_DETAILS = 'customer_revise_details';
	const STATUS_REVISED_DETAILS_ACCEPTED = 'revised_details_accepted';
	const STATUS_WRITTEN_SENT_TO_RESELLER = 'written_sent_to_reseller';
	const STATUS_RESELLER_REJECTED = 'reseller_rejected';
	const STATUS_SENT_TO_CUSTOMER = 'sent_to_customer';
	const STATUS_CUSTOMER_REJECTED = 'customer_rejected';
	const STATUS_CUSTOMER_ACCEPTED = 'customer_accepted';

	// these 2 are not currently tracked
	const STATUS_APPROVED = 'approved';
	const STATUS_REJECTED = 'rejected';

	// this is used on MOT, added
	// here for consistency.
	const WRITING_TASK_TYPE = 'PR';

	protected static $__table = 'rw_writing_order';

	// warning! this is not always used 
	public static function full_process($process_num)
	{
		$display = array(
			static::STATUS_NOT_ASSIGNED => 'Not Yet Assigned',
			static::STATUS_ASSIGNED_TO_WRITER => 'Assigned to Writer',
			static::STATUS_WRITER_REQUEST_DETAILS_REVISION => 'Writer Requested to Details', 
			static::STATUS_SENT_BACK_TO_WRITER => 'Sent Back to Writer', 
			static::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE => 'Sent to Customer (Details)',
			static::STATUS_CUSTOMER_REVISE_DETAILS => 'Customer Revised Details', 
			static::STATUS_REVISED_DETAILS_ACCEPTED => 'Revised Details Accepted',
			static::STATUS_WRITTEN_SENT_TO_RESELLER => 'Submitted by Writer',
			static::STATUS_RESELLER_REJECTED => 'Rejected by Reseller',
			static::STATUS_SENT_TO_CUSTOMER => 'Sent to Customer (Review)',
			static::STATUS_CUSTOMER_REJECTED => 'Rejected by Customer',
			static::STATUS_CUSTOMER_ACCEPTED => 'Approved by Customer',
			static::STATUS_APPROVED => 'Press Release Published',
			static::STATUS_REJECTED => 'Press Release Rejected'
		);

		return @$display[$process_num];
	}

	// warning! this is not always used 
	public static function full_angle_name($angle)
	{
		$display = array(
			static::ANGLE_PROBLEM => 'Problem / Solution - Introduces a problem and
				presents the website or product as a solution',
			static::ANGLE_DISCOUNT => 'Discount Offer or Special Offer Announcement',
			static::ANGLE_WEBSITE => 'Website or product launch', 
			static::ANGLE_ANNOUNCEMENT => 'Special Company Announcement - i.e. Company
				Merge, Company Acquisition, Anniversary etc.', 
			static::ANGLE_OTHER => 'Other',
		);	

		return @$display[$angle];
	}
	
	public static function find_code($writing_order_code_id)
	{
		return static::find('writing_order_code_id', $writing_order_code_id);
	}
	
	public function is_editor_action_required()
	{
		$statuses = array(
			Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION,
			Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS,
			Model_Writing_Order::STATUS_NOT_ASSIGNED,
			Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER,
			Model_Writing_Order::STATUS_CUSTOMER_REJECTED,
		);
		
		return (bool) in_array($this->status, $statuses);
	}
	
}

?>