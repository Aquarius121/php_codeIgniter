<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Create_Iella_Secret_Controller extends CLI_Base {
	
	public function index()
	{
		$this->console('<?php return; ?>');
		$this->console(null);

		for ($i = 0; $i < 64; $i++)
		{
			$this->console(md5(UUID::create()));
			usleep(20000);
		}
	}

}