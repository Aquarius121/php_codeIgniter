<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Agent {
	
	public static function random()
	{
		$user_agents = array(
			'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0',
			'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/49.0',
			'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/48.0',
		);

		return $user_agents[rand(0, (count($user_agents) - 1))];
	}
	
}

