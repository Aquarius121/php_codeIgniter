<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Initiate_MarketWired_Data_Scrapper extends CLI_Base {

	public function index()
	{
		
		// Checking if any pr pages
		// are yet to be fetched 
		// from marketwired

		$sql = "SELECT COUNT(id) AS count
 				FROM ac_nr_marketwired_category
				WHERE is_read = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any single pr 
		// page is yet to be fetched
		$sql = "SELECT COUNT(content_id) AS count 
				FROM nr_pb_marketwired_pr
				WHERE marketwired_company_id = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		
		// Checking if any company website is yet to 
		// be crawled for data

		$sql = "SELECT COUNT(cd.marketwired_company_id) AS count
				FROM ac_nr_marketwired_company_data cd
				LEFT JOIN ac_nr_marketwired_website_crawled w
				ON w.marketwired_company_id = cd.marketwired_company_id
				WHERE w.marketwired_company_id IS NULL 
				AND NOT ISNULL(NULLIF(website, ''))";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any social accounts
		// are yet to be verified

		$sql = "SELECT COUNT(marketwired_company_id) AS count
				FROM ac_nr_marketwired_company_data 
				WHERE (soc_fb IS NOT NULL AND soc_fb <> '' AND soc_fb_feed_status = ?)
				OR (soc_twitter IS NOT NULL AND soc_twitter <> '' AND soc_twitter_feed_status = ?)
				OR (soc_gplus IS NOT NULL AND soc_gplus <> '' AND soc_gplus_feed_status = ?)
				OR (soc_youtube IS NOT NULL AND soc_youtube <> '' AND soc_youtube_feed_status = ?)
				OR (soc_pinterest IS NOT NULL AND soc_pinterest <> '' AND soc_pinterest_feed_status = ?)";
			
		$nc = Model_MarketWired_Company_Data::SOCIAL_NOT_CHECKED;
		$counter = $this->db->query($sql, array($nc, $nc, $nc, $nc, $nc))->row()->count;
		if ($counter)
			return;


		// Checking if any website crawling 
		// for email fetching is left
		
		$sql = "SELECT COUNT(cd.marketwired_company_id) AS count
				FROM ac_nr_marketwired_company_data cd
				LEFT JOIN ac_nr_marketwired_fetch_email e
				ON e.marketwired_company_id = cd.marketwired_company_id
				WHERE e.marketwired_company_id IS NULL 
				AND cd.email IS NULL
				AND NOT ISNULL(NULLIF(website, ''))";
		
		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any duplicate companies
		// based on dup URL are yet to be removed

		$sql = "SELECT website, COUNT(website) AS count
				FROM ac_nr_marketwired_company_data
				GROUP BY website
				HAVING COUNT(website) > 1";

		$result = $this->db->query($sql);
		if ($result->num_rows())
			return;

		// Now checking if domainiq whois check
		// of any missing emails is to be performed		
		$sql = "SELECT COUNT(cd.marketwired_company_id) AS count
				FROM ac_nr_marketwired_company_data cd
				INNER JOIN ac_nr_marketwired_fetch_email fe
				ON fe.marketwired_company_id = cd.marketwired_company_id
				LEFT JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.marketwired_company_id
				AND diq.source = ?
				WHERE diq.source_company_id IS NULL 
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL";

		$counter = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_MARKETWIRED))->row()->count;
		if ($counter)
			return;


		// Now checking if domainindex whois check
		// of any missing emails is to be performed
		$sql = "SELECT COUNT(cd.marketwired_company_id) AS count
				FROM ac_nr_marketwired_company_data cd
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.marketwired_company_id
				AND diq.source = ?
				LEFT JOIN ac_nr_whois_check_domainindex di
				ON di.source_company_id = cd.marketwired_company_id
				AND di.source = ?
				WHERE di.source_company_id IS NULL
				AND cd.email is NULL 
				AND cd.website IS NOT NULL";

		$source = Model_Whois_Check_Domainiq::SOURCE_MARKETWIRED;
		$counter = $this->db->query($sql, array($source, $source))->row()->count;
		if ($counter)
			return;


		// Checking if any contact us URL
		// is yet to be retrieved
		$sql = "SELECT COUNT(cd.marketwired_company_id)  AS count
				FROM ac_nr_marketwired_company_data cd
				LEFT JOIN ac_nr_fetch_contact_us_url e
				ON e.source_company_id = cd.marketwired_company_id
				AND e.source = ?
				WHERE e.source_company_id IS NULL
				AND NOT ISNULL(NULLIF(cd.website, ''))";

		$source = Model_Whois_Check_Domainiq::SOURCE_MARKETWIRED;
		$counter = $this->db->query($sql, array($source))->row()->count;
		if ($counter)
			return;
		

		// Now that all the checks are applied
		// we are checking if any data has yet 
		// to be transferred to live db

		$sql = "SELECT COUNT(c.id) AS count
				FROM ac_nr_marketwired_company c
				INNER JOIN ac_nr_marketwired_company_data cd
				ON cd.marketwired_company_id = c.id
				WHERE c.is_migrated_to_live_site = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		$sql = "SELECT count(c.id) AS count
				FROM nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_pb_marketwired_pr prw
				ON prw.content_id = c.id
				WHERE prw.is_migrated_to_live_site = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;


		
		// If now scrapping is in progress
		// and all the scrapped data has
		// been shifted to the live db
		// continue with creating a new
		// scrape run

		$marketwired_scrape_run = new Model_MarketWired_Scrape_Run();
		$marketwired_scrape_run->date_started = Date::$now->format(Date::FORMAT_MYSQL);
		$marketwired_scrape_run->save();

		$sql = "UPDATE ac_nr_marketwired_category
				SET is_read = 0";

		$this->db->query($sql);
	}

	
}

?>
