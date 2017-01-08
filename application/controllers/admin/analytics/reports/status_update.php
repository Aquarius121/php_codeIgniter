<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');
load_controller('shared/transaction_report_trait');
load_controller('shared/new_users_report_trait');
load_controller('shared/cancellation_report_trait');
load_controller('shared/bill_failure_report_trait');
load_controller('shared/status_update_report_trait');

class Status_Update_Controller extends Admin_Base {

	use Transaction_Report_Trait;
	use New_Users_Report_Trait;
	use Cancellation_Report_Trait;
	use Bill_Failure_Report_Trait;
	use Status_Update_Report_Trait;

	public function index()
	{
		$options = $this->default_date_options();
		$this->generate_report_data($options);
		extract($options);

		$this->load->view('admin/header');
		$this->load->view('admin/analytics/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/analytics/reports/status_update');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function transaction()
	{
		extract($this->default_date_options());
		$buffer = $this->transaction_report_csv(
		 	$date_start_1d, $this->date_end);
		$this->force_download('transactions.csv', 'text/csv', filesize($buffer));
		readfile($buffer);
		unlink($buffer);
		return;
	}

	public function new_users()
	{
		extract($this->default_date_options());
		$buffer = $this->new_users_report_csv(
		 	$date_start_1d, $this->date_end);
		$this->force_download('new_users.csv', 'text/csv', filesize($buffer));
		readfile($buffer);
		unlink($buffer);
		return;
	}

	public function cancellation()
	{
		extract($this->default_date_options());
		$buffer = $this->cancellation_report_csv(
			$date_start_30d, $this->date_end);
		$this->force_download('cancellations.csv', 'text/csv', filesize($buffer));
		readfile($buffer);
		unlink($buffer);
		return;
	}

	public function bill_failure()
	{
		extract($this->default_date_options());
		$buffer = $this->bill_failure_report_zip(
			$date_start_1d, $this->date_end);
		$buffer_size = $buffer ? filesize($buffer) : 0;
		$this->force_download('bill_failures.csv', 'text/csv', $buffer_size);
		if (!$buffer) return;
		readfile($buffer);
		unlink($buffer);
		return;
	}

}