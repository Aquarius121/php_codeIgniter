<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MOT_Iella_Request extends Iella_Request {
	
	public function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
		$this->base = $ci->conf('mot_host_url');
	}
	
}

?>
