<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class View_Controller extends Manage_Base {

	const LISTING_CHUNK_SIZE = 10;
	public $title = 'View Order';
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->advanced_view = true;
	}
	
	public function thanks($order)
	{
		$this->vd->advanced_view = false;
		$this->index($order);
	}

	public function index($order, $chunk = 1)
	{
		if (!($order = Model_Order::find($order)))
			$this->redirect('manage/account/order/history');
		if ($order->user_id != Auth::user()->id)
			$this->denied();

		$this->fetch_transactions($order, $chunk);
		$cart = Virtual_Cart::create_from_order($order);
		$cart->allow_expired_coupon();
		$cart->allow_deleted_coupon();
		
		$this->vd->user_email = Auth::user()->email;
		$this->vd->order = $order;
		$this->vd->order_data = $order->raw_data();
		$this->vd->cart = $cart;
		$this->vd->first_transaction = Model_Transaction::find_order_first($order);

		$gTransaction = $this->vd->first_transaction->gateway_transaction();
		$this->vd->gTransaction = $gTransaction;
		
		$this->load->view('manage/header');
		$this->load->view('manage/account/order/view');
		$this->load->view('manage/footer');
	}
	
	protected function fetch_transactions($order, $chunk)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring("manage/account/order/view/{$order->id}/-chunk-");
		$chunkination->set_url_format($url_format);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM co_transaction t
			WHERE t.order_id = ? ORDER BY t.date_created DESC {$limit_str}";
		$db_result = $this->db->query($sql, array($order->id));
		$transactions = Model_Transaction::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$this->vd->transactions = $transactions;
		$this->vd->chunkination = $chunkination;
		$chunkination->set_total($total_results);
				
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "manage/account/order/view/{$order->id}";
			$this->redirect(gstring($url));
		}
	}
	
}

?>
