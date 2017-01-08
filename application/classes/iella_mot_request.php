<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Iella_Mot_Request extends Iella_Request {
	
	public function __construct()
	{
		parent::__construct();
		$ci =& get_instance();
		$mot_host = $ci->conf('mot_host');
		$this->base = "http://{$mot_host}/";
	}
	
}

?>
