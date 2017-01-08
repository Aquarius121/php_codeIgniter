<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/orders/main');

class Customer_Review_Controller extends Main_Controller {

	protected $tab = 'customer_review';
	protected $filter = 1;

	public function index($visible_bits = null, $chunk = 1)
	{
		$status_sent_to_customer = Model_Writing_Order::STATUS_SENT_TO_CUSTOMER;
		$this->filter = "wo.status = '{$status_sent_to_customer}'";
		$this->vd->tab_name = 'Customer Review';
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
		$this->load->view('admin/writing/orders/list-customer-review');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
}

?>