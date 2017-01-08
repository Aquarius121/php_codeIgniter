<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class PRCom_API_Base extends Iella_Base {

	public function __on_execution_start()
	{
		parent::__on_execution_start();
		$this->iella_out->status = true;
	}

	protected function authorize()
	{
		$secret_file = 'application/config/iella/virtuals/prcom.php';
		$this->secret = file_get_contents($secret_file);
		return parent::authorize();
	}
	
}