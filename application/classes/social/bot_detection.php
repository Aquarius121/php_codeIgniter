<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

interface Social_Bot_Detection {
	
	public function is_bot($env_vars);
	
}

?>