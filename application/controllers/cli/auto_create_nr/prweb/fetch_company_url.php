<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fetch_Company_URL_Controller extends CLI_Base { // fetching company url frm prweb redirect url
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT * FROM ac_nr_prweb_company_data
				WHERE ISNULL(NULLIF(website,''))
				AND NOT ISNULL(NULLIF(prweb_website_url,''))
				AND num_website_fetch_tries < 3
				ORDER BY prweb_company_id
				LIMIT 1";

		while ($cnt++ <= 5)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$prweb_c_data = Model_PRWeb_Company_Data::from_db($result);
			if (!$prweb_c_data) break;

			$url = $this->get($prweb_c_data->prweb_website_url);
			if (!empty($url))
				$prweb_c_data->website = $url;

			$num_website_fetch_tries = $prweb_c_data->num_website_fetch_tries;
			$num_website_fetch_tries++;
			$prweb_c_data->num_website_fetch_tries = $num_website_fetch_tries;
			$prweb_c_data->save();
			sleep(1);
			
		}

	}

	public function get($prweb_url = null)
	{
		if (empty($prweb_url))
			return null;
		
		$url_info = $this->get_web_page($prweb_url); 
		return $url_info["url"];
	}

	protected function get_web_page($url) 
	{ 
		$options = array( 
			CURLOPT_RETURNTRANSFER => true,     // return web page 
			CURLOPT_HEADER         => true,    // return headers 
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
			CURLOPT_ENCODING       => "",       // handle all encodings 
			CURLOPT_USERAGENT      => "spider", // who am i 
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect 
			CURLOPT_TIMEOUT        => 120,      // timeout on response 
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects 
		); 

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		$err = curl_errno($ch);
		$errmsg = curl_error($ch);
		$header = curl_getinfo($ch);
		curl_close($ch);		
		return $header; 
	}  



}

?>
