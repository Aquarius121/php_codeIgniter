<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Facebook_API {
	
	protected $facebook;
	protected $access_token;	
	protected $page;
	
	public function __construct()
	{
		$this->facebook = static::instance();
		$this->page = null;
	}
	
	public function set_auth($auth)
	{
		if (!($auth instanceof Social_Facebook_Auth))
			throw new Exception();
		$this->set_access_token($auth->access_token);
		$this->set_page($auth->page);
	}
	
	public function set_access_token($token)
	{
		$this->access_token = $token;
	}

	public function set_page($page)
	{
		if (!$page) $page = null;
		$this->page = $page;
	}
	
	public static function instance()
	{
		$ci =& get_instance();
		$config = $ci->conf('facebook_app');
		$facebook = new Facebook($config['api']);
		return $facebook;
	}

	public function __call($name, $arguments)
	{
		if (method_exists($this->facebook, $name))
			return call_user_func_array(array($this->facebook, $name), $arguments);
		throw new BadMethodCallException();
	}
	
}

?>