<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Whois_Email_Controller extends Auto_Create_NR_Base {
	
	// The purpose is to fetch whois email for 
	// the companies we were not able to scrape 
	// email from other sources
	
	public function from_domainiq()
	{
		$sql = "SELECT cd.*, cd.pr_co_company_id AS source_company_id
				FROM ac_nr_pr_co_company_data cd
				INNER JOIN ac_nr_pr_co_fetch_email fe
				ON fe.pr_co_company_id = cd.pr_co_company_id
				LEFT JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.pr_co_company_id
				AND diq.source = ?
				WHERE diq.source_company_id IS NULL 
				AND cd.email is NULL 
				AND cd.website IS NOT NULL
				AND cd.is_website_valid = 1
				ORDER BY cd.pr_co_company_id
				LIMIT 30";

		$query = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_PR_CO));
		if (!$query->num_rows()) exit;

		$results = Model_PR_Co_Company_Data::from_db_all($query);
		$this->process_domainiq_whois($results, Model_Whois_Check_Domainiq::SOURCE_PR_CO);
	}

	public function from_domainindex()
	{			
		$sql = "SELECT cd.*, cd.pr_co_company_id AS source_company_id
				FROM ac_nr_pr_co_company_data cd
				
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.pr_co_company_id
				AND diq.source = ?

				LEFT JOIN ac_nr_whois_check_domainindex di
				ON di.source_company_id = cd.pr_co_company_id
				AND di.source = ?

				WHERE di.source_company_id IS NULL 
				AND cd.email is NULL 
				AND cd.website IS NOT NULL
				AND cd.is_website_valid = 1
				ORDER BY cd.pr_co_company_id
				LIMIT 400";

		$source = Model_Whois_Check_Domainiq::SOURCE_PR_CO;
		$result = $this->db->query($sql, array($source, $source));
		if (!$result->num_rows()) exit;
		
		$results = Model_PR_Co_Company_Data::from_db_all($result);
		$this->process_domainindex_whois($results, $source);
	}	
}

?>
