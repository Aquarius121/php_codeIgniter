<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fin_Content_Feed_Website_Scraper {

	// using the feed directly from markets.financialcontent.com
	// * this can be swapped out for similar feed if the server is down
	const FEED_URL = 'http://markets.financialcontent.com/stocks/news/channelinfo?ChannelID=%d&CurrentPage=%d';
	const PAGES = 50;

	protected $channel;

	public function __construct($channel)
	{
		$this->channel = $channel;
	}

	public function fetch()
	{
		$results = array();

		for ($offset = 0; $offset < static::PAGES; $offset++)
		{
			$partial_results = $this->fetch_partial($offset);
			if (!is_array($partial_results)) continue;
			$results = array_merge($results, $partial_results);
		}

		return $results;
	}

	public function fetch_partial($offset)
	{
		$url = sprintf(static::FEED_URL, 
			$this->channel, $offset);
		$request = new HTTP_Request($url);
		$response = $request->get();
		if (!$response) return array();

		$parser = HTML_Util::parser($response->data);
		if (!$parser) return array();

		$results = array();
		$link_divs = $parser->find('div.press_item');

		foreach ($link_divs as $link_div)
		{
			$parser = htmlqp($link_div);
			if (!($anchor = $parser->find('a.title')))
				continue;

			$title = trim($anchor->text());
			$url = $anchor->attr('href');
			preg_match('#news/read/([0-9]+)#i', 
				$url, $match);

			if (is_array($match) && isset($match[1]))
			{
				$result = new stdClass();
				$result->guid = $match[1];
				$result->title = $title;
				$result->url = $url;
				$results[] = $result;
			}
		}

		return $results;
	}

}