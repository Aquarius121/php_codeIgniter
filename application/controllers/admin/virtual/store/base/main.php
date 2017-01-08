<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/base');

class VS_Main_Base extends VS_Base {

	public function index()
	{
		$this->redirect(gstring(sprintf('%s/order', $this->store_base)));
	}
	
}

?>