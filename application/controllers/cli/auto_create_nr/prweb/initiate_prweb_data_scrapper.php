<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Initiate_PRWeb_Data_Scrapper extends CLI_Base {

//load_controller('browse/base');
//class Initiate_PRWeb_Data_Scrapper extends Browse_Base {
	
	public function index()
	{
		// Checking if any pr pages
		// are yet to be fetched 
		// from prweb

		$sql = "SELECT COUNT(id) AS count
 				FROM ac_nr_prweb_category
				WHERE pages_scanned < 30
				AND is_completed = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any single pr 
		// page is yet to be fetched
		$sql = "SELECT COUNT(content_id) AS count 
				FROM nr_pb_prweb_pr
				WHERE prweb_company_id = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any url is yet to 
		// be fetched from prweb redirect.aspx
		$sql = "SELECT COUNT(`prweb_company_id`) AS count
				FROM ac_nr_prweb_company_data
				WHERE ISNULL(NULLIF(website,''))
				AND NOT ISNULL(NULLIF(prweb_website_url,''))
				AND num_website_fetch_tries < 3";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any company website is yet to 
		// be crawled for data

		$sql = "SELECT COUNT(cd.prweb_company_id) AS count
				FROM ac_nr_prweb_company_data cd
				LEFT JOIN ac_nr_prweb_website_crawled w
				ON w.prweb_company_id = cd.prweb_company_id
				WHERE w.prweb_company_id IS NULL 
				AND NOT ISNULL(NULLIF(website, ''))";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any social accounts
		// are yet to be verified

		$sql = "SELECT COUNT(prweb_company_id) AS count
				FROM ac_nr_prweb_company_data 
				WHERE (soc_fb IS NOT NULL AND soc_fb <> '' AND soc_fb_feed_status = ?)
				OR (soc_twitter IS NOT NULL AND soc_twitter <> '' AND soc_twitter_feed_status = ?)
				OR (soc_gplus IS NOT NULL AND soc_gplus <> '' AND soc_gplus_feed_status = ?)
				OR (soc_youtube IS NOT NULL AND soc_youtube <> '' AND soc_youtube_feed_status = ?)
				OR (soc_pinterest IS NOT NULL AND soc_pinterest <> '' AND soc_pinterest_feed_status = ?)";
			
		$nc = Model_PRWeb_Company_Data::SOCIAL_NOT_CHECKED;
		$counter = $this->db->query($sql, array($nc, $nc, $nc, $nc, $nc))->row()->count;
		if ($counter)
			return;

		// Checking if any website crawling 
		// for email fetching is left
		
		$sql = "SELECT COUNT(cd.prweb_company_id) AS count
				FROM ac_nr_prweb_company_data cd
				LEFT JOIN ac_nr_prweb_fetch_email e
				ON e.prweb_company_id = cd.prweb_company_id
				WHERE e.prweb_company_id IS NULL 
				AND ISNULL(NULLIF(cd.email, ''))
				AND NOT ISNULL(NULLIF(website, ''))";
		
		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any website filtering 
		// and purification is still left
		$sql = "SELECT COUNT(prweb_company_id) AS count
				FROM ac_nr_prweb_company_data 
				WHERE is_website_read = 0
				AND website IS NOT NULL";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;
		
		// Checking if getting raw website is 
		// still pending
		$sql = "SELECT COUNT(prweb_company_id) AS count
				FROM ac_nr_prweb_company_data
				WHERE is_website_updated = 0";
		
		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any duplicate companies
		// based on dup name are yet to be removed
		$sql = "SELECT name, COUNT(name) AS counter 
				FROM ac_nr_prweb_company
				GROUP BY name
				HAVING COUNT(name) > 1";

		$result = $this->db->query($sql);
		if ($result->num_rows())
			return;
		
		// Now that all the checks are applied
		// we are checking if any data has yet 
		// to be transferred to live db

		$sql = "SELECT COUNT(c.id) AS count
				FROM ac_nr_prweb_company c
				INNER JOIN ac_nr_prweb_company_data cd
				ON cd.prweb_company_id = c.id
				WHERE c.is_migrated_to_live_site = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		$sql = "SELECT count(c.id) AS count
				FROM nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN `nr_pb_prweb_pr` prw
				ON prw.content_id = c.id
				WHERE prw.is_migrated_to_live_site = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Now checking if domainiq whois check
		// of any missing emails is to be performed		
		$sql = "SELECT COUNT(cd.prweb_company_id) AS count
				FROM ac_nr_prweb_company_data cd
				INNER JOIN ac_nr_prweb_fetch_email fe
				ON fe.prweb_company_id = cd.prweb_company_id
				LEFT JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.prweb_company_id
				AND diq.source = ?
				WHERE diq.source_company_id IS NULL
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL";

		$counter = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_PRWEB))->row()->count;
		if ($counter)
			return;


		// Now checking if domainindex whois check
		// of any missing emails is to be performed		
		$sql = "SELECT COUNT(cd.prweb_company_id) AS count
				FROM ac_nr_prweb_company_data cd
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.prweb_company_id
				AND diq.source = ?
				LEFT JOIN ac_nr_whois_check_domainindex di
				ON di.source_company_id = cd.prweb_company_id
				AND di.source = ?
				WHERE di.source_company_id IS NULL 
				AND cd.email is NULL 
				AND  cd.website IS NOT NULL";

		$source = Model_Whois_Check_Domainiq::SOURCE_PRWEB;
		$counter = $this->db->query($sql, array($source, $source))->row()->count;
		if ($counter)
			return;

		// Checking if any contact us URL
		// is yet to be retrieved
		$sql = "SELECT COUNT(cd.prweb_company_id) AS count
				FROM ac_nr_prweb_company_data cd
				LEFT JOIN ac_nr_fetch_contact_us_url e
				ON e.source_company_id = cd.prweb_company_id
				AND e.source = ?
				WHERE e.source_company_id IS NULL
				AND NOT ISNULL(NULLIF(cd.website, ''))";
		
		$counter = $this->db->query($sql, array(Model_Fetch_Contact_Us_URL::SOURCE_PRWEB))->row()->count;
		if ($counter)
			return;

		// If now scrapping is in progress
		// and all the scrapped data has
		// been shifted to the live db
		// continue with creating a new
		// scrape run

		$prweb_scrape_run = new Model_PRWeb_Scrape_Run();
		$prweb_scrape_run->date_started = Date::$now->format(Date::FORMAT_MYSQL);
		$prweb_scrape_run->save();

		$sql = "UPDATE ac_nr_prweb_category
				SET pages_scanned = 0,
				is_completed = 0";

		$this->db->query($sql);
	}

	
}

?>
