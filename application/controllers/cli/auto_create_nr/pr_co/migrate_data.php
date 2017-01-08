<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Migrate_Data_Controller extends CLI_Base {
	
	public function index()
	{
		$this->notify_migration_failure();

		$sql = "UPDATE  ac_nr_pr_co_company_data
				SET email_original = email
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);

		$sql = "UPDATE  ac_nr_pr_co_company_data
				SET email = 'anthony@newswire.com'
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);


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



		// Checking if any company website 
		// is yet to be crawled for data

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



	
		// Now that all the checks are applied
		// we are ready to send data to live db

		$sql = "SELECT cd.*,
				c.id AS m_pr_co_company__id,
				c.name AS m_pr_co_company__name,
				c.date_fetched AS m_pr_co_company__date_fetched,
				c.pr_co_category_id AS m_pr_co_company__pr_co_category_id
				FROM ac_nr_pr_co_company c
				INNER JOIN ac_nr_pr_co_company_data cd
				ON cd.pr_co_company_id = c.id
				WHERE c.is_migrated_to_live_site = 0
				AND cd.is_website_valid = 1
				ORDER by c.id
				LIMIT 20";

		$scrape = new Model_Scrapped_Data_Migration();
		$scrape->data_source = Model_Scrapped_Data_Migration::SOURCE_PR_CO;	

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_PR_Co_Company_Data::from_db_all($query, array(
					'm_pr_co_company' => 'Model_PR_Co_Company'));

			
			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->pr_co_data = $results;			
				$result = $request->send('dev_listener/pr_co/pr_co_company_data/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$ids_updated = $result->pr_co_company_ids;
					if (is_array($ids_updated) && count($ids_updated))
					{
						$ids_list = sql_in_list($ids_updated);
						$sql_update = "UPDATE ac_nr_pr_co_company
									SET is_migrated_to_live_site = 1
									WHERE id IN ({$ids_list})";

						$this->db->query($sql_update);					
					}
				}
			}
			else
				break;

		}

		
		sleep(2);

		$sql = "SELECT c.*, cd.*, pb.*, prw.* 
				FROM nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_pb_pr_co_content prw
				ON prw.content_id = c.id
				INNER JOIN ac_nr_pr_co_company_data ncd
				ON prw.pr_co_company_id = ncd.pr_co_company_id
				INNER JOIN nr_pb_pr pb
				ON pb.content_id = prw.content_id
				WHERE prw.is_migrated_to_live_site = 0
				AND ncd.is_website_valid = 1
				AND c.type = ?
				ORDER BY c.id
				LIMIT 10";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Content::TYPE_PR));
			$results = Model_Content::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->prs_recs = $results;			
				$result = $request->send('dev_listener/pr_co/pr_co_prs/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql_update = "UPDATE nr_pb_pr_co_content
									SET  is_migrated_to_live_site = 1
									WHERE content_id IN ({$ids_list})";

						$this->db->query($sql_update); 
						
					}
					
				}
			}
			else
				break;

			sleep(1);
		}
	}

	public function migrate_domainiq_emails()
	{
		$sql = "SELECT cd.*
				FROM ac_nr_pr_co_company_data cd 
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.pr_co_company_id
				AND diq.source = ?
				WHERE diq.is_email_fetched = 1
				AND cd.is_diq_email_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_PR_CO));
			$results = Model_PR_Co_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->results = $results;
				$result = $request->send('dev_listener/pr_co/pr_co_company_data/save_domainiq_emails');
				if ($result->success)
				{
					$company_ids = $result->pr_co_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_pr_co_company_data
										SET  is_diq_email_migrated = 1
										WHERE pr_co_company_id IN ({$ids_list})";

						$this->db->query($sql_update); 
					}
					
				}
			}
			else
				break;

			sleep(1);
		}
	}

	public function migrate_contact_us_urls()
	{
		$sql = "SELECT cd.*
				FROM ac_nr_pr_co_company_data cd 
				INNER JOIN ac_nr_fetch_contact_us_url cu
				ON cu.source_company_id = cd.pr_co_company_id
				AND cu.source = ?
				WHERE cd.is_contact_url_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Fetch_Contact_Us_URL::SOURCE_PR_CO));
			$results = Model_PR_Co_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->results = $results;
				$result = $request->send('dev_listener/pr_co/pr_co_company_data/save_contact_page_url');
				if ($result->success)
				{
					$company_ids = $result->pr_co_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_pr_co_company_data
										SET  is_contact_url_migrated = 1
										WHERE pr_co_company_id IN ({$ids_list})";

						$this->db->query($sql_update); 
					}
				}
			}
			else
				break;

			sleep(1);
		}
	}

	public function migrate_videos()
	{
		$sql = "SELECT pb.content_id, pb.web_video_provider,
				pb.web_video_id 
				FROM nr_pb_pr_co_content prc
				INNER JOIN nr_pb_pr pb
				ON prc.content_id = pb.content_id
				WHERE prc.is_video_migrated = 0
				AND prc.is_migrated_to_live_site = 1
				ORDER BY prc.content_id
				LIMIT 500";

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_Content::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->prs_recs = $results;			
				$result = $request->send('dev_listener/pr_co/pr_co_prs/save_videos');
				if ($result->success)
				{
					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql_update = "UPDATE nr_pb_pr_co_content
										SET  is_video_migrated = 1
										WHERE content_id IN ({$ids_list})";

						$this->db->query($sql_update); 
					}
					
				}
			}
			else
				break;

			sleep(1);
		}
	}

	public function notify_migration_failure()
	{
		$date_4_days_ago = Date::days(-4, Date::$now->format(Date::FORMAT_MYSQL));
		$date_1_day_ago = Date::days(-1, Date::$now->format(Date::FORMAT_MYSQL));

		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration
				WHERE data_source = ?
				AND date_migrated_to_newswire > '$date_4_days_ago' ";

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration::SOURCE_PR_CO))->row()->count;
		
		if ($counter)
			return;

		// If no data has been migrated for the last
		// 4 days, we are going to notify the admin
		
		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration_failure_notify
				WHERE data_source = ?
				AND date_notified > '$date_1_day_ago' ";

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_PR_CO))->row()->count;
		
		if ($counter)
			return;

		// The admin has not been notified 
		// in last 1 day; notify.

		$alert = new Critical_Alert();
		$alert->set_subject('PR.Co Data Migration Failed');
		$text = 'Data migration has failed for PR.Co scrapper. ';
		$text .= ' No data has been transferred for the last 4 days from dev site to newswire. ';
		$text .= ' Please take corrective action';
		$alert->set_content($text);
		$alert->send();

		$notify = new Model_Scrapped_Data_Migration_Failure_Notify();
		$notify->data_source = Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_PR_CO;
		$notify->date_notified = Date::$now->format(Date::FORMAT_MYSQL);
		$notify->save();
	}

	public function update_title_summary_to_utf_8()
	{
		set_time_limit(86400);
		$pb_prefixes = Model_PB_PR_Co_Content::__prefixes('pb', 'pb_pr_co_content');
		$cd_prefixes = Model_Content_Data::__prefixes('cd', 'content_data');

		$sql = "SELECT c.id, c.title, 
				cd.content_id AS content_data__content_id,
				cd.summary AS content_data__summary,
				pb.content_id AS pb_pr_co_content__content_id,
				pb.is_title_summary_utf_updated AS pb_pr_co_content__is_title_summary_utf_updated
				FROM nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_pb_pr_co_content pb
				ON pb.content_id = cd.content_id
				WHERE pb.is_title_summary_utf_updated = 0
				ORDER BY cd.content_id DESC 
				LIMIT 500";

		while (1)
		{
			$results = Model_Content::from_sql_all($sql, array(), 
					array('pb_pr_co_content' => 'Model_PB_PR_Co_Content', 
						'content_data' => 'Model_Content_Data'));

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$result->title = HTML2Text::plain($result->title);
				$result->save();

				$result->content_data->summary = HTML2Text::plain($result->content_data->summary);
				$result->content_data->save();

				$result->pb_pr_co_content->is_title_summary_utf_updated = 1;
				$result->pb_pr_co_content->save();
			}
		}
	}
	
}

?>
