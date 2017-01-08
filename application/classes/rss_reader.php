<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class RSS_Reader {

	// namespace used for content:encoded elements in rss feed
	const RSS_CONTENT_NS = 'http://purl.org/rss/1.0/modules/content/';

	/* 
	 *  @param $file => filename or url.
	 *  @return see: read(SimpleXMLElement $sxml);
	*/
	public function read_file($file)
	{
		$sxml = @simplexml_load_file($file);
		if (!$sxml) return array();
		return $this->read($sxml);
	}

	/* 
	 *  @param $str => xml string.
	 *  @return see: read(SimpleXMLElement $sxml);
	*/
	public function read_string($str)
	{
		$sxml = @simplexml_load_string($str);
		if (!$sxml) return array();
		return $this->read($sxml);
	}

	/* 
	 *  @param $sxml => SimpleXMLElement
	 *  @return an array of objects with these attributes:
	 *    ->title (required)
	 *    ->link (required)
	 *    ->summary (optional)
	 *    ->description (optional)
	 *    ->date (optional)
	 *    ->image (optional)
	*/
	public function read(SimpleXMLElement $sxml)
	{
		if (!$sxml) return array();
		if ($this->can_read_atom($sxml))
			return $this->read_atom($sxml);
		if ($this->can_read_rss($sxml))
			return $this->read_rss($sxml);
		return array();
	}

	/* 
	 *  @param $file => filename or url.
	 *  @return boolean
	*/
	public function is_valid_file($file)
	{
		$sxml = @simplexml_load_file($file);
		if ($sxml === false) return false;
		return $this->is_valid($sxml);
	}

	/* 
	 *  @param $str => xml string.
	 *  @return boolean
	*/
	public function is_valid_string($str)
	{
		$sxml = @simplexml_load_string($str);
		if ($sxml === false) return false;
		return $this->is_valid($sxml);
	}

	/* 
	 *  @param $sxml => SimpleXMLElement.
	 *  @return boolean
	*/
	public function is_valid(SimpleXMLElement $sxml)
	{
		if (!$sxml) return false;
		if ($this->is_valid_atom($sxml)) return true;
		if ($this->is_valid_rss($sxml)) return true;
		return false;
	}

	/* 
	 *  @param $sxml => SimpleXMLElement.
	 *  @return boolean
	*/
	public function is_valid_atom(SimpleXMLElement $sxml)
	{
		if (!$sxml) return false;
		if (!count($sxml->entry)) return false;
		if (!count($sxml->entry->id)) return false;
		if (!count($sxml->entry->updated)) return false;
		if (!count($sxml->entry->link)) return false;
		if (!count($sxml->entry->title)) return false;
		return true;
	}

	/* 
	 *  @param $sxml => SimpleXMLElement.
	 *  @return boolean
	*/
	public function is_valid_rss(SimpleXMLElement $sxml)
	{
		if (!$sxml) return false;
		if (!count($sxml->channel)) return false;
		if (!count($sxml->channel->item)) return false;
		if (!count($sxml->channel->item->title)) return false;
		if (!count($sxml->channel->item->link)) return false;
		if (!count($sxml->channel->item->description)) return false;
		return true;
	}

	/* 
	 *  @param $sxml => SimpleXMLElement.
	 *  @return boolean
	*/
	public function can_read_atom(SimpleXMLElement $sxml)
	{
		if (!$sxml) return false;
		if (!count($sxml->entry)) return false;
		return true;
	}

	/* 
	 *  @param $sxml => SimpleXMLElement.
	 *  @return boolean
	*/
	public function can_read_rss(SimpleXMLElement $sxml)
	{
		if (!$sxml) return false;
		if (!count($sxml->channel)) return false;
		if (!count($sxml->channel->item)) return false;
		return true;
	}

	/* 
	 *  @param $sxml => SimpleXMLElement.
	 *  @return see: read(SimpleXMLElement $sxml);
	*/
	protected function read_atom(SimpleXMLElement $sxml)
	{
		$results = array();
		if (!$sxml) return $results;

		foreach ($sxml->entry as $entry)
		{
			$result = new stdClass();
			$results[] = $result;

			$result->title = (string) $entry->title;
			$result->summary = $entry->summary ? (string) $entry->summary : null;
			$result->content = $entry->content ? (string) $entry->content : null;
			$result->date = $entry->updated ? Date::utc($entry->updated) : null;
			$result->image = value_or_null((string) $entry->logo);

			foreach ($entry->link as $link)
			{
				$rel = $link->attributes()->rel;
				if ($rel && $rel != 'self') continue;
				$result->link = (string) $link->attributes()->href;
				break;
			}
		}

		return $results;
	}

	/* 
	 *  @param $sxml => SimpleXMLElement.
	 *  @return see: read(SimpleXMLElement $sxml);
	*/
	protected function read_rss(SimpleXMLElement $sxml)
	{
		$results = array();
		if (!$sxml) return $results;

		foreach ($sxml->channel->item as $item)
		{
			$result = new stdClass();
			$results[] = $result;

			$result->title = (string) $item->title;
			$result->date = $item->pubDate ? Date::utc($item->pubDate) : null;
			$result->image = value_or_null((string) $item->image);
			$result->link = (string) $item->link;

			$item->registerXPathNamespace('content', static::RSS_CONTENT_NS);
			$content_arr = $item->xpath('./content:encoded');
			$content = isset($content_arr[0]) 
				? (string) $content_arr[0] 
				: null;

			if ($content)
			{
				$result->content = $content;
				$result->summary = value_or_null((string) $item->description);
			}
			else
			{
				$result->content = value_or_null((string) $item->description);
				$result->summary = null;
			}
		}

		return $results;
	}

}
