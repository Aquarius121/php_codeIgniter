<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/orders/main');
load_controller('writing/common_writing_trait');

class Rejected_Controller extends Main_Controller {

	use Common_Writing_Trait;

	protected $tab = 'rejected';
	protected $filter = 1;

	public function index($visible_bits = null, $chunk = 1)
	{
		$status_customer_rejected = Model_Writing_Order::STATUS_CUSTOMER_REJECTED;
		$this->filter = "wo.status = '{$status_customer_rejected}'";
		$this->vd->tab_name = 'Rejected Orders';
		$this->visible_bits = $visible_bits;
		parent::index($chunk);
	}
		
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$rej_log_modal = new Modal();
		$rej_log_modal->set_title('Conversation');
		$this->add_eob($rej_log_modal->render(500, 320));
		$this->vd->rej_log_modal_id = $rej_log_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		// this uses the same list as the review page
		$this->load->view('admin/writing/orders/list-review');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
		
	public function rejection_log($order_id)
	{
		$this->rejection_conversation($order_id);
	}
	
	public function rejection_log_footer($order_id)
	{
		$this->vd->conversation_form_action =
			'admin/writing/orders/rejected/conversation_action';
		$this->rejection_conversation_footer($order_id);
	}
	
	public function conversation_action()
	{
		if ($this->input->post('rejected_send_to_customer_button'))
		{
			$this->rejected_action_sending_to_customer(false);
			$url = value_or_null($_SERVER['HTTP_REFERER']);
			$this->redirect($url, false);
		}
		
		if ($this->input->post('rejected_send_to_writer_button'))
		{
			$this->rejected_action_sending_to_writer(false,
				Model_Writing_Process::ACTOR_ADMIN);
			$url = value_or_null($_SERVER['HTTP_REFERER']);
			$this->redirect($url, false);
		}
	}
	
}

?>