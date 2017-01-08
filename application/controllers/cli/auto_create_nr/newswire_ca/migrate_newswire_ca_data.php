<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Migrate_Newswire_CA_Data_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		$this->notify_migration_failure();

		$sql = "UPDATE  ac_nr_newswire_ca_company_data
				SET email_original = email
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);

		$sql = "UPDATE  ac_nr_newswire_ca_company_data
				SET email = 'anthony@newswire.com'
				WHERE email LIKE '%aol.com' 
				OR email LIKE '%netscape.com'
				OR email LIKE '%netscape.net'";

		$this->db->query($sql);


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
				AND o.newswire_ca_company_id IS NOT NULL
				AND ISNULL(NULLIF(cd.email, ''))
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

		
		
		// Now that all the checks are applied
		// we are ready to send data to live db
		$sql = "SELECT cd.*,
				c.id AS m_newswire_ca_company__id,
				c.name AS m_newswire_ca_company__name,
				c.date_fetched AS m_newswire_ca_company__date_fetched,
				c.newswire_ca_category_id AS m_newswire_ca_company__newswire_ca_category_id
				FROM ac_nr_newswire_ca_company c
				INNER JOIN ac_nr_newswire_ca_company_data cd
				ON cd.newswire_ca_company_id = c.id
				WHERE c.is_migrated_to_live_site = 0
				AND cd.is_website_valid = 1
				ORDER by c.id
				LIMIT 20";

		$scrape = new Model_Scrapped_Data_Migration();
		$scrape->data_source = Model_Scrapped_Data_Migration::SOURCE_NEWSWIRE_CA;

		while (1)
		{
			$query = $this->db->query($sql);
			$results = Model_Newswire_CA_Company_Data::from_db_all($query, array(
					'm_newswire_ca_company' => 'Model_Newswire_CA_Company'));

			
			if (count($results))
			{
				$request = new Newswire_Iella_Request();		
				$request->data->newswire_ca_data = $results;			
				$result = $request->send('dev_listener/newswire_ca/newswire_ca_company_data/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$ids_updated = $result->newswire_ca_company_ids;
					if (is_array($ids_updated) && count($ids_updated))
					{
						$ids_list = sql_in_list($ids_updated);
						$sql_update = "UPDATE ac_nr_newswire_ca_company
									SET is_migrated_to_live_site = 1
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

		$sql = "SELECT c.*, cd.*, prw.* FROM
				nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_pb_newswire_ca_pr prw
				ON prw.content_id = c.id
				INNER JOIN ac_nr_newswire_ca_company_data ncd
				ON prw.newswire_ca_company_id = ncd.newswire_ca_company_id
				WHERE prw.is_migrated_to_live_site = 0
				AND ncd.is_website_valid = 1
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
				$result = $request->send('dev_listener/newswire_ca/newswire_ca_prs/save');
				if ($result->success)
				{
					//echo "success";
					$scrape->date_migrated_to_newswire = Date::$now->format(Date::FORMAT_MYSQL);
					$scrape->save();

					$content_ids = $result->content_ids;
					if (is_array($content_ids) && count($content_ids))
					{
						$ids_list = sql_in_list($content_ids);
						$sql_update = "UPDATE nr_pb_newswire_ca_pr
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

	public function notify_migration_failure()
	{
		$date_4_days_ago = Date::days(-4, Date::$now->format(Date::FORMAT_MYSQL));
		$date_1_day_ago = Date::days(-1, Date::$now->format(Date::FORMAT_MYSQL));

		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration
				WHERE data_source = ?
				AND date_migrated_to_newswire > '$date_4_days_ago' ";

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration::SOURCE_NEWSWIRE_CA))->row()->count;
		
		if ($counter)
			return;

		// If no data has been migrated for the last
		// 4 days, we are going to notify the admin
		
		$sql = "SELECT count(id) AS count 
				FROM ac_nr_scrapped_data_migration_failure_notify
				WHERE data_source = ?
				AND date_notified > '$date_1_day_ago' ";

		$counter = $this->db->query($sql, array(Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_NEWSWIRE_CA))->row()->count;
		
		if ($counter)
			return;

		// The admin has not been notified 
		// in last 1 day; notify.

		$alert = new Critical_Alert();
		$alert->set_subject('Newswire_CA Data Migration Failed');
		$text = 'Data migration has failed for Newswire_CA scrapper. ';
		$text .= ' No data has been transferred for the last 4 days from dev site to newswire. ';
		$text .= ' Please take corrective action';
		$alert->set_content($text);
		$alert->send();

		$notify = new Model_Scrapped_Data_Migration_Failure_Notify();
		$notify->data_source = Model_Scrapped_Data_Migration_Failure_Notify::SOURCE_NEWSWIRE_CA;
		$notify->date_notified = Date::$now->format(Date::FORMAT_MYSQL);
		$notify->save();
	}

	public function migrate_domainiq_emails()
	{
		$sql = "SELECT cd.*
				FROM ac_nr_newswire_ca_company_data cd 
				INNER JOIN ac_nr_whois_check_domainiq diq
				ON diq.source_company_id = cd.newswire_ca_company_id
				AND diq.source = ?
				WHERE diq.is_email_fetched = 1
				AND cd.is_diq_email_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Whois_Check_Domainiq::SOURCE_NEWSWIRE_CA));
			$results = Model_Newswire_CA_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->results = $results;
				$result = $request->send('dev_listener/newswire_ca/newswire_ca_company_data/save_domainiq_emails');
				if ($result->success)
				{
					$company_ids = $result->newswire_ca_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_newswire_ca_company_data
										SET  is_diq_email_migrated = 1
										WHERE newswire_ca_company_id IN ({$ids_list})";

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
				FROM ac_nr_newswire_ca_company_data cd 
				INNER JOIN ac_nr_fetch_contact_us_url cu
				ON cu.source_company_id = cd.newswire_ca_company_id
				AND cu.source = ?
				WHERE cd.is_contact_url_migrated = 0
				LIMIT 200";

		while (1)
		{
			$query = $this->db->query($sql, array(Model_Fetch_Contact_Us_URL::SOURCE_NEWSWIRE_CA));
			$results = Model_Newswire_CA_Company_Data::from_db_all($query);

			if (count($results))
			{
				$request = new Newswire_Iella_Request();
				$request->data->results = $results;
				$result = $request->send('dev_listener/newswire_ca/newswire_ca_company_data/save_contact_page_url');
				if ($result->success)
				{
					$company_ids = $result->newswire_ca_company_ids;
					if (is_array($company_ids) && count($company_ids))
					{
						$ids_list = sql_in_list($company_ids);
						$sql_update = "UPDATE ac_nr_newswire_ca_company_data
										SET  is_contact_url_migrated = 1
										WHERE newswire_ca_company_id IN ({$ids_list})";

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

		$sql = "SELECT c.*
				FROM nr_content c
				WHERE type = 'newswire_ca_pr'
				AND ISNULL(NULLIF(c.slug, ''))
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

	public function update_beats()
	{
		set_time_limit(86400);

		$sql = "SELECT c.*, pc.newswire_beat_id
				FROM nr_content c
				INNER JOIN nr_pb_newswire_ca_pr p
				ON p.content_id = c.id
				INNER JOIN ac_nr_newswire_ca_category pc  
				ON p.newswire_ca_category_id = pc.id
				LEFT JOIN nr_beat_x_content bc
				ON bc.content_id = c.id
				WHERE type = 'newswire_ca_pr'
				AND bc.content_id IS NULL
				AND pc.newswire_beat_id IS NOT NULL
				LIMIT 500";
		
		while(1)
		{
			$results = Model_Content::from_sql_all($sql);
			if (!count($results))
				break;
			foreach ($results as $m_content)
			{
				$m_content->set_beats(array($m_content->newswire_beat_id));
			}
		}
	}

	// imports cover image for all PRs
	// that belong to auto built nrs
	// based on data scraped from newswire.ca
	public function update_cover_image()
	{
		set_time_limit(86400);
		$prefixes = Model_PB_Newswire_CA_PR::__prefixes('pb', 'pb_newswire_ca_pr');

		$sql = "SELECT c.*, {$prefixes} 
				FROM nr_content c
				INNER JOIN nr_pb_newswire_ca_pr pb
				ON pb.content_id = c.id
				WHERE pb.is_cover_image_imported = 0
				AND pb.cover_image_url IS NOT NULL
				AND c.company_id > 0
				ORDER BY c.id
				LIMIT 1";
		
		while (1)
		{
			if (!$result = Model_Content::from_sql($sql, array(), 
					array('pb_newswire_ca_pr' => 'Model_PB_Newswire_CA_PR')))
				break;


			if (!empty($result->pb_newswire_ca_pr->cover_image_url))
			{
				$cover_im_file = "cover";
				$cover_im_url = $result->pb_newswire_ca_pr->cover_image_url;
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
				$result->pb_newswire_ca_pr->is_cover_image_imported = 1;
				$result->pb_newswire_ca_pr->save();
			}
		}
	}

	public function make_link_anchors()
	{
		set_time_limit(86400);
		$prefixes = Model_PB_Newswire_CA_PR::__prefixes('pb', 'pb_newswire_ca_pr');

		$sql = "SELECT cd.*, {$prefixes} 
				FROM nr_content_data cd
				INNER JOIN nr_pb_newswire_ca_pr pb
				ON pb.content_id = cd.content_id
				WHERE cd.content IS NOT NULL
				AND pb.is_link_anchor_updated = 0
				ORDER BY cd.content_id DESC 
				LIMIT 500";

		while (1)
		{
			$results = Model_Content_Data::from_sql_all($sql, array(), 
					array('pb_newswire_ca_pr' => 'Model_PB_Newswire_CA_PR'));

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$content = $this->linkify($result->content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));
				$result->content = $content;
				$result->save();

				$result->pb_newswire_ca_pr->is_link_anchor_updated = 1;
				$result->pb_newswire_ca_pr->save();
			}
		}
	}


	public function update_cnw_wording()
	{
		set_time_limit(86400);
		lib_autoload('simple_html_dom');

		$prefixes = Model_PB_Newswire_CA_PR::__prefixes('pb', 'pb_newswire_ca_pr');

		$sql = "SELECT cd.content_id, cd.content, 
				{$prefixes} 
				FROM nr_content_data cd
				INNER JOIN nr_pb_newswire_ca_pr pb
				ON pb.content_id = cd.content_id
				WHERE cd.content IS NOT NULL
				AND pb.is_cnw_wording_updated = 0
				ORDER BY cd.content_id DESC 
				LIMIT 500";

		while (1)
		{
			$results = Model_Content_Data::from_sql_all($sql, array(), 
					array('pb_newswire_ca_pr' => 'Model_PB_Newswire_CA_PR'));

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$html = str_get_html($result->content);
				
				foreach($html->find('p') as $p)
				{
					$p_text = $p->innertext;
					$pattern = '#[\s\S]+\/CNW([A-Za-z0-9\. ]+)?\/ ?\-?(\&\#160;)?#';
					$p_text = preg_replace($pattern, "", $p_text);
					$p->innertext = $p_text;
				}

				$result->content = $html->innertext;
				$result->save();

				$result->pb_newswire_ca_pr->is_cnw_wording_updated = 1;
				$result->pb_newswire_ca_pr->save();
			}
		}
	}


	public function update_title_summary_to_utf_8()
	{
		set_time_limit(86400);
		$pb_prefixes = Model_PB_Newswire_CA_PR::__prefixes('pb', 'pb_newswire_ca_pr');
		$cd_prefixes = Model_Content_Data::__prefixes('cd', 'content_data');

		$sql = "SELECT c.id, c.title, 
				cd.content_id AS content_data__content_id,
				cd.summary AS content_data__summary,
				pb.content_id AS pb_newswire_ca_pr__content_id,
				pb.is_title_summary_utf_updated AS pb_newswire_ca_pr__is_title_summary_utf_updated
				FROM nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_pb_newswire_ca_pr pb
				ON pb.content_id = cd.content_id
				WHERE pb.is_title_summary_utf_updated = 0
				ORDER BY cd.content_id DESC 
				LIMIT 500";

		while (1)
		{
			$results = Model_Content::from_sql_all($sql, array(), 
					array('pb_newswire_ca_pr' => 'Model_PB_Newswire_CA_PR', 
						'content_data' => 'Model_Content_Data'));

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$result->title = HTML2Text::plain($result->title);
				$result->save();

				$result->content_data->summary = HTML2Text::plain($result->content_data->summary);
				$result->content_data->save();

				$result->pb_newswire_ca_pr->is_title_summary_utf_updated = 1;
				$result->pb_newswire_ca_pr->save();
			}
		}
	}



	public function update_title_to_utf_8()
	{
		set_time_limit(86400);

		$sql = "SELECT c.*
				FROM nr_content c
				INNER JOIN nr_content_data cd
				ON cd.content_id = c.id
				WHERE cd.content_id = 4777185 
				ORDER BY cd.content_id DESC 
				LIMIT 500";

		while (1)
		{
			$results = Model_Content::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $result)
			{
				$result->title = HTML2Text::plain($result->title);
				$result->save();
				break;
			}
			break;
		}
	}
	
}

?>
