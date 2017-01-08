<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fin_Content_Controller extends CLI_Base {
	
	protected $trace_enabled = false;
	protected $trace_time = true;

	protected $channels = array(
		// Fin_Content_Feed_Scraper_Factory::CHANNEL_PRNEWSWIRE,
		Fin_Content_Feed_Scraper_Factory::CHANNEL_NEWSWIRE,
	);

	public function index()
	{
		$this->error('usage: .. fin_content process_feed <channel>');
		$this->error('usage: .. fin_content process_feeds');
		$this->error('usage: .. fin_content check_sites');
	}

	public function process_feeds()
	{
		$method = Fin_Content_Feed_Scraper_Factory::METHOD_SCRAPE_FEED;
		foreach ($this->channels as $channel)
			$this->process_feed($channel, $method, 1000);
	}

	public function process_feeds_fallback()
	{
		$method = Fin_Content_Feed_Scraper_Factory::METHOD_SCRAPE_WEBSITE;
		foreach ($this->channels as $channel)
			$this->process_feed($channel, $method, 50000);
	}

	public function process_feed($channel, $method, $limit)
	{
		set_time_limit(300);

		$limit = (int) $limit;
		$type = escape_and_quote(Model_Content::TYPE_PR);
		$sql = "SELECT c.id, c.title FROM nr_content c 
			  LEFT JOIN nr_fin_content_scraper fcs
			  ON c.id = fcs.content_id
			WHERE 1
			  AND ( c.date_publish > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 DAY)
			    AND c.date_updated > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 7 DAY))
			  -- PRN might go out before the actual PR is scheduled to
			  AND c.date_publish < DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 DAY)
			  AND c.is_premium = 1
			  AND c.is_draft = 0
			  AND c.type = {$type}
			  AND fcs.content_id IS NULL
			ORDER BY c.date_publish DESC
			LIMIT {$limit}";

		$dbr = $this->db->query($sql);
		$results = Model_Content::from_db_all($dbr);
		if (!count($results)) return;
		$title_idx = array();

		foreach ($results as $result)
		{
			$title = $this->normalize_text($result->title);
			$title_idx[$title] = $result;
		}

		$scraper = Fin_Content_Feed_Scraper_Factory::create($channel, $method);
		$results = $scraper->fetch();

		foreach ($results as $result)
		{
			$title = $this->normalize_text($result->title);

			if (isset($title_idx[$title]))
			{
				$content = $title_idx[$title];
				$this->trace_success($channel, 
					$content->id, 
					$content->title);

				// ensure another process hasn't already added it
				if (Model_Fin_Content_Scraper::find($content->id))
					continue;

				$fcs = new Model_Fin_Content_Scraper();
				$fcs->date_next_update = Date::minutes(10);
				$fcs->content_id = $content->id;
				$fcs->guid = $result->guid;
				$fcs->channel = $channel;
				$fcs->update_count = 0;
				$fcs->save();
				continue;
			}
		}
	}

	public function check_sites($id = null)
	{
		set_time_limit(3600);
		
		// null string will always
		// evaluate to false in sql
		$id = $id ? (int) $id : 'null';

		$sources = $this->get_sources();
		$update_limit = count($sources);
		
		$fcs = Model_Fin_Content_Scraper::__prefixes('fcs', false);
		$sql = "SELECT c.id, c.title, c.slug, {$fcs}
			FROM nr_content c 
			  INNER JOIN nr_fin_content_scraper fcs
			  ON c.id = fcs.content_id
			WHERE 1
			  AND ((  fcs.date_next_update < UTC_TIMESTAMP()
			      AND fcs.guid IS NOT NULL
			      AND {$id} IS NULL
			    ) OR id = {$id})
			ORDER BY c.date_publish DESC
			LIMIT 1000";

		$dbr = $this->db->query($sql);
		$results = Model_Content::from_db_all($dbr, array(
			'fcs' => 'Model_Fin_Content_Scraper',
		));

		$this->trace(sprintf('found %d checks', 
			count($results)));

		foreach ($results as $result)
		{
			// more natural lists
			shuffle($sources);

			// list of distribution sites (hashes) we've already found
			$hashes = $this->get_distribution_hashes($result->id);

			// we reload as the process is slow
			// and update count may have changed
			$fcs = $result->fcs;
			$fcs->reload();

			if (++$fcs->update_count >= $update_limit)
			     $fcs->date_next_update = null;
			else $fcs->date_next_update = Date::minutes(2);
			$fcs->save();

			// remove the numeric ID from end of slug
			// as slug can be anything, it isn't used
			$slug = preg_replace('#-\d+$#', null, $result->slug);

			// count the number tested
			// so we don't waste time
			// if we cannot find anything
			$sources_tested = 0;
			
			foreach ($sources as $source)
			{
				$this->trace('check', 
					$source->hash,
					$result->id);

				$url = $source->url_read;
				$hash = $source->hash;
				if (in_array($hash, $hashes))
					continue;

				// inject guid and slug into url 
				$url = String_Util::inject($url, array(
					'rcid' => $fcs->guid,
					'slug' => $slug,
				));

				$this->trace($url);
				$verified = false;
				$request = new HTTP_Request($url);
				$request->disable_redirects();
				$response = $request->get();

				if (!$response || $response->header('location'))
				{
					$fcds = Model_Fin_Content_Dead_Source::find_or_create($hash);
					$fcds->date_clear = Date::hours(1);
					$fcds->save();
					break;
				}

				// load the html into parser, break if fails
				$parser = HTML_Util::parser($response->data);
				if (!$parser) break;

				if (!$verified)
				{
					// verify by matching a canonical url
					// based on the FC default url

					$canonical = 'stocks/news/read/{{rcid}}';
					$canonical = String_Util::inject($canonical, array(
						'rcid' => $fcs->guid,
						'slug' => $slug,
					));

					foreach ($parser->find('link') as $link)
					{
						if ($link->attr('rel') !== 'canonical') continue;
						if (!str_contains($link->attr('href'), $canonical)) continue;
						$verified = true;
						break;
					}
				}

				if (!$verified)
				{
					// verify by checking for the PR title
					// within the body text of the page

					$text = $parser->text();
					$text = $this->normalize_text($text);
					$title = $this->normalize_text($result->title);
					if (str_contains($text, $title))
						$verified = true;
				}

				if ($verified)
				{
					$this->trace_success($result->id, $hash);
					
					Model_Distribution_Site::enable_cache();
					$mds = Model_Distribution_Site::find_hash($hash);
					$mdi = new Model_Distribution_Index();
					$mdi->distribution_site_id = $mds->id;
					$mdi->content_id = $result->id;
					$mdi->date_discovered = Date::$now;
					$mdi->url = $url;
					$mdi->save();
					break;
				}

				// do not test more than 10 sources
				// in a single attempt because
				// this wastes time when a PR
				// title has been changed and 
				// we end up testing every site for it
				if (++$sources_tested >= 10)
					break;
			}
		}
	}

	protected function normalize_text($text)
	{
		// remove all non alphanumeric characters
		return strtolower(preg_replace('#[^a-z0-9]#is', null, $text));
	}

	protected function get_distribution_hashes($id)
	{
		$sql = "SELECT ds.hash FROM nr_distribution_site ds
			  INNER JOIN nr_distribution_index di 
			  ON di.distribution_site_id = ds.id
			WHERE di.content_id = ?";

		$dbr = $this->db->query($sql, array($id));
		return Model::values_from_db($dbr, 'hash');
	}

	protected function get_sources()
	{
		$this->config->load('fin_content', true);
		$sources = $this->config->item('sources', 'fin_content');

		$dead_hashes = array();
		$dead = Model_Fin_Content_Dead_Source::find_all_dead();
		foreach ($dead as $d) $dead_hashes[$d->hash] = true;

		foreach ($sources as $k => $v)
			if (isset($dead_hashes[$v->hash]))
				unset($sources[$k]);

		return array_values($sources);
	}

}
