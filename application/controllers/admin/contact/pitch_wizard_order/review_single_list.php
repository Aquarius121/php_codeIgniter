<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/pitch_wizard_order/main');

class Review_Single_List_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Review List';
	}

	public function index($pitch_list_id)
	{
		
		$post = $this->input->post();
				
		if ($this->input->post('bt_delete'))
		{
			$this->delete_selected_contacts($post);
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');			
			$feedback->set_text('Contacts deleted successfully.');	
			$redirect_url = "admin/contact/pitch_wizard_order/review_single_list/$pitch_list_id";
			$this->add_feedback($feedback);
			$this->redirect(gstring($redirect_url));			
		}
		
		if ($this->input->post('bt_upload_to_customer'))
		{
			$this->upload_to_customer($post);
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Sent to customer successfully.');
			$redirect_url = "admin/contact/pitch_wizard_order/review_list";
			$this->add_feedback($feedback);
			$this->redirect(gstring($redirect_url));
		}
		
		if ($this->input->post('bt_send_to_list_builder'))
		{
			$this->send_to_list_builder($post);
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');			
			$feedback->set_text('Sent to list builder successfully.');
			$redirect_url = 'admin/contact/pitch_wizard_order/rejected_list';
			$this->add_feedback($feedback);
			$this->redirect(gstring($redirect_url));
		}
		
		$this->vd->results = $this->fetch_list_contacts($pitch_list_id);
		$this->vd->rejection_log = Model_Pitch_List_Process::get_rejection_conversation($pitch_list_id);
		$this->vd->pitch_list_id = $pitch_list_id;
		$m_pitch_list = Model_Pitch_List::find($pitch_list_id);
		$this->vd->m_pitch_list = $m_pitch_list;
		
		$this->add_rejection_modal();
		$this->add_order_detail_modal();
		
		$this->add_counters();
		
		$this->load->view('admin/header');
		$this->load->view('admin/contact/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/contact/pitch_wizard_order/review_single_list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');	
	}
	
	protected function fetch_list_contacts($pitch_list_id)
	{
		$sql = "SELECT  co.*, 
			pl.id as pitch_list_id,
			pl.pitch_order_id
			FROM pw_pitch_list pl
			INNER JOIN nr_contact_list cl
			ON pl.contact_list_id = cl.id
			INNER JOIN nr_contact_list_x_contact clxc
			ON clxc.contact_list_id = cl.id
			INNER JOIN nr_contact co
			ON clxc.contact_id = co.id
			WHERE pl.id = ?";
		
		$db_result = $this->db->query($sql, array($pitch_list_id));
		$results = Model_Contact::from_db_all($db_result);			
		return $results;
		
	}
	
	protected function delete_selected_contacts($post)
	{
		$selected = $post['selected'];
		foreach ($selected as $contact_id)
		{
			$contact = Model_Contact::find($contact_id);
			$contact->delete();
		}
	}
	
	
	
	protected function upload_to_customer($post)
	{
		$pitch_list_id = $post['pitch_list_id'];
		$m_pitch_list = Model_Pitch_List::find($pitch_list_id);
		$m_pitch_order = Model_Pitch_Order::find($m_pitch_list->pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pitch_order->campaign_id);
		
		$sql = "UPDATE nr_contact_list 
				SET	company_id = ?
				WHERE id = ?";					
				
		$this->db->query($sql, array($m_campaign->company_id, $m_pitch_list->contact_list_id));
		
		$m_pitch_list->status = Model_Pitch_List::STATUS_SENT_TO_CUSTOMER;
		$m_pitch_list->save();
		
		Model_Pitch_List_Process::create_and_save($pitch_list_id, Model_Pitch_List_Process::PROCESS_SENT_TO_CUSTOMER);	
	}
}

?>