<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fetch_Category_Page_Controller extends CLI_Base { // fetching topseos category page
	
	public function index()
	{
		$cnt = 1;

		$sql = "SELECT * 
				FROM ac_nr_topseos_category
				WHERE is_completed = 0
				ORDER BY id
				LIMIT 1";

		while ($cnt++ <= 30)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$category = Model_TopSeos_Category::from_db($result);
			if (!$category) break;

			$is_a_new_rec_added = $this->get($category->url, $category);			
			
			$category->is_completed = 1;
			$category->save();
			sleep (1);
		}
	}

	public function get($url, $category)
	{
		set_memory_limit('1024M');
		lib_autoload('simple_html_dom');		
		$response = Unirest\Request::get($url);

		if (empty($response->raw_body))
		{
			echo "going";
			return false;
		}

		$parser = htmlqp($response->raw_body);
		$ranks_table = $parser->find('div#ranks_table');

		foreach ($ranks_table as $rank_table)
		{
			$parser = htmlqp($rank_table);
			$scripts = $parser->find('script');
			$j = 0;
			foreach ($scripts as $i => $script)
			{
				$text = $script->text();
				
				if (String_Util::starts_with($text, 'cData.push('))
				{
					$j++;
					$text = str_replace("cData.push(", "", $text);
					$text = substr($text, 0, strlen($text)-2);
					
					$j_text = json_decode($text);

					$company_name = $j_text->company_name;
					$permalink = stripslashes($j_text->permalink);
					$contact_name = $j_text->contact_person;
					$website = $j_text->website_link;
					$phone = $j_text->phone;
					$address = $j_text->address;
					$city = $j_text->city;
					$state = $j_text->state;
					$zip = $j_text->zip_code;
					$soc_fb = $j_text->facebook;
					$soc_twitter = $j_text->twitter;
					$soc_linkedin = $j_text->linkedin;

					$is_new_comp = 0;
					if (!empty($company_name) && $topseos_comp = Model_TopSeos_Company::find('name', $company_name))
						$topseos_c_data = Model_Topseos_Company_Data::find($topseos_comp->id);

					// check if the company already exists 
					// with the same website
					elseif (!empty($website) && $topseos_c_data = Model_Topseos_Company_Data::find('website', $website))
						$topseos_comp = Model_TopSeos_Company::find($topseos_c_data->topseos_company_id);
					else
					{
						$is_new_comp = 1;
						$topseos_comp = new Model_TopSeos_Company();
						$topseos_comp->name = $company_name;
						$topseos_comp->permalink = $permalink;
						$topseos_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
						$topseos_comp->topseos_category_id = $category->id;
						$topseos_comp->save();

						$topseos_c_data = new Model_TopSeos_Company_Data();
						$topseos_c_data->topseos_company_id = $topseos_comp->id;
					}


					if ($is_new_comp || empty($topseos_c_data->contact_name))
						$topseos_c_data->contact_name = value_or_null($contact_name);

					if ($is_new_comp || empty($topseos_c_data->website))
						$topseos_c_data->website = value_or_null($website);
					
					if ($is_new_comp || empty($topseos_c_data->phone))
						$topseos_c_data->phone = value_or_null($phone);

					if ($is_new_comp || empty($topseos_c_data->address))
						$topseos_c_data->address = value_or_null($address);

					if ($is_new_comp || empty($topseos_c_data->city))
						$topseos_c_data->city = value_or_null($city);

					if ($is_new_comp || empty($topseos_c_data->state))
						$topseos_c_data->state = value_or_null($state);

					if ($is_new_comp || empty($topseos_c_data->zip))
						$topseos_c_data->zip = value_or_null($zip);
						
					if (($is_new_comp || empty($topseos_c_data->soc_fb)) && !empty($soc_fb))
					{
						$topseos_c_data->soc_fb = value_or_null($soc_fb);
						$topseos_c_data->soc_fb_feed_status = Model_TopSeos_Company_Data::SOCIAL_NOT_CHECKED;
					}

					if (($is_new_comp || empty($topseos_c_data->soc_twitter)) && !empty($soc_twitter))
					{
						$topseos_c_data->soc_twitter = value_or_null($soc_twitter);
						$topseos_c_data->soc_twitter_feed_status = Model_TopSeos_Company_Data::SOCIAL_NOT_CHECKED;
					}

					if (($is_new_comp || empty($topseos_c_data->soc_linkedin)) && !empty($soc_linkedin))
						$topseos_c_data->soc_linkedin = value_or_null($soc_linkedin);

					$topseos_c_data->save();

				}
			}			
		}
	}

	public function update_soc_twitter()
	{
		$sql = "SELECT * 
				FROM ac_nr_topseos_company_data
				WHERE soc_twitter_feed_status = 'invalid'
				AND is_soc_twitter_updated = 0
				LIMIT 1";

		while (1)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_TopSeos_Company_Data::from_db($result);
			if (!$c_data) break;

			$c_data->soc_twitter = Social_Twitter_Profile::parse_id($c_data->soc_twitter);		
			$c_data->is_soc_twitter_updated = 1;
			$c_data->save();
		}
	}
}

?>
