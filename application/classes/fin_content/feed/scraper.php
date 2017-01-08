<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fin_Content_Feed_Scraper {

	// using the feed directly from markets.financialcontent.com
	// * this can be swapped out for similar feed if the server is down
	const FEED_URL = 'http://markets.financialcontent.com/stocks/action/rssfeed?ChannelID=%d';

	protected $channel;

	public function __construct($channel)
	{
		$this->channel = $channel;
	}

	public function fetch()
	{
		$url = sprintf(static::FEED_URL, $this->channel);
		$request = new HTTP_Request($url);
		$response = $request->get();
		if (!$response) return array();

		$rss = new RSS_Reader();
		$rss_items = $rss->read_string($response->data);
		if (!$rss_items) return array();
		$results = array();
		
		foreach ($rss_items as $item)
		{
			preg_match('#GUID=([0-9]+)#i', 
				$item->link, $match);

			$result = new stdClass();
			$result->guid = $match[1];
			$result->title = $item->title;
			$result->url = $item->link;
			$results[] = $result;
		}

		return $results;
	}

}