<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/orders/main');

class Assign_Controller extends Main_Controller {

	protected $tab = 'assign';
	protected $filter = 1;

	public function index($visible_bits = null, $chunk = 1)
	{
		$status_not_assigned = Model_Writing_Order::STATUS_NOT_ASSIGNED;
		$this->filter = "wo.status = '{$status_not_assigned}'";
		$this->vd->tab_name = 'Assign Orders';
		$this->visible_bits = $visible_bits;
		parent::index($chunk);
	}
		
	protected function render_list($chunkination, $results)
	{
		$this->vd->writers = Model_MOT_Writer::find_all();
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/orders/list-assign');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function assign_to_writer()
	{
		$writing_order_id = $this->input->post('writing_order_id');
		$writer_id = $this->input->post('writer_id');
		
		$wr_order = Model_Writing_Order::find($writing_order_id);
		$writer = Model_MOT_Writer::find($writer_id);
		if (!$wr_order) return;
		if (!$writer) return;
		
		$wr_order->status = Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER;
		$wr_order->writer_id = $writer->id;
		$wr_order->latest_status_date = Date::$now->format(Date::FORMAT_MYSQL);
		$wr_order->save();
		
		$w_process = Model_Writing_Process::create();
		$w_process->writing_order_id = $writing_order_id;
		$w_process->process = Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER;
		$w_process->actor = Model_Writing_Process::ACTOR_ADMIN;
		$w_process->target = $writer->id;
		$w_process->save();
		
		$w_mailer = new Writing_Mailer();
		$w_mailer->send_new_task_to_writer($writer);
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The order has been assigned');
		$this->add_feedback($feedback);
		
		// redirect back to the last location
		$url = value_or_null($_SERVER['HTTP_REFERER']);
		$this->redirect($url, false);
	}
	
}

?>