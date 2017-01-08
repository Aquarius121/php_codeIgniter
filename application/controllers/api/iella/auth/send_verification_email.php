<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Send_Verification_Email_Controller extends Iella_Base {
	
	public function index()
	{
		$email = $this->iella_in->email;
		if (!($user = Model_User::find_email($email)))
			return $this->iella_out->status = false;
		$this->iella_out->status = true;
		$this->vd->include_link = false;
		$this->vd->user = $user;
		
		if (!$user->is_verified)
		{
			// generate random name/value
			$this->vd->include_link = true;
			$this->vd->secret = substr(md5(microtime(true)), 0, 16);
			
			// stores name/value in db
			$nv = new Model_Name_Value();
			$nv->date_expires = Date::days(30);
			$nv->name = $this->vd->secret;
			$nv->value = 'verified';
			$nv->save();
		}
		
		// password has been generated, so send that too
		$this->vd->password = @$this->iella_in->password;
		
		// welcome email message to be sent to the user 
		$message = $this->load->view('email/registration', null, true);
		
		$email = new Email();
		$email->__avoid_conversation();
		$email->set_to_email($user->email);
		$email->set_from_email($this->conf('email_address'));
		$email->set_to_name($user->name());
		$email->set_from_name($this->conf('email_name'));
		$email->set_subject('Activate Your Newswire Account');
		$email->set_message($message);
		$email->enable_html();
		Mailer::queue($email, false, Mailer::POOL_TRANSACTIONAL);
	}
	
}

?>
