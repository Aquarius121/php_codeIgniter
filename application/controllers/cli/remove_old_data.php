<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Remove_Old_Data_Controller extends CLI_Base {
	
	public function index()
	{	
		$dt30 = Date::days(-30);
		$dt60 = Date::days(-60);
		$dt90 = Date::days(-90);

		// planner entries that were never finished older than 60 days
		$sql = "DELETE FROM nr_sales_planner WHERE date_created < ? AND is_finished = 0";
		$this->db->query($sql, array($dt60));

		// autosave data older than 30 days
		$sql = "DELETE FROM nr_content_auto_save WHERE date_created < ?";
		$this->db->query($sql, array($dt30));

		// collaboration sessions older than 60 days
		$sql = "DELETE FROM nr_content_collab WHERE date_created < ?";
		$this->db->query($sql, array($dt60));

		// content history records
		$sql = "DELETE FROM nr_content_change WHERE date_saved < ?";
		$this->db->query($sql, array($dt30));

		// content history deleted records
		$sql = "DELETE FROM nr_content_change_deleted WHERE date_deleted < ?";
		$this->db->query($sql, array($dt90));
	}
	
}