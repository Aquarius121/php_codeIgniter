<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Fetch_News_URL_Controller extends Auto_Create_NR_Base { // fetching actual news URL from owler news URL

	public function index()
	{
		$cnt = 1;

		$sql = "SELECT * FROM nr_pb_owler_news 
				WHERE ISNULL(NULLIF(actual_news_url,''))
				ORDER by content_id DESC
				LIMIT 1";
			
		while ($cnt++ <= 1000)
		{
			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;

			$news = Model_PB_Owler_News::from_db($result);
			if (!$news) break;
		
			$url = $news->url;
			//echo "{$url} <hr>";

			if (empty($url))
				$actual_news_url = "NOTHING";
			else
				$actual_news_url = $this->find_news_url($url);

			if (!empty($actual_news_url))
			{
				$actual_news_url = urlencode($actual_news_url);
				$content_id = $news->content_id;

				$update_sql = "UPDATE nr_pb_owler_news
								SET actual_news_url = '{$actual_news_url}'
								WHERE content_id = {$content_id}";

				$this->db->query($update_sql);
			}

			if ($cnt%50 == 0)
				sleep(5);
		}
	}


	public function find_news_url($url)
	{
		lib_autoload('simple_html_dom');
		
		$text = $this->disguise_curl($url);
		$html = str_get_html($text);

		$actual_news_url = @$html->find('a[class=icon_close]', 0)->href;

		if (strstr($actual_news_url, 'vimeo.com'))
			$actual_news_url = preg_replace('/vimeo.com\/(.+)/i', "player.vimeo.com/video/$1", $actual_news_url);

		return $actual_news_url;
	}


	public function disguise_curl($url)
	{
		$header = array();
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: ";

		$ch = curl_init();
	 
		curl_setopt($ch, CURLOPT_PROXY, '202.106.16.36:3128');    // Set CURLOPT_PROXY with proxy in $proxy variable
		// curl_setopt($ch, CURLOPT_PROXY, '165.139.149.169:3128');

		
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	  	//curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com');

		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
	 
		$results = curl_exec($ch);  // Execute a cURL request
		curl_close($ch);    // Closing the cURL handle
		return $results;
	}

}

?>
