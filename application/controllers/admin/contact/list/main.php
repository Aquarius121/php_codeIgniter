<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Main_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;

	public function index()
	{
		$this->redirect('admin/contact/list/customer');
	}

}

?>