<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
class Migrate_Data_Controller extends CLI_Base {
	
	public function index()
	{
		$this->notify_migration_failure();

		// Checking if any permalink 
		// page is yet to be fetched
		$sql = "SELECT count(c.id) AS count
				FROM ac_nr_topseos_company c
				LEFT JOIN ac_nr_topseos_crawl_permalink cp
				ON cp.topseos_company_id = c.id
				WHERE cp.topseos_company_id IS NULL 
				AND NOT ISNULL(NULLIF(c.permalink, ''))";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;



		// Checking if any company website 
		// is yet to be crawled for data
		$sql = "SELECT COUNT(cd.topseos_company_id) AS count
				FROM ac_nr_topseos_company_data cd
				LEFT JOIN ac_nr_topseos_website_crawled w
				ON w.topseos_company_id = cd.topseos_company_id
				WHERE w.topseos_company_id IS NULL 
				AND NOT ISNULL(NULLIF(website, ''))";

		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

		// Checking if any social accounts
		// are yet to be verified
		$sql = "SELECT COUNT(topseos_company_id) AS count
				FROM ac_nr_topseos_company_data 
				WHERE (soc_fb IS NOT NULL AND soc_fb <> '' AND soc_fb_feed_status = ?)
				OR (soc_twitter IS NOT NULL AND soc_twitter <> '' AND soc_twitter_feed_status = ?)
				OR (soc_gplus IS NOT NULL AND soc_gplus <> '' AND soc_gplus_feed_status = ?)
				OR (soc_youtube IS NOT NULL AND soc_youtube <> '' AND soc_youtube_feed_status = ?)
				OR (soc_pinterest IS NOT NULL AND soc_pinterest <> '' AND soc_pinterest_feed_status = ?)";
			
		$nc = Model_TopSeos_Company_Data::SOCIAL_NOT_CHECKED;
		$counter = $this->db->query($sql, array($nc, $nc, $nc, $nc, $nc))->row()->count;
		if ($counter)
			return;

		// Checking if any website crawling 
		// for email fetching is left
		
		$sql = "SELECT COUNT(cd.topseos_company_id) AS count
				FROM ac_nr_topseos_company_data cd
				LEFT JOIN ac_nr_topseos_fetch_email e
				ON e.topseos_company_id = cd.topseos_company_id
				WHERE e.topseos_company_id IS NULL 
				AND ISNULL(NULLIF(cd.email, ''))
				AND NOT ISNULL(NULLIF(website, ''))";
		
		$counter = $this->db->query($sql)->row()->count;
		if ($counter)
			return;

				
		// Now that all the checks are applied
		// we are ready to send data to live db

		$sql = "SELECT cd.*,
				c.id AS m_topseos_company__id,
				c.name AS m_topseos_company__name,
				c.date_fetched AS m_topseos_company__date_fetched,
				c.topseos_category_id AS m_topseos_company__topseos_category_id
				FROM ac_nr_topseos_company c
				INNER JOIN ac_nr_topseos_company_data cd
				ON cd.topseos_company_id = c.id
				WHERE c.is_migrated_to_live_site = 0
				ORDER by c.id
				LIMIT 50";

		$scrape = new Model_Scrapped_Data_Migration();
		$scrape->data_source = Model_Scrapped_Data_Migration::SOURCE_TOPSEOS;	

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_TopSeos_Company_Data::from_db_all($query, array(
					'm_topseos_company' => 'Model_TopSeos_Company'));

			
			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->topseos_data = $results;			
				$result = $request->send('dev_listener/topseos/company_data/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$ids_updated = $result->topseos_company_ids;
					if (is_array($ids_updated) && count($ids_updated))
					{
						$ids_list = sql_in_list($ids_updated);
						$sql_update = "UPDATE ac_nr_topseos_company
									SET is_migrated_to_live_site = 1
									WHERE id IN ({$ids_list})";

						$this->db->query($sql_update);					
					}
				}
			}
			else
				break;

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

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration::SOURCE_TOPSEOS))->row()->count;
		
		if ($counter)
			return;

		// If no data has been migrated for the last
		// 4 days, we are going to notify the admin
		
		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration_failure_notify
				WHERE data_source = ?
				AND date_notified > '$date_1_day_ago' ";

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_TOPSEOS))->row()->count;
		
		if ($counter)
			return;

		// The admin has not been notified 
		// in last 1 day; notify.

		$alert = new Critical_Alert();
		$alert->set_subject('TopSEOs Data Migration Failed');
		$text = 'Data migration has failed for TopSEOs scrapper. ';
		$text .= ' No data has been transferred for the last 4 days from dev site to newswire. ';
		$text .= ' Please take corrective action';
		$alert->set_content($text);
		$alert->send();

		$notify = new Model_Scrapped_Data_Migration_Failure_Notify();
		$notify->data_source = Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_TOPSEOS;
		$notify->date_notified = Date::$now->format(Date::FORMAT_MYSQL);
		$notify->save();
	}
	
}

?>
