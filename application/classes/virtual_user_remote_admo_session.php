<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Virtual_User_Remote_Admo_Session extends Raw_Data {

	public $id;
	public $admin_user;
	public $user;

	public static function create(Virtual_User $user, Model_User $admin_user = null)
	{
		if ($admin_user === null) 
			$admin_user = Auth::admin_user();

		$instance = new static();
		$instance->id = md5(UUID::create());
		$instance->user = $user;
		$instance->admin_user = $admin_user;
		$serialized = json_encode($instance);
		$key = static::cache_key($instance->id);
		Data_Cache_LT::write($key, $serialized, 86400);
		return $instance;
	}

	public static function load($id)
	{
		$key = static::cache_key($id);
		$serialized = Data_Cache_LT::read($key);
		if (!$serialized) return null;
		$instance = static::from_object(json_decode($serialized));
		$instance->id = $instance->id;
		$instance->admin_user = Model_User::from_object($instance->admin_user);
		$instance->user = Virtual_User::from_object($instance->user);
		return $instance;
	}

	protected static function cache_key($id)
	{
		return sprintf('vsras_%s', $id);
	}

	public function url($relative = null)
	{
		$source = $this->user->virtual_source();
		if (!$source->admo_url)
			return false;

		return sprintf($source->admo_url, 
			$this->id, $relative);
	}

}

?>