<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/base');

class Main_Controller extends Reseller_Base {

	public function index()
	{
		if ($this->is_reseller_editor())
		     $this->redirect('reseller/dashboard');
		else $this->redirect('reseller/publish/pr');
	}

}

?>
