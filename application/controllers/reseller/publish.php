<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/base');
load_controller('writing/common_writing_trait');

class Publish_Controller extends Reseller_Base {
	
	use Common_Writing_Trait;
	
	public function index($chunk = 1, $tab = '')
	{
		if (!$this->is_reseller_editor())
			$this->redirect('reseller/publish/pr');
		$this->auth_reseller_editor();
		
		$this->vd->archive = $is_archived = 0;
		$redirect_url = 'reseller/publish';
		
		if ($this->input->post('pending_reply_to_customer_button'))	
			return $this->pending_action_message_to_customer($redirect_url, Model_Writing_Process::ACTOR_RESELLER);		
		if ($this->input->post('pending_reply_to_writer_button'))
			return $this->pending_action_message_to_writer($redirect_url, Model_Writing_Process::ACTOR_RESELLER);		
		if ($this->input->post('rejected_send_to_customer_button'))
			return $this->rejected_action_sending_to_customer($redirect_url);			
		if ($this->input->post('rejected_send_to_writer_button'))
			return $this->rejected_action_sending_to_writer($redirect_url, Model_Writing_Process::ACTOR_RESELLER);
		
		$writers = Model_MOT_Writer::find_all();		
		$this->vd->writers = $writers;	
		
		$this->vd->prs_assign = $this->to_be_assigned_prs();
		$this->vd->tab = $tab;
		
		if ($tab == 'approved')
		     $result = $this->approved_pr_drafts($is_archived, $chunk);
		else $result = $this->approved_pr_drafts($is_archived);
			
		$this->vd->approved = $result['approved'];
		$this->vd->approved_chunkination = $result['chunkination'];
		
		$this->vd->prs_rejected = $this->rejected_pr_drafts();
		$this->vd->prs_review = $this->to_be_reviewed_pr_drafts();
		$this->vd->prs_pending = $this->pending_pr_writing();
		
		if ($tab == 'no_details')
		     $result = $this->no_details_yet_orders($is_archived, $chunk);
		else $result = $this->no_details_yet_orders($is_archived);
			
		$this->vd->no_details_yet = $result['no_details_yet'];
		$this->vd->no_details_yet_chunkination = $result['chunkination'];
		
		if (!$tab)
		     $result = $this->all_writing_orders($is_archived, $chunk);
		else $result = $this->all_writing_orders($is_archived, 1);
			
		$this->vd->prs_all = $result['prs_all'];
		$this->vd->chunkination = $result['chunkination'];	
		
		$rej_log_modal = new Modal();
		$rej_log_modal->set_title('Rejection Log');
		$this->add_eob($rej_log_modal->render(500, 180));
		$this->vd->rej_log_modal_id = $rej_log_modal->id;
		
		$pending_log_modal = new Modal();
		$pending_log_modal->set_title('Press Release Comments');
		$this->add_eob($pending_log_modal->render(500, 180));
		$this->vd->pending_log_modal_id = $pending_log_modal->id;		
			
		$this->load->view('reseller/header');
		$this->load->view('reseller/publish/menu');
		$this->load->view('reseller/pre-content');
		$this->load->view('reseller/publish/index');
		$this->load->view('reseller/post-content');				
		$this->load->view('reseller/footer');
	}
	
	public function archive($chunk = 1, $tab = '')
	{
		$this->auth_reseller_editor();
		$is_archived = 1;
		$this->vd->archive = $is_archived;
		$redirect_url = 'reseller/publish/archive';
		
		if ($this->input->post('pending_reply_to_customer_button'))
			return $this->pending_action_message_to_customer($redirect_url, $actor);
		
		if ($this->input->post('pending_reply_to_writer_button'))
			return $this->pending_action_message_to_writer($redirect_url, $actor);
		
		if ($this->input->post('rejected_send_to_customer_button'))
			return $this->rejected_action_sending_to_customer($redirect_url);
			
		if ($this->input->post('rejected_send_to_writer_button'))					
			return $this->rejected_action_sending_to_writer($redirect_url, $actor);
		
		$writers = Model_MOT_Writer::find_all();
		$this->vd->writers = $writers;
		
		$this->vd->prs_assign = $this->to_be_assigned_prs($is_archived);
		
		if ($tab == 'approved')
		     $result = $this->approved_pr_drafts($is_archived, $chunk);
		else $result = $this->approved_pr_drafts($is_archived);
			
		$this->vd->approved = $result['approved'];
		$this->vd->approved_chunkination = $result['chunkination'];
		
		$this->vd->prs_rejected = $this->rejected_pr_drafts($is_archived);
		$this->vd->prs_review = $this->to_be_reviewed_pr_drafts($is_archived);	
		$this->vd->prs_pending = $this->pending_pr_writing($is_archived);
		
		if ($tab == 'no_details')
		     $result = $this->no_details_yet_orders($is_archived, $chunk);
		else $result = $this->no_details_yet_orders($is_archived);
			
		$this->vd->no_details_yet = $result['no_details_yet'];
		$this->vd->no_details_yet_chunkination = $result['chunkination'];	
			
		if (!$tab)
		     $result = $this->all_writing_orders($is_archived, $chunk);
		else $result = $this->all_writing_orders($is_archived, 1);
						
		$this->vd->prs_all = $result['prs_all'];
		$this->vd->chunkination = $result['chunkination'];	
		
		$rej_log_modal = new Modal();
		$rej_log_modal->set_title('Rejection Log');
		$this->add_eob($rej_log_modal->render(500, 180));
		$this->vd->rej_log_modal_id = $rej_log_modal->id;
		
		$pending_log_modal = new Modal();
		$pending_log_modal->set_title('Press Release Comments');
		$this->add_eob($pending_log_modal->render(500, 180));
		$this->vd->pending_log_modal_id = $pending_log_modal->id;
						
		$this->load->view('reseller/header');
		$this->load->view('reseller/publish/menu');
		$this->load->view('reseller/pre-content');
		$this->load->view('reseller/publish/index');
		$this->load->view('reseller/post-content');				
		$this->load->view('reseller/footer');
	}	
	
