<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Required_JS_Enforcer {
	
	public static function enforce()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		$ci =& get_instance();
		
		if ($ci->input->post('required_enforcer'))
		{
			ob_clean();
			$content = $ci->load->view('partials/required_enforcer', null, true);
			echo $content;
			die();
		}
	}
	
}

?>