<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Initiate_Data_Scrapper_Controller extends CLI_Base {
	
	public function index()
	{
		// Checking if any single pr 
		// page is yet to be fetched
		$sql = "SELECT COUNT(content_id) AS count 
				FROM nr_pb_pr_co_content
				WHERE pr_co_company_id = 0";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		

		// Checking if any newsroom
		// on pr.co is yet 
		// to be crawled for data

		$sql = "SELECT count(cd.pr_co_company_id) AS count
				FROM ac_nr_pr_co_company_data cd
				LEFT JOIN ac_nr_pr_co_nr_crawled w
				ON w.pr_co_company_id = cd.pr_co_company_id
				WHERE w.pr_co_company_id IS NULL 
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;
		

		// Checking if any company website is yet to 
		// be crawled for data

		$sql = "SELECT COUNT(cd.pr_co_company_id) AS count
				FROM ac_nr_pr_co_company_data cd
				LEFT JOIN ac_nr_pr_co_website_crawled w
				ON w.pr_co_company_id = cd.pr_co_company_id
				WHERE w.pr_co_company_id IS NULL 
				AND NOT ISNULL(NULLIF(website, ''))
				AND cd.is_website_valid = 1";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any social accounts
		// are yet to be verified

		$sql = "SELECT COUNT(pr_co_company_id) AS count
				FROM ac_nr_pr_co_company_data 
				WHERE (soc_fb IS NOT NULL AND soc_fb <> '' AND soc_fb_feed_status = ?)
				OR (soc_twitter IS NOT NULL AND soc_twitter <> '' AND soc_twitter_feed_status = ?)
				OR (soc_gplus IS NOT NULL AND soc_gplus <> '' AND soc_gplus_feed_status = ?)
				OR (soc_youtube IS NOT NULL AND soc_youtube <> '' AND soc_youtube_feed_status = ?)
				OR (soc_pinterest IS NOT NULL AND soc_pinterest <> '' AND soc_pinterest_feed_status = ?)";
			
		$nc = Model_PR_Co_Company_Data::SOCIAL_NOT_CHECKED;
		$counter = $this->db->query($sql, array($nc, $nc, $nc, $nc, $nc))->row()->count;
		if ($counter)
			return;

		// Checking if any website crawling 
		// for email fetching is left
		
		$sql = "SELECT COUNT(cd.pr_co_company_id) AS count
				FROM ac_nr_pr_co_company_data cd
				LEFT JOIN ac_nr_pr_co_fetch_email e
				ON e.pr_co_company_id = cd.pr_co_company_id
				WHERE e.pr_co_company_id IS NULL 
				AND ISNULL(NULLIF(cd.email, ''))
				AND NOT ISNULL(NULLIF(website, ''))
				AND cd.is_website_valid = 1";
		
		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

				
		// Now checking if domainiq whois check
		// of any missing emails is to be performed
		$sql = "SELECT COUNT(cd.pr_co_company_id) AS count
				FROM ac_nr_pr_co_company_data cd
				INNER JOIN ac_nr_pr_co_fetch_email fe
				ON fe.pr_co_company_id = cd.pr_co_company_id
				LEFT JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.pr_co_company_id
				AND diq.source = ?
				WHERE diq.source_company_id IS NULL 
				AND cd.email is NULL 
				AND cd.website IS NOT NULL
				AND cd.is_website_valid = 1";

		$counter = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_PR_CO))->row()->count;
		if ($counter)
			return;

		// Now checking if domainindex whois check
		// of any missing emails is to be performed
		$sql = "SELECT COUNT(cd.pr_co_company_id) AS count
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
				AND cd.is_website_valid = 1";

		$source = Model_Whois_Check_Domainiq::SOURCE_PR_CO;
		$counter = $this->db->query($sql, array($source, $source))->row()->count;
		if ($counter)
			return;


		// Checking if any contact us URL
		// is yet to be retrieved
		$sql = "SELECT COUNT(cd.pr_co_company_id) AS count
				FROM ac_nr_pr_co_company_data cd
				LEFT JOIN ac_nr_fetch_contact_us_url e
				ON e.source_company_id = cd.pr_co_company_id
				AND e.source = ?
				WHERE e.source_company_id IS NULL
				AND NOT ISNULL(NULLIF(cd.website, ''))";

		$source = Model_Whois_Check_Domainiq::SOURCE_PR_CO;
		$counter = $this->db->query($sql, array($source))->row()->count;
		if ($counter)
			return;

		// Checking if language has been
		// detected for all the scraped PRs
		$sql = "SELECT COUNT(p.content_id) AS count
				FROM nr_pb_pr_co_content p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				INNER JOIN nr_content_data cd
				ON p.content_id = cd.content_id
				WHERE p.pr_co_company_id > 0
				AND p.language IS NULL
				AND c.type = ?";

		$counter = $this->db->query($sql, array(Model_Content::TYPE_PR))->row()->count;
		if ($counter)
			return;


		// Checking if language has been detected 
		// for all companies' about blurbs
		$sql = "SELECT COUNT(pr_co_company_id) AS count
				FROM ac_nr_pr_co_company_data
				WHERE NOT ISNULL(NULLIF(about_company, ''))
				AND about_company_lang IS NULL";

		$counter = $this->db->query($sql, array(Model_Content::TYPE_PR))->row()->count;
		if ($counter)
			return;


		// Checking if any company is
		// yet to be transfered to the 
		// live site

		$sql = "SELECT COUNT(c.id) AS count
				FROM ac_nr_pr_co_company c
				INNER JOIN ac_nr_pr_co_company_data cd
				ON cd.pr_co_company_id = c.id
				WHERE c.is_migrated_to_live_site = 0
				AND cd.is_website_valid = 1";

		$counter = $this->db->query($sql, array(Model_Content::TYPE_PR))->row()->count;
		if ($counter)
			return;

				

		
		$sql = "UPDATE ac_nr_pr_co_category
				SET pages_scanned = 0,
				is_completed = 0";

		$this->db->query($sql);
	}
}

?>
