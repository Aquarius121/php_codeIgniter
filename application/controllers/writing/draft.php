<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('writing/base');
load_controller('writing/common_writing_trait');

class Draft_Controller extends Writing_Base {
	
	use Common_Writing_Trait;
	
	public function review($writing_order_id, $writing_order_code)
	{
		$m_order = Model_Writing_Order::find($writing_order_id);
		$m_order_code = Model_Writing_Order_Code::find_code($writing_order_code);
		if (!$m_order_code) show_404();
		if (!$m_order) show_404();
		
		// we looked up the writing order and the code didn't match
		if ($m_order_code->writing_order_code != $writing_order_code)
			show_404();
		
		if ($this->input->post('reseller_action') && $this->input->post('reseller_action') == 'send_to_customer')
		{
			$this->reseller_action_send_to_customer($m_order, $m_order_code);
			$this->vd->action_message = 'Thanks for your response. Your customer
				has been notified via email. You will be updated if they
				approve or reject the PR.';
		}
		
		if ($this->input->post('reseller_action') && $this->input->post('reseller_action') == 'reject')
		{
			$this->reseller_action_send_to_writer($m_order, $m_order_code, $this->input->post('reason'));
			$this->vd->action_message = 'We have received your request and will
				review your comments and suggestions with our writer. <br>Once 
				the writer has completed the revisions you\'ll receive an email
				notification with a link to review the press release';									  
		}
		
		if ($this->input->post('reseller_action') && $this->input->post('reseller_action') == 'approve')
		{
			$this->reseller_action_approve($m_order, $m_order_code);
			$this->vd->action_message = 'Thanks for your response.
				Your PR is queued successfully';
		}
		
		if ($this->input->post('customer_action') && $this->input->post('customer_action') == 'reject')
		{
			$this->customer_action_send_to_reseller($m_order, $m_order_code, $this->input->post('reason'));
			$this->vd->action_message = 'We have received your request and will
				review your comments and suggestions with our writer. Once the
				writer has completed the revisions you\'ll receive an email
				notification with a link to review the press release.';
		}
		
		if ($this->input->post('customer_action') && $this->input->post('customer_action') == 'approve')
		{
			$this->customer_action_approve($m_order, $m_order_code);
			$this->vd->action_message = 'Thanks for your response.
				Your PR is queued successfully';
		}
		
		if (!($reseller_id = $m_order_code->reseller_id))
		{
			$reseller = Model_User::find($this->conf('writing_admin_user'));
			$reseller_id = $reseller->id;
		}
		
		$m_reseller_details = Model_Reseller_Details::find($reseller_id);
		$m_content = Model_Content::find($m_order->content_id);
		$m_content_data = Model_Content_Data::find($m_content->id);
		$m_pb_pr = Model_PB_PR::find($m_content->id);
		$m_company = Model_Company::find($m_content->company_id);
		$m_comp_profile = Model_Company_Profile::find($m_content->company_id);
		$m_comp_contact = Model_Company_Contact::find($m_company->company_contact_id);
		$m_country = Model_Country::find($m_comp_profile->address_country_id);
		$images = $m_content->get_images();
		
		$this->vd->pr_title = $m_content->title;
		$this->vd->summary = $m_content_data->summary;
		$this->vd->content = $m_content_data->content;
		$this->vd->company_details = $m_comp_profile->summary;
		$this->vd->company_website = $m_comp_profile->website;
		$this->vd->company_name = $m_company->name;
		$this->vd->company_contact = $m_comp_contact;
		$this->vd->company_profile = $m_comp_profile;
		$this->vd->country = @$m_country->name;
		$this->vd->m_order = $m_order;
		$this->vd->m_order_code = $m_order_code;
		$this->vd->web_video_id = $m_pb_pr->web_video_id;
		$this->vd->web_video_provider = $m_pb_pr->web_video_provider;
		$this->vd->logo_image_id = $m_comp_profile->logo_image_id;
		$this->vd->m_content = $m_content_data;
		$this->vd->images_count = count($images);
		$this->vd->reseller_logged_in = 0;
		
		// the PR has been approved and queued already 
		if ($m_order->status == Model_Writing_Order::STATUS_APPROVED ||
		    $m_order->status == Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED)
			$this->vd->cant_render_message = 'PR already written and approved';
					
		// writing/revision is in progress or will be shortly
		else if ($m_order->status == Model_Writing_Order::STATUS_NOT_ASSIGNED || 
				$m_order->status == Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER || 
				$m_order->status == Model_Writing_Order::STATUS_SENT_BACK_TO_WRITER || 
				$m_order->status == Model_Writing_Order::STATUS_RESELLER_REJECTED || 
				$m_order->status == Model_Writing_Order::STATUS_REVISED_DETAILS_ACCEPTED)
			$this->vd->cant_render_message = 'PR writing/revision in progress';
			
		// the reseller/admin is online but the order is waiting for customer
		else if ((Auth::is_user_online() && (Auth::user()->id == $reseller_id || Auth::is_admin_online())) 
				&& ($m_order->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE || 
				    $m_order->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER))
			$this->vd->cant_render_message = 'PR waiting for an action from the customer';
			
		// the reseller/admin is not online and the PR is waiting for reseller/admin action
		else if (!(($m_reseller_details 
		         	&& $m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_RESELLER_EDITOR 
						&& Auth::is_user_online() && Auth::user()->id == $reseller_id) 
					|| ($m_reseller_details 
					   && $m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_ADMIN_EDITOR
						&& Auth::is_admin_online())
					|| (!$m_reseller_details && Auth::is_admin_online()))
				&& ($m_order->status == Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION || 
					$m_order->status == Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS || 
					$m_order->status == Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER ||
					$m_order->status == Model_Writing_Order::STATUS_CUSTOMER_REJECTED))
			$this->vd->cant_render_message = "PR waiting an action from the editor";
		
		else 
		{
			// determine if the reseller is logged in
			$this->vd->reseller_logged_in = (int) ((bool)
				(($m_reseller_details 
		         	&& $m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_RESELLER_EDITOR 
						&& Auth::is_user_online() && Auth::user()->id == $reseller_id) 
					|| ($m_reseller_details 
					   && $m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_ADMIN_EDITOR
						&& Auth::is_admin_online())
					|| (!$m_reseller_details && Auth::is_admin_online())));
			
			$cust_rej = Model_Writing_Process::get_latest_customer_rejection_comments($m_order->id);
			$this->vd->last_cust_rejection_comments = $cust_rej;
			$reseller_rej = Model_Writing_Process::get_latest_reseller_rejection_comments($m_order->id);
			$this->vd->last_reseller_rejection_comments = $reseller_rej;
			$this->vd->times_rejected = Model_Writing_Process::how_many_times_rejected($m_order->id);
			$writer = Model_MOT_Writer::find($m_order->writer_id);
			$this->vd->writer_name = "{$writer->first_name} {$writer->last_name}";
		}
		
		$this->vd->writing_order_code = $m_order_code->writing_order_code;
		$this->vd->writing_order_id = $m_order->id;		
		
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/draft/review');
		$this->load->view('writing/post-content');
		$this->load->view('writing/footer');
	}
	
