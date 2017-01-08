<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contact_Unsubscribe_Controller extends CIL_Controller {
	
	public function index()
	{
		$id = $this->input->get('id');
		$data = $this->input->get('data');
		$campaign_id = $this->input->get('cid'); 
		
		if (!$id) return;
		if (!$data) return;
		if (!$campaign_id) return;
		$contact = Model_Contact::find($id);
		if (!$contact) return;
		$campaign = Model_Campaign::find($campaign_id);
		if (!$campaign) return;		
		$newsroom = Model_Newsroom::find_company_id($campaign->company_id);
		if (!$newsroom) return;
		
		$this->vd->newsroom = $newsroom;
		$this->vd->contact = $contact;

		if ($this->input->post('confirm') && $this->input->post('unsubscribe'))
		{
			if ($this->input->post('unsubscribe') == "company")
				$this->vd->result = $contact->unsubscribe($data, $campaign->company_id);
			elseif ($this->input->post('unsubscribe') == "all")
				$this->vd->result = $contact->unsubscribe($data);

			$this->load->view('common/header');
			$this->load->view('common/unsubscribe_status');
			$this->load->view('common/footer');
		}
		else
		{
			$this->load->view('common/header');
			$this->load->view('common/unsubscribe');
			$this->load->view('common/footer');
		}
	}

	public function instant()
	{
		$id = $this->input->get('id');
		$data = $this->input->get('data');
		
		if (!$id) return;
		if (!$data) return;
		$contact = Model_Contact::find($id);
		if (!$contact) return;
		
		$contact->unsubscribe($data);
	}
	
}

?>