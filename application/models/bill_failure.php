<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Bill_Failure extends Model {

	use Raw_Data_Trait;

	const TYPE_ORDER   = 'order';
	const TYPE_RENEWAL = 'renewal';
	const TYPE_BILLING = 'billing';

	const BILL_BLOCK_THRESHOLD = 3;
	const BILL_BLOCK_PERIOD = 86400;
	
	protected static $__table = 'co_bill_failure';

	public function __construct()
	{
		parent::__construct();
		$this->date_created = (string) Date::$now;
	}

	public function notify()
	{
		$this->notify_staff();
	}

	public function notify_staff()
	{
		$emails_block = Model_Setting::value('staff_email_bill_failure');
		$emails_addr = Model_Setting::parse_block($emails_block);

		foreach ($emails_addr as $email_addr)
		{
			$mock_user = new Mock_User();
			$mock_user->email = $email_addr;
			$subject = 'Bill Failure';

			$buffer_file = File_Util::buffer_file();
			$failure_report = $this->generate_report();
			file_put_contents($buffer_file, $failure_report);

			$notification = new Email_Notification('admin/bill_failure');
			$notification->set_data('failure', $this);
			$email = $notification->generate($mock_user, $subject, true);
			$email->add_attachment($buffer_file, 'details.html');
			Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
		}
	}

	public function generate_report()
	{
		$ci =& get_instance();
		$raw_data = $this->raw_data();

		$vd = new stdClass();
		$vd->title = $this->date_created;
		$vd->data = new stdClass();
		$vd->data->user = Model_User::find($this->user_id);
		$vd->data->date = $this->date_created;
		foreach ($raw_data as $k => $v)
			$vd->data->{$k} = $v;

		if ($raw_data->cart)
		{
			$vd->data->cart = Virtual_Cart::instance();
			$vd->data->cart->unserialize($raw_data->cart);
			foreach ($vd->data->cart->items() as $item) 
				// expose the item name for debug
				$item->_cache_item_name();
			$vd->data->cart = $vd->data->cart->serialize(true);
		}

		$output = $ci->load->view('shared/report/json', $vd, true);
		return $output;	
	}

	public static function has_bill_block_user($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;

		$criteria = array();
		$criteria[] = array('user_id', $user);
		$criteria[] = array('date_created', Model::CMP_GREATER_THAN,
			Date::seconds(-static::BILL_BLOCK_PERIOD));
		$criteria[] = array('is_safe', 0);
		return static::count_all($criteria) >= static::BILL_BLOCK_THRESHOLD;
	}

	public static function has_bill_block_addr($addr)
	{
		$criteria = array();
		$criteria[] = array('remote_addr', $addr);
		$criteria[] = array('date_created', Model::CMP_GREATER_THAN,
			Date::seconds(-static::BILL_BLOCK_PERIOD));
		$criteria[] = array('is_safe', 0);
		return static::count_all($criteria) >= static::BILL_BLOCK_THRESHOLD;
	}

	public static function mark_safe_user($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;

		$criteria = array();
		$criteria[] = array('user_id', $user);
		$criteria[] = array('date_created', Model::CMP_GREATER_THAN,
			Date::seconds(-static::BILL_BLOCK_PERIOD));
		$criteria[] = array('is_safe', 0);
		$failures = static::find_all($criteria);
		
		foreach ($failures as $failure)
		{
			$failure->is_safe = 1;
			$failure->save();
		}
	}

	public static function mark_safe_addr($addr)
	{
		$criteria = array();
		$criteria[] = array('remote_addr', $addr);
		$criteria[] = array('date_created', Model::CMP_GREATER_THAN,
			Date::seconds(-static::BILL_BLOCK_PERIOD));
		$criteria[] = array('is_safe', 0);
		$failures = static::find_all($criteria);
		
		foreach ($failures as $failure)
		{
			$failure->is_safe = 1;
			$failure->save();
		}
	}

	
}

?>