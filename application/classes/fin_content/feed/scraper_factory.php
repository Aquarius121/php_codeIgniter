<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fin_Content_Feed_Scraper_Factory {

	const CHANNEL_NEWSWIRE       = 6751;
	const CHANNEL_PRNEWSWIRE     = 3197;
	const METHOD_SCRAPE_FEED     = 'METHOD_SCRAPE_FEED';
	const METHOD_SCRAPE_WEBSITE  = 'METHOD_SCRAPE_WEBSITE';

	public static function create($channel, $method = self::METHOD_SCRAPE_FEED)
	{
		if ($method == static::METHOD_SCRAPE_WEBSITE)
			return new Fin_Content_Feed_Website_Scraper($channel);
		if ($method == static::METHOD_SCRAPE_FEED)
			return new Fin_Content_Feed_Scraper($channel);
		throw new Exception('invalid method');
	}

}