<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Order_Claim_Newsroom_Controller extends Iella_Base {

	public function index()
	{
		$this->iella_out->status = false;

		$cart_item = Cart_Item::from_object($this->iella_in->cart_item);
		$user = Model_User::from_object($this->iella_in->user);
		$item = $cart_item->item();

		$claim_company_id = @$cart_item->track->claim_company_id;
		if (!empty($claim_company_id))
		{
			$m_comp = Model_Company::find($claim_company_id);
			$m_comp->user_id = $user->id;
			$m_comp->save();

			$newsroom = Model_Newsroom::find($claim->company_id);
			$newsroom->is_active = 1;
			$newsroom->save();
			
			$claim = new Model_Newsroom_Claim();

			$claim->company_id = $claim_company_id;
			$claim->rep_name = $user->first_name;
			$claim->email = $user->email;
			$claim->date_claimed = Date::$now->format(Date::FORMAT_MYSQL);
			$claim->date_admin_updated = Date::$now->format(Date::FORMAT_MYSQL);
			$claim->status = Model_Newsroom_Claim::STATUS_CONFIRMED;
			$claim->remote_addr = $user->remote_addr;
			$claim->is_paid = 1;
			$claim->save();

			// sending the email now
			$this->vd->user_first_name = $user->first_name;
			$this->vd->company_name = $m_comp->name;
			$message = $this->load->view('email/confirm_claim_paid', null, true);
			$email = new Email();
			$email->set_to_email($user->email);
			$email->set_from_email($this->conf('email_address'));
			$email->set_to_name($user->name());
			$email->set_from_name($this->conf('email_name'));
			$email->set_subject('Activation Complete - Your Company Newsroom Details');
			$email->set_message($message);
			$email->enable_html();
			Mailer::send($email, Mailer::POOL_TRANSACTIONAL);

			$proxy_event = new Iella_Event();
			$proxy_event->data = $this->iella_in;
			$proxy_event->emit('item_order_plan');

			$this->iella_out->status = true;

		}
	}

}

?>