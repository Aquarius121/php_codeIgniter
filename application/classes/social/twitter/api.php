<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Twitter_API {
	
	protected $twitter;
	protected $access_token;
	
	public function __construct()
	{
		$this->twitter = static::instance();
	}

	public function set_auth($auth)
	{
		if (!($auth instanceof Social_Twitter_Auth))
			throw new Exception();
		$this->set_access_token((array) $auth);
	}

	public function set_access_token($token)
	{
		// expects as an array with keys:
		// oauth_token, oauth_token_secret
		$this->access_token = $token;
		$this->twitter->setAccessToken($token);
	}

	public function set_default_access_token()
	{
		$ci =& get_instance();
		$config = $ci->conf('twitter_app');
		$this->set_access_token($config['api']['oauth']);
	}
	
	public static function instance()
	{
		$ci =& get_instance();
		$config = $ci->conf('twitter_app');
		$api_key = $config['api']['key'];
		$api_secret = $config['api']['secret'];
		$twitter = new Twitter($api_key, $api_secret);
		return $twitter;
	}

	public function __call($name, $arguments)
	{
		if (method_exists($this->twitter, $name))
			return call_user_func_array(array($this->twitter, $name), $arguments);
		throw new BadMethodCallException();
	}
	
}

?>