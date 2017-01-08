<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class CB_Fetch_Whois_Email_Controller extends CLI_Base {
	
	// The purpose is to fetch whois
	// email for ready to build nrs
	// only missing email 
	
	public function index()
	{
		// TODO: WRONG INPUT / EXPIRY CHECKING
				
		$sql = "SELECT cd.*
				FROM ac_nr_cb_company_data cd
				LEFT JOIN ac_nr_cb_whois_email_fetch e
				ON cd.company_id = e.cb_company_id
				WHERE e.cb_company_id IS NULL 
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL
				ORDER BY cd.company_id
				LIMIT 400";

		$result = $this->db->query($sql);
		if (!$result->num_rows()) exit;
		
		$results = Model_CB_Company_Data::from_db_all($result);
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

			$m_wh = new Model_CB_Whois_Email_Fetch();
			$m_wh->cb_company_id = $c_data->company_id;
			$m_wh->is_api_read_success = 1;
			if (!empty($email))
				$m_wh->is_email_fetched = 1;

			$m_wh->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
			$m_wh->save();

		}

		//echo "<hr>" . $k . " emails found";
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
