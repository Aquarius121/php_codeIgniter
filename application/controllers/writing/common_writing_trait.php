<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait Common_Writing_Trait {

	protected function to_be_assigned_prs($is_archived = 0)
	{		
		$sql = "SELECT w.*,
			rc.writing_order_code, 
			c.name AS company_name
			FROM rw_writing_order w
			INNER JOIN rw_writing_order_code rc
			ON w.writing_order_code_id = rc.id
			INNER JOIN nr_content t
			ON w.content_id = t.id
			INNER JOIN nr_company c
			ON t.company_id = c.id
			WHERE w.status = 'not_assigned'
			AND w.is_archived = ?
			AND rc.reseller_id = ?";
				
		$query = $this->db->query($sql, array($is_archived, Auth::user()->id));
		$prs_assign = array();
		foreach ($query->result() as $result)
			$prs_assign[] = $result;
			
		return $prs_assign;
	}
	
	protected function approved_pr_drafts($is_archived = 0, $chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();		

		$sql = "SELECT SQL_CALC_FOUND_ROWS m.name as company_name, 
			c.company_id as company_id, o.id as id, 
			date_format(o.date_ordered,'%m/%d') as date_ordered,
			rc.writing_order_code as writing_order_code, 
			o.primary_keyword as primary_keyword,
			o.status as status,
			c.slug as slug,
			c.title as title,
			c.is_approved as is_approved,
			c.is_rejected, c.is_published,
			c.is_draft, c.is_premium,
			c.is_under_review,
			c.id as content_id,
			c.date_publish
			FROM rw_writing_order o 
			INNER JOIN rw_writing_order_code rc
			ON o.writing_order_code_id = rc.id
			INNER JOIN nr_content c 
			ON o.content_id = c.id 
			INNER JOIN nr_company m 
			ON c.company_id = m.id 
			WHERE rc.reseller_id = ? AND o.is_archived = ? 
			AND (c.is_draft = 0 OR c.is_rejected = 1)
			ORDER BY o.latest_status_date DESC 
			{$limit_str}";
				
		$query = $this->db->query($sql, array(Auth::user()->id, $is_archived));
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
						
		$chunkination->set_total($total_results);		
		$results = Model_Content::from_db_all($query);
		$approved = array();
		
		foreach ($results as $result)
		{			
			$result->url = $result->url(false);
			$approved[] = $result;
		}		
		
		if ($is_archived == 1)
		     $chunkination->set_url_format('reseller/publish/archive/-chunk-/approved');
		else $chunkination->set_url_format('reseller/publish/-chunk-/approved');	
		
		$result = array();
		$result['approved'] = $approved;
		$result['chunkination'] = $chunkination;
		
		return $result;
	}
	
	protected function rejected_pr_drafts($is_archived = 0)
	{
		$sql = "SELECT t.company_id as company_id, w.id as id, 
			c.name as company_name, 
			date_format(w.date_ordered,'%m/%d') as date_ordered,
			rc.writing_order_code as writing_order_code, 
			w.primary_keyword as primary_keyword,
			w.status AS status,
			t.title AS title,
			p_max.max_status,				
			a_date.date_assigned_to_writer,
			w_date.date_submitted_by_writer,
			rej_t.times_rejected 
			FROM rw_writing_order w 
			INNER JOIN rw_writing_order_code rc
			ON w.writing_order_code_id = rc.id
			INNER JOIN nr_content t
			ON w.content_id = t.id 
			INNER JOIN nr_company c 
			ON t.company_id = c.id 
			
			LEFT JOIN (
				SELECT writing_order_id,
				MAX(process+0) AS max_status
				FROM rw_writing_process
				GROUP BY writing_order_id
			) AS p_max ON p_max.writing_order_id = w.id
			
			LEFT JOIN (
				SELECT writing_order_id, 
				process_date AS date_assigned_to_writer
				FROM rw_writing_process
				WHERE process = 'assigned_to_writer'
			) AS a_date ON a_date.writing_order_id = w.id
			
			LEFT JOIN (
				SELECT writing_order_id, 
				MAX(process_date) AS date_submitted_by_writer
				FROM rw_writing_process
				WHERE process = 'written_sent_to_reseller'
				GROUP BY writing_order_id
			) AS w_date ON w_date.writing_order_id = w.id

			LEFT JOIN (
				SELECT writing_order_id, 
				COALESCE(count(writing_order_id), 0) AS times_rejected 
				FROM rw_writing_process
				WHERE process IN ('reseller_rejected', 'customer_rejected') 
				GROUP BY writing_order_id 
			) AS rej_t ON rej_t.writing_order_id = w.id
				
			WHERE w.status IN ('reseller_rejected', 'customer_rejected', 'sent_to_customer')
			AND rc.reseller_id =  ?
			AND w.is_archived = ?
			AND rej_t.times_rejected > 0
			ORDER BY w.latest_status_date DESC";
				
		$query = $this->db->query($sql, array(Auth::user()->id, $is_archived));
		$orders = Model_Content::from_db_all($query);
		foreach ($orders as $k => $order)
			$order->status_title = Model_Writing_Order::full_process($order->status);
				
		return $orders;
	}
	
	protected function to_be_reviewed_pr_drafts($is_archived = 0)
	{
		$sql = "SELECT t.company_id as company_id, w.id as id, 
			c.name as company_name, 
			date_format(w.date_ordered,'%m/%d') as date_ordered,
			rc.writing_order_code as writing_order_code, 
			w.primary_keyword as primary_keyword,
			w.status AS status,
			t.title AS title,
			a_date.date_assigned_to_writer,
			w_date.date_submitted_by_writer
			FROM rw_writing_order w
			INNER JOIN rw_writing_order_code rc
			ON w.writing_order_code_id = rc.id
			INNER JOIN nr_content t
			ON w.content_id = t.id 				
			INNER JOIN nr_company c
			ON t.company_id = c.id			
			
			LEFT JOIN (
				SELECT writing_order_id, 
				process_date AS date_assigned_to_writer					
				FROM rw_writing_process
				WHERE process = 'assigned_to_writer'
			) AS a_date ON a_date.writing_order_id = w.id
			
			LEFT JOIN (
				SELECT writing_order_id, 
				MAX(process_date) AS date_submitted_by_writer
				FROM rw_writing_process
				WHERE process = 'written_sent_to_reseller'
				GROUP BY writing_order_id
			) AS w_date ON w_date.writing_order_id = w.id
			
			WHERE w.status = 'written_sent_to_reseller' 
			AND rc.reseller_id =  ?
			AND w.is_archived = ?
			ORDER BY w.latest_status_date DESC";
		
		$query = $this->db->query($sql, array(Auth::user()->id, $is_archived));
		$orders = Model_Writing_Order::from_db_all($query);
		return $orders;
	}
	
	protected function pending_pr_writing($is_archived = 0)
	{
		$sql = "SELECT t.company_id as company_id, 
			w.id as id, c.name as company_name,
			date_format(w.date_ordered,'%m/%d') as date_ordered,
			tc.writing_order_code as writing_order_code, 
			w.primary_keyword as primary_keyword,
			w.status as status,
			a_date.date_assigned
			FROM rw_writing_order_code tc 
			INNER JOIN rw_writing_order w 
			ON tc.id = w.writing_order_code_id 
			INNER JOIN nr_content t
			ON w.content_id = t.id				
			INNER JOIN nr_company c 
			ON t.company_id = c.id 
			
			LEFT JOIN (
				SELECT writing_order_id, 
				process_date AS date_assigned					
				FROM rw_writing_process
				WHERE process = 'assigned_to_writer'
			) AS a_date ON a_date.writing_order_id = w.id
			
			WHERE w.status in ('assigned_to_writer', 'writer_request_details_revision',
				'sent_back_to_writer', 'sent_to_customer_for_detail_change',
				'customer_revise_details', 'revised_details_accepted') 
			AND tc.reseller_id = ? 
			AND (w.is_archived = ? OR tc.is_archived = ?)
			ORDER BY w.id DESC";
				
		$query = $this->db->query($sql, array(Auth::user()->id, $is_archived, $is_archived));
		$results = Model_Writing_Order::from_db_all($query);
		return $results;
	}
	
	protected function all_writing_orders($is_archived = 0, $chunk)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();
		$search_query = $this->vd->esc($this->input->get('terms')); 
		
		if (trim($search_query))
		{  
			$search_fields = array(
				'tc.writing_order_code',
				'tc.customer_email',
				'c.name',
				'o.title',
				'w.primary_keyword'
			);			
			
			$search_condition = sql_search_terms($search_fields, $search_query);
		   $queryParams = array(Auth::user()->id);
		}
		else
		{			
			$search_condition = '(w.is_archived = ? OR tc.is_archived = ?)';
			$queryParams = array(Auth::user()->id, $is_archived, $is_archived); 
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS o.company_id AS company_id,
			w.id AS id, c.name AS company_name,
			w.date_ordered AS date_ordered,
			tc.date_ordered AS date_code_ordered,
			w.primary_keyword AS primary_keyword, 
			w.status AS status,
			w.writer_id AS writer_id,
			tc.writing_order_code AS writing_order_code, 
			tc.customer_name AS customer_name,
			tc.customer_email AS customer_email,
			tc.is_used AS is_used,
			o.id AS content_id,
			o.slug AS slug,
			o.title AS pr_title,
			o.is_approved AS is_approved,
			o.is_published, 
			o.date_publish,				
			p_max.max_process_num AS status_num,
			a_date.date_assigned_to_writer,
			w_date.date_written,
			s_date.date_sent_to_customer,
			r_date.date_customer_rejected,
			ap_date.date_customer_approved,
			rp.date_sent AS date_report_sent
			FROM rw_writing_order_code tc 
			LEFT JOIN rw_writing_order w 
			ON tc.id = w.writing_order_code_id
			LEFT JOIN nr_content o 
			ON w.content_id = o.id 
			LEFT JOIN nr_company c 
			ON o.company_id = c.id 
			LEFT JOIN rw_report_sent rp
			ON rp.content_id = o.id
			
			LEFT JOIN (
				SELECT writing_order_id,
				MAX(process+0) AS max_process_num
				FROM rw_writing_process
				GROUP BY writing_order_id
			) AS p_max ON p_max.writing_order_id = w.id

			LEFT JOIN (
				SELECT writing_order_id, 
					process_date AS date_assigned_to_writer
				FROM rw_writing_process
					WHERE process = 'assigned_to_writer'
			) AS a_date ON a_date.writing_order_id = w.id

			LEFT JOIN (
				SELECT writing_order_id, 
					MAX(process_date) AS date_written				
				FROM rw_writing_process
					WHERE process = 'written_sent_to_reseller'
				GROUP BY writing_order_id
			) AS w_date ON w_date.writing_order_id = w.id

			LEFT JOIN (
				SELECT writing_order_id, 
					MAX(process_date) AS date_sent_to_customer				
				FROM rw_writing_process
					WHERE process = 'sent_to_customer'
				GROUP BY writing_order_id
			) AS s_date ON s_date.writing_order_id = w.id

			LEFT JOIN (
				SELECT writing_order_id, 
					MAX(process_date) AS date_customer_rejected				
				FROM rw_writing_process
					WHERE process = 'customer_rejected'
				GROUP BY writing_order_id
			) AS r_date ON r_date.writing_order_id = w.id

			LEFT JOIN (
				SELECT writing_order_id, 
					MAX(process_date) AS date_customer_approved
				FROM rw_writing_process
					WHERE process = 'customer_accepted'
				GROUP BY writing_order_id
			) AS ap_date ON ap_date.writing_order_id = w.id
			
			WHERE tc.reseller_id = ? AND {$search_condition}
			ORDER BY w.latest_status_date DESC, tc.date_ordered DESC 
			{$limit_str}";
				
		$query = $this->db->query($sql, $queryParams);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
						
		$chunkination->set_total($total_results);
		$prs_all = Model_Writing_Order::from_db_all($query);
		
		if ($is_archived == 1)
		     $chunkination->set_url_format(gstring('reseller/publish/archive/-chunk-'));
		else $chunkination->set_url_format(gstring('reseller/publish/-chunk-'));	
		
		$result = array();
		$result['prs_all'] = $prs_all;
		$result['chunkination'] = $chunkination;
		return $result;
	}
	
	protected function pending_conversation($order_id)
	{
		$pr = Model_Writing_Order::find($order_id);
		$pr->hist = Model_Writing_Process::get_pre_writing_conversation($order_id);
		$this->vd->pr = $pr;
		$this->load->view('reseller/publish/partials/pending_log_modal_box');
	}
	
	protected function pending_conversation_footer($order_id)
	{
		$pr = Model_Writing_Order::find($order_id);
		if ($pr->status != Model_Writing_Order::STATUS_RESELLER_REJECTED
		 && $pr->status != Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)
		{
			$this->vd->pr = $pr;
			$this->load->view('reseller/publish/partials/pending_log_modal_box_footer');
		}	
	}
	
	protected function no_details_yet_orders($is_archived = 0, $chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();
				
		$sql = "SELECT SQL_CALC_FOUND_ROWS *  
			FROM rw_writing_order_code 				
			WHERE reseller_id = ? AND 
			is_used = 0 AND is_archived = ?
			ORDER BY date_ordered DESC
			{$limit_str}";
				
		$query = $this->db->query($sql, array(Auth::user()->id, 
			$is_archived));			
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		$no_details_yet = array();
		foreach ($query->result() as $result)
			$no_details_yet[] = $result;
		
		if ($is_archived == 1)
		     $chunkination->set_url_format('reseller/publish/archive/-chunk-/no_details');
		else $chunkination->set_url_format('reseller/publish/-chunk-/no_details');		
		
		$result = array();
		$result['no_details_yet'] = $no_details_yet;
		$result['chunkination'] = $chunkination;
		return $result;
	}
	
	protected function pending_action_message_to_writer($redirect_url, $actor)
	{		
		$pending_msg = $this->input->post('reply_msg_pending_' . $this->input->post('pr_id_for_action'));
		$sql = "update rw_writing_order set status=?,latest_status_date=? WHERE id=?";		
		$this->db->query($sql,array('sent_back_to_writer',Date::$now->format(DATE::FORMAT_MYSQL),
									$this->input->post('pr_id_for_action')));
		
		$this->save_writing_process($this->input->post('pr_id_for_action'), 'sent_back_to_writer', $actor,
										$pending_msg);	

		//Now sending the email to the writer
		$m_order = Model_Writing_Order::find($this->input->post('pr_id_for_action'));
		$writer = Model_MOT_Writer::find($m_order->writer_id);
		
		$this->vd->writer_first_name = $writer->first_name;		
		$this->vd->writing_angle = $m_order->full_angle_name($m_order->writing_angle);
		$m_content = Model_Content::find($m_order->content_id);
		$m_comp = Model_Company::find($m_content->company_id);
		$this->vd->company_name = $m_comp->name;
		$this->vd->comments = $pending_msg;
		$subject = "URGENT: Info Added/Clarified For PR Task, You Can Proceed With PR Task - " . 
					$this->vd->writing_angle . " - " . $this->vd->company_name;
					
		$message = $this->load->view('writing/email/writer_reply_for_details_revision', null, true);		
		$email = new Email();
		$email->set_to_email($writer->email);
		$email->set_from_email("support@myoutsourceteam.com");
		$email->set_to_name($writer->first_name . " " . $writer->last_name);
		$email->set_from_name("MyOutSourceTeam Support");
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();		
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);				
		
		// load feedback message for the user
		$feedback_view = 'admin/writing/partials/message_sent';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);		
		if ($redirect_url) $this->redirect($redirect_url);
	}
	
	protected function pending_action_message_to_customer($redirect_url, $actor)
	{		
		$comments = $this->input->post('reply_msg_pending_' . $this->input->post('pr_id_for_action'));
		$sql = "update rw_writing_order set status=?, latest_status_date=? WHERE id=?";		
		$this->db->query($sql,array('sent_to_customer_for_detail_change', Date::$now->format(DATE::FORMAT_MYSQL),
									$this->input->post('pr_id_for_action')));
		$this->save_writing_process($this->input->post('pr_id_for_action'), 'sent_to_customer_for_detail_change',
									$actor, $comments);	
		
		//Now sending the email to the customer
		$m_order = Model_Writing_Order::find($this->input->post('pr_id_for_action'));		
		$m_code = Model_Writing_Order_Code::find($m_order->writing_order_code_id);
		$m_content = Model_Content::find($m_order->content_id);
		
		$m_comp = Model_Company::find($m_content->company_id);
		$reseller_id = $m_code->reseller_id;
		
		if (!$reseller_id)  
		{
			// locate the users writing session and notify them
			$m_wr_session = Model_Writing_Session::find_order($m_order->id);
			if (!$m_wr_session) show_404();
			$m_wr_session->notify_update_required($comments);
		}
		else
		{
			$reseller = Model_User::find($reseller_id);
			$m_reseller_comp = Model_Reseller_Details::find($reseller_id);
			$subject = "Important: You are Required to Edit the PR Writing Order Details";		
			$this->vd->customer_name = $m_code->customer_name;		
			$this->vd->writing_orders_edit_link = $m_reseller_comp->website_url("edit_form1.php?id=" . 
					$m_order->id . "&prcode="  . 
					$m_code->writing_order_code);
			
			$this->vd->editing_reason = $comments;
			$this->vd->reseller_company_name = $m_reseller_comp->company_name;
			$message = $this->load->view('writing/email/customer_pr_details_revise_link', null, true);		
			$email = new Email();
			$email->set_to_email($m_code->customer_email);
			$email->set_from_email($reseller->email);
			$email->set_to_name($m_code->customer_name);
			$email->set_from_name($m_reseller_comp->company_name);
			$email->set_subject($subject);
			$email->set_message($message);
			$email->enable_html();
			
			Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
		}	
		
		// load feedback message for the user
		$feedback_view = 'admin/writing/partials/message_sent';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);		
		if ($redirect_url) $this->redirect($redirect_url);
	}	
	
	protected function rejected_action_sending_to_customer($redirect_url)
	{
		$order_id = $this->input->post('pr_id_for_action');
		$comments = $this->input->post('reply_msg_rejected_' . $order_id);
		
		$m_order = Model_Writing_Order::find($order_id);
		$m_order_code = Model_Writing_Order_Code::find($m_order->writing_order_code_id);
		$this->reseller_action_send_to_customer($m_order, $m_order_code, $comments);
		
		// load feedback message for the user
		$feedback_view = 'admin/writing/partials/message_sent';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
		if ($redirect_url) $this->redirect($redirect_url);
	}
	
	protected function rejected_action_sending_to_writer($redirect_url, $actor)
	{		
		$order_id = $this->input->post('pr_id_for_action');
		$rejected_msg = $this->input->post('reply_msg_rejected_' . $order_id);
		$sql = "update rw_writing_order set 
					status='reseller_rejected', 
					latest_status_date=?
					where id=?";
		
		$this->db->query($sql, array(Date::$now->format(DATE::FORMAT_MYSQL), $order_id));
		$w_process = new stdClass();
		$w_process->writing_order_id = $order_id;
		$w_process->process = "reseller_rejected";
		$w_process->actor = $actor;
		$w_process->comments = $rejected_msg;
		$w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$this->db->insert('rw_writing_process', $w_process);
		
		//Now sending email to the writer
		$m_order = Model_Writing_Order::find($order_id);
		$m_order_code = Model_Writing_Order_Code::find($m_order->writing_order_code_id);
		$m_content = Model_Content::find($m_order->content_id);
		
		$writer = $this->get_single_writer_from_mot($m_order->writer_id);
		
		$this->vd->writer_first_name = $writer->first_name;
		$this->vd->pr_draft_title = $m_content->title;
		$this->vd->editor_comments = $rejected_msg;
		$this->vd->writing_order_code = $m_order_code->writing_order_code;
		$subject = "URGENT: A PR You Wrote Requires Editing - " . $m_content->title;
		$message = $this->load->view('writing/email/writer_pr_draft_rejection', null, true);
		$email=new Email();
		$email->set_to_email($writer->email);
		$email->set_from_email("support@myoutsourceteam.com");
		$email->set_to_name($writer->first_name . " " . $writer->last_name);
		$email->set_from_name("MyOutSourceTeam Support");
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
		
		// load feedback message for the user
		$feedback_view = 'admin/writing/partials/message_sent';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);		
		$this->set_redirect($redirect_url);
	}
	
	protected function reseller_action_send_to_customer($m_order, $m_order_code, $comments = null)
	{
		if (!$m_order_code->reseller_id)
		{
			// locate the users writing session and notify them
			$m_wr_session = Model_Writing_Session::find_order($m_order->id);
			if (!$m_wr_session) show_404();
			$m_wr_session->notify_written();
			
			// store the comments in the process log
			$this->save_writing_process($m_order->id,
				Model_Writing_Order::STATUS_SENT_TO_CUSTOMER,
				Model_Writing_Process::ACTOR_ADMIN,
				$comments);
		}
		else
		{
			$reseller_id = $m_order_code->reseller_id;
			$reseller = Model_User::find($reseller_id);
			$m_reseller_details = Model_Reseller_Details::find($reseller_id);
			
			if ($m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_ADMIN_EDITOR)
			     $actor = Model_Writing_Process::ACTOR_ADMIN;
			else $actor = Model_Writing_Process::ACTOR_RESELLER;
			
			$this->save_writing_process($m_order->id,
				Model_Writing_Order::STATUS_SENT_TO_CUSTOMER,
				$actor, $comments);
			
			$preview_link = "PreviewNewPR.php?id={$m_order->id}&view=customer&tcode={$m_order_code->writing_order_code}";
			$this->vd->preview_link = $m_reseller_details->website_url($preview_link);
			$this->vd->customer_name = $m_order_code->customer_name;	
			$this->vd->reseller_company_name = $m_reseller_details->company_name;
			
			if (Model_Writing_Process::how_many_times_rejected_by_customer($m_order->id))
			{
				// send email that the PR draft has been revised
				$subject = 'Your Press Release Was Revised and is Ready for Review';	
				$message_view = 'writing/email/customer_press_release_revised';
				$message = $this->load->view($message_view, null, true);	
			}
			else
			{
				// send email that the PR draft has been written
				$subject = 'Your Press Release is Ready for Review';
				$message_view = 'writing/email/customer_press_release_written';
				$message = $this->load->view($message_view, null, true);						
			} 
			
			$email = new Email();
			$email->set_to_email($m_order_code->customer_email);
			$email->set_from_email($reseller->email);
			$email->set_to_name($m_order_code->customer_name);
			$email->set_from_name($m_reseller_details->company_name);
			$email->set_subject($subject);
			$email->set_message($message);
			$email->enable_html();
			Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
		}
		
		$m_order->status = Model_Writing_Order::STATUS_SENT_TO_CUSTOMER;
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();		
	}	
	
	protected function rejection_conversation($order_id)
	{
		$rejections = array();
		$this->vd->pr = $pr = Model_Writing_Order::find($order_id);
		$pr->rejections = Model_Writing_Process::get_rejection_conversation($order_id);
		$this->load->view('reseller/publish/partials/rejected_log_modal_box');
	}
	
	protected function rejection_conversation_footer($order_id)
	{
		$this->vd->pr = $pr = Model_Writing_Order::find($order_id);
		if ($pr->status != Model_Writing_Order::STATUS_RESELLER_REJECTED 
			&& $pr->status != Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)
			$this->load->view('reseller/publish/partials/rejected_log_modal_box_footer');
	}
		
	protected function save_writing_process($writing_order_id, $process, $actor, $comments = null)
	{		
		Model_Writing_Process::create_and_save($writing_order_id, $process, $actor, $comments);
	}
	
	public function get_single_writer_from_mot($writer_id)
	{
		$request = new MOT_Iella_Request();
		$request->data->writer_id = $writer_id;
		$request->send('mot_writers/get_single_writer');
		if ($request->response->response == 'success!')
			return $request->response->writer;
		return null;
	}
	
	protected function get_all_writers_from_mot()
	{
		$request = new MOT_Iella_Request();
		$request->send('mot_writers/get_all_writers');
		$writers = array();
		if ($request->response->response == 'success!')
			$writers = $request->response->writers;
		return $writers;
	}
	
	protected function get_single_writer_from_array($writers, $writer_id)
	{
		foreach ($writers as $writer)
			if ($writer->id == $writer_id)
				return $writer;
		return null;
	}
	
}

?>