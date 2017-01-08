<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');
load_shared_fnc('shared/auth');

class Register_Controller extends Website_Base {

	protected $ssl_required = true;
	protected $title = 'Register';

	public function index()
	{
		if ($this->input->post())
			$this->do_register();
		
		$this->load->view('website/header');
		$this->load->view('website/register');
		$this->load->view('website/footer');
	}
	
	public function quick()
	{
		$this->load->view('website/header');
		$this->load->view('website/register');
		$this->load->view('website/footer');
	}
	
	public function thanks()
	{
		$this->render_website('website/register-thanks');
	}
	
	protected function do_register()
	{
		Auth_Shared::do_logout($this);

		$remote_addr = $this->env['remote_addr'];
		$limiter = Model_Auth_Limiter::instance($remote_addr);
		
		if ($limiter->limit())
		{
			$this->vd->error_text = 'You have been rate limited.';
			return false;
		}
		
		$email = trim($this->input->post('email'));
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$first_name = trim($this->input->post('first_name'));
		$last_name = trim($this->input->post('last_name'));
		$company_name = trim($this->input->post('company_name'));
		$password = $this->input->post('real_password');
		
		// <password> is a fake field to catch bots
		// if it is filled in then we fail
		if ($this->input->post('password')) die();
		
		if (!$email)
		{
			// the email is not provided or is not valid
			$this->vd->error_text = 'You must provide a valid email address.';
			return;
		}
		
		if (!$password)
		{
			// the password is not provided or is not valid
			$this->vd->error_text = 'You must provide a valid password.';
			return;
		}

		$raw_blocked = Model_Setting::value('blocked_registration_domains');
		$blocked_domains = Model_Setting::parse_block($raw_blocked);

		foreach ($blocked_domains as $domain)
		{
			if (str_ends_with($email, "@{$domain}"))
			{
				// the email provider is blocked - cannot continue.
				$this->vd->error_text = 'The email provider is blocked.';
				return;
			}
		}

		if (!$email)
		{
			// the email is not provided or is not valid
			$this->vd->error_text = 'You must provide a valid email address.';
			return;
		}
		
		// attempt to find an existing user with this email
		$user_exists = (bool) Model_User::find_email($email);
		
		if ($user_exists)
		{
			// the email already exists so we cannot register
			$this->vd->error_text = 'An account already exists with that email address.';
			return;
		}

		if ($first_name && !$last_name && 
			// attempt to extract last name from the full name
			preg_match('#^([^\s]+\s+)+([^\s]+)$#i', $first_name, $ex))
		{
			$first_name = trim($ex[1]);
			$last_name = trim($ex[2]);
		}
		
		$user = Model_User::create();
		$user->email = $email;
		$user->is_enabled = 1;
		$user->first_name = $first_name;
		$user->last_name = $last_name;
		$user->remote_addr = $this->env['remote_addr'];
		$user->set_password($password);
		$user->save();
		
		if ($company_name)
			// create company with given name for user
			$newsroom = Model_Newsroom::create($user, $company_name);
		
		// send welcome/verification email
		$request = new Iella_Request();
		$request->data->email = $email;
		$request->send('auth/send_verification_email');
		
		// schedule event for next run
		$event = new Scheduled_Iella_Event();
		$event->data->user = $user->values();
		if (isset($newsroom))
		     $event->data->newsroom = $newsroom->values();
		else $event->data->newsroom = null;
		$event->schedule('user_register');
		
		// record the event within KM
		$kmec = new KissMetrics_Event_Library($user);
		$kmec->event_signed_up();
		
		// add register tracking feedback for thanks page
		$this->vd->user = $user;
		$feedback = new Feedback_View('partials/track-register');
		$this->add_feedback($feedback);

		// use these suggested values later on
		$this->session->set('suggested_email', $user->email);
		$this->session->set('suggested_company', $company_name);
		$this->session->set('suggested_first_name', $user->first_name);
		$this->session->set('suggested_last_name', $user->last_name);
		
		// redirect to specified next page
		$redirect_url = $this->input->post('redirect_url');
		if ($redirect_url) $this->redirect($redirect_url);

		// redirect to thanks page
		$this->redirect('register/thanks');
	}

}

?>