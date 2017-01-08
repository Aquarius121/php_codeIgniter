<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mock_User {
	
	public $first_name;
	public $last_name;
	public $email;

	public function __get($name)
	{
		return null;
	}
		
	public function name()
	{
		return trim(sprintf('%s %s', 
			$this->first_name, 
			$this->last_name));
	}

	public static function from_object($object)
	{
		$instance = new static();
		foreach ($object as $k => $v)
			$instance->{$k} = $v;
		return $instance;
	}
	
}