<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Flush_Approval_Data_Controller extends CLI_Base {
	
	public function index()
	{
		// this is the date 90 days ago
		$date_90 = Date::days(-90)->format(Date::FORMAT_MYSQL);
		
		// search for approval data created 
		// more than 90 days ago and delete it
		$sql = "DELETE FROM nr_approval_data WHERE date_created < ?";
		$this->db->query($sql, array($date_90));
	}
	
}

?>