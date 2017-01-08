<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Initiate_Data_Scrapper_Controller extends CLI_Base {
	
	public function index()
	{
		// Checking if any single pr 
		// page is yet to be fetched
		$sql = "SELECT COUNT(content_id) AS count 
				FROM nr_pb_newswire_ca_pr
				WHERE newswire_ca_company_id = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any company organization
		// page on newswire.ca is yet to be 
		// crawled for data

		$sql = "SELECT count(cd.newswire_ca_company_id) AS count
				FROM ac_nr_newswire_ca_company_data cd
				LEFT JOIN ac_nr_newswire_ca_org_page_crawled w
				ON w.newswire_ca_company_id = cd.newswire_ca_company_id
				WHERE w.newswire_ca_company_id IS NULL 
				AND NOT ISNULL(NULLIF(newswire_ca_org_link, ''))
				ORDER BY cd.newswire_ca_company_id";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;



		// Checking if any company website is yet to 
		// be crawled for data

		$sql = "SELECT COUNT(cd.newswire_ca_company_id) AS count
				FROM ac_nr_newswire_ca_company_data cd
				LEFT JOIN ac_nr_newswire_ca_website_crawled w
				ON w.newswire_ca_company_id = cd.newswire_ca_company_id
				LEFT JOIN ac_nr_newswire_ca_org_page_crawled o
				ON o.newswire_ca_company_id = cd.newswire_ca_company_id
				WHERE w.newswire_ca_company_id IS NULL 
				AND o.newswire_ca_company_id IS NOT NULL
				AND NOT ISNULL(NULLIF(website, ''))
				AND cd.is_website_valid = 1";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any social accounts
		// are yet to be verified

		$sql = "SELECT COUNT(newswire_ca_company_id) AS count
				FROM ac_nr_newswire_ca_company_data 
				WHERE (soc_fb IS NOT NULL AND soc_fb <> '' AND soc_fb_feed_status = ?)
				OR (soc_twitter IS NOT NULL AND soc_twitter <> '' AND soc_twitter_feed_status = ?)
				OR (soc_gplus IS NOT NULL AND soc_gplus <> '' AND soc_gplus_feed_status = ?)
				OR (soc_youtube IS NOT NULL AND soc_youtube <> '' AND soc_youtube_feed_status = ?)
				OR (soc_pinterest IS NOT NULL AND soc_pinterest <> '' AND soc_pinterest_feed_status = ?)";
			
		$nc = Model_Newswire_CA_Company_Data::SOCIAL_NOT_CHECKED;
		$counter = $this->db->query($sql, array($nc, $nc, $nc, $nc, $nc))->row()->count;
		if ($counter)
			return;

		// Checking if any website crawling 
		// for email fetching is left
		
		$sql = "SELECT COUNT(cd.newswire_ca_company_id) AS count
				FROM ac_nr_newswire_ca_company_data cd
				LEFT JOIN ac_nr_newswire_ca_fetch_email e
				ON e.newswire_ca_company_id = cd.newswire_ca_company_id
				LEFT JOIN ac_nr_newswire_ca_org_page_crawled o
				ON o.newswire_ca_company_id = cd.newswire_ca_company_id
				WHERE e.newswire_ca_company_id IS NULL 
				AND ISNULL(NULLIF(cd.email, ''))
				AND o.newswire_ca_company_id IS NOT NULL
				AND NOT ISNULL(NULLIF(website, ''))
				AND cd.is_website_valid = 1";
		
		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

				
		// Checking if any duplicate companies
		// based on dup URL are yet to be removed

		$sql = "SELECT website, COUNT(website) AS counter 
				FROM ac_nr_newswire_ca_company_data
				WHERE is_website_valid = 1
				GROUP BY website
				HAVING COUNT(website) > 1";

		$result = $this->db->query($sql);
		if ($result->num_rows())
			return;

		// Now checking if domainiq whois check
		// of any missing emails is to be performed
		$sql = "SELECT COUNT(cd.newswire_ca_company_id) AS count
				FROM ac_nr_newswire_ca_company_data cd
				INNER JOIN ac_nr_newswire_ca_fetch_email fe
				ON fe.newswire_ca_company_id = cd.newswire_ca_company_id
				LEFT JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.newswire_ca_company_id
				AND diq.source = ?
				WHERE diq.source_company_id IS NULL 
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL
				AND cd.is_website_valid = 1";

		$counter = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_NEWSWIRE_CA))->row()->count;
		if ($counter)
			return;


		// Now checking if domainidex whois check
		// of any missing emails is to be performed
		$sql = "SELECT COUNT(cd.newswire_ca_company_id) AS count
				FROM ac_nr_newswire_ca_company_data cd				
				INNER JOIN ac_nr_newswire_ca_fetch_email fe
				ON fe.newswire_ca_company_id = cd.newswire_ca_company_id
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.newswire_ca_company_id
				AND diq.source = ?
				LEFT JOIN ac_nr_whois_check_domainindex di
				ON di.source_company_id = cd.newswire_ca_company_id
				AND di.source = ?
				WHERE di.source_company_id IS NULL 
				AND cd.email is NULL
				AND cd.website IS NOT NULL
				AND cd.is_website_valid = 1";

		$source = Model_Whois_Check_Domainiq::SOURCE_NEWSWIRE_CA;
		$counter = $this->db->query($sql, array($source, $source))->row()->count;
		if ($counter)
			return;

		// Checking if any contact us URL
		// is yet to be retrieved
		$sql = "SELECT COUNT(cd.newswire_ca_company_id) AS count
				FROM ac_nr_newswire_ca_company_data cd
				LEFT JOIN ac_nr_fetch_contact_us_url e
				ON e.source_company_id = cd.newswire_ca_company_id
				AND e.source = ?
				WHERE e.source_company_id IS NULL
				AND NOT ISNULL(NULLIF(cd.website, ''))";

		$source = Model_Whois_Check_Domainiq::SOURCE_NEWSWIRE_CA;
		$counter = $this->db->query($sql, array($source))->row()->count;
		if ($counter)
			return;
			
		
		$sql = "UPDATE ac_nr_newswire_ca_category
				SET pages_scanned = 0,
				is_completed = 0";

		$this->db->query($sql);
	}
}

?>
