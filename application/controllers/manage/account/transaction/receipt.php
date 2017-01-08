<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Receipt_Controller extends Manage_Base {

	public function index($transaction_id)
	{
		$user = Auth::user();		
		if (!($transaction = Model_Transaction::find($transaction_id))) return;
		if ($transaction->user_id != $user->id) return;
	
		// setting destination email for the receipt
		if ($this->input->post('email'))
		{
			$email_to = trim($this->input->post('email'));
			if(!filter_var($email_to, FILTER_VALIDATE_EMAIL))
			{
				$feedback = new Feedback('error');
				$feedback->set_title('Error!');
				$feedback->set_text('The email you entered is invalid.');
				$this->add_feedback($feedback);
				return;
			}
		} 
		else 
		{
			$email_to = $user->email;
			$this->set_redirect('manage/account/transaction/history');
		}

		$order = Model_Order::find($transaction->order_id);
		$cart = Virtual_Cart::instance();
		$cart->unserialize($transaction->virtual_cart);
		$cart->allow_expired_coupon();
		$cart->allow_deleted_coupon();	
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Please check the email inbox.');
		$this->add_feedback($feedback);
		
		$this->vd->user = $user;
		$this->vd->cart = $cart;
		$this->vd->transaction = $transaction;
		$this->vd->order = $order;

		// use billing data if available 
		// so the address is included in receipt
		$this->vd->data = Model_Billing::find($user->id);
		if (!$this->vd->data) $this->vd->data = new Raw_Data();

		// receipt email message to be sent to the user 
		$message = $this->load->view('email/receipt', null, true);
		
		// send receipt
		$email = new Email();
		$email->set_to_email($email_to);
		$email->set_from_email($this->conf('email_address'));
		if($email_to === $user->email) 
			$email->set_to_name($user->name());
		$email->set_from_name($this->conf('email_name'));
		$email->set_subject('Newswire Receipt');
		$email->set_message($message);
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}

}

?>
