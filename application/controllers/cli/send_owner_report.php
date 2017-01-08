<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('shared/transaction_report_trait');
load_controller('shared/new_users_report_trait');
load_controller('shared/cancellation_report_trait');
load_controller('shared/bill_failure_report_trait');
load_controller('shared/status_update_report_trait');

class Send_Owner_Report_Controller extends CLI_Base {

	use Transaction_Report_Trait;
	use New_Users_Report_Trait;
	use Cancellation_Report_Trait;
	use Bill_Failure_Report_Trait;
	use Status_Update_Report_Trait;

	public function index($to_email = null)
	{
		if ($to_email === null)
		{
			$emails_block = Model_Setting::value('staff_email_status_report');
			$emails = Model_Setting::parse_block($emails_block);
			foreach ($emails as $email) 
				$this->latest($email);
		}
		else
		{
			$this->latest($to_email);
		}
	}
	
	public function latest($to_email)
	{
		$options = $this->default_date_options();
		$this->generate_report_data($options);
		extract($options);

		// add the date used for today stats to the subject		
		$email_subject = sprintf('Newswire Status Update (%s)',
			$this->date_end_local->format('Y-m-d'));
		
		$email_message = $this->load->view('email/owner_report', null, true);
		$transaction_report_csv = $this->transaction_report_csv(
		 	$date_start_1d, $this->date_end);
		$new_users_report_csv = $this->new_users_report_csv(
		 	$date_start_1d, $this->date_end);
		$cancellation_report_csv = $this->cancellation_report_csv(
			$date_start_30d, $this->date_end);
		$bill_failure_report_zip = $this->bill_failure_report_zip(
			$date_start_1d, $this->date_end);
		
		$email = new Email();
		$email->set_to_email($to_email);
		$email->set_from_email($this->conf('email_address'));
		$email->add_attachment($transaction_report_csv, 'transactions.csv');
		$email->add_attachment($new_users_report_csv, 'new_users.csv');
		$email->add_attachment($cancellation_report_csv, 'cancellations.csv');
		if ($bill_failure_report_zip)
			$email->add_attachment($bill_failure_report_zip, 'bill_failures.zip');
		$email->set_subject($email_subject);
		$email->set_message($email_message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);

		unlink($transaction_report_csv);
		unlink($new_users_report_csv);
		unlink($cancellation_report_csv);
		if ($bill_failure_report_zip)
			unlink($bill_failure_report_zip);
	}
	
	public function selection($to_email, $date_start, $date_end)
	{
		$timezone = new DateTimeZone('America/New_York');
		$this->date_end = new DateTime($date_end, $timezone);
		$this->date_end->setTimezone(Date::$utc);
		$date_start = new DateTime($date_start, $timezone);
		$date_start->setTimezone(Date::$utc);
		
		$this->vd->date_start = clone $date_start;
		$this->vd->date_end = clone $this->date_end;
		$this->vd->date_start->setTimezone($timezone);
		$this->vd->date_end->setTimezone($timezone);

		// $this->vd->order_stats = $this->order_stats($date_start);
		$this->vd->renew_stats = $this->renew_stats($date_start);
		// $this->vd->pr_stats = $this->pr_stats($date_start);
		// $this->vd->register_stats = $this->register_stats($date_start);
		$email_message = $this->load->view('email/owner_report_selection', null, true);
		
		$email = new Email();
		$email->set_to_email($to_email);
		$email->set_from_email($this->conf('email_address'));
		$email->set_subject('Newswire Status Update');
		$email->set_message($email_message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}

}

?>