<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feed_Reader_Base extends RSS_Reader {
	
	protected $m_profile;
	protected $content_type;
	protected $feed_url;
	protected $internal_url_pattern;

	public function __construct(Model_Company_Profile $m_profile)
	{
		$this->m_profile = $m_profile;
		$this->internal_url_pattern = get_instance()->conf('url_pattern');
	}

	protected function is_internal_url($link)
	{
		return (bool) preg_match($this->internal_url_pattern, $link);
	}

	protected function fetch_items()
	{
		if (!$this->feed_url) return null;
		$items = $this->read_file($this->feed_url);
		if (!$items) return null;

		return $items;
	}

	protected function link_hash($link = null)
	{
		if (!$link) return null;
		$hash = Data_Hash::__hash_hex($link, 'sha256');
		return $hash;
	}

	protected function save_content($item)
	{
		if ($item->date)
			$date = $item->date;
		else $date = Date::$now;

		if ($item->image)
		{
			$m_image = $this->save_image($item->image);
			$m_images = array($m_image);
		}
		else
		{
			$m_image = null;
			$m_images = array();
		}

		$m_content = new Model_Content();
		$m_content->company_id = $this->m_profile->company_id;
		$m_content->cover_image_id = $m_image ? $m_image->id : null;
		$m_content->type = $this->content_type;
		$m_content->title = $item->title;
		$m_content->title_to_slug();
		$m_content->date_created = $date;
		$m_content->date_publish = $date;
		$m_content->is_identity_locked = 1;
		$m_content->is_published = (int) !Model_Content::is_internal_type($m_content->type);
		$m_content->is_excluded_from_news_center = (int) $item->link;
		$m_content->is_draft = 0;
		$m_content->is_backdated = 1;
		$m_content->save();

		$m_content->set_images($m_images);

		return $m_content;
	}

	protected function save_content_data($item, $content_id)
	{
		$content = html_entity_decode($item->content);
		$content = value_or_null((new View_Data())->pure($content));

		// no summary => get one from content
		if (!($summary = $item->summary))
			$summary = $content;

		$summary = strip_tags($summary);
		$summary = html_entity_decode($summary);
		$summary = (new View_Data())->cut($summary, 200);


		$m_content_data = new Model_Content_Data();
		$m_content_data->content = $content;
		$m_content_data->summary = $summary;
		// $m_content_data->rel_res_pri_link = $item->link;
		$m_content_data->content_id = $content_id;
		$m_content_data->save();

		return $m_content_data;
	}

	protected function save_image($url)
	{
		$v_sizes = get_instance()->conf('v_sizes');
		$variants = array(
			'cover',
			'cover-website',
			'featured',
			'finger',
			'view-cover',
			'view-web',
			'web',
		);

		$buffer = File_Util::buffer_file();
		if (!($result = @copy($url, $buffer))) return;
		$si_original = Stored_Image::from_file($buffer);
		
		if (!$si_original->is_valid_image())
		{
			unlink($buffer);
			return;
		}

		$image = new Model_Image();
		$image->company_id = $this->m_profile->company_id;
		$image->save();	
		
		$image->add_variant($si_original->save_to_db(), 'original');
		
		$im_original = Image::from_file($si_original->actual_filename());
		$im_width = $im_original->width();
		$im_height = $im_original->height();

		foreach ($variants as $variant)
		{
			if ($variant === 'original') continue;
			if (!isset($v_sizes[$variant])) continue;
				
			$v_size = $v_sizes[$variant];
			if (isset($v_size->min_width) && $im_width < $v_size->min_width)
				continue;
				
			$si_variant = $si_original->from_this_resized($v_size);
			$image->add_variant($si_variant->save_to_db(), $variant);
		}

		return $image;
	}

}
