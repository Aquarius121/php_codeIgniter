<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/orders/main');

class No_Details_Controller extends Main_Controller {
	
	protected $tab = 'no_details';
	protected $filter = 'wo.id IS NULL';
		
	public function index($visible_bits = null, $chunk = 1)
	{
		$this->vd->tab_name = 'No Details Yet';
		$this->visible_bits = $visible_bits;
		parent::index($chunk);
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/orders/list-no-details');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function send_reminder($id)
	{
		$wo_code = Model_Writing_Order_Code::find($id);
		if (!$wo_code->customer_email) return;
		
		if ($wo_code->reseller_id)
		{
			$reseller_id = $wo_code->reseller_id; 
			$reseller = Model_User::find($reseller_id);
			$mailer = new Writing_Mailer();
			$mailer->send_no_details_reminder($reseller, $wo_code);
		}
		else
		{
			if ($wr_session = Model_Writing_Session::find_code($wo_code->id))
				$wr_session->notify_no_details_yet();
		}
		
		$wo_code->date_last_reminder_sent = 
			Date::$now->format(DATE::FORMAT_MYSQL);
		$wo_code->save();
		
		$feedback_view = 'admin/writing/partials/message_sent';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
		
		// redirect back to the last location
		$url = value_or_null($_SERVER['HTTP_REFERER']);
		$this->redirect($url, false);
	}
	
}

?>