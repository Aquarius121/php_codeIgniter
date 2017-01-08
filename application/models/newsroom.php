<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Newsroom extends Model {
	
	public $name;
	public $company_id;
	public $company_name;
	public $domain;
	public $timezone;
	public $user_id;
	public $is_common;
	
	protected $_custom = null;
	protected $_profile = null;
	protected $_contact = null;
	
	protected static $__table = 'nr_newsroom';
	protected static $__primary = 'company_id';

	protected static $__allow_zero_id = true;
	
	public static function find_company_id($company)
	{		
		$ci =& get_instance();
		if (isset($ci->newsroom))
			if ($ci->newsroom->company_id == $company)
				return $ci->newsroom;
		
		return static::find($company);
	}
	
	public static function find_name($name)
	{
		return static::find('name', $name);
	}
	
	public static function find_domain($domain)
	{
		return static::find('domain', $domain);
	}
	
	public static function find_user_id($user_id)
	{
		$criteria = array();
		$criteria[] = array('user_id', $user_id);
		$criteria[] = array('is_archived', 0);
		$order = array('order_default', 'desc');
		$newsrooms = static::find_all($criteria, $order);
		return $newsrooms;
	}
	
	public static function find_user_default($user)
	{
		if ($user instanceof Model_User)
			$user = $user->id;
		
		$criteria = array();
		$criteria[] = array('user_id', $user);
		$criteria[] = array('is_archived', 0);
		$order = array('order_default', 'desc');
		$newsrooms = static::find_all($criteria, $order, 1);
		if (!count($newsrooms)) return null;
		return $newsrooms[0];
	}
	
	public static function current()
	{
		$ci =& get_instance();
		return $ci->newsroom;
	}
	
	public static function common()
	{
		$ci =& get_instance();
		$common = new static();
		$common->name = $ci->conf('common_host_name');
		$common->company_name = 'Newswire';
		$common->company_id = 0;
		$common->is_common = true;
		$common->user_id = 0;
		return $common;
	}
	
	public function set_domain($domain)
	{
		$ci =& get_instance();
		$admo_suffix = $ci->conf('admo_suffix');
		$detached_suffix = $ci->conf('detached_suffix');
		$host_suffix = $ci->conf('host_suffix');
		if (str_ends_with($domain, $admo_suffix)) return false;
		if (str_ends_with($domain, $detached_suffix)) return false;
		if (str_ends_with($domain, $host_suffix)) return false;
		if (Model_Newsroom::find_domain($domain)) return false;
		if (!Newsroom_Assist::reaches_newsroom($domain)) return false;
		$this->domain = $domain;
		return true;
	}
	
	public static function create($user, $company_name = null)
	{
		$newsroom = Newsroom_Assist::create($user, $company_name);
		$newsroom->setup_defaults();
		return $newsroom;
	}
	
	public function requires_own_domain()
	{
		if (Auth::is_admin_online())
			return false;
		if (Auth::is_user_online() && 
			 Auth::user()->id == $this->user_id)
			return false;
		if (Auth::requires_user())
			return false;
		return true;
	}
	
	public static function from_company_model($m_company)
	{
		$newsroom = new static();
		$newsroom->name = $m_company->newsroom;
		$newsroom->company_name = $m_company->name;
		$newsroom->company_id = $m_company->id;
		$newsroom->domain = $m_company->domain;
		$newsroom->user_id = $m_company->user_id;
		return $newsroom;
	}
	
	public function url($relative_url = null, $use_domain = false, $requires_own_domain = false)
	{
		// refuse to convert an absolute url
		if (is_absolute_url($relative_url))
			return $relative_url;

		// make sure we don't get double slashes
		if (str_starts_with($relative_url, '/'))
			$relative_url = substr($relative_url, 1);

		$ci =& get_instance();
		$suffix = $ci->conf('host_suffix');
		$host = sprintf('%s%s', $this->name, $suffix);
		$use_ssl = $ci->use_ssl();		
		$base = null;

		// the argument can override the behaviour 
		// so that the domain is forced is available
		$requires_own_domain = $requires_own_domain ||
			$this->requires_own_domain();
		
		// inject the custom host and base
		// because the newsroom is hosted
		// on an external domain/website
		if ($requires_own_domain && 
			$use_domain && 
			$this->domain)
		{
			$use_ssl = false;
			$host = $this->domain;
			$base = $this->domain_base;
			// if base url always end in a slash
			if ($base && !str_ends_with($base, '/'))
				$base = sprintf('%s/', $base);
		}
		
		$url = sprintf('http://%s/%s%s', 
			$host, $base, $relative_url);

		if ($use_ssl)
		     return $ci->ssl_url($url);
		else return $url;
	}
		
	public static function name_available($name)
	{
		return Newsroom_Assist::name_available($name);
	}
	
	public static function normalize_name($name)
	{
		return Newsroom_Assist::normalize_name($name);
	}
	
	public function custom()
	{
		if ($this->_custom === null)
		{
			$company_id = $this->company_id;
			$this->_custom = Model_Newsroom_Custom::find($company_id);
		}
		
		return $this->_custom;
	}
	
	public function contact()
	{		
		if ($this->_contact === null)
		{
			$cc_id = $this->company_contact_id;
			$this->_contact = Model_Company_Contact::find($cc_id);
		}
		
		return $this->_contact;
	}
	
	public function profile()
	{
		if ($this->_profile === null)
		{
			$company_id = $this->company_id;
			$this->_profile = Model_Company_Profile::find($company_id);
		}
		
		return $this->_profile;
	}
	
	public function owner()
	{
		return Model_User::find($this->user_id);
	}

	public function abbr($length)
	{
		return String_Util::abbr($this->company_name, $length);
	}

	public function is_scraped()
	{
		$scraping_sources = Model_Company::scraping_sources();
		return in_array($this->source, $scraping_sources);
	}

	public function setup_defaults()
	{
		// default report settings
		$report = new Model_Report_Settings();
		$report->company_id = $this->company_id;
		$report->pr_email = $this->owner()->email;
		$report->pr_when = 1;
		$report->save();
	}

}
