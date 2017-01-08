<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fetch_Email_Controller extends CLI_Base {
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT cd.website, cd.marketwired_company_id
				FROM ac_nr_marketwired_company_data cd
				LEFT JOIN ac_nr_marketwired_fetch_email e
				ON e.marketwired_company_id = cd.marketwired_company_id
				WHERE e.marketwired_company_id IS NULL 
				AND NOT ISNULL(NULLIF(website, ''))
				AND ISNULL(NULLIF(email, ''))
				ORDER BY cd.marketwired_company_id
				LIMIT 1";

		while ($cnt++ <= 3)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_MarketWired_Company_Data::from_db($result);
			if (!$c_data) break;

			$this->get($c_data);
		}
	}

	public function get($c_data)
	{
		
		if (empty($c_data->website))
			return false;

		lib_autoload('simple_html_dom');
		$url = $c_data->website;
		$html = @file_get_html($url);

		$m_f_email = new Model_MarketWired_Fetch_Email();
		$m_f_email->marketwired_company_id = $c_data->marketwired_company_id;
		$m_f_email->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$m_f_email->is_website_read_success = 0;
			$m_f_email->save();
			return;
		}

		$m_f_email->is_website_read_success = 1;

		// Trying to locate email address directly 
		// on the home page if one exists

		$email = $this->extract_email_address($html);
		if (!empty($email))
		{	
			$m_f_email->is_email_fetched = 1;
			$m_f_email->save();
			
			$this->update_marketwired_c_data($c_data->marketwired_company_id, $email);
			return;
		}

		// email not found on home page
		// now reading the contact page url

		$contact_pattern = '/contact/i';
		$contact_page_slug = null;

		foreach($html->find('a') as $element)
		{
			$href = $element->href;
			
			if (preg_match($contact_pattern, $href, $match))
				$contact_page_slug = $href;
		}


		if (!empty($contact_page_slug))
		{
			$contact_page_web_url = $this->find_complete_url($c_data->website, $contact_page_slug);
			$m_f_email->is_contact_page_found = 1;
			$m_f_email->contact_page_slug = $contact_page_slug;
			
			$html = @file_get_html($contact_page_web_url);

			if (!empty($html))
			{
				$m_f_email->is_contact_page_read_success = 1;
				$email = $this->extract_email_address($html);
				if (!empty($email))
				{	
					$m_f_email->is_email_fetched = 1;
					$this->update_marketwired_c_data($c_data->marketwired_company_id, $email);
				}
			}
		}

		$m_f_email->save();
		
	}

	protected function find_complete_url($website, $contact_page_slug)
	{
		if (substr($contact_page_slug, 0, 4) == "http")
			return $contact_page_slug;

		if (substr($contact_page_slug, 0, 1) == "/")
			$contact_page_slug = substr($contact_page_slug, 1);

		$m = parse_url($website);
		$website = "http://".$m['host'];

		$complete_url = "{$website}/{$contact_page_slug}";
		return $complete_url;
	}

	protected function extract_email_address ($html) 
	{
		$email = "";
		$pattern = '/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/i';
		$pattern_wrong = '/(.*)(\.png)|(\.gif)|(\.jpg)|(\.bmp)|(\.js)$/i'; // make sure its not an image
		if (preg_match_all($pattern, $html, $matches))
		{
			//print_r($matches);
			foreach ($matches as $i => $match_array)
				foreach ($match_array as $match)
					if (! preg_match($pattern_wrong, $match, $email_matches))
					{
						$email = $match; // . "<br>";
						break;
					}
		} 

		return $email;
	}

	protected function update_marketwired_c_data($marketwired_company_id, $email)
	{
		$c_data = Model_MarketWired_Company_Data::find($marketwired_company_id);
		$c_data->email = $email;
		$c_data->save();
	}

	protected function get_web_url($url) 
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
