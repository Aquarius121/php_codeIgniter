<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feed_Reader_Blog extends Feed_Reader_Base {
	
	public function __construct(Model_Company_Profile $m_profile)
	{
		$this->content_type = Model_Content::TYPE_BLOG;
		$this->feed_url = $m_profile->soc_rss;
		parent::__construct($m_profile);
	}

	public function update()
	{
		$items = $this->fetch_items();

		if (!$items)
			return;

		$blog_content_ids = array();

		foreach ($items as $item)
		{
			if (!$item->link) continue;

			$hash = $this->link_hash($item->link);
			$m_import = Model_Blog_Feed_Import::find($hash);

			if ($m_import)
			{
				$blog_content_ids[] = $m_import->content_id;
				continue;
			}

			$m_import = $this->log_feed_import($item, $hash);

			$m_content = $this->save_content($item);
			$m_content_data = $this->save_content_data($item, $m_content->id);

			$m_pb_blog = new Model_PB_Blog();
			$m_pb_blog->content_id = $m_content->id;
			$m_pb_blog->source_url = $item->link;
			$m_pb_blog->save();

			$m_import->content_id = $m_content->id;
			$m_import->save();

			$blog_content_ids[] = $m_content->id;
		}

		$this->delete_older_blog_posts($blog_content_ids);
	}

	public function delete_older_blog_posts($blog_content_ids = array())
	{
		$criteria = array();
		$criteria[] = array('company_id', $this->m_profile->company_id);
		$criteria[] = array('type', Model_Content::TYPE_BLOG);
		$blog_contents = Model_Content::find_all($criteria);

		$ci =& get_instance();

		foreach ($blog_contents as $blog_content)
		{
			if (!in_array($blog_content->id, $blog_content_ids))
			{
				$blog_content->delete();
				$ci->db->delete('nr_blog_feed_import', 
					array('content_id' => $blog_content->id));
			}
		}
	}

	protected function log_feed_import($item, $hash)
	{
		$m_import = new Model_Blog_Feed_Import();
		$m_import->hash = $hash;
		$m_import->raw_data($item);
		$m_import->save();

		return $m_import;
	}

}
