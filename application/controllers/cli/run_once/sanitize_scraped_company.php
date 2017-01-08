<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Sanitize_Scraped_Company_Controller extends Auto_Create_NR_Base { 

	public function index()
	{
		$this->sanitize_scraped_company_data();
	}

	public function sanitize_scraped_company_data()
	{
		set_time_limit(86400);

		$scraping_sources = Model_Company::scraping_sources();
		$scraping_sources_list = sql_in_list($scraping_sources);
		
		$sql = "SELECT nr.company_id, nr.company_name,				
				{{ cp.* AS company_profile USING Model_Company_Profile }}
				FROM nr_newsroom nr
				INNER JOIN nr_company_profile cp
				ON cp.company_id = nr.company_id 
				WHERE nr.source IN ({$scraping_sources_list})
				AND nr.user_id = 1
				AND cp.is_scraped_company_data_sanitized = 0
				LIMIT 200";
		
		while (1)
		{
			$results = Model_Newsroom::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$result->company_name = $this->sanitize($result->company_name);
				$result->save();

				$result->company_profile->description = $this->sanitize($result->company_profile->description);
				$result->company_profile->summary = $this->sanitize($result->company_profile->summary);

				$result->company_profile->is_scraped_company_data_sanitized = 1;
				$result->company_profile->save();
			}
		}

	}


}
