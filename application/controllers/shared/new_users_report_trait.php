<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait New_Users_Report_Trait {
	
	protected function new_users_report_csv($date_start = null, $date_end = null)
	{
		if ($date_start === null) $date_start = Date::days(-1);
		if ($date_end === null)	$date_end = Date::$now;
		
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $date_end->format(Date::FORMAT_MYSQL);
		
		$file = File_Util::buffer_file();
		$csv = new CSV_Writer($file);
		
		$csv->write(array(
			'creation_date',
			'user_id',
			'email',
			'first_name',
			'last_name',
			'is_verified',
			'is_active',
			'remote_addr',
		));
		
		$sql = "SELECT u.* FROM nr_user u 
			WHERE u.date_created >= '{$date_start_str}' 
			AND u.date_created < '{$date_end_str}'
			AND u.is_verified = 1
			ORDER BY u.id ASC";
		
		$db_result = $this->db->query($sql);
		$users = Model_User::from_db_all($db_result);
		
		foreach ($users as $user)
		{				
			$date_active = Date::utc($user->date_active);
			$is_active = $date_active >= $date_start;
			
			$csv->write(array(
				$user->date_created,
				$user->id,
				$user->email,
				$user->first_name,
				$user->last_name,
				$user->is_verified,
				(int) $is_active,
				$user->remote_addr,
			));
		}
		
		$csv->close();
		return $file;
	}
	
}

?>