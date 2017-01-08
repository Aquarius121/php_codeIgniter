<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/orders/rejected');
load_controller('writing/common_writing_trait');

class Review_Controller extends Rejected_Controller {

	use Common_Writing_Trait;

	protected $tab = 'review';
	protected $filter = 1;

	public function index($visible_bits = null, $chunk = 1)
	{
		$status_written_sent_to_reseller = Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER;
		$this->filter = "wo.status = '{$status_written_sent_to_reseller}'";
		$this->vd->tab_name = 'Review';
		$this->visible_bits = $visible_bits;
		parent::index_sub($chunk);
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
		$this->load->view('admin/writing/orders/list-review');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
}

?>