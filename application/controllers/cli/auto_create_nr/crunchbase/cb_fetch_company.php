<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('shared/cb_api'); //crunchbase API

class CB_Fetch_Company_Controller extends CLI_Base { // fetching detail for a crunchbase company
	
	use CB_API_Trait;

	public function index()
	{
		error_reporting(E_ALL);
		// $user_key = "bfc43d0c11fc0227d6f98c6103f42c15";

		$user_key = $this->select_api_key();
		
		if (empty($user_key))
		{
			echo "keys used";
			return;
		}

		$ci =& get_instance();
		$cnt = 1;
		while ($cnt <= 30)
		{
			$cnt++;

			$sql = "SELECT * FROM ac_nr_cb_company 
					WHERE raw_data is NULL
					LIMIT 1";
				
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$company = Model_CB_Company::from_db($result);
			if (!$company) break;
			
			$slug = $company->path;
			$slug = str_replace("organization/", '', $slug);
			$slug = utf8_encode($slug);
			$url = "http://api.crunchbase.com/v/2/organization/{$slug}?user_key={$user_key}";
			// $url = "http://api.crunchbase.com/v/2/{$slug}?user_key={$user_key}";
			//echo $url;
			//exit;
			
			$res = @file_get_contents($url);
			$this->log_key_usage($user_key);

			if(empty($res))
			{
				$company->raw_data = '404 - Not Found';
				$company->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
				$company->save();
				continue;
			}
			
			
			$company->raw_data = $res;
			$company->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
			$company->save();

			$res = json_decode($res);
			$c_data = new Model_CB_Company_Data();
			$c_data->company_id = $company->id;
			$c_data->about_company = @$res->data->properties->description;
			$c_data->short_description = @$res->data->properties->short_description;
			$c_data->website = @$res->data->properties->homepage_url;
			$c_data->email = @$res->data->properties->email_address;
			$image_path_prefix = @$res->metadata->image_path_prefix;

			if (!empty($res->data->relationships->primary_image))
				$c_data->logo_image_path = $image_path_prefix .
							$res->data->relationships->primary_image->items[0]->path;
			
			if (!empty($res->data->relationships->offices->items))
			{
				foreach (@$res->data->relationships->offices->items as $item)
				{
					if (@$item->type == "Address")
					{
						$c_data->address = $item->street_1;
						$c_data->city = $item->city;
						$c_data->state = $item->region;
						$c_data->zip = $item->postal_code;
						$country = @$item->country;
						if ($m_country = Model_Country::find('name', $country))
							$c_data->country_id = $m_country->id;
					}
				}
			}
			

			if (!empty($res->data->relationships->websites->items))
			{
				foreach (@$res->data->relationships->websites->items as $item)
				{
					if (@$item->type == "WebPresence" && $item->title == "facebook")
					{
						$fb_url = $item->url;
						$c_data->soc_fb = Social_Facebook_Profile::parse_id($fb_url);
					}
					elseif (@$item->type == "WebPresence" && $item->title == "twitter")
					{
						$twitter_url = $item->url;
						$c_data->soc_twitter = Social_Twitter_Profile::parse_id($item->url);
					}
					
					elseif (@$item->type == "WebPresence" && $item->title == "linkedin")
					{
						$linkedin_url = $item->url;
						$c_data->soc_linkedin = Social_Linkedin_Profile::parse_id($linkedin_url);
					}

					elseif (@$item->type == "WebPresence" && $item->title == "blog")
						$c_data->blog_url = $item->url;
					
				}
			}

			$c_data->save();

		}
	}

	protected function get_http_response_code($url) {
    	$headers = get_headers($url);
    	return substr($headers[0], 9, 3);
	}

	public function check_calls()
	{		
		$sql = "SELECT * FROM ac_nr_cb_api_key_usage";

		$result = $this->db->query($sql);
		$results = Model::from_db_all($result);
		print_r($results);
	}

	public function check_fields()
	{
		$fh = fopen("raw/cb_company.txt", "r");
		$data = fread ($fh, 102400);
		fclose($fh);
		$res = json_decode($data);
		print_r(@$res->data->relationships->primary_image->items[0]);
	}
	
}

?>
