<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Auth_Limiter extends Model {

	protected static $__table = 'nr_auth_limiter';
	protected static $__primary = 'remote_addr';

	protected $max = PHP_INT_MAX;

	public function __construct()
	{
		$this->max = get_instance()->conf('auth_limiter_count');
	}
	
	public static function instance($remote_addr)
	{
		$instance = static::find($remote_addr);
		if (!$instance) $instance = new static();
		$instance->remote_addr = $remote_addr;
		return $instance;
	}

	public function limit()
	{
		if ($this->count >= $this->max)
			return true;

		$this->count++;
		$this->save();
		return false;
	}
	
}
