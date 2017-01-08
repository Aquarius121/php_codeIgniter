<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Limit_PR extends Model_Limit_Base {
	
	const PERIOD_MONTHLY  = 'MONTHLY';
	const PERIOD_WEEKLY   = 'WEEKLY';
	const PERIOD_DAILY    = 'DAILY';
	
	protected static $__table   = 'nr_limit_pr';
	protected static $__primary = array('limit_id', 'type');
	
	protected $calculated_used;
	
	public function __construct()
	{
		$this->calculated_used = NR_DEFAULT;
		parent::__construct();
	}
	
	public static function find_premium($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		
		$criteria = array();
		$criteria[] = array('user_id', $user);
		$criteria[] = array('type', Model_Content::PREMIUM);
		return static::find($criteria);
	}
	
	public static function find_basic($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		
		$criteria = array();
		$criteria[] = array('user_id', $user);
		$criteria[] = array('type', Model_Content::BASIC);
		return static::find($criteria);
	}
	
	protected function calculate_used()
	{
		if ($this->calculated_used === NR_DEFAULT)
		{			
			$is_premium = (int) ($this->type == Model_Content::PREMIUM);
			$used = static::__calculate_used($this->user_id, $this->period, $is_premium);		
			$this->calculated_used = $used;
		}
		
		return $this->calculated_used;
	}

	public static function __calculate_used($user_id, $period, $is_premium = 0)
	{
		if ($period == static::PERIOD_MONTHLY)
			  $dt_cut = Date::days(-30);
		else if ($period == static::PERIOD_WEEKLY)
			  $dt_cut = Date::days(-7);
		else if ($period == static::PERIOD_DAILY)
			  $dt_cut = Date::days(-1);
		else $dt_cut = Date::days(-$period);

		$dt_cut_str = $dt_cut->format(Date::FORMAT_MYSQL);	
		$sql = "SELECT 1 FROM nr_company cm
			INNER JOIN nr_content ct ON 
			cm.user_id = {$user_id} AND
			cm.id = ct.company_id AND
			ct.type = 'pr' AND
			ct.is_premium = {$is_premium} AND
			(ct.is_published = 1 OR 
			 ct.is_under_review = 1 OR 
			 ct.is_approved = 1 OR
			 ct.is_credit_locked = 1) AND
			ct.date_publish > '{$dt_cut_str}'";

		$dbr = static::__db()->query($sql);
		$used = $dbr->num_rows();
		return max(0, $used);
	}

	public function calculate_next_available_date()
	{
		$is_premium = (int) ($this->type == Model_Content::PREMIUM);
		return static::__calculate_next_available_date($this->user_id, 
			$this->period, $this->total(), $is_premium);
	}

	public static function __calculate_next_available_date($user_id, $period, $count, $is_premium = 0)
	{
		if ($count === 0) return false;
		if ($period == static::PERIOD_MONTHLY)
			  $dt_cut = Date::days(-30);
		else if ($period == static::PERIOD_WEEKLY)
			  $dt_cut = Date::days(-7);
		else if ($period == static::PERIOD_DAILY)
			  $dt_cut = Date::days(-1);
		else $dt_cut = Date::days(-$period);
				
		$dt_cut_str = $dt_cut->format(Date::FORMAT_MYSQL);			
		$sql = "SELECT ct.date_publish
			FROM nr_company cm
			INNER JOIN nr_content ct ON 
			cm.user_id = {$user_id} AND
			cm.id = ct.company_id AND
			ct.type = 'pr' AND
			ct.is_premium = {$is_premium} AND
			(ct.is_published = 1 OR 
			 ct.is_under_review = 1 OR 
			 ct.is_approved = 1 OR
			 ct.is_credit_locked = 1) AND
			ct.date_publish > '{$dt_cut_str}'
			ORDER BY ct.date_publish DESC";
			
		$dbr = static::__db()->query($sql);
		$dates = Model_Base::from_db_all($dbr);
		if (count($dates) < $count)
			return Date::$now;
		$dt_last = Date::utc($dates[$count-1]->date_publish);
		$dt_interval = $dt_cut->diff($dt_last);
		$dt_next = clone Date::$now;
		$dt_next->add($dt_interval);
		return $dt_next;
	}
	
	public function consume($count)
	{
		$this->calculated_used = NR_DEFAULT;
		if ($this->uses_calculated) return;
		
		$consumed = min($count, $this->available());
		$this->amount_used += $consumed;
		$this->save();
		return $consumed;
	}
		
	public function restore()
	{
		$this->calculated_used = NR_DEFAULT;
		if ($this->uses_calculated) return;
		if ($this->amount_used > 0)
			$this->amount_used--;
		$this->save();
	}
	
	public function available()
	{
		return max(0, ($this->total() - $this->used()));
	}
	
	public function used()
	{
		if ($this->uses_calculated)
			return $this->calculate_used();
		return $this->amount_used;
	}
	
	public function total()
	{
		return $this->amount_total;
	}
	
}

?>