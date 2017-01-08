<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait Iella_Public {
	
	protected function authorize()
	{
 		if (parent::authorize()) return true;
		if ($secret = $this->input->post('iella-user-secret'))
		{
			$user_secret = Model_User_Secret::find($secret);
			if (!$user_secret) return false;
			$user = Model_User::find($user_secret->user_id);
			if (!$user) return false;

			if (Auth::is_user_enabled($user))
			{
				Auth::login($user);
				return true;
			}
		}
	}
	
}

?>
