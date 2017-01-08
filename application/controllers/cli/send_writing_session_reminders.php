<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Send_Writing_Session_Reminders_Controller extends CLI_Base {
		
	public function index()
	{
		$date_24h = Date::hours(-24)->format(Date::FORMAT_MYSQL);
		$date_60h = Date::hours(-60)->format(Date::FORMAT_MYSQL);
		
		$sql = "SELECT ws.* FROM nr_writing_session ws
			LEFT JOIN rw_writing_order wo ON 
			ws.writing_order_id = wo.id
			INNER JOIN rw_writing_order_code woc
			ON woc.id = ws.writing_order_code_id
			WHERE wo.status IS NULL 
			/* cannot be older than 60 hours */
			AND ws.date_created >= '{$date_60h}'
			/* last send must be more than 24 hours ago */
			AND woc.date_last_reminder_sent <= '{$date_24h}'
			/* must have been at least 24 hours */
			AND ws.date_created < '{$date_24h}'";
			
		$query = $this->db->query($sql);
		$sessions = Model_Writing_Session::from_db_all($query);
		
		foreach ($sessions as $session)
		{
			$m_woc = Model_Writing_Order_Code::find($session->writing_order_code_id);
			$m_woc->date_last_reminder_sent = Date::$now->format(Date::FORMAT_MYSQL);
			$m_woc->save();
						
			$session->notify_no_details_yet();
		}
	}
	
}

?>