<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
lib_autoload('simple_html_dom');

class SpeedLinks_Base extends CLI_Base { 
	
	const SPEEDLINK_USERNAME = 'prnews';
	const SPEEDLINK_PASSWORD = 'Prnews123zz';

	protected function get_remaining_limit()
	{
		// Logging into speed-links
		$url = "http://speed-links.net/login.php";
		$params = array();
		$params['username'] = static::SPEEDLINK_USERNAME;
		$params['password'] = static::SPEEDLINK_PASSWORD;
		$params['doLogin'] = '1';
		$params['submit'] = '1';

		$response = Unirest\Request::post($url, null, $params);
		$cookie = $response->headers['Set-Cookie'];

		// Setting the cookie that will be needed for further actions
		Unirest\Request::cookie($cookie);

		// Logged in, now checking the limit left
		$links_url = "http://speed-links.net/links.php";
		$response = Unirest\Request::get($links_url);
		if (empty($response->body))
			return false;

		$html = str_get_html($response->body);
		
		$today_left = 0;
		if ($today_left_b = @$html->find('b[id=todayleft]', 0))
			$today_left = (int) $today_left_b->plaintext;

		return $today_left;
	}

	protected function submit(&$urls, $batch_title = null, $drip_feed = 1)
	{
		$post_url = "http://speed-links.net/ajax/controller.php";

		$params = array();
		$params['urls'] = $urls;
		$params['uploadLinks'] = 1;
		$params['dripfeed'] = $drip_feed;
		$params['upname'] = $batch_title;

		$response = Unirest\Request::post($post_url, null, $params);

		sleep(2);

		// URLs submitted, now reading the submitted links
		$links_url = "http://speed-links.net/links.php";
		$response = Unirest\Request::get($links_url);
		
		if (empty($response->body))
			return false;

		$html = str_get_html($response->body);

		$link = "";
		foreach ($html->find('a') as $anchor)
			if (trim($anchor->plaintext) === $batch_title)
				$link = $anchor->href;

		if (empty($link))
			return false;

		$file_url = "http://speed-links.net{$link}";
		$response = Unirest\Request::get($file_url);
		
		$content = $response->body;
		$submitted_urls = explode("\n", $content);

		if (!count($submitted_urls))
			return false;

		return $submitted_urls;
	}
}

?>
