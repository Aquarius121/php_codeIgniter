<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Migrate_PRWeb_Data_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		$this->notify_migration_failure();

		$sql = "UPDATE  ac_nr_prweb_company_data
				SET email_original = email
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);

		$sql = "UPDATE  ac_nr_prweb_company_data
				SET email = 'anthony@newswire.com'
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);


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
		
		// Checking if any duplicate companies
		// based on dup name are yet to be removed
		$sql = "SELECT name, COUNT(name) AS counter 
				FROM ac_nr_prweb_company
				GROUP BY name
				HAVING COUNT(name) > 1";
				
		$result = $this->db->query($sql);
		if ($result->num_rows())
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

		// Now that all the checks are applied
		// we are ready to send data to live db

		$sql = "SELECT cd.*,
				c.id AS m_prweb_company__id,
				c.name AS m_prweb_company__name,
				c.date_fetched AS m_prweb_company__date_fetched,
				c.prweb_category_id AS m_prweb_company__prweb_category_id,
				c.is_name_valid AS m_prweb_company__is_name_valid,
				c.date_last_pr_submitted AS m_prweb_company__date_last_pr_submitted
				FROM ac_nr_prweb_company c
				INNER JOIN ac_nr_prweb_company_data cd
				ON cd.prweb_company_id = c.id
				WHERE c.is_migrated_to_live_site = 0
				ORDER by c.id
				LIMIT 20";

		$scrape = new Model_Scrapped_Data_Migration();
		$scrape->data_source = Model_Scrapped_Data_Migration::SOURCE_PRWEB;

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_PRWeb_Company_Data::from_db_all($query, array(
					'm_prweb_company' => 'Model_PRWeb_Company'));

			
			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->prweb_data = $results;			
				$result = $request->send('dev_listener/prweb/prweb_company_data/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$ids_updated = $result->prweb_company_ids;
					if (is_array($ids_updated) && count($ids_updated))
					{
						$ids_list = sql_in_list($ids_updated);
						$sql_update = "UPDATE ac_nr_prweb_company
									SET is_migrated_to_live_site = 1,
									is_last_pr_date_migrated = 1
									WHERE id IN ({$ids_list})";

						$this->db->query($sql_update);					
					}
				}
			}
			else
				break;

			sleep(2);
		}

		
		sleep(2);

		$sql = "SELECT * FROM
				nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN `nr_pb_prweb_pr` prw
				ON prw.content_id = c.id
				WHERE prw.is_migrated_to_live_site = 0
				ORDER BY c.id
				LIMIT 10";

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_Content::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->prs_recs = $results;			
				$result = $request->send('dev_listener/prweb/prweb_prs/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql_update = "UPDATE nr_pb_prweb_pr
									SET  is_migrated_to_live_site = 1
									WHERE content_id IN ({$ids_list})";

						$this->db->query($sql_update); 
						
					}
					
				}
			}
			else
				break;

			sleep (2);
		}
	}

	public function migrate_missing_content()
	{
		$sql = "SELECT * FROM
				nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN `nr_pb_prweb_pr` prw
				ON prw.content_id = c.id
				WHERE prw.is_missing_content_migrated = 0
				AND prw.live_site_content_id IS NOT NULL
				AND cd.content IS NOT NULL
				ORDER BY c.id DESC
				LIMIT 50";

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_Content::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();	

				$request->data->prs_recs = $results;			

				$result = $request->send('dev_listener/prweb/prweb_prs/save_missing_content');

				if ($result->success)
				{
					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql_update = "UPDATE nr_pb_prweb_pr
									SET  is_missing_content_migrated = 1
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

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration::SOURCE_PRWEB))->row()->count;
		
		if ($counter)
			return;

		// If no data has been migrated for the last
		// 4 days, we are going to notify the admin
		
		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration_failure_notify
				WHERE data_source = ?
				AND date_notified > '$date_1_day_ago' ";

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_PRWEB))->row()->count;
		
		if ($counter)
			return;

		// The admin has not been notified 
		// in last 1 day; notify.

		$alert = new Critical_Alert();
		$alert->set_subject('PRWeb Data Migration Failed');
		$text = 'Data migration has failed for PRWeb scrapper. ';
		$text .= ' No data has been transferred for the last 4 days from dev site to newswire. ';
		$text .= ' Please take corrective action';
		$alert->set_content($text);
		$alert->send();

		$notify = new Model_Scrapped_Data_Migration_Failure_Notify();
		$notify->data_source = Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_PRWEB;
		$notify->date_notified = Date::$now->format(Date::FORMAT_MYSQL);
		$notify->save();
	}


	public function migrate_domainiq_emails()
	{
		$sql = "SELECT cd.*
				FROM ac_nr_prweb_company_data cd 
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.prweb_company_id
				AND diq.source = ?
				WHERE diq.is_email_fetched = 1
				AND cd.is_diq_email_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_PRWEB));
			$results = Model_PRWeb_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->results = $results;
				$result = $request->send('dev_listener/prweb/prweb_company_data/save_domainiq_emails');
				if ($result->success)
				{
					$company_ids = $result->prweb_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_prweb_company_data
										SET  is_diq_email_migrated = 1
										WHERE prweb_company_id IN ({$ids_list})";

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
				FROM ac_nr_prweb_company_data cd 
				INNER JOIN ac_nr_fetch_contact_us_url cu
				ON cu.source_company_id = cd.prweb_company_id
				AND cu.source = ?
				WHERE cd.is_contact_url_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Fetch_Contact_Us_URL::SOURCE_PRWEB));
			$results = Model_PRWeb_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->results = $results;
				$result = $request->send('dev_listener/prweb/prweb_company_data/save_contact_page_url');
				if ($result->success)
				{
					$company_ids = $result->prweb_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_prweb_company_data
										SET  is_contact_url_migrated = 1
										WHERE prweb_company_id IN ({$ids_list})";

						$this->db->query($sql_update); 
					}
				}
			}
			else
				break;

			sleep(1);
		}
	}

	public function migrate_missing_publish_date_recs()
	{
		$sql = "SELECT * FROM
				nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN `nr_pb_prweb_pr` prw
				ON prw.content_id = c.id
				WHERE is_date_fetched_from_prweb = 1
				AND is_date_sent_to_live_site = 0
				ORDER BY c.id
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_Content::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->prs_recs = $results;			
				$result = $request->send('prweb/dev_listener/prweb_prs/update_missing_dates');
				if ($result->success)
				{
					//echo "success";
					//$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					//$scrape->save();

					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql_update = "UPDATE nr_content
									SET  is_date_sent_to_live_site = 1
									WHERE id IN ({$ids_list})";

						$this->db->query($sql_update); 
						
					}
					
				}
			}
			else
				break;

			sleep (1);
		}
	}

	public function migrate_missing_phone_nums()
	{
		$sql = "SELECT * FROM
				ac_nr_prweb_company_data cd
				WHERE is_phone_updated_on_retry = 1
				AND phone IS NOT NULL
				AND is_phone_resent_to_live_site = 0
				ORDER BY cd.prweb_company_id
				LIMIT 200";

		while (1)
		{
			$results = Model_Content::from_sql_all($sql);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->prweb_c_data_recs = $results;			
				$result = $request->send('dev_listener/prweb/prweb_company_data/update_missing_phone_nums');
				if ($result->success)
				{
					$prweb_company_ids = $result->prweb_company_ids;
					if (is_array($prweb_company_ids) && count($prweb_company_ids))
					{
						$ids_list = sql_in_list($prweb_company_ids);
						$sql_update = "UPDATE ac_nr_prweb_company_data
									SET is_phone_resent_to_live_site = 1
									WHERE prweb_company_id IN ({$ids_list})";

						$this->db->query($sql_update);
					}
				}
			}
			else
				break;

			sleep (1);
		}
	}

	public function display_owned_prs()
	{
		set_time_limit(86400);

		$sql = "SELECT c.*, pc.newswire_beat_id
				FROM nr_content c
				INNER JOIN nr_pb_prweb_pr p
				ON p.content_id = c.id
				LEFT JOIN ac_nr_prweb_category pc  
				ON p.prweb_category_id = pc.id
				LEFT JOIN ac_nr_scraped_content_update cu
				ON cu.content_id = c.id
				WHERE type = 'prweb_pr'
				AND cu.content_id IS NULL
				LIMIT 500";
		
		while(1)
		{
			$results = Model_Content::from_sql_all($sql);
			foreach ($results as $m_content)
			{
				$m_content = Model_Content::from_sql($sql);
				$m_content->id;
				$m_content->title_to_slug();
				$m_content->is_excluded_from_news_center = 1;
				$m_content->type = 'pr';
				$m_content->save();
				$m_content->set_beats(array($m_content->newswire_beat_id));

				$c_update = new Model_Scraped_Content_Update();
				$c_update->content_id = $m_content->id;
				$c_update->is_slug_updated = 1;
				$c_update->is_beats_updated = 1;
				$c_update->is_excl_from_news_center_updated = 1;
				$c_update->is_type_updated = 1;
				$c_update->save();
			}
		}
	}

	// imports cover image for all PRs
	// that belong to auto built nrs
	// based on data scraped from prweb.com
	public function update_cover_image()
	{
		set_time_limit(86400);
		$prefixes = Model_PB_PRWEB_PR::__prefixes('pb', 'pb_prweb_pr');

		$sql = "SELECT c.*, {$prefixes} 
				FROM nr_content c
				INNER JOIN nr_pb_prweb_pr pb
				ON pb.content_id = c.id
				WHERE pb.is_cover_image_imported = 0
				AND pb.cover_image_url IS NOT NULL
				AND c.company_id > 0
				ORDER BY c.id
				LIMIT 1";
		
		while (1)
		{
			if (!$result = Model_Content::from_sql($sql, array(), array('pb_prweb_pr' => 'Model_PB_PRWEB_PR')))
				break;


			if (!empty($result->pb_prweb_pr->cover_image_url))
			{
				$cover_im_file = "cover";
				$cover_im_url = $result->pb_prweb_pr->cover_image_url;
				if (@copy($cover_im_url, $cover_im_file))
				{
					if (Image::is_valid_file($cover_im_file))
					{
						// import the logo image into the system
						$cover_im = LEGACY_Image::import("cover", $cover_im_file);
						 
						// assign to the new company and save
						$cover_im->company_id = $result->company_id;
						$cover_im->save();

						// set it to use the new logo image and save
						$result->cover_image_id = $cover_im->id;
						$result->save();
					}
				}
				$result->pb_prweb_pr->is_cover_image_imported = 1;
				$result->pb_prweb_pr->save();
			}
		}
		
		
	}


	public function make_link_anchors()
	{
		set_time_limit(86400);
		$prefixes = Model_PB_PRWeb_PR::__prefixes('pb', 'pb_prweb_pr');

		$sql = "SELECT cd.*, {$prefixes} 
				FROM nr_content_data cd
				INNER JOIN nr_pb_prweb_pr pb
				ON pb.content_id = cd.content_id
				WHERE cd.content IS NOT NULL
				AND pb.is_link_anchor_updated = 0
				ORDER BY cd.content_id DESC 
				LIMIT 500";

		while (1)
		{
			$results = Model_Content_Data::from_sql_all($sql, array(), 
					array('pb_prweb_pr' => 'Model_PB_PRWeb_PR'));

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$content = $this->linkify($result->content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));
				$result->content = $content;	
				$result->save();

				$result->pb_prweb_pr->is_link_anchor_updated = 1;
				$result->pb_prweb_pr->save();
			}
		}
	}	

	public function migrate_refetched_pr_body()
	{
		$sql = "SELECT cd.content_id, cd.content, cd.supporting_quote
				FROM nr_pb_prweb_pr p
				INNER JOIN nr_content_data cd
				ON p.content_id = cd.content_id
				WHERE p.is_content_refetched = 1
				AND p.is_pr_body_migrated = 0
				ORDER BY p.content_id DESC
				LIMIT 200";

		$cnt = 1;
		while (1)
		{
			$results = Model_Content_Data::from_sql_all($sql);
			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->prweb_pr_recs = $results;
				$result = $request->send('dev_listener/prweb/prweb_prs/update_pr_body');
				if ($result->success)
				{
					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql_update = "UPDATE nr_pb_prweb_pr
									SET  is_pr_body_migrated = 1
									WHERE content_id IN ({$ids_list})";

						$this->db->query($sql_update);
					}
				}
			}
			else
				break;

			if (++$cnt%10 == 0)
				sleep(1);
		}
	}
	
}

?>
