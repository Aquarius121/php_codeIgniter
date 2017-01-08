<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Contact_Us_URL_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		set_time_limit(86400);
		lib_autoload('simple_html_dom');

		$sql = "SELECT cd.*, cd.mynewsdesk_company_id AS source_company_id
				FROM ac_nr_mynewsdesk_company_data cd
				LEFT JOIN ac_nr_fetch_contact_us_url e
				ON e.source_company_id = cd.mynewsdesk_company_id
				AND e.source = ?
				WHERE e.source_company_id IS NULL
				AND NOT ISNULL(NULLIF(cd.website, ''))
				ORDER BY cd.mynewsdesk_company_id
				LIMIT 100";
		
		while (1)
		{
			$source = Model_Fetch_Contact_Us_URL::SOURCE_MYNEWSDESK;
			$results = Model_MyNewsDesk_Company_Data::from_sql_all($sql, array($source));

			if (!count($results))
				break;

			foreach ($results as $result)
				$this->fetch_contact_us_url($result, $source);
		}
	}	
}

?>
