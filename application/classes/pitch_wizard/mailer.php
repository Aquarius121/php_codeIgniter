<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class Pitch_Wizard_Mailer extends Writing_Mailer {
	
	public function send_new_task_to_list_builder($list_id, $list_builder_user_id)
	{
		$list_builder = Model_User::find($list_builder_user_id);
		$en = new Email_Notification();
		$en->set_content_view('admin/pitch_wizard/assign_list_building');
		$en->send($list_builder);
	}
	
	public function send_rejection_to_list_builder($pitch_list_id, $comments)
	{
		$m_pitch_list = Model_Pitch_List::find($pitch_list_id);
		$list_builder = Model_User::find($m_pitch_list->list_builder_user_id);
		$en = new Email_Notification();
		$en->set_data('comments', $comments);
		$en->set_content_view('admin/pitch_wizard/pitch_list_rejected');
		$en->send($list_builder);
	}
	
	public function assign_writing_task_to_writer($writer)
	{
		$this->vd->writer = $writer;
		$subject = "{$writer->first_name}, 1 Pitch Writing Task Has Been Assigned to You";
		$message = $this->load->view('admin/writing/pitch/email/writer_writing_task_assignment', null, true);
		
		$email = new Email();
		$email->set_to_email($writer->email);
		$email->set_from_email(static::MOT_EMAIL);
		$email->set_to_name($writer->name());
		$email->set_from_name(static::MOT_NAME);
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}
	
	public function pitch_written($pitch_order_id, $writer_name)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
		$ci =& get_instance();
		$preview_link = $ci->common()->url('admin/writing/pitch/review_single/'.$pitch_order_id);
			
		$en = new Email_Notification();
		$en->set_data('writer_name', $writer_name);
		$en->set_data('pitch_subject', $m_pw_content->subject);
		$en->set_data('content_title', $m_content->title);
		$en->set_data('preview_link', $preview_link);

		$writing_admin = Model_User::find($ci->conf('writing_admin_user'));
		$en->set_content_view('admin/pitch_wizard/writer_wrote_pitch.php');
		$en->send($writing_admin);
	}
	
	public function writer_requests_to_revise_detail($pitch_order_id, $comments, $writer_name)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
					
		$en = new Email_Notification();
		$en->set_data('writer_name', $writer_name);
		$en->set_data('content_title', $m_content->title);
		$en->set_data('comments', $comments);
		$ci =& get_instance();
		$writing_admin = Model_User::find($ci->conf('writing_admin_user'));
		$en->set_content_view('admin/pitch_wizard/writer_requests_to_revise_detail');		
		$en->send($writing_admin);
	}
	
	public function send_message_to_writer($pitch_order_id, $comments)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
		
		$writer = Model_MOT_Writer::find($m_pw_order->writer_id);
				
		$this->vd->writer_first_name = $writer->first_name;
		$this->vd->content_title = $m_content->title;
		$this->vd->comments = $comments;
		$subject = "URGENT: Info Added/Clarified For Pitch Writing Task";
					
		$message = $this->load->view('admin/writing/pitch/email/admin_message_to_writer', null, true);		
		$email = new Email();
		$email->set_to_email($writer->email);
		$email->set_from_email(static::MOT_EMAIL);
		$email->set_to_name($writer->name());
		$email->set_from_name(static::MOT_NAME);
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();		
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);		
	}
	
	public function pitch_rejected_messge_to_writer($pitch_order_id, $comments)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$writer = Model_MOT_Writer::find($m_pw_order->writer_id);				
		
		$this->vd->writer_first_name = $writer->first_name;
		$this->vd->pitch_subject = $m_pw_content->subject;
		$this->vd->comments = $comments;
		$this->vd->pitch_order_id = $pitch_order_id;
		$subject = "URGENT: A Pitch You Wrote Requires Editing";
					
		$message = $this->load->view('admin/writing/pitch/email/writer_pitch_rejection', null, true);		
		$email = new Email();
		$email->set_to_email($writer->email);
		$email->set_from_email(static::MOT_EMAIL);
		$email->set_to_name($writer->name());
		$email->set_from_name(static::MOT_NAME);
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();		
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}
	
	public function send_to_customer_to_revise_details($pitch_order_id, $comments)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_company = Model_Company::find($m_campaign->company_id);
		$m_pw_session = Model_Pitch_Session::find('pitch_order_id', $pitch_order_id);
		$m_user = Model_User::find($m_company->user_id);
		
		$ci =& get_instance();
		$pitch_edit_link = $ci->common()->url('manage/contact/pitch/process/'.$m_pw_session->id);
		
		$en = new Email_Notification();
		$en->set_data('customer_name', $m_user->name());
		$en->set_data('pitch_edit_link', $pitch_edit_link);
		$en->set_data('comments', $comments);		
		$en->set_content_view('pitch_wizard/customer_revise_pitch_details');		
		$en->send($m_user);
	}
	
	public function customer_revised_details($pitch_order_id)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
					
		$en = new Email_Notification();
		$en->set_data('content_title', $m_content->title);
		$ci =& get_instance();
		$writing_admin = Model_User::find($ci->conf('writing_admin_user'));
		$en->set_content_view('admin/pitch_wizard/customer_revised_details');		
		$en->send($writing_admin);		
	}
	
	public function send_pitch_to_customer_for_review($pitch_order_id)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_company = Model_Company::find($m_campaign->company_id);
		$m_user = Model_User::find($m_company->user_id);
		
		$ci =& get_instance();
		$pitch_review_link = $ci->common()->url('manage/contact/campaign/edit/'.$m_pw_order->campaign_id);
		
		$en = new Email_Notification();
		$en->set_data('customer_name', $m_user->name());
		$en->set_data('pitch_review_link', $pitch_review_link);
		
		$en->set_content_view('pitch_wizard/customer_review_pitch');		
		$en->send($m_user);
	}
	
	public function user_accepted_pitch($pitch_order_id)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
		$ci =& get_instance();

		$en = new Email_Notification();
		$en->set_data('pitch_subject', $m_pw_content->subject);
		$en->set_data('content_title', $m_content->title);

		$writing_admin = Model_User::find($ci->conf('writing_admin_user'));
		$en->set_content_view('admin/pitch_wizard/user_accepted_pitch');		
		$en->send($writing_admin);		
	}
	
	public function user_rejected_pitch($pitch_order_id, $comments)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
		$ci =& get_instance();

		$en = new Email_Notification();
		$en->set_data('pitch_subject', $m_pw_content->subject);
		$en->set_data('content_title', $m_content->title);
		$en->set_data('comments', $comments);

		$writing_admin = Model_User::find($ci->conf('writing_admin_user'));
		$en->set_content_view('admin/pitch_wizard/user_rejected_pitch');		
		$en->send($writing_admin);		
	}
	
	public function send_reminder_email_to_writer($pitch_order_id)
	{
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);		
		$m_campaign = Model_Campaign::find($m_pw_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);		
		
		$writer = Model_MOT_Writer::find($m_pw_order->writer_id);
		$this->vd->writer_first_name = $writer->first_name;
		$this->vd->content_title = $m_content->title;
		
		$subject = "Reminder: A Pitch Writing Task Needs Your Attention";
		$message = $this->load->view('admin/writing/pitch/email/writer_reminder_email_standard', null, true);
		
		if ($m_pw_order->delivery == Model_Pitch_Order::DELIVERY_RUSH)
		{
			$subject = "Reminder: A Pitch Writing Task (RUSH) Needs Your Attention";
			$message = $this->load->view('admin/writing/pitch/email/writer_reminder_email_rush', null, true);
		}
		
		$email = new Email();
		$email->set_to_email($writer->email);
		$email->set_from_email(static::MOT_EMAIL);
		$email->set_to_name($writer->name());
		$email->set_from_name(static::MOT_NAME);
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}
}
 
?>