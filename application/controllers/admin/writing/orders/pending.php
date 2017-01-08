<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/orders/main');
load_controller('writing/common_writing_trait');

class Pending_Controller extends Main_Controller {

	use Common_Writing_Trait;

	protected $tab = 'pending';
	protected $filter = 1;

	public function index($visible_bits = null, $chunk = 1)
	{
		$status_list = sql_in_list(array(
			Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER,
			Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION,
			Model_Writing_Order::STATUS_SENT_BACK_TO_WRITER,
			Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE,
			Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS,
			Model_Writing_Order::STATUS_REVISED_DETAILS_ACCEPTED,
			Model_Writing_Order::STATUS_RESELLER_REJECTED,
		));
		
		$this->filter = "wo.status IN ({$status_list})";
		$this->vd->tab_name = 'Pending Orders';
		$this->visible_bits = $visible_bits;
		parent::index($chunk);
	}
		
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$pending_log_modal = new Modal();
		$pending_log_modal->set_title('Conversation');
		$this->add_eob($pending_log_modal->render(500, 320));
		$this->vd->pending_log_modal_id = $pending_log_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/orders/list-pending');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
		
	public function pending_log($order_id)
	{
		$this->pending_conversation($order_id);
	}
	
	public function pending_log_footer($order_id)
	{
		$this->vd->conversation_form_action =
			'admin/writing/orders/pending/conversation_action';
		$this->pending_conversation_footer($order_id);
	}
	
	public function conversation_action()
	{
		if ($this->input->post('pending_reply_to_customer_button'))
		{
			$this->pending_action_message_to_customer(false, 
				Model_Writing_Process::ACTOR_ADMIN);
			$url = value_or_null($_SERVER['HTTP_REFERER']);
			$this->redirect($url, false);
		}
		
		if ($this->input->post('pending_reply_to_writer_button'))
		{
			$this->pending_action_message_to_writer(false,
				Model_Writing_Process::ACTOR_ADMIN);
			$url = value_or_null($_SERVER['HTTP_REFERER']);
			$this->redirect($url, false);
		}
	}
	
}

?>