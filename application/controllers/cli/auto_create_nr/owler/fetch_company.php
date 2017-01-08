<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Fetch_Company_Controller extends Auto_Create_NR_Base { 

	public function index()
	{
		$cnt = 1;
		while ($cnt++ <= 50)
		{
			if (!$owler_url_data = Model_Owler_URL_Data::find('is_processed', 0))
				return;

			$this->get($owler_url_data);
		}
	}

	public function get($owler_url_data)
	{
		lib_autoload('simple_html_dom');
		
		$html = str_get_html($owler_url_data->html);

		$company_name = @$html->find('input[name=company_short_name]', 0)->value;
		// echo "COMPANY NAME = {$company_name}";

		$category_name = @$html->find('div[id=industryList] span', 0)->plaintext;
		if (!empty($category_name))
			$category_name = trim($category_name);
		else
			$category_name = 'other';
		// echo "<hr>category_name = {$category_name}";

		$street1 = @$html->find('span[id=street1]', 0)->plaintext;
		$street2 = @$html->find('span[id=street2]', 0)->plaintext;
		$address = @$street1. " ".@$street2;
		// echo "<hr>address = {$address}";

		$city = @$html->find('span[id=city]', 0)->plaintext;
		// echo "<hr>city = {$city}";

		$state = @$html->find('span[itemprop=addressRegion] span[id=state]', 0)->plaintext;
		// echo "<hr>state = {$state}";

		$zip = @$html->find('span[id=zipcode]', 0)->plaintext;
		// echo "<hr>zip = {$zip}";

		$country = @$html->find('span[id=country]', 0)->plaintext;
		// echo "<hr>country = {$country}";

		$phone = @$html->find('span[id=phone]', 0)->plaintext;
		// echo "<hr>phone = {$phone}";


		$website = @$html->find('p[class=names_url section_block]', 0)->content;
		// echo "<hr>Website = {$website}";

		$logo_image_path = @$html->find('div[class=logo] img', 0)->src;
		// echo "<hr>logo = {$logo_image_path}";

		$about_blurb = @$html->find('span[id=description]', 0)->innertext;
		// echo "<hr>about_blurb = {$about_blurb}";

		$links = str_get_html(@$html->find('div[id=linksTile]', 0)->innertext);

		// Finding social data
		foreach($links->find('a') as $element)
		{
			$href = $element->href;
			$pattern_fb = '/facebook.com/';
			$pattern_twitter = '/twitter.com/';
			$pattern_linkedin = '/linkedin.com/';
			$pattern_pinterest = '/pinterest.com/';
			$pattern_youtube = '/youtube.com/';
			$pattern_gplus = '/plus.google.com/';
			
			if (preg_match($pattern_fb, $href, $match)) 
			{
				$soc_fb = $href;
				$soc_fb = Social_Facebook_Profile::parse_id($soc_fb);
				// echo "<hr>soc_fb = {$soc_fb}";
			}

			if (preg_match($pattern_twitter, $href, $match)) 
			{
				$href = str_replace('/#!', '', $href);
				$soc_twitter = $href;
				$soc_twitter = Social_Twitter_Profile::parse_id($soc_twitter);
				// echo "<hr>soc_twitter = {$soc_twitter}";
			}

			if (preg_match($pattern_linkedin, $href, $match)) 
			{
				$soc_linkedin = $href;
				$soc_linkedin = Social_Linkedin_Profile::parse_id($soc_linkedin);
				// echo "<hr>soc_linkedin = {$soc_linkedin}";
			}

			if (preg_match($pattern_pinterest, $href, $match)) 
			{
				$soc_pinterest = $href;
				$soc_pinterest = Social_Pinterest_Profile::parse_id($soc_pinterest);
				// echo "<hr>soc_pinterest = {$soc_pinterest}";
			}

			if (preg_match($pattern_youtube, $href, $match)) 
			{
				$soc_youtube = $href;
				$soc_youtube = Social_Youtube_Profile::parse_id($soc_youtube);
				
				// echo "<hr>soc_youtube = {$soc_youtube}";

			}

			if (preg_match($pattern_gplus, $href, $match)) 
			{
				$soc_gplus = $href;
				$soc_gplus = Social_GPlus_Profile::parse_id($soc_gplus);
				// echo "<hr>soc_gplus = {$soc_gplus}";
			}
		}

		$category_id = 0;
		if (!empty($category_name))
			if (!$m_category = Model_Owler_Category::find('name', $category_name))
			{
				$m_category = new Model_Owler_Category();
				$m_category->name = $category_name;
				$m_category->save();
				$category_id = $m_category->id;
			}
			else
				$category_id = $m_category->id;

		$owler_comp = new Model_Owler_Company();
		$owler_comp->name = $company_name;
		$owler_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
		$owler_comp->owler_category_id = $category_id;
		$owler_comp->owler_url_id = $owler_url_data->owler_url_id;
		$owler_comp->save();

		$owler_c_data = new Model_Owler_Company_Data();
		$owler_c_data->owler_company_id = $owler_comp->id;
		//$owler_c_data->contact_name = value_or_null(@$contact_name);
		$owler_c_data->website = value_or_null(@$website);
		$owler_c_data->address = value_or_null($address);
		$owler_c_data->city = value_or_null($city);
		$owler_c_data->state = value_or_null($state);
		$owler_c_data->zip = value_or_null($zip);
		$owler_c_data->phone = value_or_null(@$phone);
		$owler_c_data->logo_image_path = value_or_null($logo_image_path);
		
		if (!empty($logo_image_path))
			$owler_c_data->is_logo_valid = 1;

		$owler_c_data->short_description = value_or_null(@$about_blurb);
		$owler_c_data->about_company = value_or_null(@$about_blurb);

		$owler_c_data->soc_fb = value_or_null(@$soc_fb);
		$owler_c_data->soc_twitter = value_or_null(@$soc_twitter);
		$owler_c_data->soc_gplus = value_or_null(@$soc_gplus);
		$owler_c_data->soc_linkedin = value_or_null(@$soc_linkedin);
		$owler_c_data->soc_youtube = value_or_null(@$soc_youtube);
		$owler_c_data->soc_pinterest = value_or_null(@$soc_pinterest);

		$owler_c_data->save();

		$owler_url_data->is_processed = 1;
		$owler_url_data->save();
		
		
		// echo "<hr>";

		$all_news = @$html->find('div[class=top_news_row]');
		$news_list = array();
		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

		if (is_array($all_news) && count($all_news) > 0)
			foreach ($all_news as $news)
			{
				$title = "";
				$date_time = "";
				$link = "";

				$single = str_get_html($news->innertext);
				$n = array();
				$source = @$single->find('a[class=link source ellipsis fb_ne_list_lowlink]', 0)->title;

				$title = @$single->find('a[class=feedTitle]', 0)->innertext; 

				if (!empty($source) && !empty($title))
					$title = str_ireplace($source, '', $title);
				// echo "<br>". $title;
				$link = @$single->find('a[class=feedTitle]', 0)->href; 
				// echo "<br>" . $link;
				$date_time = @$single->find('span[class=duration]', 0)->innertext;
				// echo "<br>" . $date_time;


				if (strlen($date_time) > 0 && (substr($date_time, strlen($date_time)-2, 2) == " h"
						|| substr($date_time, strlen($date_time)-2, 2) == " m")) // e.g. 7 h or 28 m
				{
					$date_time = Date::$now->format('M d, Y');
					// echo "<br>" . $date_time;
					$publish_date = Date::$now->format(DATE::FORMAT_MYSQL);
				}
				
				elseif (strlen($date_time) > 0 && in_array(substr($date_time, strlen($date_time)-3, 3), $months)) // e.g. May 07
				{
					$date_time = $date_time.", ".Date::$now->format('Y');
					$date_time = date('M d, Y', strtotime($date_time));
					// echo "<br>" . $date_time;
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($date_time));
				}	
				else // its like May 14
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($date_time));
					
				// echo "<br>" . $publish_date;
				//// echo htmlspecialchars($single);
				// echo "<hr>";

				$m_content = new Model_Content();
				$m_content->type = Model_Content::TYPE_OWLER_NEWS;
				$m_content->title = $title;
				$m_content->date_publish = $publish_date;
				$m_content->date_created = $publish_date;
				$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
				$m_content->is_published = 1;
				$m_content->is_approved = 1;
				$m_content->save();

				// Now saving the content data
				$m_c_data = new Model_Content_Data();
				$m_c_data->content_id = $m_content->id;
				$m_c_data->summary = $title;
				$m_c_data->content = value_or_null($title);
				$m_c_data->save();

				$owler_pr = new Model_PB_Owler_News();
				$owler_pr->content_id = $m_content->id;
				$owler_pr->url = $link;
				$owler_pr->owler_company_id = $owler_comp->id;
				$owler_pr->date_from_owler = $date_time;
				$owler_pr->save();

			}
		
		
	}
}

?>
