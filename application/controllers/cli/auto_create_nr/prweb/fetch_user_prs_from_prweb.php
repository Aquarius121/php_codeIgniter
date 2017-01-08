<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Fetch_User_PRs_From_PRWeb_Controller extends CLI_Base { // fetching prweb prs for a user
	
	public function index()
	{
		$cnt = 1;
		
		$user_id = 1073741944;

		$urls[0] = "http://www.prweb.com/releases/2015/05/prweb12730457.htm";
		$urls[] = "http://www.prweb.com/releases/2015/05/prweb12744687.htm";
		$urls[] = "http://www.prweb.com/releases/2015/03/prweb12580473.htm";
		$urls[] = "http://www.prweb.com/releases/2015/03/prweb12557978.htm";
		$urls[] = "http://www.prweb.com/releases/2015/03/prweb12552682.htm";
		
		$urls[] = "http://www.prweb.com/releases/2015/02/prweb12534950.htm";
		$urls[] = "http://www.prweb.com/releases/2015/02/prweb12530856.htm";
		$urls[] = "http://www.prweb.com/releases/2015/02/prweb12518657.htm";
		$urls[] = "http://www.prweb.com/releases/2014/10/prweb12280315.htm";
		$urls[] = "http://www.prweb.com/releases/2014/10/prweb12276005.htm";

		$urls[] = "http://www.prweb.com/releases/2014/09/prweb12198050.htm";
		$urls[] = "http://www.prweb.com/releases/2014/09/prweb12182208.htm";
		$urls[] = "http://www.prweb.com/releases/2014/09/prweb12180224.htm";
		$urls[] = "http://www.prweb.com/releases/2014/09/prweb12134560.htm";
		$urls[] = "http://www.prweb.com/releases/2014/08/prweb12118864.htm";

		$urls[] = "http://www.prweb.com/releases/2014/07/prweb12040655.htm";
		$urls[] = "http://www.prweb.com/releases/2014/07/prweb12012188.htm";
		$urls[] = "http://www.prweb.com/releases/2014/07/prweb12007508.htm";
		$urls[] = "http://www.prweb.com/releases/2014/06/prweb11928115.htm";
		$urls[] = "http://www.prweb.com/releases/2014/06/prweb11912870.htm";
		
		$urls[] = "http://www.prweb.com/releases/2014/05/prweb11847452.htm";
		$urls[] = "http://www.prweb.com/releases/2014/05/prweb11843248.htm";
		$urls[] = "http://www.prweb.com/releases/2014/03/prweb11655078.htm";
		$urls[] = "http://www.prweb.com/releases/2014/02/prweb11589073.htm";
		$urls[] = "http://www.prweb.com/releases/2014/02/prweb11584598.htm";
		
		$urls[] = "http://www.prweb.com/releases/2014/02/prweb11573322.htm";
		$urls[] = "http://www.prweb.com/releases/2014/01/prweb11503131.htm";
		$urls[] = "http://www.prweb.com/releases/2013/12/prweb11421983.htm";
		$urls[] = "http://www.prweb.com/releases/2013/10/prweb11223325.htm";
		$urls[] = "http://www.prweb.com/releases/2013/10/prweb11167424.htm";
		
		$urls[] = "http://www.prweb.com/releases/2013/9/prweb11100679.htm";
		$urls[] = "http://www.prweb.com/releases/2013/7/prweb10926499.htm";
		$urls[] = "http://www.prweb.com/releases/2013/7/prweb10926497.htm";
		$urls[] = "http://www.prweb.com/releases/2013/9/prweb10926496.htm";
		$urls[] = "http://www.prweb.com/releases/2013/7/prweb10926495.htm";
		
		$urls[] = "http://www.prweb.com/releases/2013/5/prweb10553455.htm";
		$urls[] = "http://www.prweb.com/releases/2013/7/prweb10553430.htm";
		$urls[] = "http://www.prweb.com/releases/2013/3/prweb10491908.htm";
		$urls[] = "http://www.prweb.com/releases/2013/3/prweb10460471.htm";
		$urls[] = "http://www.prweb.com/releases/2013/2/prweb10416693.htm";
		
		$urls[] = "http://www.prweb.com/releases/2013/2/prweb10416655.htm";
		$urls[] = "http://www.prweb.com/releases/2012/12/prweb10153321.htm";
		$urls[] = "http://www.prweb.com/releases/2012/10/prweb10066363.htm";
		$urls[] = "http://www.prweb.com/releases/2012/9/prweb9908320.htm";
		$urls[] = "http://www.prweb.com/releases/2012/8/prweb9648449.htm";
		
		$urls[] = "http://www.prweb.com/releases/2012/5/prweb9550653.htm";
		$urls[] = "http://www.prweb.com/releases/2012/2/prweb9220396.htm";
		$urls[] = "http://www.prweb.com/releases/2012/2/prweb9129263.htm";
		$urls[] = "http://www.prweb.com/releases/2011/12/prweb9016901.htm";
		$urls[] = "http://www.prweb.com/releases/2011/12/prweb9006557.htm";
		
		$urls[] = "http://www.prweb.com/releases/2011/7/prweb8632247.htm";
		$urls[] = "http://www.prweb.com/releases/2011/6/prweb8588214.htm";
		$urls[] = "http://www.prweb.com/releases/2011/6/prweb8463934.htm";
		$urls[] = "http://www.prweb.com/releases/2011/5/prweb5257654.htm";
		$urls[] = "http://www.prweb.com/releases/2011/3/prweb5160744.htm";

		$urls[] = "http://www.prweb.com/releases/2011/2/prweb5094214.htm";
		$urls[] = "http://www.prweb.com/releases/2011/2/prweb5051454.htm";
		$urls[] = "http://www.prweb.com/releases/2011/1/prweb4950984.htm";
		$urls[] = "http://www.prweb.com/releases/2010/12/prweb4866004.htm";
		$urls[] = "http://www.prweb.com/releases/2010/11/prweb4825354.htm";
		
		$urls[] = "http://www.prweb.com/releases/2009/11/prweb3150694.htm";
		$urls[] = "http://www.prweb.com/releases/2009/11/prweb3150634.htm";
		$urls[] = "http://www.prweb.com/releases/2009/9/prweb2906064.htm";
		$urls[] = "http://www.prweb.com/releases/2009/9/prweb2859724.htm";
		$urls[] = "http://www.prweb.com/releases/2009/9/prweb2823554.htm";

		$urls[] = "http://www.prweb.com/releases/2009/9/prweb2811114.htm";
		$urls[] = "http://www.prweb.com/releases/2009/9/prweb2782544.htm";
		$urls[] = "http://www.prweb.com/releases/2009/8/prweb2775844.htm";
		$urls[] = "http://www.prweb.com/releases/2009/6/prweb2475364.htm";
		$urls[] = "http://www.prweb.com/releases/2009/4/prweb2339364.htm";

		$urls[] = "http://www.prweb.com/releases/2009/3/prweb2251694.htm";
		$urls[] = "http://www.prweb.com/releases/2009/2/prweb2058454.htm";
		$urls[] = "http://www.prweb.com/releases/2009/1/prweb1866474.htm";
		$urls[] = "http://www.prweb.com/releases/2008/12/prweb1686404.htm";
		$urls[] = "http://www.prweb.com/releases/2008/9/prweb1255414.htm";

		$urls[] = "http://www.prweb.com/releases/2008/8/prweb1241614.htm";
		$urls[] = "http://www.prweb.com/releases/2008/8/prweb1177244.htm";
		$urls[] = "http://www.prweb.com/releases/2008/6/prweb1042824.htm";
		$urls[] = "http://www.prweb.com/releases/2008/3/prweb776414.htm";
		$urls[] = "http://www.prweb.com/releases/2008/1/prweb623851.htm";

		for ($cnt = count($urls) - 1; $cnt >= 0; $cnt--)
		{
			$this->get($urls[$cnt]);
			sleep(1);
			//exit;
		}
	}

	protected function get($url)
	{
		$company_id = 462167;

		lib_autoload('simple_html_dom');

		if (empty($url))
			return false;

		$html = @file_get_html($url);

		if (empty($html))
			return false;

		$title = @$html->find('div[class=one content] h1[class=title]', 0)->innertext;
		//echo $title;

		$criteria = array();
		$criteria[] = array('title', $title);
		$criteria[] = array('company_id', $company_id);
		$criteria[] = array('type', Model_Content::TYPE_PR);

		if ($m_content = Model_Content::find($criteria))
			return false; // this PR already exists for this company.

		
		$m_content = new Model_Content();
		$m_content->company_id = $company_id;
		$m_content->type = Model_Content::TYPE_PR;
		$m_content->title = $title;
		$m_content->title_to_slug();
		$m_content->save();

		$m_c_data = new Model_Content_Data();
		$m_c_data->content_id = $m_content->id;
		
		$summary = @$html->find('div[class=one content] h2[class=subtitle]', 0)->innertext;
		$summary = strip_tags($summary);

		$supporting_quote = @$html->find('div[class=one content] div[class=releaseQuote]', 0)->innertext;
		$supporting_quote = strip_tags($supporting_quote);
		$supporting_quote = trim($supporting_quote);
		
		$date_line = @$html->find('p[class=releaseDateline]', 0)->innertext;
		if (!empty($date_line))
		{
			preg_match("/(.*)\(PRWEB\)(.*)/", $date_line, $match);

			if (is_array($match) && count($match) > 1)
			{
				$address = $match[1];
				$p_date = $match[2];
				$publish_date = date(DATE::FORMAT_MYSQL, strtotime($p_date));
			}
			else
			{
				$text = @$html->find('div[class=fullWidth]', 0)->innertext;
				if (!empty($text))
				{
					$reg = '/(\w+)\s*(\d+),\s*(\d{4})/';
					$match = preg_match($reg, $text, $matches);
					$publish_date = date(DATE::FORMAT_MYSQL, strtotime($matches[0]));
				}
			}
		}		

		$video_url = @$html->find('div[id=video] div iframe', 0)->src;
		if ( ! empty($video_url))
			$video_url = "http:{$video_url}";
		

		$img_src = @$html->find('div[class=nismall clearfix] img[class=newsImage]', 0)->src;
		if (empty($img_src))
			$img_src = @$html->find('div[class=ninormal clearfix] img[class=newsImage]', 0)->src;
					
		$img_id = null;
		
		if (!empty($img_src))
			$img_id = $this->import_image($img_src);

		$contact_name = @$html->find('div[class=contactInfo] ul li strong', 0)->innertext;

		foreach($html->find('div[class=contactInfo] ul li a') as $element)
		{
			$href = $element->href;
			$pattern1 = '#^(https?://|)(www\.|)([a-z\-\.]+\.)?prweb\.net/Redirect(.*)#is';
			$pattern2 = '#^(.*)Redirect\.aspx(.*)#is';
			if (preg_match($pattern1, $href, $match) || preg_match($pattern2, $href, $match)) 
			{
				$company_name = $element->innertext;
				$prweb_comp_url = $href;
			}
		}		

		$content = "";
		$counter = 1;
		foreach($html->find('div[class=fullWidth floatLeft dottedTop] p') as $element)
		{
			if ($counter++ > 2)
			{
				$text = $element->plaintext;
				$content = "{$content} <p>{$text}</p>";
			}
		}
		
		if (empty($prweb_comp_url))
		{
			$iframe = @$html->find('div[class=iframe] iframe', 0)->src;
			
			if ( ! empty($iframe))
				$prweb_comp_url = $iframe;			
		}

		$string = @$html->find('div[class=contactInfo] ul li', 0)->plaintext;
		if ( ! empty($string))
		{
			$lines = explode("\n", $string);

			$pattern1 = '#(\+[0-9]*)#'; // format +1
			$pattern2 = '#([0-9]{3}\-[0-9]{4}\-*)#'; // format 123-1234

			for($k=0; $k < count($lines); $k++)
			{	
				if (preg_match($pattern1, $lines[$k], $match) || 
					preg_match($pattern2, $lines[$k], $match))
					$phone_num = $lines[$k];
			}


			if (empty($company_name))
			{
				$is_after_contact_name = 0;
				for($k=0; $k < count($lines); $k++)
				{	
					if ($is_after_contact_name)
					{
						$company_name = $lines[$k];
						$is_after_contact_name = 0;
						break;
					}

					if (strpos($lines[$k], $contact_name) !== false) 
						$is_after_contact_name = 1;
				}
			}
		}


		$soc_twitter = @$html->find('a[id=twitterLink]', 0)->href;
		if (!empty($soc_twitter))
			$soc_twitter = Social_Twitter_Profile::parse_id($soc_twitter);

		$soc_facebook = @$html->find('a[id=facebookLink]', 0)->href;
		if (!empty($soc_facebook))
			$soc_facebook = Social_Facebook_Profile::parse_id($soc_facebook);
		

		$soc_linkedin = @$html->find('a[id=linkedInLink]', 0)->href;
		if (!empty($soc_linkedin))
			$soc_linkedin = Social_Linkedin_Profile::parse_id($soc_linkedin);
		
		$soc_gplus = @$html->find('a[id=googlePlusLink]', 0)->href;
		if (!empty($soc_gplus))
			$soc_gplus = Social_GPlus_Profile::parse_id($soc_gplus);
		

		$m_content->date_publish = $publish_date;
		$m_content->date_created = $publish_date;
		$m_content->date_updated = $publish_date;
		$m_content->is_published = 1;
		$m_content->is_premium = 1;
		$m_content->is_approved = 1;
		$m_content->cover_image_id = value_or_null($img_id);
		
		if (!empty($img_id))
			$m_content->set_images(array($img_id));
			
		
		$m_content->save();


		// Now saving the content data
		$m_c_data->summary = $summary;
		$m_c_data->content = value_or_null($content);
		$m_c_data->supporting_quote = value_or_null($supporting_quote);
		$m_c_data->save();

		$pb_pr = new Model_PB_PR();
		$pb_pr->content_id = $m_content->id;
		$pb_pr->location = value_or_null($address);
		
		if (! empty($video_url))
		{
			$pb_pr->web_video_provider = Video::PROVIDER_YOUTUBE;
			$video = Video::get_instance(Video::PROVIDER_YOUTUBE);
			$pb_pr->web_video_id = $video->parse_video_id($video_url);
		}

		$pb_pr->save();

		//$m_prweb_cat = Model_PRWeb_Category::find($prweb_pr->prweb_category_id);

	}

	protected function import_image($img_url)
	{
		if (!empty($img_url))
		{
			$img_file = "image";
			@copy($img_url, $img_file);

			if (Image::is_valid_file($img_file))
			{
				// import the image into the system
				$img_im = LEGACY_Image::import("cover", $img_file);
				$img_im->save();

				return $img_im->id;
			}
		}
	}
	
}

?>
