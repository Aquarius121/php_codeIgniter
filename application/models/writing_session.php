<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Writing_Session extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_writing_session';
	
	public static function find_order($writing_order_id)
	{
		return static::find('writing_order_id', $writing_order_id);
	}
	
	public static function find_code($writing_order_code_id)
	{
		return static::find('writing_order_code_id', $writing_order_code_id);
	}
	
	public static function create($uuid = null)
	{
		$instance = new static();
		if ($uuid === null)
		     $instance->id = UUID::create();
		else $instance->id = $uuid;
		$instance->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		return $instance;
	}
	
	public static function is_editable($status)
	{
		// customer can edit details when they are first added or
		// during the period between request for detail change and 
		// transfer back to the writer (admin approval of changes). 
		if ($status == Model_Writing_Order::STATUS_NOT_ASSIGNED) return true;
		if ($status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE) return true;
		if ($status == Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS) return true;
		if (!$status) return true;
		return false;
	}
	
	public static function is_preview_available($status)
	{
		// customer can preview the pr if sent to them or accepted/rejected
		if ($status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER) return true;
		if ($status == Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED) return true;
		if ($status == Model_Writing_Order::STATUS_CUSTOMER_REJECTED) return true;
		return false;
	}
	
	public static function is_customer_action_required($status)
	{
		// customer needed to act if set for review or details required
		if ($status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER) return true;
		if ($status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE) return true;
		if (!$status) return true;
		return false;
	}
	
	public static function is_customer_submit_required($status)
	{
		// customer needs to fill in or submit the writing order
		if ($status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE) return true;
		if (!$status) return true;
		return false;
	}
	
	public static function is_customer_review_required($status)
	{
		// customer needs to review the writing order press release
		if ($status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER) return true;
		return false;
	}
	
	public function nice_id()
	{
		$short = substr($this->id, 0, 8);
		$short = strtoupper($short);
		return $short;
	}
	
	public function id_to_code()
	{
		$short = substr($this->id, 0, 18);
		$short = str_replace('-', null, $short);
		$short = strtoupper($short);
		return $short;
	}
	
	public function notify_written()
	{
		$m_content = Model_Content::find($this->content_id);
		$m_user = $m_content->owner();
		$en = new Email_Notification();
		$en->set_content_view('ws_notify_written');
		$en->set_data('m_content', $m_content);
		$en->send($m_user);
	}
	
	public function notify_written_queued()
	{
		$m_content = Model_Content::find($this->content_id);
		$m_user = $m_content->owner();
		$en = new Email_Notification();
		$en->set_content_view('ws_notify_written_queued');
		$en->set_data('m_content', $m_content);
		$en->send($m_user);
	}
	
	public function notify_no_details_yet()
	{
		$m_newsroom = Model_Newsroom::find($this->company_id);
		$m_user = $m_newsroom->owner();
		$en = new Email_Notification();
		$en->set_content_view('ws_notify_no_details_yet');
		$en->set_data('m_newsroom', $m_newsroom);
		$en->set_data('m_wr_session', $this);
		$en->send($m_user);
	}
	
	public function notify_update_required($comments)
	{
		$raw_data = $this->raw_data();
		$raw_data->editor_comments = $comments;
		$this->raw_data($raw_data);
		$this->save();
		
		$m_company = Model_Company::find($this->company_id);
		$m_user = Model_User::find($m_company->user_id);
		$en = new Email_Notification();
		$en->set_content_view('ws_notify_update_required');
		$en->set_data('m_wr_session', $this);
		$en->send($m_user);
	}
	
}

?>