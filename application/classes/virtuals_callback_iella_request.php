<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Virtuals_Callback_Iella_Request extends Iella_Request {
	
	public $enable_debug = true;

	// prevent loading of the default
	// as we are going to replace anyway
	protected $secret = true;

	public function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
		$secret_file = $ci->conf('iella_virtuals_callback_secret_file');
		$this->secret = file_get_contents($secret_file);
		$this->base = null;
	}

	public static function create(Model_Virtual_Source $virtual_source)
	{
		if (!$virtual_source->callback)
			return new Mock_Iella_Request();

		$iella = new static();
		$iella->base = $virtual_source->callback;
		return $iella;
	}
	
}

?>
