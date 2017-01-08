<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class View_Controller extends Admin_Base {

	public function index($writer_id = null)
	{
		$writer = Model_MOT_Writer::find($writer_id);
		if (!$writer) $writer = new Model_MOT_Writer();
			
		if ($this->input->post('save'))
		{			
			$writer->id = $writer_id;
			$writer->writer_id = $writer_id;
			$writer->email = strtolower($this->input->post('email'));
			$writer->notes = value_or_null($this->input->post('notes'));
			$writer->first_name = value_or_null($this->input->post('first_name'));
			$writer->last_name = value_or_null($this->input->post('last_name'));
			$writer->is_enabled = $this->input->post('is_enabled');
			$response = $writer->save();
			
			if ($response->success)
			{				
				$feedback_view = 'admin/writing/writers/partials/save_feedback';
				$feedback = $this->load->view($feedback_view, null, true);
				$this->add_feedback($feedback);
				$this->redirect("admin/writing/writers/view/{$writer->id}");
			}
			else 
			{				
				if ($response->response_text == 'duplicate email')
				{
					$feedback_view = 'admin/writing/writers/partials/duplicate_email_feedback';
					$feedback = $this->load->view($feedback_view, null, true);
					$this->add_feedback($feedback);					
					$this->redirect("admin/writing/writers/view/{$writer_id}");
				}
			}
		}

		$this->vd->writer = $writer;		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/writers/view');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function reset($writer_id = null)
	{
		if (!$this->input->post('confirm')) return;
		
		$writer = Model_MOT_Writer::find($writer_id);
		$password = $writer->reset_password();
		$response = new stdClass();
		$response->password = $password;
		$this->json($response);
	}
	
}
