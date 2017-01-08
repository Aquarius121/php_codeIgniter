<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Whois_Email_Controller extends Auto_Create_NR_Base {
	
	// The purpose is to fetch whois
	// email for ready to build nrs
	// only missing email 
	
	public function index()
	{
				
		$sql = "SELECT cd.*
				FROM ac_nr_owler_company_data cd
				LEFT JOIN ac_nr_owler_whois_email_fetch e
				ON cd.owler_company_id = e.owler_company_id
				
				INNER JOIN ac_nr_owler_fetch_email fe 
				ON fe.owler_company_id = cd.owler_company_id

				WHERE e.owler_company_id IS NULL 
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL
				ORDER BY cd.owler_company_id
				LIMIT 400";

		$result = $this->db->query($sql);
		if (!$result->num_rows()) exit;
		
		$results = Model_Owler_Company_Data::from_db_all($result);
		$domains = array();
		foreach ($results as $result)
		{
			if ($domain = $this->get_domain($result->website))
				$domains[] = $domain;
		}

		if (is_array($domains) && count($domains))
			$domain_string = implode(",", $domains);
		else
			exit;

		
		$url = "http://domainindex.com/api.php?action=whois";
		$url = "{$url}&domain={$domain_string}";
		$url = "{$url}&key=8f82ecb0-2013-0525-1316-40516e0e903c&mode=xml";

		$xml_results = new SimpleXMLElement($url, null, true);

		if (!empty($xml_results->error))
		{
			$this->log($xml_results);
			exit;
		}

		foreach ($results as $result)
		{
			$c_data = $result;
			if ($email = $this->find_email($result->website, $xml_results))
			{
				if (!empty($email))
				{
					$email = str_replace("(", "", $email);
					$email = str_replace(")", "", $email);
					$c_data->email = $email;
					$c_data->save();
				}
			}

			$m_wh = new Model_Owler_Whois_Email_Fetch();
			$m_wh->owler_company_id = $c_data->owler_company_id;
			$m_wh->is_api_read_success = 1;
			if (!empty($email))
				$m_wh->is_email_fetched = 1;

			$m_wh->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
			$m_wh->save();

		}

		//echo "<hr>" . $k . " emails found";
	}

	public function from_domainiq()
	{
		$sql = "SELECT cd.*, cd.owler_company_id AS source_company_id
				FROM ac_nr_owler_company_data cd
				INNER JOIN ac_nr_owler_fetch_email fe
				ON fe.owler_company_id = cd.owler_company_id
				LEFT JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.owler_company_id
				AND diq.source = ?
				WHERE diq.source_company_id IS NULL
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL
				ORDER BY cd.owler_company_id
				LIMIT 200";

		$query = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_OWLER));
		if (!$query->num_rows()) exit;

		$results = Model_Owler_Company_Data::from_db_all($query);
		$this->process_domainiq_whois($results, Model_Whois_Check_Domainiq::SOURCE_OWLER);		
	}

	protected function find_email($website, $xml_results)
	{
		$website = $this->get_domain($website);
		
		foreach($xml_results as $xml_result) // loop through results
		{
			if (trim(strtolower($xml_result->domain)) == trim(strtolower($website))
				&& !empty($xml_result->admin_email))
				return $xml_result->admin_email;
		}

		return false;
	}


	protected function get_domain($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) 
			return $regs['domain'];
		
		return false;
	}

	
	
}

?>
