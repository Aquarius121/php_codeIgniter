<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Archive_Reseller_Orders_Controller extends CLI_Base {

	public function index()
	{
		$date_30d_ago = Date::days(-30)->format(Date::FORMAT_MYSQL);
		$current_date = Date::$now->format(DATE::FORMAT_MYSQL);
		
		$sql = "UPDATE nr_content c
			INNER JOIN rw_writing_order w
			ON w.content_id = c.id 
			INNER JOIN rw_writing_order_code wc
			ON w.writing_order_code_id = wc.id
			SET wc.is_archived = 1, w.is_archived = 1,
			wc.archived_date = '{$current_date}',
			w.archived_date = '{$current_date}'
			WHERE (w.is_archived = 0 OR wc.is_archived = 0) 
			AND c.date_publish < '{$date_30d_ago}'
			AND c.is_published = 1";
		
		$this->db->query($sql);
		
		$date_90d_ago = Date::days(-90)->format(Date::FORMAT_MYSQL);
		$current_date = Date::$now->format(DATE::FORMAT_MYSQL);
		
		$sql = "UPDATE rw_writing_order w
			INNER JOIN rw_writing_order_code wc
			ON w.writing_order_code_id = wc.id
			SET wc.is_archived = 1, w.is_archived = 1,
			wc.archived_date = '{$current_date}',
			w.archived_date = '{$current_date}'
			WHERE (w.is_archived = 0 OR wc.is_archived = 0) 
			AND w.latest_status_date < '{$date_90d_ago}'
			AND w.date_ordered < '{$date_90d_ago}'";
		
		$this->db->query($sql);
	}
	
}

?>