<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Migrate_Data_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		$this->notify_migration_failure(Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_OWLER);
		
		$sql = "UPDATE  ac_nr_owler_company_data
				SET email_original = email
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);

		$sql = "UPDATE  ac_nr_owler_company_data
				SET email = 'anthony@newswire.com'
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);

		
		$sql = "SELECT cd.*,
				c.id AS m_owler_company__id,
				c.name AS m_owler_company__name,
				c.date_fetched AS m_owler_company__date_fetched,
				c.owler_category_id AS m_owler_company__owler_category_id
				FROM ac_nr_owler_company c
				INNER JOIN ac_nr_owler_company_data cd
				ON cd.owler_company_id = c.id

				LEFT JOIN ac_nr_owler_website_crawled w
				ON w.owler_company_id = cd.owler_company_id

				LEFT JOIN ac_nr_owler_fetch_email e
				ON e.owler_company_id = cd.owler_company_id	

				LEFT JOIN ac_nr_owler_whois_email_fetch we
				ON cd.owler_company_id = we.owler_company_id			
				

				WHERE c.is_migrated_to_live_site = 0

				AND w.owler_company_id IS NOT NULL 
				AND NOT ISNULL(NULLIF(cd.website, ''))

				AND e.owler_company_id IS NOT NULL 

				AND (NOT ISNULL(NULLIF(cd.email, '')) OR we.owler_company_id IS NOT NULL)

				AND (ISNULL(NULLIF(cd.soc_fb, '')) OR cd.soc_fb_feed_status <> ? )
				AND (ISNULL(NULLIF(cd.soc_twitter, '')) OR cd.soc_twitter_feed_status <> ?)
				AND (ISNULL(NULLIF(cd.soc_gplus, '')) OR cd.soc_gplus_feed_status  <> ?)
				AND (ISNULL(NULLIF(cd.soc_youtube, '')) OR cd.soc_youtube_feed_status <> ?)
				AND (ISNULL(NULLIF(cd.soc_pinterest, '')) OR cd.soc_pinterest_feed_status <> ?)
				ORDER by c.id

				LIMIT 100";

		$nc = Model_Owler_Company_Data::SOCIAL_NOT_CHECKED;

		$scrape = new Model_Scrapped_Data_Migration();
		$scrape->data_source = Model_Scrapped_Data_Migration::SOURCE_OWLER;

		while (1)
		{
			set_time_limit(60);
			
			$query = $this->db->query($sql, array($nc, $nc, $nc, $nc, $nc));
			$results = Model_Owler_Company_Data::from_db_all($query, array(
					'm_owler_company' => 'Model_Owler_Company'));

			
			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->owler_data = $results;			
				$result = $request->send('dev_listener/owler/owler_company_data/save');
				if ($result->success)
				{
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$ids_updated = $result->owler_company_ids;
					if (is_array($ids_updated) && count($ids_updated))
					{
						$ids_list = sql_in_list($ids_updated);
						$sql1 = "UPDATE ac_nr_owler_company
								SET is_migrated_to_live_site = 1
								WHERE id IN ({$ids_list})";

						$this->db->query($sql1);
					}
				}
			}
			else
				break;

			sleep(2);
		}

		$sql = "SELECT * FROM
				nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_pb_owler_news prw
				ON prw.content_id = c.id
				WHERE prw.is_migrated_to_live_site = 0
				AND NOT ISNULL(NULLIF(prw.actual_news_url,''))
				ORDER BY c.id
				LIMIT 100";

		while(1)
		{
			set_time_limit(60);

			$query = $this->db->query($sql);
			$results = Model_Content::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->prs_recs = $results;	
				$result = $request->send('dev_listener/owler/owler_news/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql1 = "UPDATE nr_pb_owler_news
								SET  is_migrated_to_live_site = 1
								WHERE content_id IN ({$ids_list})";

						$this->db->query($sql1); 
						
					}
					
				}
			}
			else
				break;

			sleep(2);
		}



		// Now sending the owler categories

		$sql = "SELECT * FROM
				ac_nr_owler_category c
				WHERE is_migrated_to_live_site = 0
				ORDER BY c.id";

		$query = $this->db->query($sql);
		$results = Model_Owler_Category::from_db_all($query);

		if (count($results))
		{
			$request = new Newswire_Iella_Request();
			$request->data->owler_cats = $results;	
			$result = $request->send('dev_listener/owler/owler_category/save');
			if ($result->success)
			{
				$ids_updated = $result->owler_cat_ids;
				if (is_array($ids_updated) && count($ids_updated))
				{
					$ids_list = sql_in_list($ids_updated);
					$sql1 = "UPDATE ac_nr_owler_category
							SET is_migrated_to_live_site = 1
							WHERE id IN ({$ids_list})";

					$this->db->query($sql1);
				}
			}
		}		
	}

	public function migrate_domainiq_emails()
	{
		$sql = "SELECT cd.*
				FROM ac_nr_owler_company_data cd 
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.owler_company_id
				AND diq.source = ?
				WHERE diq.is_email_fetched = 1
				AND cd.is_diq_email_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_OWLER));
			$results = Model_Owler_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->results = $results;
				$result = $request->send('dev_listener/owler/owler_company_data/save_domainiq_emails');
				if ($result->success)
				{
					$company_ids = $result->owler_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_owler_company_data
										SET  is_diq_email_migrated = 1
										WHERE owler_company_id IN ({$ids_list})";

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
				FROM ac_nr_owler_company_data cd 
				INNER JOIN ac_nr_fetch_contact_us_url cu
				ON cu.source_company_id = cd.owler_company_id
				AND cu.source = ?
				WHERE cd.is_contact_url_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Fetch_Contact_Us_URL::SOURCE_OWLER));
			$results = Model_Owler_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->results = $results;
				$result = $request->send('dev_listener/owler/owler_company_data/save_contact_page_url');
				if ($result->success)
				{
					$company_ids = $result->owler_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_owler_company_data
										SET  is_contact_url_migrated = 1
										WHERE owler_company_id IN ({$ids_list})";

						$this->db->query($sql_update); 
					}
				}
			}
			else
				break;

			sleep(1);
		}
	}

	public function generate_slug()
	{
		set_time_limit(86400);

		$sql = "SELECT *
				FROM nr_content c
				WHERE type = 'owler_news'
				AND ISNULL(NULLIF(slug, ''))
				ORDER BY id
				LIMIT 700";
		
		while(1)
		{
			$results = Model_Content::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $m_content)
			{
				$m_content->title_to_slug();
				$m_content->save();
			}
		}
	}
}

?>
