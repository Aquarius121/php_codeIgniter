<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fetch_Whois_Email_Controller extends CLI_Base {
	
	// The purpose is to fetch whois
	// email for ready to build nrs
	// only missing email 


	public function from_domainiq()
	{
		$sql = "SELECT cd.*
				FROM ac_nr_topseos_company_data cd
				LEFT JOIN ac_nr_topseos_whois_email_fetch e
				ON cd.topseos_company_id = e.topseos_company_id
				WHERE e.topseos_company_id IS NULL 
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL
				ORDER BY cd.topseos_company_id
				LIMIT 1";

		while (1)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) exit;
			
			$c_data = Model_TopSeos_Company_Data::from_db($result);
			$domain = $this->get_domain($c_data->website);

			$url = "http://www.domainiq.com/api?key=i1ds9f9wnkh9cphgigr3emgy6n68fwfe&service=domain_report";
			$url = "{$url}&domain={$domain}&output_mode=json";

			$response = Unirest\Request::get($url);

			if (empty($response->raw_body))
				return false;

			$js_response = json_decode($response->raw_body);
			$email = $js_response->data->whois->emails[0];

			if (!empty($email))
			{
				$c_data->email = $email;
				$c_data->save();
			}

			$m_wh = new Model_TopSeos_Whois_Email_Fetch();
			$m_wh->topseos_company_id = $c_data->topseos_company_id;
			$m_wh->is_domainiq_api_read_success = 1;
			if (!empty($email))
				$m_wh->is_email_fetched = 1;

			$m_wh->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
			$m_wh->save();
			sleep(1);
		}	
	}

	public function from_domainindex()
	{
		$whois_prefixes = Model_TopSeos_Whois_Email_Fetch::__prefixes('e', 'topseos_whois_email_fetch');

		$sql = "SELECT cd.*, {$whois_prefixes}
				FROM ac_nr_topseos_company_data cd 
				INNER JOIN ac_nr_topseos_whois_email_fetch e 
				ON cd.topseos_company_id = e.topseos_company_id 
				WHERE e.is_domainindex_checked = 0 
				AND cd.email is NULL 
				AND cd.website IS NOT NULL 
				ORDER BY cd.topseos_company_id 
				LIMIT 50";

		$result = $this->db->query($sql);
		if (!$result->num_rows()) exit;
		
		$results = Model_TopSeos_Company_Data::from_db_all($result, array(), array(
					'topseos_whois_email_fetch' => 'Model_TopSeos_Whois_Email_Fetch'));
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
		$url = "{$url}&key=6afb79e2-2015-0514-0446-0521b050fb15&mode=xml";

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

			$result->topseos_whois_email_fetch->is_domainindex_checked = 1;
			$result->topseos_whois_email_fetch->is_domainindex_api_read_success = 1;
			if (!empty($email))
				$result->topseos_whois_email_fetch->is_email_fetched = 1;

			$result->topseos_whois_email_fetch->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);
			$result->topseos_whois_email_fetch->save();
		}
	}

	protected function get_domain($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) 
			return $regs['domain'];
		
		return false;
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

	
	
}

?>
