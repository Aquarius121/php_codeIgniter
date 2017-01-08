<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Controller {

	private static $instance;
	private $__ci_setup_done = false;

	public function __construct()
	{
		if (!$this->__ci_setup_done)
			$this->__ci_setup();
	}

	public static function &get_instance()
	{
		return self::$instance;
	}

	protected function __ci_setup()
	{
		$this->__ci_setup_done = true;
		self::$instance =& $this;
		
		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
	}

}