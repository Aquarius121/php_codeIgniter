<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Facebook_Bot_Detection implements Social_Bot_Detection {
	
	public function is_bot($env_vars)
	{
		$user_agent = $env_vars['user_agent'];
		if (preg_match('#facebookexternalhit/[0-9\.]+#is', $user_agent))
			return true;
		return false;
	}
	
}

?>