	protected function reseller_action_send_to_customer($m_order, $m_order_code)
	{
		if (!$m_order_code->reseller_id)
		{
			// locate the users writing session and notify them
			$m_wr_session = Model_Writing_Session::find_order($m_order->id);
			if (!$m_wr_session) show_404();
			$m_wr_session->notify_written();
			
			// store the writing process update		
			$this->save_writing_process($m_order->id,
				Model_Writing_Order::STATUS_SENT_TO_CUSTOMER,
				Model_Writing_Process::ACTOR_ADMIN);
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
				$actor);
			
			$preview_link = $m_reseller_details->preview_url($m_order->id, $m_order_code->writing_order_code);
			$this->vd->preview_link = $preview_link;
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
	
	protected function reseller_action_send_to_writer($m_order, $m_order_code, $comments)
	{	
		$m_order->status = Model_Writing_Order::STATUS_RESELLER_REJECTED;
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();
		
		if ($m_order_code->reseller_id)
		{
			$reseller_id = $m_order_code->reseller_id;
			$m_reseller_details = Model_Reseller_Details::find($reseller_id);			
			if ($m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_ADMIN_EDITOR)
			     $actor = Model_Writing_Process::ACTOR_ADMIN;
			else $actor = Model_Writing_Process::ACTOR_RESELLER;
		}
		else
		{
			// direct order => actor is admin
			$actor = Model_Writing_Process::ACTOR_ADMIN;
		}
		
		$this->save_writing_process($m_order->id,
			Model_Writing_Order::STATUS_RESELLER_REJECTED, 
			$actor, $comments);
		
		$writer = Model_MOT_Writer::find($m_order->writer_id);
		$m_content = Model_Content::find($m_order->content_id);
		
		$this->vd->writing_order_code = $m_order_code->writing_order_code;
		$this->vd->pr_draft_title = $m_content->title;
		$this->vd->editor_comments = $comments;
		
		$subject = 'URGENT: A PR You Wrote Requires Editing';
		$message_view = 'writing/email/writer_pr_draft_rejection';
		$message = $this->load->view($message_view, null, true);
		
		$email = new Email();
		$email->set_to_email($writer->email);
		$email->set_to_name("{$writer->first_name} {$writer->last_name}");
		$email->set_from_email('support@myoutsourceteam.com');
		$email->set_from_name('MyOutSourceTeam Support');
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}	
	
	protected function reseller_action_approve($m_order, $m_order_code)
	{
		if (!$m_order_code->reseller_id)
		{
			// locate the users writing session and notify them
			$m_wr_session = Model_Writing_Session::find_order($m_order->id);
			if (!$m_wr_session) show_404();
			$m_wr_session->notify_written_queued();
			$m_wr_session->is_archived = 1;
			$m_wr_session->save();
		}
		
		$m_content = Model_Content::find($m_order->content_id);
		$m_content->is_draft = 0;
		$m_content->is_under_review = 0;
		$m_content->is_under_writing = 0;
		$m_content->date_publish = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_content->save();
		
		$m_order->status = Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED;
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();
				
		$this->save_writing_process($m_order->id,
			Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED,
			Model_Writing_Process::ACTOR_RESELLER);
	}
		
	protected function customer_action_send_to_reseller($m_order, $m_order_code, $comments)
	{		
		$m_order->status = Model_Writing_Order::STATUS_CUSTOMER_REJECTED;
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();
		
		$this->save_writing_process($m_order->id,
			Model_Writing_Order::STATUS_CUSTOMER_REJECTED,
			Model_Writing_Process::ACTOR_RESELLER, $comments);
		
		$reseller_id = $m_order_code->reseller_id;
		$reseller = Model_User::find($reseller_id);
		$m_reseller_details = Model_Reseller_Details::find($reseller_id);
			
		if ($m_reseller_details && $m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_RESELLER_EDITOR)
			// reseller found with reseller_editor => contact reseller
			$user_for_contact = $reseller;
		else if ($m_reseller_details && $m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_ADMIN_EDITOR)
			// reseller uses admin_editor => contact admin
		   $user_for_contact = Model_User::find($this->conf('writing_admin_user'));
		else if (!$m_reseller_details)
			// no reseller details => direct order (or bad id) => contact admin
			$user_for_contact = Model_User::find($this->conf('writing_admin_user'));
		else return;
		
		$m_content = Model_Content::find($m_order->content_id);
		$m_company = Model_Company::find($m_content->company_id);
		
		$wo_code = $m_order_code->writing_order_code;
		$preview_link = "writing/draft/review/{$m_order->id}/{$wo_code}";
		$preview_link = $this->website_url($preview_link);
		
		$en = new Email_Notification();
		$en->set_content_view('writing/draft_rejected_by_customer');
		$en->set_data('pr_title', $m_content->title);
		$en->set_data('reseller', $reseller);
		$en->set_data('customer_contact_name', $m_order_code->customer_name);
		$en->set_data('customer_contact_email', $m_order_code->customer_email);
		$en->set_data('customer_company_name', $m_company->name);
		$en->set_data('preview_link', $preview_link);
		$en->set_data('comments', $comments);
		$en->send($user_for_contact);	
	}
	
	protected function customer_action_approve($m_order, $m_order_code)
	{	
		// reseller id null => use inewswire direct
		// so this function should not be called
		if (!($reseller_id = $m_order_code->reseller_id))
			show_404();
		
		$m_content = Model_Content::find($m_order->content_id);
		$m_content->is_draft = 0;
		$m_content->is_under_review = 0;
		$m_content->is_under_writing = 0;
		$m_content->date_publish = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_content->save();
		
		$m_order->status = Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED;
		$m_order->latest_status_date = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_order->save();
		
		$this->save_writing_process($m_order->id,
			Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED,
			Model_Writing_Process::ACTOR_CUSTOMER);
		
		$m_company = Model_Company::find($m_content->company_id);
		$reseller = Model_User::find($reseller_id);
		$preview_link = $m_content->url();
		$preview_link = $this->website_url($preview_link);
		
		$en = new Email_Notification();
		$en->set_content_view('reseller/reseller_pr_approved_by_customer');
		$en->set_data('preview_link', $preview_link);
		$en->set_data('cust_name', $m_order_code->customer_name); 
		$en->set_data('cust_company_name', $m_company->name);
		$en->send($reseller);
	}
	
	public function edit($writing_order_id, $writing_order_code)
	{		
		$criteria = array();
		$criteria[] = array('id', $writing_order_id);
		$criteria[] = array("status NOT IN ('customer_accepted', 'approved')");
		$m_order_code = Model_Writing_Order_Code::find(array('writing_order_code', $writing_order_code));
		
		$m_order = Model_Writing_Order::find($criteria);
		$reseller_id = $m_order_code->reseller_id;
		//if (!$reseller_id) 
		//	show_404();
		$m_reseller_details = Model_Reseller_Details::find($reseller_id);		
		
		if ($m_order && $m_order_code->writing_order_code == $writing_order_code && !$reseller_id)
		{}
		elseif(!$m_order || $m_order_code->writing_order_code != $writing_order_code || 
				((@Auth::user()->id == $reseller_id || @Auth::user()->is_admin) && 
						$m_order->status == 'sent_to_customer') ||
				($m_order->status != 'sent_to_customer' && 
				!($m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_RESELLER_EDITOR && @Auth::user()->id == $reseller_id 
				|| $m_reseller_details->editing_privilege == Model_Reseller_Details::PRIV_ADMIN_EDITOR && @Auth::user()->is_admin))
			)	
		{
			$this->load->view('writing/header');
			$this->load->view('writing/menu');
			$this->load->view('writing/pre-content');
			$this->load->view('writing/prdetails/not_editable');
			$this->load->view('writing/post-content');				
			$this->load->view('writing/footer');
			return;
		}
		
		$m_order = Model_Writing_Order::find($writing_order_id);
		$m_content = Model_Content::find($m_order->content_id);
		$m_content_data = Model_Content_Data::find($m_content->id);
		$m_comp_profile = Model_Company_Profile::find(array('company_id', $m_content->company_id));
		
		if($this->input->post('save'))
		{
			$m_content->title = $this->input->post('title');
			$m_content->save();
			$m_content_data->summary = $this->input->post('summary');
			$m_content_data->content = $this->input->post('content');
			
			$m_content_data->supporting_quote = $this->input->post('supporting_quote');
			$m_content_data->supporting_quote_name = $this->input->post('supporting_quote_name');
			$m_content_data->supporting_quote_title = $this->input->post('supporting_quote_title');
			
			$m_content_data->save();
			$m_comp_profile->summary = $this->input->post('about_company');
			$m_comp_profile->save();
			$feedback_view = 'writing/partials/successfully_updated';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);			
			$this->set_redirect('writing/draft/review/' . $writing_order_id . '/' . $writing_order_code);			
		}
		
		$this->vd->content = $m_content;
		$this->vd->content_data = $m_content_data;
		$this->vd->company_profile = $m_comp_profile;			
		
		$this->load->view('writing/header');
		$this->load->view('writing/menu');
		$this->load->view('writing/pre-content');
		$this->load->view('writing/draft/edit');
		$this->load->view('writing/post-content');				
		$this->load->view('writing/footer');	
	}
	
}

?>
