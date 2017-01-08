<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Pitch_Order extends Model {

	const STATUS_NOT_ASSIGNED                       = 'not_assigned';
	const STATUS_ASSIGNED_TO_WRITER                 = 'assigned_to_writer';
	const STATUS_WRITER_REQUEST_DETAILS_REVISION    = 'writer_request_details_revision';
	const STATUS_SENT_BACK_TO_WRITER                = 'sent_back_to_writer';
	const STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE = 'sent_to_customer_for_detail_change';
	const STATUS_CUSTOMER_REVISE_DETAILS            = 'customer_revise_details';
	const STATUS_WRITTEN_SENT_TO_ADMIN              = 'written_sent_to_admin';
	const STATUS_ADMIN_REJECTED                     = 'admin_rejected';
	const STATUS_SENT_TO_CUSTOMER                   = 'sent_to_customer';
	const STATUS_CUSTOMER_REJECTED                  = 'customer_rejected';
	const STATUS_CUSTOMER_ACCEPTED                  = 'customer_accepted';

	const DELIVERY_RUSH     = 'rush';
	const DELIVERY_STANDARD = 'standard';

	const DISTRIBUTION_NONE           = null;
	const DISTRIBUTION_LOCAL          = 'local';
	const DISTRIBUTION_LOCAL_REGIONAL = 'local_regional';
	const DISTRIBUTION_NATIONAL       = 'national';

	const WRITING_TASK_TYPE = 'Pitch';
	const WRITING_ANLE      = 'Media Pitch';
	
	const ITEM_PW_ORDER_SLUG           = 'pitch-wizard-order';
	const ITEM_PW_SECOND_INDUSTRY_SLUG = 'pw-second-industry';
	const ITEM_PW_NATIONAL_DIST_SLUG   = 'pw-national-us-distribution';
	const ITEM_PW_RUSH_DELIVERY_SLUG   = 'pw-rush-delivery';

	const ORDER_TYPE_OUTREACH = Model_Pitch_Session::ORDER_TYPE_OUTREACH;
	const ORDER_TYPE_WRITING = Model_Pitch_Session::ORDER_TYPE_WRITING;

	protected static $__table = 'pw_pitch_order';

	public static function distribution_title($dist)
	{
		$display = array(
			static::DISTRIBUTION_LOCAL => 'Strictly Local Distribution Only',
			static::DISTRIBUTION_LOCAL_REGIONAL => 'Local & Regional U.S. Distribution',
			static::DISTRIBUTION_NATIONAL => 'National U.S. Distribution (+200 Contacts)'
		);
		
		return @$display[$dist];
	}

	public static function full_process($status)
	{	
		$display = array(
			static::STATUS_NOT_ASSIGNED => 'Not Yet Assigned',
			static::STATUS_ASSIGNED_TO_WRITER => 'Assigned to Writer',
			static::STATUS_WRITER_REQUEST_DETAILS_REVISION => 'Writer Requested to Revise Details', 
			static::STATUS_SENT_BACK_TO_WRITER => 'Sent Back to Writer', 
			static::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE => 'Sent to Customer for Revising Details',
			static::STATUS_CUSTOMER_REVISE_DETAILS => 'Customer Revised Details', 
			static::STATUS_WRITTEN_SENT_TO_ADMIN => 'Submitted by Writer',
			static::STATUS_ADMIN_REJECTED => 'Rejected by Admin',
			static::STATUS_SENT_TO_CUSTOMER => 'Sent to Customer',
			static::STATUS_CUSTOMER_REJECTED => 'Rejected by Customer',
			static::STATUS_CUSTOMER_ACCEPTED => 'Approved by Customer'
		);
		
		return @$display[$status];
	}

	public static function status_num_value($status)
	{
		$status_list = array(
			static::STATUS_NOT_ASSIGNED, 
			static::STATUS_ASSIGNED_TO_WRITER,
			static::STATUS_WRITER_REQUEST_DETAILS_REVISION,
			static::STATUS_SENT_BACK_TO_WRITER,
			static::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE,
			static::STATUS_CUSTOMER_REVISE_DETAILS,
			static::STATUS_WRITTEN_SENT_TO_ADMIN,
			static::STATUS_ADMIN_REJECTED,
			static::STATUS_SENT_TO_CUSTOMER,
			static::STATUS_CUSTOMER_REJECTED,
			static::STATUS_CUSTOMER_ACCEPTED
		);

		return array_search($status, $status_list); 
	}

	public function nice_id()
	{
		$chars  = 'abcdefghijklmnopqrstuwxyz';
		$chars .= 'ABCDEFGHIJKLMNOPQRSTUWXYZ';
		$chars .= '0123456789';

		for ($i = 0, $code = null; $i < 8; $i++)
			$code .= $chars[mt_rand(0, strlen($chars) - 1)];

		return $code;
	}

	public function save()
	{
		if ($this->__original->status != $this->status || ! $this->__original)
    			$this->date_of_last_status = Date::$now->format(DATE::FORMAT_MYSQL);

		$previous_max = (int) static::status_num_value($this->__original->max_status);
		$this_process_num = (int) static::status_num_value($this->status);
		if ($this_process_num >= $previous_max)
		{
			$this->max_status = $this->status;
			$this->date_of_max_status = Date::$now->format(DATE::FORMAT_MYSQL);
		}
		parent::save();
	}


}

?>