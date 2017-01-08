<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_parent_controller('reseller/base');

class Dashboard_Controller extends Reseller_Base {
		
	public function index()
	{	
		$this->auth_reseller_editor();
		
		$writers = Model_MOT_Writer::find_all();
		$this->vd->activities = $this->get_activities($writers);
		$this->vd->no_details_count = $this->get_no_details_count();
		$this->vd->this_week_orders_count = $this->get_this_week_orders_count();
		
		// Querying the db for PRs Assign tab
		$sql = "SELECT SQL_CALC_FOUND_ROWS w.id as id, 
				c.name as company_name, 
				date_format(w.date_ordered,'%m/%d') as date_ordered,
				rc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword
				FROM rw_writing_order w 
				INNER JOIN rw_writing_order_code rc
				ON w.writing_order_code_id = rc.id
				INNER JOIN nr_content t
				ON w.content_id = t.id
				INNER JOIN nr_company c 
				ON t.company_id = c.id
				WHERE w.status = 'not_assigned' 
				AND rc.reseller_id = ? 
				ORDER BY w.id DESC 
				LIMIT 0,4";				
				
		$query = $this->db->query($sql, array(Auth::user()->id));
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		$this->vd->assign_counter = $total_results;
		
		$this->vd->prs_assign = array();
		foreach ($query->result() as $result)
			$this->vd->prs_assign[] = $result;
		
		$this->vd->writers = $writers;
				
		// Querying the db for PRs rejected tab		
		$user_id_like = Auth::user()->id."%";
		$sql = "SELECT SQL_CALC_FOUND_ROWS t.company_id as company_id, 
				w.id as id, 
				c.name as company_name, 
				date_format(w.date_ordered,'%m/%d') as date_ordered,
				rc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword,
				t.title as title 
				FROM rw_writing_order w 
				INNER JOIN rw_writing_order_code rc
				ON w.writing_order_code_id = rc.id
				INNER JOIN nr_content t
				ON w.content_id = t.id 
				INNER JOIN nr_company c 
				ON t.company_id = c.id				
				WHERE w.status IN ('reseller_rejected', 'customer_rejected')
				AND rc.reseller_id =  ?
				ORDER BY w.id DESC 
				LIMIT 0,4";
										
		$query = $this->db->query($sql, array(Auth::user()->id));
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		$this->vd->rejected_counter = $total_results;
		
		$results = Model_Content::from_db_all($query);
		$this->vd->prs_rejected = array();
		
		foreach ($results as $result)
		{			
			$result->title_short = $this->vd->cut($result->title, 35);			
			$this->vd->prs_rejected[] = $result;
		}
		
		
		// Querying the db for PRs review tab
		$sql = "SELECT SQL_CALC_FOUND_ROWS t.company_id as company_id, 
				w.id as id, c.name as company_name, 
				date_format(w.date_ordered,'%m/%d') as date_ordered,
				rc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword,
				t.title as title 
				FROM rw_writing_order w 
				INNER JOIN rw_writing_order_code rc
				ON w.writing_order_code_id = rc.id
				INNER JOIN nr_content t
				ON w.content_id = t.id 				
				INNER JOIN nr_company c 
				ON t.company_id = c.id 
				WHERE w.status = 'written_sent_to_reseller'
				AND rc.reseller_id = ? 
				ORDER BY w.id DESC 
				LIMIT 0,4";	
								
		$query = $this->db->query($sql, array(Auth::user()->id));
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		$this->vd->review_counter = $total_results;
		
		$results = Model_Content::from_db_all($query);
		$this->vd->prs_review = array();
		
		foreach ($results as $result)
		{
			$result->title_short = $this->vd->cut($result->title, 35);
			$this->vd->prs_review[] = $result;
		}
			
		// Querying the db for PRs Pending tab		
		$sql = "SELECT SQL_CALC_FOUND_ROWS t.company_id as company_id, 
				w.id as id, c.name as company_name, 
				date_format(w.date_ordered,'%m/%d') as date_ordered,
				rc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword
				FROM rw_writing_order w
				INNER JOIN rw_writing_order_code rc
				ON w.writing_order_code_id = rc.id
				
				INNER JOIN nr_content t
				ON w.content_id = t.id				
				INNER JOIN nr_company c 
				ON t.company_id = c.id 
							
				WHERE w.status IN ('assigned_to_writer','writer_request_details_revision',
									'sent_back_to_writer','sent_to_customer_for_detail_change',
									'customer_revise_details','revised_details_accepted') 
				AND rc.reseller_id = ? 
				ORDER BY w.id DESC 
				LIMIT 0,4";
					
		$query = $this->db->query($sql, array(Auth::user()->id));	
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		$this->vd->pending_counter = $total_results;
			
		$this->vd->prs_pending = array();
		
		foreach ($query->result() as $result)
			$this->vd->prs_pending[] = $result;	
		
		// Querying the db for Reseller Overview Area.
		$sql = "SELECT o.company_id as company_id, w.id as id, c.name as company_name,
				date_format(w.date_ordered,'%m/%d') as date_ordered,
				date_format(tc.date_ordered,'%m/%d') as date_code_ordered,
				w.primary_keyword as primary_keyword, 
				w.status as status,
				tc.writing_order_code as writing_order_code, 
				tc.customer_name as customer_name,
				o.slug as slug,
				o.title as pr_title,
				o.is_approved as is_approved,
				o.is_published, 
				o.id AS content_id,
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
				
				WHERE tc.reseller_id = ? 
				ORDER BY w.latest_status_date DESC, 
				tc.date_ordered DESC 
				LIMIt 0,5";
				
		$query = $this->db->query($sql, array(Auth::user()->id));
		$results = Model_Writing_Process::from_db_all($query);
		$this->vd->prs_all = array();
		foreach ($results as $result)
			$this->vd->prs_all[] = $result;

		$this->load->view('reseller/header');
		$this->load->view('reseller/dashboard/menu');
		$this->load->view('reseller/pre-content');
		$this->load->view('reseller/dashboard/index');
		$this->load->view('reseller/post-content');
		$this->load->view('reseller/footer');
	}	
	
	
	protected function get_no_details_count()
	{
		$criteria = array();
		$criteria[] = array('is_used', '0');
		$criteria[] = array("reseller_id", Auth::user()->id);
		return Model_Writing_Order_Code::count_all($criteria);
	}
	
