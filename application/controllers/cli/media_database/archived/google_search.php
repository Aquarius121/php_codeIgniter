<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Google_Search {
	
	public $error;
	
	public function search($search)
	{
		lib_autoload('php_query');
		
		$url = $this->search_url($search);
		$request = new HTTP_Request($url);
		$request->set_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
		$request->set_header('Accept-Language', 'en-us,en;q=0.5');
		$request->set_header('User-Agent', User_Agent::random());
		
		$proxies = array(
			'tcp://162.243.219.86:5555',
			'tcp://69.16.243.87:5555',
			'tcp://178.32.48.241:5555',
		);
		
		$request->conf['http']['proxy'] = $proxies[array_rand($proxies)];		
		$response = $request->get();
			
		if (!$response)
		{
			$this->error = new stdClass();
			$this->error->message = $request->error;
			$this->error->proxy = $request->conf['http']['proxy'];
			return false;
		}
		
		$doc = phpQuery::newDocumentHTML($response->data);
		$doc_links = pq('li h3 > a', $doc);
		$links = array();
		
		for ($i = 0; $i < $doc_links->size(); $i++)
		{
			$doc_link = $doc_links->eq($i);
			// if this is found it must be additional links for first match
			if ($doc_link->parents('li')->eq(0)->find('table')->size()) continue;
			$link = new stdClass();
			$link->text = $doc_link->text();
			$link->href = $doc_link->attr('href');
			// appears to be some sort of advert
			if (preg_match('#adurl=#i', $link->href)) continue;
			if (!trim($link->text)) continue;
			if (!trim($link->href)) continue;
			$links[] = $link;
		}
		
		return $links;
	}
	
	public function search_url($search)
	{
		$params = array();
		$params['ie'] = 'utf-8';
		$params['oe'] = 'utf-8';
		$params['gl'] = 'us';
		$params['q'] = $search;
		$params['filter'] = '0';
		$params = http_build_query($params);
		$url = "http://www.google.com/search?{$params}";
		return $url;
	}
	
}

?>