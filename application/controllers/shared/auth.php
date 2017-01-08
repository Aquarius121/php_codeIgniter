<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth_Shared {
	
	public static function do_login($ci, $redirect = 'default')
	{
		if (Auth::is_user_online() && !$ci->input->post())
			$ci->redirect('default');
		
		if (!$ci->input->post()) return;
		static::do_logout($ci);

		$remote_addr = $ci->env['remote_addr'];
		$limiter = Model_Auth_Limiter::instance($remote_addr);
		
		if ($limiter->limit())
		{
			$ci->vd->error_text = 'Rate Limit: Please wait.';
			$ci->vd->error_code = 999;
			return false;
		}
		
		$email = strtolower($ci->input->post('email'));
		$password = $ci->input->post('password');
		$ci->vd->email = $email;
		
		if (!empty($email))
		{
			// sleep to prevent detection 
			// of failure by response time
			usleep(500000);
			
			$errors = array(
				Auth::ERROR_NONE 				=> null,
				Auth::ERROR_CREDENTIALS 	=> 'Invalid Credentials: Please try again.',
				Auth::ERROR_DISABLED 		=> 'Account Disabled: Please contact our staff.',
				Auth::ERROR_NOT_VERIFIED 	=> 'Account Not Verified: Please check the email inbox.',
			);
			
			// by default we only allow verified
			$allow_non_verified = false;

			// anybody purchasing can login without verify
			if (Cart::instance()->total_with_discount() > 0)
				$allow_non_verified = true;

			// authenticate with new system first
			// * we trim password because they are often copied
			//   from registration emails with extra whitespace
			if (Auth::authenticate($email, $password, $allow_non_verified) ||
			    Auth::authenticate($email, trim($password), $allow_non_verified)) 
			{
				if ($ci->is_fallback_server() && !Auth::is_admin_online())
				{
					$ci->set_redirect('login');
					Auth::logout();
					return;
				}

				// record the events within KM
				$kmec = new KissMetrics_Event_Library(Auth::user());
				$kmec->event_signed_in();

				if ($redirect === false)
					return Auth::user();
		
				if ($hash = $ci->input->get('intent'))
				{
					$intent = Data_Cache_LT::read($hash);
					Data_Cache_LT::delete($hash);
					$ci->redirect($intent, false);
				}
				 
				$ci->redirect($redirect, false);
			}
			
			// failed => extra sleep
			usleep(500000);
			
			// use error code from new system
			$error_code = Auth::__error_code();
			$ci->vd->error_code = $error_code;
			$ci->vd->error_text = $errors[$error_code];

			return false;
		}

		return false;
	}
	
	public static function do_logout($ci)
	{
		Auth::logout();
	}
	
}

?>