	public function send_reminder_email($id, $is__archived = false)
	{		
		$this->auth_reseller_editor();		
		$w_order = Model_Writing_Order_Code::find($id);
		if (!$w_order->customer_email) return;
		
		$reseller = Model_Reseller_Details::find($w_order->reseller_id);
		$this->vd->customer_name = $w_order->customer_name;
		$this->vd->reseller_website = $reseller->website;
		$this->vd->writing_orders_detail_link = $reseller->website."prdetailsform.php";
		$this->vd->writing_order_code = $w_order->writing_order_code;
		$this->vd->reseller_company_name = $reseller->company_name;
		$message = $this->load->view('writing/email/customer_no_details_reminder', null, true);
		$subject = "Reminider: Instructions to Send in Your PR Details";
		$email = new Email();
		$email->set_to_email($w_order->customer_email);
		$email->set_from_email(Auth::user()->email);
		$email->set_to_name($w_order->customer_name);
		$email->set_from_name($reseller->company_name);
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
					
		// Now updating the database
		$sql = "UPDATE rw_writing_order_code 
				SET date_last_reminder_sent = ? 
				WHERE id = ?";
		$this->db->query($sql, array(Date::$now->format(DATE::FORMAT_MYSQL), $id));
		
		$sql = "INSERT INTO rw_writing_no_detail_reminder(writing_order_code, date_reminder_sent) values(?, ?)";
		$this->db->query($sql, array($w_order->writing_order_code, Date::$now->format(DATE::FORMAT_MYSQL)));
		
		if ($is__archived)
		     $redirect_url = 'reseller/publish/archive';
		else $redirect_url = 'reseller/publish';
				
		$feedback_view = 'reseller/publish/partials/message_sent';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);		
		$this->set_redirect($redirect_url);		
	}
	
	public function mark_archived($t_code)
	{
		$this->auth_reseller_editor();
		$m_code = Model_Writing_Order_Code::find(array('writing_order_code', $t_code));
		$m_code->is_archived = 1;
		$m_code->archived_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_code->save();
		
		$m_order = Model_Writing_Order::find(array('writing_order_code_id', $m_code->id));
		$m_order->is_archived = 1;
		$m_order->archived_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();
		
		echo 'success';
	}
	
	public function assign_to_writer($is__archived = false)
	{	
		$this->auth_reseller_editor();
		
		$order_id = $this->input->post('selected_pr');
		$writer_id = $this->input->post('selected_writer');
		if ($order_id!= "0" && $writer_id)
		{	   
			$sql = "update rw_writing_order set 
					status = 'assigned_to_writer', writer_id = ? , 
					latest_status_date = ?
					where id = ?";
			$this->db->query($sql, array($writer_id, Date::$now->format(DATE::FORMAT_MYSQL), $order_id));				
			
			$w_process = new Model_Writing_Process();
			$w_process->writing_order_id = $order_id;
			$w_process->process = "assigned_to_writer";
			$w_process->actor = "reseller";
			$w_process->target = $writer_id;
			$w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
			$this->db->insert('rw_writing_process', $w_process);
															
			//now sending email(s) to the writer
			$w_order = Model_Writing_Order::find(array('id', $order_id));
			$writer = Model_MOT_Writer::find($w_order->writer_id);
			$this->vd->fname = $writer->first_name;
			$subject = $writer->first_name.", 1 PR Writing Task(s) Have Been Assigned to You";
			$message = $this->load->view('writing/email/writer_writing_task_assignment', null, true);				
			$this->send_assigned_email_to_writer($subject, $message, $writer->email, $writer->first_name, 
												$writer->last_name, "support@myoutsourceteam.com",
												 "MyOutSourceTeam Support");
		}
		  
		// load feedback message for the user
		$feedback_view = 'reseller/publish/partials/assigned_to_writer';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
				
		if ($is__archived)
		     $this->set_redirect('reseller/publish/archive');	
		else $this->set_redirect('reseller/publish');
	}
	
	protected function send_assigned_email_to_writer($subject, $desc, $to, $toFName, $toLName, $from, $fromName)
	{
		$email = new Email();
		$email->set_to_email($to);
		$email->set_from_email($from);
		$email->set_to_name("{$toFName} {$toLName}");
		$email->set_from_name($fromName);
		$email->set_subject($subject);
		$email->set_message($desc);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}
	
	public function pr($chunk = 1)
	{		 
		$this->auth_non_reseller_editor();
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS m.name as company_name, 
			c.company_id as company_id, 
			o.date_ordered,      
			wc.writing_order_code, o.primary_keyword, 
			o.status as status, 
			c.id as id, c.slug, 
			c.title, c.is_approved, 
			c.is_rejected, c.is_published, 
			c.is_draft, c.is_premium, 
			c.is_under_review, 
			date_publish
			FROM rw_writing_order o 
			LEFT JOIN rw_writing_order_code wc
			ON o.writing_order_code_id = wc.id 
			INNER JOIN nr_content c 
			ON o.content_id = c.id 
			INNER JOIN nr_company m ON c.company_id = m.id 
			WHERE wc.reseller_id = ? AND 
				(c.is_draft = 0 OR c.is_rejected = 1)
			ORDER BY o.latest_status_date DESC
			{$limit_str}";

		$query = $this->db->query($sql, array(Auth::user()->id));
		
		$total_results = $this->db
						->query("SELECT FOUND_ROWS() AS count")
						->row()->count;
		$chunkination->set_total($total_results);
		
		$this->vd->results = array();
		foreach ($query->result() as $result)
		{
			$m_content = Model_Content::find($result->id);
			$result->url = gstring($m_content->url());			
			$this->vd->results[] = $result;
		}
		$chunkination->set_url_format('reseller/publish/pr/-chunk-');
		$this->vd->chunkination = $chunkination;
		
		$this->load->view('reseller/header');
		$this->load->view('reseller/publish/menu');
		$this->load->view('reseller/pre-content');
		$this->load->view('reseller/publish/pr');
		$this->load->view('reseller/post-content');
		$this->load->view('reseller/footer');
	}
	
	public function rejection_log($order_id)
	{
		$this->rejection_conversation($order_id);
	}
	
	public function rejection_log_footer($order_id)
	{
		$this->rejection_conversation_footer($order_id);
	}
	
	public function pending_log($order_id)
	{
		$this->pending_conversation($order_id);
	}
	
	public function pending_log_footer($order_id)
	{
		$this->pending_conversation_footer($order_id);
	}
	
	public function report($content_id)
	{
		$generate_url = "reseller/publish/report_generate/{$content_id}";
		$this->vd->generate_url = $generate_url;
		$this->vd->return_url = null;
		
		$this->load->view('reseller/header');
		$this->load->view('reseller/publish/menu');
		$this->load->view('reseller/pre-content');
		$this->load->view('manage/analyze/report-generate');
		$this->load->view('reseller/post-content');
		$this->load->view('reseller/footer');
	}
	
	public function report_generate($content_id)
	{
		$m_content = Model_Content::find($content_id);
		$m_newsroom = Model_Newsroom::find($m_content->company_id);
		
		$response = new stdClass();
		$url = "manage/analyze/content/dist_index/{$content_id}";
		$url = $m_newsroom->url($url);
		$report = new PDF_Generator($url);
		$report->generate();
		$response->download_url = $report->indirect();
		$this->json($response);
	}
	
	public function edit($content_id)
	{
		$m_content = Model_Content::find($content_id);
		$m_newsroom = Model_Newsroom::find($m_content->company_id);
		$url = "manage/publish/pr/edit/bundled/{$content_id}";
		$url = $m_newsroom->url($url);
		$this->redirect($url, false);
	}
	
	public function credit_to_code()
	{
		if (!Auth::user()->writing_credits()) $this->json(false);
		Auth::user()->consume_writing_credits(1);
		
		$code_str = Model_Writing_Order_Code::generate_code();
		$m_order_code = new Model_Writing_Order_Code();
		$m_order_code->writing_order_code = $code_str;
		$m_order_code->reseller_id = Auth::user()->id;
		$m_order_code->date_ordered = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order_code->save();
				
		$this->json($code_str);
	}
	
}

?>