<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Details_Controller extends Admin_Base {
	
	public function index()
	{
		$id = $this->input->get_post('id');
		$mContent = Model_Content::find($id);
		$mDistBundle = $mContent->distribution_bundle();
		$mExtras = Model_Content_Distribution_Extras::find($mContent->id);
		$mReleasePlus = Model_Content_Release_Plus::find_all_content($mContent->id);

		$this->vd->mContent = $mContent;
		$this->vd->mDistBundle = $mDistBundle;
		$this->vd->mExtras = $mExtras;
		$this->vd->mReleasePlus = $mReleasePlus;

		$this->load->view('admin/publish/distribution/details');
	}

}