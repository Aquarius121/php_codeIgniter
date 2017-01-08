<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Flock_Mutex {

	protected $handle;

	public function __construct($identifier = null)
	{
		if ($identifier === null)
			$identifier = microtime(true);
		$hash = md5($identifier);
		$base = sys_get_temp_dir();
		$file = "{$base}/{$hash}.lock";
		$this->handle = fopen($file, 'w');
	}
	
	public function lock()
	{
		flock($this->handle, LOCK_EX);
	}
	
	public function unlock()
	{
		flock($this->handle, LOCK_UN);
	}
	
}

?>