<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class World_Now_Controller extends CLI_Base {
	
	protected $trace_enabled = false;
	protected $trace_time = true;
	protected $cached_requests = array();

	public function index($id = null)
	{
		set_time_limit(3600);

		// null string will always
		// evaluate to false in sql
		$id = $id ? (int) $id : 'null';

		$sources = (require 'raw/distribution/world_now.php');
		$hashes = sql_in_list(array_map(function($n) {
			return $n->hash;
		}, $sources));

		$providers = sql_in_list(array(
			Model_Content_Release_Plus::PROVIDER_WORLDNOW
		));

		$sql = "SELECT c.id, c.title FROM nr_content c 
			INNER JOIN nr_content_release_plus crp
			ON crp.content_id = c.id
			AND crp.provider IN ({$providers})
			AND crp.is_confirmed = 1
			WHERE c.is_published = 1 
			AND c.type = ? 
			AND c.is_premium = 1
			AND (( c.date_publish < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 3 HOUR)
			  AND (c.date_updated > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 12 HOUR)
			    OR c.date_publish > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 12 HOUR))
			  AND {$id} IS NULL
			) OR c.id = {$id})
			ORDER BY c.date_publish DESC
			LIMIT 500";

		$dbr = $this->db->query($sql, array(Model_Content::TYPE_PR));
		$results = Model_Content::from_db_all($dbr);

		foreach ($results as $result)
		{
			$this->trace_info($result->title);
			$this->flush();

			$sql = "SELECT ds.hash FROM nr_distribution_index di
				INNER JOIN nr_distribution_site ds 
				ON ds.id = di.distribution_site_id
				AND ds.hash IN ({$hashes})
				AND di.content_id = ?";

			$dbr = $this->db->query($sql, array($result->id));
			$done_hashes = Model::values_from_db($dbr, 'hash');
			$title = $this->normalize_text($result->title);
			$this->flush();

			foreach ($sources as $source)
			{
				$this->trace($source->hash);
				if (in_array($source->hash, $done_hashes))
					continue;

				$response = $this->do_request($source->url_list);
				if (!$response) continue;

				$parser = htmlqp($response->data);
				$as = $parser->find('h4.headline:not(.abridged) > a');

				foreach ($as as $a)
				{
					$a_title = $this->normalize_text($a->text());

					if ($a_title == $title)
					{
						$url = $a->attr('href');
						if (preg_match('#(story/\d+/.+)$#i', $url, $match));
							$url = $match[1];
						$url = concat($source->url_site, $url);
						$this->trace_success($url);

						Model_Distribution_Site::enable_cache();
						$mds = Model_Distribution_Site::find_hash($source->hash);
						$mdi = new Model_Distribution_Index();
						$mdi->distribution_site_id = $mds->id;
						$mdi->content_id = $result->id;
						$mdi->date_discovered = Date::$now;
						$mdi->url = $url;
						$mdi->save();
						break;
					}
				}
			}
		}
	}

	protected function normalize_text($text)
	{
		// remove all non alphanumeric characters
		return strtolower(preg_replace('#[^a-z0-9]#is', null, $text));
	}

	protected function do_request($url)
	{
		if (isset($this->cached_requests[$url]))
			return $this->cached_requests[$url];
		$request = new HTTP_Request($url);
		$request->disable_redirects();
		$response = $request->get();
		$this->cached_requests[$url] = $response;
		return $response;
	}

}