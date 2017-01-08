<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Accesswire_Scraper_Client {

	const ARTICLE_REPORT_URL = 'https://www.accesswire.com/articlereport.aspx?id=%d';
	const FEED_URL = 'https://www.accesswire.com/accountmain.aspx?viewall=1';

	protected $cookies;
	protected $config;

	public function __construct($config)
	{
		$this->config = $config;
		$this->construct_cookies(array(
			'allowmultiplecompanies' => 'True',
			'customerid' => $config['id'],
			'email' => $config['email'],
			'name' => $config['name'],
			'password' => $config['password'],
			'trust' => 'Delayed',
		));
	}

	public function get_views($article_id)
	{
		if (!is_int($article_id)) return false;
		Unirest\Request::cookie($this->cookies);
		$url = sprintf(static::ARTICLE_REPORT_URL, $article_id);
		$response = Unirest\Request::get($url);

		if (isset($response->headers) && $response->code === 200)
		{
			$views_txt = htmlqp($response->raw_body)
				->find('span:contains(Total Views)')
				->first()->text();

			if (!empty($views_txt)) 
			     return (int) preg_replace('/[^0-9]/', null, $views_txt);
			else return false;
		}
	}

	public function get_feed()
	{
		$feed = array();
		Unirest\Request::cookie($this->cookies);
		$response = Unirest\Request::get(static::FEED_URL);

		if (isset($response->headers) && $response->code === 200)
		{
			foreach (htmlqp($response->raw_body, 'ul.articlebox li') as $item) 
			{
				$feed_item = new stdClass();
				$feed_item->title = trim($item->find('label')->text());
				preg_match('/([0-9]+)/', $item->find('.bottom .corner a')->attr('href'), $match);
				$feed_item->id = (int) $match[1];
				$feed[] = $feed_item;
			}
		}

		return $feed;
	}

	protected function construct_cookies($cookies)
	{
		$raw_cookies = array();
		foreach ($cookies as $name => $value)
			$raw_cookies[] = "$name=$value";
		$this->cookies = implode('; ', $raw_cookies);
	}

}