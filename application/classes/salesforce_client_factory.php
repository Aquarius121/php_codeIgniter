<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SalesForce_Client_Factory {
	
	protected static $conf;
	
	public static function create()
	{
		$config = static::load_config();
		return static::create_client($config);
	}
	
	public static function load_config()
	{
		if (static::$conf)
			return static::$conf;
		$ci =& get_instance();
		$config = $ci->conf('salesforce');
		static::$conf = $config;
		return $config;
	}
	
	protected static function create_client($config)
	{
		$sfClient = new SforceEnterpriseClient();
		$sfConnection = $sfClient->createConnection($config['wsdl']);
		$sfClient->login($config['username'], $config['password']);
		return $sfClient;
	}
	
}