<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feed_Reader_News extends Feed_Reader_Base {
	
	public function __construct(Model_Company_Profile $m_profile)
	{
		$this->content_type = Model_Content::TYPE_NEWS;
		$this->feed_url = $m_profile->rss_news_url;
		parent::__construct($m_profile);
	}

	public function update()
	{
		$items = $this->fetch_items();

		if (!$items)
			return;

		foreach ($items as $item)
		{
			if (!$item->link) continue;
			if ($this->is_internal_url($item->link))
				continue;

			$hash = $this->link_hash($item->link);
			$m_import = Model_News_Feed_Import::find($hash);
			if ($m_import) break;

			$m_import = $this->log_feed_import($item, $hash);

			$m_content = $this->save_content($item);
			$m_content_data = $this->save_content_data($item, $m_content->id);

			$m_pb_news = new Model_PB_News();
			$m_pb_news->content_id = $m_content->id;
			$m_pb_news->source_url = $item->link;
			$m_pb_news->is_external = 1;
			$m_pb_news->save();

			$m_import->content_id = $m_content->id;
			$m_import->save();
		}
	}

	protected function log_feed_import($item, $hash)
	{
		$m_import = new Model_News_Feed_Import();
		$m_import->hash = $hash;
		$m_import->raw_data($item);
		$m_import->save();

		return $m_import;
	}
	
}