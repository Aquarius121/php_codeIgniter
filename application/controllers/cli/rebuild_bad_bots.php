<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Rebuild_Bad_Bots_Controller extends CLI_Base {
	
	public function index()
	{	
		$adminAddrs = array();	
		$mAdmins = Model_User::find_all(array('is_admin', 1));
		foreach ($mAdmins as $mAdmin)
			if ($mAdmin->remote_addr) 
				$adminAddrs[] = $mAdmin->remote_addr;

		$processor = new \Bad_Bots\Processor(array(
			'data_dir' => $this->conf('bad_bots_directory'),
		));

		$processor->exclude_addresses($adminAddrs);
		$processor->remove_old_data($this->conf('bad_bots_max_age'));
		$processor->build_map_file($this->conf('bad_bots_map_file'));
	}

}
