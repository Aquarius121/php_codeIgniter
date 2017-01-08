<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class LEGACY_Subscription_Terminator {
	
	protected static $ci;
	
	public function __construct()
	{
		$this->ci =& get_instance();
	}
	
	// cancel all legacy subscriptions
	public function cancel_all($user)
	{
		if (!($user instanceof Model_User))
			$user = Model_User::find($user);
		if (!$user) throw new Exception();

		$email = new Email();
		$email->__avoid_conversation();
		$email->set_to_email($this->ci->conf('email_address'));
		$email->set_from_email($this->ci->conf('email_address'));
		$email->add_cc($this->ci->conf('dev_email'));		
		$email->set_subject('Cancel Subscription');
		$email->set_message(sprintf('User %s wishes to cancel 
			their legacy (ultracart) subscription. Please cancel.', 
			$this->ci->vd->esc($user->email)));
		$email->enable_html();
		Mailer::send($email, Mailer::POOL_TRANSACTIONAL);
	}
	
}
