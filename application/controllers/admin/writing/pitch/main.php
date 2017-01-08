<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');
load_controller('shared/common_pw_orders_trait');

class Main_Controller extends Admin_Base {

	use Common_PW_Orders_Trait;
	
	const LISTING_CHUNK_SIZE = 20;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->vd->title[] = 'Writing';
		$this->vd->title[] = 'Pitches';
	}

	public function index($chunk = 1)
	{
		$this->redirect('admin/writing/pitch/pw_order/all');
	}
	
	protected function add_counters()
	{
		$criteria_assign = array();
		$criteria_assign[] = array('status', Model_Pitch_Order::STATUS_NOT_ASSIGNED);
		$criteria_assign[] = array('is_writing_archived', 0);
		$this->vd->assign_count = Model_Pitch_Order::count_all($criteria_assign);
		
		$pending_processes = sql_in_list(array(Model_Pitch_ORDER::STATUS_WRITER_REQUEST_DETAILS_REVISION, 
								Model_Pitch_ORDER::STATUS_CUSTOMER_REVISE_DETAILS));
		$criteria_pending = array();
		$criteria_pending[] = array('is_writing_archived', 0);
		$criteria_pending[] = array("status IN ({$pending_processes})");	
		$this->vd->pending_count = Model_Pitch_Order::count_all($criteria_pending);
		
		$criteria_review = array();
		$criteria_review[] = array('status', Model_Pitch_Order::STATUS_WRITTEN_SENT_TO_ADMIN);
		$criteria_review[] = array('is_writing_archived', 0);
		$this->vd->review_count = Model_Pitch_Order::count_all($criteria_review);
		
		$criteria_rejected = array();
		$criteria_rejected[] = array('status', Model_Pitch_Order::STATUS_CUSTOMER_REJECTED);
		$criteria_rejected[] = array('is_writing_archived', 0);
		$this->vd->rejected_count = Model_Pitch_Order::count_all($criteria_rejected);
	}
	
	protected function render($chunkination, $results, $view_name)
	{
		$this->vd->chunkination = $chunkination;		
		$this->vd->results = $results;
		$this->add_counters();
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view($view_name);
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	protected function add_user_company_search_filter($filter = 1)
	{	
		$use_additional_tables = false;
		$additional_tables = '';		
		
		if (($filter_user = $this->input->get('filter_user')) !== false)
		{
			$filter_user = (int) $filter_user;
			$this->create_filter_user($filter_user);	
			// restrict search results to this user
			$filter = "{$filter} AND u.id = {$filter_user}";
			$use_additional_tables = true;
		}
		
		if (($filter_company = $this->input->get('filter_company')) !== false)
		{
			$filter_company = (int) $filter_company;
			$this->create_filter_company($filter_company);	
			// restrict search results to this user
			$filter = "{$filter} AND cm.id = {$filter_company}";
			$use_additional_tables = true;
		}
		
		// add sql for connecting in additional tables
		if ($use_additional_tables) $additional_tables = 
			"INNER JOIN nr_company cm ON c.company_id = cm.id
			 INNER JOIN nr_user u ON cm.user_id = u.id";
			 
		return array('filter' => $filter, 
			'additional_tables' => $additional_tables);
	}
	
	protected function add_rejection_modal()
	{
		$rejection_modal = new Modal();
		$rejection_modal->set_title('Rejection Log');
		$this->add_eob($rejection_modal->render(420, 350));
		$this->vd->rejection_modal_id = $rejection_modal->id;
	}
	
	public function load_rejection_log_modal($pitch_order_id)
	{
		$this->vd->rejections = Model_Pitch_Writing_Process::get_rejection_conversation($pitch_order_id);
		$this->vd->pitch_order_id = $pitch_order_id;
		$this->load->view('admin/writing/pitch/partials/rejection_log_modal_box');
	}	
	
	public function review_single($pitch_order_id)
	{
		if ($this->input->post('bt_send_to_writer'))
		{
			$this->send_rejection_to_writer();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Sent to writer successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/writing/pitch/rejected'));
		}
		
		if ($this->input->post('bt_send_to_customer'))
		{
			$this->send_to_customer_for_review();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Sent to customer successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/writing/pitch/rejected'));		
		}
		
		if ($this->input->post('bt_save'))
		{
			$post = $this->input->post();
			$pitch_order_id = $post['pitch_order_id'];
						
			$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
			$m_pw_content->subject = $post['subject'];
			$m_pw_content->pitch_text = $this->vd->pure($post['pitch_text']);
			$m_pw_content->save();
		
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Saved successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/writing/pitch/review_single/'.$pitch_order_id));		
		}
		
		if ($this->input->post('bt_purge_customer_changes'))
		{
			$post = $this->input->post();
			$pitch_order_id = $post['pitch_order_id'];
						
			$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
			$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
			$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);			
			$m_campaign->subject = $m_pw_content->subject;
			$m_campaign->save();
			
			$m_campaign_data = Model_Campaign_Data::find($m_pw_order->campaign_id);
			$m_campaign_data->content = $m_pw_content->pitch_text;
			$m_campaign_data->save();
			
			$criteria = array();
			$criteria[] = array('pitch_order_id', $pitch_order_id);
			$criteria[] = array('process', Model_Pitch_Writing_Process::PROCESS_CUSTOMER_REJECTED);
			$comments = null;
			
			if ($result = Model_Pitch_Writing_Process::find_all($criteria, 
				array('process_date', 'desc'), 1))
			{
				$process = $result[0];
				$process->comments = Model_Pitch_Writing_Process::COMMENTS_ADMIN_PURGED_CHANGES;
				$process->save();
			}

			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Customer changes purged successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/writing/pitch/review_single/'.$pitch_order_id));	
		}
		
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_list = Model_Pitch_List::find('pitch_order_id', $pitch_order_id);
		$this->vd->list_completed = 0;
		if (($m_pw_list && $m_pw_list->status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER) 
				|| ($m_pw_order->order_type = Model_Pitch_Order::ORDER_TYPE_WRITING))
			$this->vd->list_completed = 1;
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_campaign_data = Model_Campaign_Data::find($m_campaign->id);
		$rejection_log = Model_Pitch_Writing_Process::get_rejection_conversation($pitch_order_id);
		$this->vd->rejection_log = $rejection_log;
		$this->vd->markers = Model_Campaign::markers();
		
		$this->add_order_detail_modal();
		$this->add_rejection_modal();
		$this->vd->m_pw_order = $m_pw_order;
		$this->vd->m_pw_content = $m_pw_content;
		$this->vd->m_campaign = $m_campaign;
		$this->vd->m_campaign_data = $m_campaign_data;
		
		$last_rej_comment = Model_Pitch_Writing_Process::get_last_customer_rejection_comments($pitch_order_id);
		if ($m_pw_order->status == Model_Pitch_Order::STATUS_CUSTOMER_REJECTED
				&& $last_rej_comment == Model_Pitch_Writing_Process::COMMENTS_CUSTOMER_EDITED)
			$this->vd->customer_edited_rejected = 1;
		
		$this->add_counters();
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/pitch/review_single');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');		
	}	
	
	protected function send_to_customer_for_review()
	{
		$post = $this->input->post();
		$pitch_order_id = $post['pitch_order_id'];
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER;
		$m_pw_order->save();
		
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_campaign->subject = $post['subject'];
		$m_campaign->save();
		
		$m_campaign_data = Model_Campaign_Data::find($m_campaign->id);
		$m_campaign_data->content = $post['pitch_text'];
		$m_campaign_data->save();
		
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$m_pw_content->subject = $post['subject'];
		$m_pw_content->pitch_text = $post['pitch_text'];
		$m_pw_content->save();
		
		Model_Pitch_Writing_Process::create_and_save($pitch_order_id, 
			Model_Pitch_Writing_Process::PROCESS_SENT_TO_CUSTOMER);

		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->send_pitch_to_customer_for_review($pitch_order_id);
	}
	
	protected function send_rejection_to_writer()
	{
		$post = $this->input->post();
		$pitch_order_id = $post['pitch_order_id'];
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_ADMIN_REJECTED;
		$m_pw_order->save();
		
		Model_Pitch_Writing_Process::create_and_save($pitch_order_id, 
			Model_Pitch_Writing_Process::PROCESS_ADMIN_REJECTED, $post['comments']);
		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->pitch_rejected_messge_to_writer($pitch_order_id, $post['comments']);								
	}
}

?>