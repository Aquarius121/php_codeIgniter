<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// this class seems a bit strange? 
// need to find out what this does

class Newswire_Iella_Request extends Iella_Request {
	
	public function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
		$this->base = $ci->conf('newswire_host_url');
	}
	
}

?>
