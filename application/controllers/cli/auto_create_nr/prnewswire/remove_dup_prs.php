<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Remove_Dup_PRs_Controller extends Auto_Create_NR_Base { 

	public function index()
	{
		$this->remove_prnewswire_dup_prs();
	}
}

?>
