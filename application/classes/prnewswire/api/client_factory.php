<?php 

// creates PRNewswire_API_Client instance
// using the standard CI configuration
class PRNewswire_API_Client_Factory {
	
	public static function create()
	{
		$ci =& get_instance();
		$config = $ci->conf('prnewswire');
		if (!isset($config['api_key_confirm']) ||
			$config['api_key'] !== $config['api_key_confirm'])
			throw new Exception('api confirmation mismatch');
		$instance = new PRNewswire_API_Client($config);
		return $instance;
	}
	
}