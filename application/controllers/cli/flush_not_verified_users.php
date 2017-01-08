<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Flush_Not_Verified_Users_Controller extends CLI_Base {
	
	public function index()
	{
		$dt_90d = Date::days(-90)->format(Date::FORMAT_MYSQL);
		$sql = "DELETE FROM nr_user_base WHERE
			date_created < ? AND is_verified = 0";
		$this->db->query($sql, array($dt_90d));
	}
	
}

?>