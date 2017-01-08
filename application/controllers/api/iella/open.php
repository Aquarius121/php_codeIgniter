<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Open_Controller extends Iella_Base {

	protected function authorize()
	{
 		return true;
	}

	public function index()
	{
		$this->iella_out = $this->iella_in;
	}
	
}

?>