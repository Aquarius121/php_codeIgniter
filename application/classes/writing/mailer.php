<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Writing_Mailer {
	
	const MOT_EMAIL = 'support@myoutsourceteam.com';
	const MOT_NAME = 'MyOutSourceTeam Support';
	
	public function __construct()
	{
		$ci =& get_instance();
		$this->vd =& $ci->vd;
		$this->load =& $ci->load;
	}
	
	public function send_new_task_to_writer($writer)
	{
		$this->vd->writer = $writer;
		$subject = "{$writer->first_name}, 1 PR Writing Task(s) Have Been Assigned to You";
		$message = $this->load->view('writing/email/writer_writing_task_assignment', null, true);
		
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
	
	public function send_no_details_reminder($reseller, $wo_code)
	{
		$reseller_details = Model_Reseller_Details::find($reseller->id);
		
		$this->vd->customer_name = $wo_code->customer_name;
		$this->vd->reseller_website = $reseller_details->website;
		$this->vd->writing_orders_detail_link = $reseller_details->website_url('prdetailsform.php');
		$this->vd->writing_order_code = $wo_code->writing_order_code;
		$this->vd->reseller_company_name = $reseller_details->company_name;
		$message = $this->load->view('writing/email/customer_no_details_reminder', null, true);
		$subject = 'Reminider: Instructions to Send in Your PR Details';
		
		$email = new Email();
		$email->set_to_email($wo_code->customer_email);
		$email->set_from_email($reseller->email);
		$email->set_to_name($wo_code->customer_name);
		$email->set_from_name($reseller_details->company_name);
		$email->set_subject($subject);
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}
	
}

?>