<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Bill_Failure_Report_Trait {
	
	protected function bill_failure_report_zip($date_start = null, $date_end = null)
	{
		$hash = md5(microtime(true));
		$build_dir = sys_get_temp_dir();
		$build_dir = implode(DIRECTORY_SEPARATOR, array($build_dir, $hash));
		$build_zip = sprintf('%s.zip', $build_dir);
		$html_files = array();

		if ($date_start === null) $date_start = Date::days(-1);
		if ($date_end === null)	$date_end = Date::$now;
		
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $date_end->format(Date::FORMAT_MYSQL);
		
		$sql = "SELECT bf.*, 
			u.id AS user__id,
			u.email AS user__email
			FROM co_bill_failure bf 
			LEFT JOIN nr_user u 
			ON bf.user_id = u.id 
			WHERE bf.date_created >= '{$date_start_str}' 
			AND bf.date_created <  '{$date_end_str}'
			ORDER BY bf.date_created ASC";
		
		$db_result = $this->db->query($sql);
		$failures = Model_Bill_Failure::from_db_all($db_result, array(
			'user' => 'Model_Base',
		));

		if (!$failures) return false;
		Model_Item::enable_cache();
		mkdir($build_dir);
		
		foreach ($failures as $failure)
		{
			$output = $failure->generate_report();
			$filename = sprintf('%d-%s', $failure->id, 
				Slugger::create($failure->date_created));
			$filepath = implode(DIRECTORY_SEPARATOR, array($build_dir, $filename));
			$filepath = sprintf('%s.html', $filepath);

			file_put_contents($filepath, $output);
			shell_exec(sprintf('cd %s && zip -qjm %s %s', 
				escapeshellarg($build_dir),
				escapeshellarg($build_zip), 
				escapeshellarg($filepath)));
		}
		
		Model_Item::disable_cache();

		rmdir($build_dir);
		return $build_zip;
	}
	
}

?>