	protected function get_this_week_orders_count()
	{		
		$criteria = array();
		$criteria[] = array("date_ordered > '".date("Y-m-d 00:00:01" , strtotime('monday this week'))."'");
		$criteria[] = array("reseller_id", Auth::user()->id);
		return Model_Writing_Order_Code::count_all($criteria);
	}
	
	public function assign_to_writers()
	{
		$this->auth_reseller_editor();
		
		$writers = Model_MOT_Writer::find_all();	   
		$prID = $this->input->post('prID');
		$writer = $this->input->post('writer');	   
		if (count($prID) > 0)
		{	
			for ($c = 0; $c < count($prID); $c++)		  
			{
				if ($writer[$c] != "0")
				{
					$sql="UPDATE rw_writing_order set 
							status='assigned_to_writer', writer_id=? ,
							latest_status_date=?
							WHERE id=?";
					$this->db->query($sql, array($writer[$c], Date::$now->format(DATE::FORMAT_MYSQL), $prID[$c]));				
					$w_process = new stdClass();
					$w_process->writing_order_id = $prID[$c];
					$w_process->process = "assigned_to_writer";
					$w_process->actor = "reseller";
					$w_process->target = $writer[$c];
					$w_process->process_date = Date::$now->format(DATE::FORMAT_MYSQL);
					$this->db->insert('rw_writing_process', $w_process);	
																	
					// Now sending email(s) to the writer(s)				
					$w = Model_MOT_Writer::find($writer[$c]);
					
					$this->vd->fname = $w->first_name;
					$subject = $w->first_name.", 1 PR Writing Task(s) Have Been Assigned to You";
					$message = $this->load->view('writing/email/writer_writing_task_assignment', null, true);				
					$this->send_email_to_writer($subject, $message, $w->email, $w->first_name, $w->last_name, 
									"support@myoutsourceteam.com", "MyOutSourceTeam Support");						
				
				}
					 
			}			
			// Load feedback message for the user			
			$feedback_view = 'reseller/dashboard/partials/assigned_to_writer';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);		
			$this->set_redirect('reseller/dashboard');			  
		}	

	}
	
	protected function send_email_to_writer($subject, $desc, $to, $toFName, $toLName, $from, $fromName)
	{			
		$email = new Email();		
		$email->set_to_email($to);
		$email->set_from_email($from);
		$email->set_to_name($toFName." ".$toLName);
		$email->set_from_name($fromName);
		$email->set_subject($subject);
		$email->set_message($desc);
		$email->enable_html();		
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);					
	}
	
	protected function send_reminder_email_to_customer($w_order, $reseller)
	{		
		if ($reseller->website)
			if (substr($reseller->website, strlen($reseller->website)-1, 1) != "/")
				$reseller->website .= "/";
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
		$sql = "update rw_writing_order_code set date_last_reminder_sent=? where id=?";
		$this->db->query($sql, array(Date::$now->format(DATE::FORMAT_MYSQL), $w_order->id));
		
		$sql = "insert into rw_writing_no_detail_reminder(writing_order_code, date_reminder_sent) values(?, ?)";
		$this->db->query($sql, array($w_order->writing_order_code, Date::$now->format(DATE::FORMAT_MYSQL)));				
		
	}
	
	public function send_reminders_for_no_details()
	{		
		$this->auth_reseller_editor();
		
		$reseller_company = Model_Reseller_Details::find(array("user_id", Auth::user()->id));
		$criteria = array();
		$criteria[] = array('is_used', '0');
		$criteria[] = array("reseller_id", Auth::user()->id);	
		$no_detail_recs = Model_Writing_Order_Code::find_all($criteria);
		
		foreach ($no_detail_recs as $result)
			$this->send_reminder_email_to_customer($result, $reseller_company);
			
		$feedback_view = 'reseller/dashboard/partials/message_sent';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);		
		$this->set_redirect('reseller/dashboard');	
	}	
	
	protected function aasort (&$array, $key) 
	{
		$sorter = array();
		$ret = array();
		reset($array);
		foreach ($array as $ii => $va) 		
			$sorter[$ii] = $va[$key];

		asort($sorter);
		foreach ($sorter as $ii => $va)
			$ret[$ii] = $array[$ii];

		$array = $ret;
	}
	
	protected function get_activities($writers)
	{
		$sql = "select writing_order_code, customer_name, customer_email, 
				date_format(date_ordered,'%m/%d/%y') as date_ordered, 
				date_format(date_ordered,'%Y%m%d') as date_ordered_raw  
				FROM rw_writing_order_code 
				WHERE writing_order_code like ?
				ORDER BY date_ordered DESC
				LIMIT 40";
				
		$query = $this->db->query($sql, array(Auth::user()->id."%"));
		$activities = array();
		foreach ($query->result() as $result)
		{
			$act = array();
			$act['caption'] = "New Order Placed";
			$act['code'] = $result->writing_order_code;
			$act['dt'] = $result->date_ordered;
			$act['date_raw'] = $result->date_ordered_raw;
			$act['custName'] = $result->customer_name;
			$act['custEmail'] = $result->customer_email;
			$activities[] = $act;
		}	
				
		$sql = "SELECT o.id as order_id, 
				c.company_id as company_id,
				c.id as content_id, c.slug,
				rc.writing_order_code,
				date_format(p.process_date,'%m/%d/%y') as process_date,
				date_format(p.process_date,'%Y%m%d') as process_date_raw,
				p.process as process,
				p.process+0 as process_num, o.writer_id as writer_id,
				p_max.max_process_num,
				p.comments			 
				FROM rw_writing_order o
				INNER JOIN rw_writing_order_code rc
				ON o.writing_order_code_id = rc.id
				INNER JOIN rw_writing_process p
				ON p.writing_order_id = o.id
				INNER JOIN nr_content c
				ON o.content_id = c.id
				INNER JOIN (
					SELECT writing_order_id,
					MAX(process+0) AS max_process_num
					FROM rw_writing_process
					GROUP BY writing_order_id
				) AS p_max ON p_max.writing_order_id = o.id
				WHERE rc.reseller_id = ?
				ORDER BY p.process_date DESC
				LIMIT 40";

		$query = $this->db->query($sql, array(Auth::user()->id));
		$results = Model_Writing_Process::from_db_all($query);		
				
		foreach ($results as $result)
		{			
			$act = array();
			$act['code'] = $result->writing_order_code;
			$process_status = Model_Writing_Process::index_to_status($result->process_num);
			$act['caption'] = Model_Writing_Order::full_process($process_status);
			$act['dt'] = $result->process_date;
			$act['date_raw'] = $result->process_date_raw;
			$act['orderID'] = $result->order_id;
			$max_status = $result->max_process_num;
			
			if ($result->process == "assigned_to_writer") 
			{				
				$w = Model_MOT_Writer::find($result->writer_id);
				$act['writerName'] = $w->name();
			}
			
			if ($result->process == "approved") 
				$act['url'] = Model_Content::permalink_from_id($result->content_id);
						
			if ($max_status >= Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED) 
				$act['content_slug'] = $result->slug;
				
			if ($result->process == "reseller_rejected" || $result->process == "customer_rejected")
				$act['rejectionReason'] = $result->comments;

			$activities[] = $act;
		}
		
		$this->aasort($activities, 'date_raw');
		$activities = array_reverse($activities);
		$activities = array_slice($activities, 0, 40);
		return $activities;
			
	}
}

?>
