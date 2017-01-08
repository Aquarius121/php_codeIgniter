<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class View_Controller extends Manage_Base {

	public $title = 'View Transaction';

	public function index($transaction)
	{
		if (!($transaction = Model_Transaction::find($transaction)))
			$this->redirect('manage/acco	unt/transaction/history');
		$gTransaction = $transaction->gateway_transaction();
		$cart = Virtual_Cart::instance();
		$cart->unserialize($transaction->virtual_cart);
		$cart->allow_expired_coupon();
		$cart->allow_deleted_coupon();

		$this->vd->transaction = $transaction;
		$this->vd->gTransaction = $gTransaction;
		$this->vd->status = false;
		$this->vd->isVoidable = false;
		$this->vd->isRefundable = false;
		$this->vd->refundAvailable = 0;
		$this->vd->order = Model_Order::find($transaction->order_id);
		$this->vd->cart = $cart;

		if ($gTransaction)
		{
			$this->vd->status = $gTransaction->status_text();
			$this->vd->isVoidable = $gTransaction->is_voidable();
			$this->vd->isRefundable = $gTransaction->is_refundable();
			$this->vd->refundAvailable = sprintf('%0.2f', 
				$gTransaction->refund_available());
		}

		$this->load->view('manage/header');
		$this->load->view('manage/account/transaction/view');
		$this->load->view('manage/footer');
	}

	public function void()
	{
		$mTransaction = $this->action_init();
		$status = $mTransaction->gateway_transaction()->void();

		if ($status)
		{
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Transaction has been voided.');
			$this->add_feedback($feedback);
		}
		else
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Error!');
			$feedback->set_text('Transaction could not be voided.');
			$this->add_feedback($feedback);
		}

		$this->redirect(sprintf('manage/account/transaction/view/%s',
			$mTransaction->id));
	}

	public function refund()
	{
		$mTransaction = $this->action_init();
		$amount = $this->input->post('amount');
		$status = $mTransaction->gateway_transaction()->refund($amount);

		if ($status)
		{
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Transaction has been refunded.');
			$this->add_feedback($feedback);
		}
		else
		{
			$feedback = new Feedback('error');
			$feedback->set_title('Error!');
			$feedback->set_text('Transaction could not be refunded.');
			$this->add_feedback($feedback);
		}

		$this->redirect(sprintf('manage/account/transaction/view/%s',
			$mTransaction->id));
	}

	protected function action_init()
	{
		if (!Auth::is_admin_online()) 
			$this->denied();

		$id = $this->input->post('id');
		$mTransaction = Model_Transaction::find($id);
		if (!$mTransaction) $this->denied();
		return $mTransaction;
	}
	
}