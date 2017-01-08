<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');
load_controller('api/iella/public');

class API_Base extends Iella_Base {
	
	use Iella_Public;
	
	protected $ssl_required = true;

	public function __construct()
	{
		parent::__construct();
		
		$this->iella_out->warnings = array();
		$this->iella_out->success = true;
		$this->iella_out->errors = array();
		
		if (!Auth::is_user_online() ||
			!Auth::user()->is_reseller)
		{
			$this->iella_out->success = false;
			$this->iella_out->errors[] = 'access denied';
			$this->send();
			exit;
		}
	}

}

?>
