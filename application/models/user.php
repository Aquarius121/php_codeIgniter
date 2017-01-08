<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_User extends Model {

	use Raw_Data_Trait;
	
	public $has_full_access = false;
	
	protected $n_pr_free_available;
	protected $m_limit_pr_premium;
	protected $m_limit_pr_basic;
	protected $m_limit_pr_held_premium;
	protected $m_limit_pr_held_basic;
	protected $m_limit_email_held;
	protected $m_limit_newsroom_held;
	protected $m_limit_email;
	protected $m_limit_newsroom;
	protected $m_limit_writing_held;
	protected $m_limit_writing;
	protected $m_user_mail_blocks;
	protected $m_user_plan;
	protected $m_plan;
	
	const PACKAGE_SILVER   = Package::PACKAGE_SILVER;
	const PACKAGE_GOLD     = Package::PACKAGE_GOLD;
	const PACKAGE_PLATINUM = Package::PACKAGE_PLATINUM;
	const PACKAGE_BASIC    = Package::PACKAGE_BASIC;

	const DEFAULT_ACCOUNT_ID = 1;
	
	protected static $__table = 'nr_user';
	protected static $__compressed = array('raw_data');
	
	public function __construct()
	{
		$this->n_pr_free_available = NR_DEFAULT;
		$this->m_limit_pr_premium = NR_DEFAULT;
		$this->m_limit_pr_basic = NR_DEFAULT;
		$this->m_limit_pr_held_premium = NR_DEFAULT;
		$this->m_limit_pr_held_basic = NR_DEFAULT;
		$this->m_limit_email_held = NR_DEFAULT;
		$this->m_limit_newsroom_held = NR_DEFAULT;
		$this->m_limit_email = NR_DEFAULT;
		$this->m_limit_newsroom = NR_DEFAULT;
		$this->m_limit_writing_held = NR_DEFAULT;
		$this->m_limit_writing = NR_DEFAULT;
		$this->m_user_mail_blocks = NR_DEFAULT;
		$this->m_user_plan = NR_DEFAULT;
		$this->m_plan = NR_DEFAULT;
		
		parent::__construct();
	}
	
	public static function find_email($email)
	{
		$ci =& get_instance();
		$dbi = $ci->db->select('*')
			->from(static::$__table)
			->where('email', $email);
			
		return static::from_db($dbi->get());
	}
	
	public static function find_company_id($company)
	{
		$table = static::$__table;
		$sql = "SELECT u.* FROM {$table} u INNER JOIN 
			nr_newsroom n ON n.user_id = u.id WHERE
			n.company_id = ?";
		
		$ci =& get_instance();
		$dbr = $ci->db->query($sql, array($company));
			
		return static::from_db($dbr);
	}
	
	public static function authenticate($email, $password)
	{
		$user = static::find_email($email);
		if ($user === false) return false;
		if (!Blowfish::__hash($password, $user->password)) return false;
		return $user;
	}
	
	public function set_password($password)
	{
		$password = Blowfish::__hash($password);
		$this->password = $password;
	}
	
	public function newsrooms()
	{
		return Model_Newsroom::find_user_id($this->id);
	}
	
	public function default_newsroom()
	{
		if (!($newsroom = Model_Newsroom::find_user_default($this)))
			$newsroom = Model_Newsroom::create($this);
		return $newsroom;
	}
	
	public function name()
	{
		return trim(sprintf('%s %s', 
			$this->first_name, 
			$this->last_name));
	}
	
	public function m_limit_pr_premium()
	{
		if ($this->m_limit_pr_premium === NR_DEFAULT)
			$this->m_limit_pr_premium = Model_Limit_PR::find_premium($this);
		return $this->m_limit_pr_premium;
	}
	
	public function m_limit_pr_basic()
	{
		if ($this->m_limit_pr_basic === NR_DEFAULT)
			$this->m_limit_pr_basic = Model_Limit_PR::find_basic($this);
		return $this->m_limit_pr_basic;
	}
	
	public function m_limit_pr_held_premium()
	{
		if ($this->m_limit_pr_held_premium === NR_DEFAULT)
			$this->m_limit_pr_held_premium = Model_Limit_PR_Held::find_premium($this);
		return $this->m_limit_pr_held_premium;
	}
	
	public function m_limit_pr_held_basic()
	{
		if ($this->m_limit_pr_held_basic === NR_DEFAULT)
			$this->m_limit_pr_held_basic = Model_Limit_PR_Held::find_basic($this);
		return $this->m_limit_pr_held_basic;
	}
	
	public function m_limit_email_held()
	{
		if ($this->m_limit_email_held === NR_DEFAULT)
			$this->m_limit_email_held = Model_Limit_Email_Held::find_user($this);
		return $this->m_limit_email_held;
	}
	
	public function m_limit_email()
	{
		if ($this->m_limit_email === NR_DEFAULT)
			$this->m_limit_email = Model_Limit_Email::find_user($this);
		return $this->m_limit_email;
	}
	
	public function m_limit_writing_held()
	{
		if ($this->m_limit_writing_held === NR_DEFAULT)
			$this->m_limit_writing_held = Model_Limit_Writing_Held::find_user($this);
		return $this->m_limit_writing_held;
	}
	
	public function m_limit_writing()
	{
		if ($this->m_limit_writing === NR_DEFAULT)
			$this->m_limit_writing = Model_Limit_Writing::find_user($this);
		return $this->m_limit_writing;
	}
	
	public function m_limit_newsroom_held()
	{
		if ($this->m_limit_newsroom_held === NR_DEFAULT)
			$this->m_limit_newsroom_held = Model_Limit_Newsroom_Held::find_user($this);
		return $this->m_limit_newsroom_held;
	}
	
	public function m_limit_newsroom()
	{
		if ($this->m_limit_newsroom === NR_DEFAULT)
			$this->m_limit_newsroom = Model_Limit_Newsroom::find_user($this);
		return $this->m_limit_newsroom;
	}
	
	public function m_user_plan()
	{
		if ($this->m_user_plan === NR_DEFAULT)
			$this->m_user_plan = Model_User_Plan::find_active($this);
		return $this->m_user_plan;
	}

	public function m_plan()
	{
		$m_user_plan = $this->m_user_plan();
		if (!$m_user_plan) return null;
		if ($this->m_plan === NR_DEFAULT)
			$this->m_plan = Model_Plan::find($m_user_plan->plan_id);
		return $this->m_plan;
	}

	public function plan_renewal_ci()
	{
		// find the renewal that is currently active
		// * there should only be 1 such renewal
		$sql = "SELECT ci.* FROM co_component_item ci 
			INNER JOIN co_component_set cs 
			ON ci.component_set_id = cs.id 
			AND (ci.is_renewable = 1 OR ci.is_auto_renew_enabled = 1)
			AND cs.user_id = ? 
			INNER JOIN co_item i 
			ON ci.item_id = i.id AND i.type = ?
			LIMIT 1";
			
		$db_result = $this->db->query($sql, 
			array($this->id, Model_Item::TYPE_PLAN));
		$ex_component_item = Model_Component_Item::from_db($db_result);
		return $ex_component_item;
	}
	
	public function email_credits()
	{
		$available = 0;
		
		if ($this->m_limit_email())
			$available += $this->m_limit_email()->available();
		if ($this->m_limit_email_held())
			$available += $this->m_limit_email_held()->available();
		
		return $available;
	}
	
	public function writing_credits()
	{
		$available = 0;
		
		if ($this->m_limit_writing())
			$available += $this->m_limit_writing()->available();			
		if ($this->m_limit_writing_held())
			$available += $this->m_limit_writing_held()->available();
		
		return $available;
	}
	
	public function pr_credits_premium()
	{
		$available = 0;
		
		if ($this->m_limit_pr_premium())
			$available += $this->m_limit_pr_premium()->available();
		if ($this->m_limit_pr_held_premium())
			$available += $this->m_limit_pr_held_premium()->available();
		
		return $available;
	}
	
	public function pr_credits_basic()
	{
		$available = 0;
		
		if ($this->m_limit_pr_basic() && $this->m_limit_pr_basic()->total())
		{
			$available += $this->m_limit_pr_basic()->available();
		}
		else
		{
			$count = Model_Setting::value('free_basic_pr_count');
			$period = Model_Setting::value('free_basic_pr_period');
			$used = Model_Limit_PR::__calculate_used($this->id, $period, 0);
			$available += max(0, ($count - $used));
		}
		
		if ($this->m_limit_pr_held_basic())
			$available += $this->m_limit_pr_held_basic()->available();
		
		return $available;
	}
	
	public function email_credits_stat()
	{
		$stat = new stdClass();
		$stat->held_available = 0;
		$stat->held_total = 0;
		$stat->rollover_available = 0;
		$stat->rollover_total = 0;
		$stat->available = 0;
		$stat->total = 0;
		$stat->used = 0;
		
		if ($this->m_limit_email())
		{
			$stat->rollover_available += $this->m_limit_email()->available();
			$stat->rollover_total += $this->m_limit_email()->total();
		}
		
		if ($this->m_limit_email_held())
		{
			$stat->held_available += $this->m_limit_email_held()->available();
			$stat->held_total += $this->m_limit_email_held()->total();
		}
		
		$stat->rollover_used = $stat->rollover_total - $stat->rollover_available;
		$stat->held_used = $stat->held_total - $stat->held_available;
		
		$stat->available += $stat->rollover_available;
		$stat->total += $stat->rollover_total;			
		$stat->available += $stat->held_available;
		$stat->total += $stat->held_total;
		$stat->used = $stat->total - $stat->available;
		
		return $stat;
	}
	
	public function writing_credits_stat()
	{
		$stat = new stdClass();
		$stat->held_available = 0;
		$stat->held_total = 0;
		$stat->rollover_available = 0;
		$stat->rollover_total = 0;
		$stat->available = 0;
		$stat->total = 0;
		$stat->used = 0;
		
		if ($this->m_limit_writing())
		{
			$stat->rollover_available += $this->m_limit_writing()->available();
			$stat->rollover_total += $this->m_limit_writing()->total();
		}
		
		if ($this->m_limit_writing_held())
		{
			$stat->held_available += $this->m_limit_writing_held()->available();
			$stat->held_total += $this->m_limit_writing_held()->total();
		}
		
		$stat->rollover_used = $stat->rollover_total - $stat->rollover_available;
		$stat->held_used = $stat->held_total - $stat->held_available;
		
		$stat->available += $stat->rollover_available;
		$stat->total += $stat->rollover_total;			
		$stat->available += $stat->held_available;
		$stat->total += $stat->held_total;
		$stat->used = $stat->total - $stat->available;
		
		return $stat;
	}
	
	public function pr_credits_premium_stat()
	{
		$stat = new stdClass();
		$stat->held_available = 0;
		$stat->held_total = 0;
		$stat->rollover_available = 0;
		$stat->rollover_total = 0;
		$stat->available = 0;
		$stat->total = 0;
		$stat->used = 0;
		
		if ($this->m_limit_pr_premium())
		{
			$stat->rollover_available += $this->m_limit_pr_premium()->available();
			$stat->rollover_total += $this->m_limit_pr_premium()->total();
		}
		
		if ($this->m_limit_pr_held_premium())
		{
			$stat->held_available += $this->m_limit_pr_held_premium()->available();
			$stat->held_total += $this->m_limit_pr_held_premium()->total();
		}
		
		$stat->rollover_used = $stat->rollover_total - $stat->rollover_available;
		$stat->held_used = $stat->held_total - $stat->held_available;
		
		$stat->available += $stat->rollover_available;
		$stat->total += $stat->rollover_total;			
		$stat->available += $stat->held_available;
		$stat->total += $stat->held_total;
		$stat->used = $stat->total - $stat->available;
		
		return $stat;
	}
	
	public function pr_credits_basic_stat()
	{
		$stat = new stdClass();		
		$stat->next_available = clone Date::$now;
		$stat->held_available = 0;
		$stat->held_total = 0;
		$stat->rollover_available = 0;
		$stat->rollover_total = 0;
		$stat->available = 0;
		$stat->total = 0;
		$stat->used = 0;
						
		if ($this->m_limit_pr_held_basic())
		{
			$stat->held_available += $this->m_limit_pr_held_basic()->available();
			$stat->held_total += $this->m_limit_pr_held_basic()->total();
			$stat->available += $stat->held_available;
			$stat->total += $stat->held_total;
		}

		if ($this->m_limit_pr_basic() && $this->m_limit_pr_basic()->total())
		{
			$stat->rollover_available += $this->m_limit_pr_basic()->available();
			$stat->rollover_total += $this->m_limit_pr_basic()->total();
			$stat->available += $stat->rollover_available;
			$stat->total += $stat->rollover_total;

			if (!$stat->available)
				$stat->next_available = $this->m_limit_pr_basic()
					->calculate_next_available_date();
		}		
		else 
		{
			$count = Model_Setting::value('free_basic_pr_count');
			$period = Model_Setting::value('free_basic_pr_period');
			$used = Model_Limit_PR::__calculate_used($this->id, $period, 0);
			$stat->rollover_available += max(0, ($count - $used));
			$stat->rollover_total += $count;
			$stat->available += $stat->rollover_available;
			$stat->total += $stat->rollover_total;

			if (!$stat->available)
				$stat->next_available = Model_Limit_PR::__calculate_next_available_date(
					$this->id, $period, $count, 0);
		}
		
		$stat->rollover_used = $stat->rollover_total - $stat->rollover_available;
		$stat->held_used = $stat->held_total - $stat->held_available;
		$stat->used = $stat->total - $stat->available;
		
		return $stat;
	}

	protected function calculate_free_pr_credits_basic_next_date()
	{
		if ($this->n_pr_free_available === NR_DEFAULT)
		{
			$count = Model_Setting::value('free_basic_pr_count');
			$period = Model_Setting::value('free_basic_pr_period');
			$dt_cut = Date::days(-$period)->format(Date::FORMAT_MYSQL);
			
			$sql = "SELECT 1 FROM nr_company cm
				INNER JOIN nr_content ct ON 
				cm.user_id = {$this->id} AND
				cm.id = ct.company_id AND
				ct.type = 'pr' AND
				ct.is_premium = 0 AND
				(ct.is_published = 1 OR 
				 ct.is_under_review = 1 OR 
				 ct.is_approved = 1 OR
				 ct.is_credit_locked = 1) AND
				ct.date_publish > '{$dt_cut}'";
				
			$dbr = $this->db->query($sql);
			$used = $dbr->num_rows();			
			$this->n_pr_free_available = max(0, ($count - $used));
		}
		
		return $this->n_pr_free_available;
	}
	
	public function consume_pr_credit_basic($content)
	{
		$consumer = new PR_Credit_Consumer();
		$consumer->set_held($this->m_limit_pr_held_basic());
		$consumer->set_plan($this->m_limit_pr_basic());
		$consumer->consume($content);
	}
	
	public function consume_pr_credit_premium($content)
	{
		$consumer = new PR_Credit_Consumer();
		$consumer->set_held($this->m_limit_pr_held_premium());
		$consumer->set_plan($this->m_limit_pr_premium());
		$consumer->consume($content);
	}
	
	public function consume_email_credits($count)
	{	
		$consumer = new Email_Credit_Consumer();
		$consumer->set_held($this->m_limit_email_held());
		$consumer->set_plan($this->m_limit_email());
		$consumer->consume($count);
	}
	
	public function consume_writing_credits($count)
	{	
		$consumer = new Writing_Credit_Consumer();
		$consumer->set_held($this->m_limit_writing_held());
		$consumer->set_plan($this->m_limit_writing());
		$consumer->consume($count);
	}
	
	public function newsroom_credits_stat()
	{
		$stat = new stdClass();
		$stat->rollover = 0;
		if ($this->m_limit_newsroom())
			$stat->rollover += $this->m_limit_newsroom()->total();
		$stat->total = $stat->rollover;
		if ($this->m_limit_newsroom_held())
			$stat->total += $this->m_limit_newsroom_held()->total();
		$stat->used = $this->newsroom_credits_used();
		$stat->available = $stat->total - $stat->used;
		return $stat;
	}
	
	public function newsroom_credits_total()
	{
		$total = 0;
		if ($this->m_limit_newsroom())
			$total += $this->m_limit_newsroom()->total();
		if ($this->m_limit_newsroom_held())
			$total += $this->m_limit_newsroom_held()->total();
		return $total;
	}
	
	public function newsroom_credits_used()
	{
		$criteria = array();
		$criteria[] = array('user_id', $this->id);
		$criteria[] = array('is_active', 1);
		return Model_Newsroom::count($criteria);
	}
	
	public function newsroom_credits_available()
	{
		return max(0, $this->newsroom_credits_total() 
			- $this->newsroom_credits_used());
	}
	
	public function newsroom_credits()
	{
		return $this->newsroom_credits_available();
	}
	
	public function is_free_user()
	{
		return ! $this->has_basic_access();
	}
	
	public function has_platinum_access()
	{
		return $this->package == static::PACKAGE_PLATINUM ||
		       $this->has_full_access;
	}
	
	public function has_gold_access()
	{
		return $this->package == static::PACKAGE_PLATINUM ||
		       $this->package == static::PACKAGE_GOLD ||
		       $this->has_full_access;
	}
	
	public function has_silver_access()
	{
		return $this->package == static::PACKAGE_PLATINUM ||
		       $this->package == static::PACKAGE_GOLD || 
		       $this->package == static::PACKAGE_SILVER ||
		       $this->has_full_access;
	}

	public function has_basic_access()
	{
		return $this->package == static::PACKAGE_PLATINUM ||
		       $this->package == static::PACKAGE_GOLD || 
		       $this->package == static::PACKAGE_SILVER ||
		       $this->package == static::PACKAGE_BASIC ||
		       $this->has_full_access;
	}
	
	public function package_name()
	{
		return Package::name($this->package);
	}
	
	public function package_expires()
	{
		return $this->plan_expires();
	}
	
	public function plan_name()
	{
		$u_plan = $this->m_user_plan();
		if (!$u_plan) return 'Free';
		$plan = Model_Plan::find($u_plan->plan_id);
		if (!$plan) return 'Free';
		return $plan->name;
	}
	
	public function plan_expires()
	{
		if ($this->m_user_plan()) 
			return $this->m_user_plan()->date_expires;
		return null;
	}

	public function m_user_mail_blocks()
	{
		if ($this->m_user_mail_blocks === NR_DEFAULT)
			$this->m_user_mail_blocks = Model_User_Mail_Blocks::find_user($this);
		return $this->m_user_mail_blocks;
	}

	public function is_mail_blocked($pref)
	{
		if (!$this->m_user_mail_blocks()) return false;
		return $this->m_user_mail_blocks()->has($pref);
	}
	
	public static function generate_password($length = 16)
	{
		// password can contain common symbols
		// letters (including capital) and numbers
		$chars  = '-=~!@#$%^&*()_+,.<>?;:[]{}';
		$chars .= 'abcdefghijklmnopqrstuwxyz';
		$chars .= 'ABCDEFGHIJKLMNOPQRSTUWXYZ';
		$chars .= '0123456789';		
		for ($i = 0, $pass = null; $i < $length; $i++)
			$pass .= $chars[mt_rand(0, strlen($chars) - 1)];
		return $pass;
	}

	public static function generate_alphanumeric_password($length = 16)
	{
		// password can contain common symbols
		// letters (including capital) and numbers		
		$chars  = 'abcdefghijklmnopqrstuwxyz';
		$chars .= 'ABCDEFGHIJKLMNOPQRSTUWXYZ';
		$chars .= '0123456789';		
		for ($i = 0, $pass = null; $i < $length; $i++)
			$pass .= $chars[mt_rand(0, strlen($chars) - 1)];
		return $pass;
	}
	
	public static function create()
	{
		return Model_User_Base::create_user();
	}

	public function has_clients()
	{
		if ($this->has_platinum_access()) return true;
		$raw_data = $this->raw_data();
		if (isset($raw_data->has_clients) &&
			$raw_data->has_clients)
			return true;
		return false;
	}

	public function is_virtual()
	{
		return Virtual_User::is_virtual_email($this->email);
	}

	public function virtual_user()
	{
		if (!$this->is_virtual()) return null;
		if (empty($this->raw_data()->virtual_user)) return null;
		$virtual_user = Virtual_User::from_object($this->raw_data()->virtual_user);
		$virtual_user->virtual_source_id = $this->virtual_source_id;
		return $virtual_user;
	}

	public function virtual_source()
	{
		if (!$this->is_virtual()) return null;
		return Model_Virtual_Source::find($this->virtual_source_id);
	}

}
