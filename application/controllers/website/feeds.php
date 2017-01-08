<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class Feeds_Controller extends Website_Base {

	protected $title = 'RSS Feeds';

	public function index()
	{
		$groups = Model_Beat::list_all_beats_by_group();
		$this->vd->beat_groups = $groups;
		
		$this->load->view('website/header');
		$this->load->view('website/feeds');
		$this->load->view('website/footer');
	}

}

?>