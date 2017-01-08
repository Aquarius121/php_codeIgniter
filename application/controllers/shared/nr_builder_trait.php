<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait NR_Builder_Trait {

	protected function date_2_days_ago()
	{
		$n = -2;
		$day_today = Date::$now->format('D');
		
		if ($day_today == 'Mon')
			$n = -3;
		elseif ($day_today == 'Tue')
			$n = -4;
		
		$dt_2_days_ago = Date::days($n)->format(Date::FORMAT_MYSQL);
		return $dt_2_days_ago;
	}

	protected function auto_built_not_exported_counter($source)
	{
		if (!$source || !$dt_2_days_ago = $this->date_2_days_ago())	
			return false;

		$tbl_prefix = $source;
		$column_prefix = "{$source}_";

		if ($tbl_prefix === Model_Company::SOURCE_CRUNCHBASE)
		{
			$column_prefix = null;
			$tbl_prefix = 'cb';
		}		

		$sql = "SELECT COUNT(n.company_id) AS counter
				FROM ac_nr_{$tbl_prefix}_company pc 

				INNER JOIN ac_nr_{$tbl_prefix}_company_data cd 
				ON cd.{$column_prefix}company_id = pc.id 

				INNER JOIN nr_newsroom n 
				ON pc.company_id = n.company_id 
				
				LEFT JOIN ac_nr_newsroom_claim cl 
				ON cl.company_id = pc.company_id AND cl.status='confirmed' 

				LEFT JOIN nr_company_profile cp
				ON cp.company_id <> pc.company_id
				AND cp.website = cd.website

				WHERE ISNULL(NULLIF(pc.date_exported_to_csv,'0000-00-00 00:00:00')) 
				AND cp.company_id is NULL 
				AND cl.id is NULL 
				AND n.date_created < '$dt_2_days_ago'";
		
		$query = $this->db->cached->query($sql, array(), 60);
		$counter = Model::from_db($query)->counter;
		return $counter;
	}

	protected function claim_submissions_counter($source)
	{
		if (!$source || !$dt_2_days_ago = $this->date_2_days_ago())	
			return false;

		$tbl_prefix = $source;

		if ($tbl_prefix === Model_Company::SOURCE_CRUNCHBASE)
			$tbl_prefix = 'cb';

		$sql = "SELECT COUNT(pc.company_id) AS counter 
				FROM ac_nr_{$tbl_prefix}_company pc 
				INNER JOIN ac_nr_newsroom_claim c 
				ON c.company_id = pc.company_id 
				AND c.status = ? 
				WHERE pc.company_id IS NOT NULL 
				AND c.date_claimed  < '$dt_2_days_ago'";

		$query = $this->db->cached->query($sql, array(Model_Newsroom_Claim::STATUS_CLAIMED), 60);		
		$counter = Model::from_db($query)->counter;

		return $counter;
	}

	protected function verified_submissions_counter($source)
	{
		if (!$source || !$dt_2_days_ago = $this->date_2_days_ago())	
			return false;

		$tbl_prefix = $source;

		if ($tbl_prefix === Model_Company::SOURCE_CRUNCHBASE)
			$tbl_prefix = 'cb';

		$sql = "SELECT COUNT(pc.company_id) AS counter 
				FROM ac_nr_{$tbl_prefix}_company pc 
				INNER JOIN ac_nr_newsroom_claim c 
				ON c.company_id = pc.company_id 
				AND c.status = ? 
				WHERE ISNULL(NULLIF(c.date_exported_to_csv,'0000-00-00 00:00:00')) 
				AND pc.company_id IS NOT NULL 
				AND c.date_admin_updated < '$dt_2_days_ago'";

		$query = $this->db->cached->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED), 60);		
		$counter = Model::from_db($query)->counter;

		return $counter;
	}
}