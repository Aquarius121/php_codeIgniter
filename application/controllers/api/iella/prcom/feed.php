<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');
load_shared_fnc('api/iella/feed/common');

class Feed_Controller extends PRCom_API_Base {

	use Feed_Common_Trait;

	public function index()
	{
		$this->feed_list();
	}

	public function view()
	{
		$this->feed_view();
	}

}

?>