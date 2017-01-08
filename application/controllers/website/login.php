<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_shared_fnc('shared/auth');
load_controller('website/base');

class Login_Controller extends Website_Base {

	protected $ssl_required = true;
	protected $title = 'Authentication';

	public function index()
	{
		Auth_Shared::do_login($this);
		
		$this->load->view('website/header');
		$this->load->view('website/login');
		$this->load->view('website/footer');
	}
	
	public function forgot()
	{
		if ($email = $this->input->post('email'))
		{
			if ($user = Model_User::find_email($email))
			{
				$request = new Iella_Request();
				$request->data->email = $email;
				$request->send('auth/password_reset');
				if ($request->response->status)
					$this->vd->success = true;
			}
			else
			{
				// feedback message 
				$feedback = new Feedback('error');
				$feedback->text = 'Account not found.';
				$feedback = $feedback->render();
				$this->use_feedback($feedback);
			}
		}
		
		$this->load->view('website/header');
		$this->load->view('website/login-forgot');
		$this->load->view('website/footer');
	}
	
	public function resend()
	{
		if ($email = $this->input->post('email'))
		{
			if ($user = Model_User::find_email($email))
			{
				$request = new Iella_Request();
				$request->data->email = $email;
				$request->send('auth/send_verification_email');
				if ($request->response->status)
					$this->vd->success = true;
			}
			else
			{
				// feedback message 
				$feedback = new Feedback('error');
				$feedback->text = 'Account not found.';
				$feedback = $feedback->render();
				$this->use_feedback($feedback);
			}
		}
		
		$this->load->view('website/header');
		$this->load->view('website/login-resend');
		$this->load->view('website/footer');
	}
	
	public function verified()
	{
		// default email to fill in
		if ($email = $this->session->get('verified_email'))
		     $this->vd->email = $email;
		else $this->vd->email = $this->session->get('suggested_email');
		
		// feedback message to confirm
		$feedback = new Feedback('success');
		$feedback->set_text('The email address has been verified.');
		$feedback = $feedback->render();
		$this->use_feedback($feedback);
		$this->index();
	}
	
	public function verify($user_id, $secret)
	{
		$verified_url = 'login/verified';
		if (!($user = Model_User::find($user_id))) return;
		$this->session->set('verified_email', $user->email);
		if ($user->is_verified) $this->redirect($verified_url);
		$nv = Model_Name_Value::find($secret);
		if (!$nv || $nv->value !== 'verified') return;
		$nv->delete();
		
		// schedule event for next run
		$event = new Scheduled_Iella_Event();
		$event->data->user = $user->values();
		$event->schedule('user_verify');
		
		$this->set_redirect($verified_url);
		$user->is_verified = 1;
		$user->save();
	}
	
	public function reset($user_id, $nonce, $hash)
	{
		if (Auth::is_user_online()) $this->redirect('default');
		if (!($user = Model_User::find($user_id))) return;
		$actual_hash = md5("{$nonce}{$user->password}");
		if ($actual_hash !== $hash) return;
		
		$password = Model_User::generate_password();
		$user->set_password($password);
		$user->is_verified = 1;
		Auth::login($user);
		$user->save();
		
		// load feedback message for the user
		$this->vd->password = $password;
		$feedback_view = 'manage/account/partials/password_reset_feedback';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
		
		// allows the user to set a new password
		// without knowing the old one
		$this->session->set('assume_account_owner', 1);
		$this->redirect('manage/account/details');
	}

}

?>
