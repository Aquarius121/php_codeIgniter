<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Sync_Accesswire_Controller extends CLI_Base {
	
	const TITLE_PATTERN = '#[^a-z0-9]#is';
	const TITLE_WRAP_LIMIT = 75;

	public function index()
	{
		$accesswire = Accesswire_Scraper_Factory::create();
		$feed = $accesswire->get_feed();
		$acw_content = $this->get_unmapped_content();

		foreach ($acw_content as $content)
		{
			foreach ($feed as $feed_item)
			{
				$this->trace_info($content->title, $feed_item->title);

				$feed_title = $this->normalize_text($feed_item->title);
				if ($feed_title && $feed_title === $this->normalize_text($content->title)) 
				{
					$this->trace_success($feed_item->title);
					$acw = new Model_Content_Accesswire();
					$acw->content_id = $content->id;
					$acw->accesswire_id = $feed_item->id;
					$acw->date_created = Date::utc();
					$acw->save();
					break;
				}
			}
		}
	}

	protected function get_unmapped_content()
	{
		$provider = sql_in_list(array(Model_Content_Release_Plus::PROVIDER_ACCESSWIRE));

		$sql = "SELECT c.title, c.id FROM nr_content c
				INNER JOIN nr_content_release_plus crp
				ON c.id = crp.content_id
				LEFT JOIN nr_content_accesswire acw
				ON c.id = acw.content_id
				WHERE acw.accesswire_id IS NULL
				AND crp.is_confirmed = 1
				AND crp.provider IN ({$provider})
				ORDER BY c.id DESC
				LIMIT 100";

		return Model_Content::from_db_all($this->db->query($sql));
	}

	protected function normalize_text($text)
	{
		$text = strtolower(preg_replace(static::TITLE_PATTERN, null, $text));
		return substr($text, 0, static::TITLE_WRAP_LIMIT);
	}

}
