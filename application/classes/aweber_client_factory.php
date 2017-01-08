<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class AWeber_Client_Factory {
	
	protected static $conf;
	
	public static function create()
	{
		$config = static::load_config();
		return static::create_client($config);
	}
	
	public static function create_and_authenticate()
	{
		$config = static::load_config();
		$client = static::create_client($config);
		$account = $client->getAccount($config['access_key'], $config['access_secret']);
		return $account;
	}
	
	public static function load_config()
	{
		if (static::$conf)
			return static::$conf;
		$ci =& get_instance();
		$config = $ci->conf('aweber');
		static::$conf = $config;
		return $config;
	}
	
	protected static function create_client($config)
	{
		return new AWeberAPI($config['consumer_key'], $config['consumer_secret']);
	}
	
}

?>