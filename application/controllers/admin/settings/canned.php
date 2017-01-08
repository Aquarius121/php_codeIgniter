<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Canned_Controller extends Admin_Base {

	public $title = 'Canned Messages';
	
	public function index()
	{
		$this->vd->results = $canned = 
			Model_Canned::find_all();
		
		$this->load->view('admin/header');
		$this->load->view('admin/settings/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/settings/canned');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function edit($id = null)
	{
		if (!($canned = Model_Canned::find($id)))
			$canned = new Model_Canned();
		$this->vd->canned = $canned;
		
		if ($this->input->post('save'))
		{
			$this->set_redirect('admin/settings/canned');
			$canned->values($this->input->post());
			$canned->content = $this->vd->pure($canned->content);
			$canned->save();
			
			// load feedback message for the user
			$feedback_view = 'admin/settings/partials/canned_save_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
		}
		
		$this->load->view('admin/header');
		$this->load->view('admin/settings/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/settings/canned-edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function delete($id)
	{
		if (!($canned = Model_Canned::find($id)))
			$this->redirect('admin/settings/canned');
		
		$this->vd->is_delete = true;
		$this->vd->canned = $canned;
		
		if ($this->input->post('confirm'))
		{
			$canned->delete();
						
			// load feedback message for the user
			$feedback_view = 'admin/settings/partials/canned_delete_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			$this->redirect('admin/settings/canned');
		}
		
		$this->load->view('admin/header');
		$this->load->view('admin/settings/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/settings/canned-edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

}

?>