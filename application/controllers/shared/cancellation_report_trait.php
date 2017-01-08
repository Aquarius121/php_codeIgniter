<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Cancellation_Report_Trait {
	
	protected function cancellation_report_csv($date_start = null, $date_end = null)
	{
		if ($date_start === null) $date_start = Date::days(-1);
		if ($date_end === null)	$date_end = Date::$now;
		
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $date_end->format(Date::FORMAT_MYSQL);
		
		$file = File_Util::buffer_file();
		$csv = new CSV_Writer($file);
		
		$csv->write(array(
			'date_cancelled',
			'user_id',
			'user_email',
			'item_name',
			'reason',
		));
		
		$sql = "SELECT c.*, u.id AS user_id, 
			u.email AS user_email, 
			i.name AS item_name
			FROM co_cancellation c
			INNER JOIN co_component_item ci 
			ON ci.id = c.component_item_id
			INNER JOIN co_component_set cs
			ON cs.id = ci.component_set_id
			INNER JOIN nr_user u 
			ON u.id = cs.user_id
			INNER JOIN co_item i
			ON i.id = ci.item_id
			WHERE c.date_cancel >= '{$date_start_str}' 
			AND c.date_cancel < '{$date_end_str}'
			ORDER BY c.id ASC";
		
		$db_result = $this->db->query($sql);
		$cancellations = Model_Cancellation::from_db_all($db_result);
		
		foreach ($cancellations as $cancellation)
		{				
			$csv->write(array(
				$cancellation->date_cancel,
				$cancellation->user_id,
				$cancellation->user_email,
				$cancellation->item_name,
				$cancellation->raw_data()->reason,
			));
		}
		
		$csv->close();
		return $file;
	}
	
}

?>