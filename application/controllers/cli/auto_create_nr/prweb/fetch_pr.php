<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_PR_Controller extends Auto_Create_NR_Base { // fetching prweb single pr
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT * FROM nr_pb_prweb_pr
				WHERE prweb_company_id = 0
				ORDER BY content_id
				LIMIT 1";

		while ($cnt++ <= 50)
		{
			$this->inspect("---------------------------");
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$prweb_pr = Model_PB_PRWeb_PR::from_db($result);
			if (!$prweb_pr) break;

			$this->get($prweb_pr);
			sleep(1);
		}
	}

	protected function get($prweb_pr)
	{
		lib_autoload('simple_html_dom');

		if (empty($prweb_pr->url))
			return false;

		$html = @file_get_html($prweb_pr->url);

		if (empty($html))
		{
			$prweb_pr = Model_PB_PRWeb_PR::find($prweb_pr->content_id);
			$prweb_pr->prweb_company_id = -1;
			$prweb_pr->save();
			return;
		}

		$m_content = Model_Content::find($prweb_pr->content_id);		

		if (! $m_c_data = Model_Content_Data::find($prweb_pr->content_id))
		{
			$m_c_data = new Model_Content_Data();
			$m_c_data->content_id = $prweb_pr->content_id;
		}

		$summary = @$html->find('div[class=article-intro-right] h2', 0)->innertext;
		$summary = $this->sanitize($summary);

		$address = @$html->find('p[class=article-date] span[itemprop=contentLocation]', 0)->innertext;
		$address = str_replace("(PRWEB)", "", $address);
		$address = trim($address);
		
		$video_url = @$html->find('div[id=video] div iframe', 0)->src;
		if ( ! empty($video_url))
			$video_url = "http:{$video_url}";
		
		$img_src = @$html->find('div[class=middle-column] img[class=qa-news-image]', 0)->src;
		
		$img_id = null;		

		$contact_name = @$html->find('div[class=box-cont] div[class=box-contact-name]', 0)->innertext;		

		$contact_text = @$html->find('div[class=box-contact]', 0)->plaintext;
		
		if (!empty($contact_text))
		{
			$phone = $this->extract_phone_number($contact_text);
			$email = $this->extract_email_address($contact_text);
		}
		
		$element = $html->find('div[class=box-cont] div[class=box-contact-el]', 0)->plaintext;
		$lines = explode("\n", $element);
		$company_name = $lines[0];

		$element = $html->find('div[class=box-cont] div[class=box-contact-el] a', 0);
		$href = $element->href;
		
		$pattern1 = '#^(https?://|)(www\.|)([a-z\-\.]+\.)?prweb\.net/Redirect(.*)#is';
		$pattern2 = '#^(.*)Redirect\.aspx(.*)#is';
		if (preg_match($pattern1, $href, $match) || preg_match($pattern2, $href, $match)) 
		{
			$company_name = $element->innertext;
			$prweb_comp_url = $href;
		}
		
		if (empty($prweb_comp_url))
			$prweb_comp_url = @$html->find('a[class=box-contact-btn]', 0)->href;

		$content = "";
		$counter = 1;
		$keep_tags = true;
		foreach($html->find('p[class=responsiveNews]') as $element)
		{
			$text = $element->innertext;
			$text = $this->sanitize($text, $keep_tags);
			$content = "{$content}<p>{$text}</p>";
		}

		// may be there is just one 
		// paragraph with <br> tags
		if (empty($content))
		{
			if ($p = $html->find('p[class=responsiveNews]', 0))
			{
				$text = $p->innertext;
				$text = $this->sanitize($text, $keep_tags);
				$content = "{$content} <p>{$text}</p>";
			}
		}

		$content = $this->linkify($content, array('http', 'https', 'mail'), 
					array('target' => '_blank'));


		// now finding supporting quote
		$supporting_quote = "";
		if ($mid_col = $html->find('div.middle-column', 0))
			if ($s_q_span = $mid_col->find("span.blockquote-text", 0))
			{
				$supporting_quote = $s_q_span->plaintext;
				$supporting_quote = $this->sanitize($supporting_quote);
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

		$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);
		$m_content->is_published = 1;
		$m_content->is_approved = 1;
		$m_content->is_premium = 1;		
		$m_content->title_to_slug();
		//$m_content->cover_image_id = value_or_null($img_id);
		
		$m_content->save();
		
		// Now saving the content data
		$m_c_data->summary = $summary;
		$m_c_data->content = value_or_null($content);
		$m_c_data->supporting_quote = value_or_null($supporting_quote);
		$m_c_data->save();

		$m_prweb_cat = Model_PRWeb_Category::find($prweb_pr->prweb_category_id);

		$m_pb_pr = new Model_PB_PR();
		$m_pb_pr->content_id = $m_content->id;
		$m_pb_pr->is_distribution_disabled = 1;
		$m_pb_pr->save();

		$m_content->set_beats(array($m_prweb_cat->newswire_beat_id));

		// check if the company already exists
		//if (empty($prweb_comp_url) || 
		//		! $prweb_c_data = Model_PRWeb_Company_Data::find('prweb_website_url', $prweb_comp_url))
		$company_name = $this->sanitize($company_name);
		if ( ! $prweb_comp = Model_PRWeb_Company::find('name', $company_name))
		{
			$prweb_comp = new Model_PRWeb_Company();
			$prweb_comp->name = $company_name;
			$prweb_comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
			$prweb_comp->prweb_category_id = $prweb_pr->prweb_category_id;
			$prweb_comp->save();

			$prweb_c_data = new Model_PRWeb_Company_Data();
			$prweb_c_data->prweb_company_id = $prweb_comp->id;
			$prweb_c_data->contact_name = value_or_null($contact_name);

			$prweb_c_data->cover_image_url = value_or_null($img_src);

			if (!empty($img_id))
				$prweb_c_data->is_cover_image_fetched = 1;

			$prweb_c_data->prweb_website_url = value_or_null(@$prweb_comp_url);
			$prweb_c_data->address = value_or_null($address);
			$prweb_c_data->phone = value_or_null($phone);
			$prweb_c_data->email = value_or_null($email);
			$prweb_c_data->soc_fb = value_or_null($soc_facebook);
			$prweb_c_data->soc_twitter = value_or_null($soc_twitter);
			$prweb_c_data->soc_gplus = value_or_null($soc_gplus);
			$prweb_c_data->soc_linkedin = value_or_null($soc_linkedin);
			$prweb_c_data->video = value_or_null($video_url);
			$prweb_c_data->save();
		}

		$prweb_pr = Model_PB_PRWeb_PR::find($prweb_pr->content_id);
		$prweb_pr->prweb_company_id = $prweb_comp->id;

		if (!empty($img_src))
			$prweb_pr->cover_image_url = $img_src;

		if (! empty($video_url))
		{
			$prweb_pr->web_video_provider = Video::PROVIDER_YOUTUBE;
			$video = Video::get_instance(Video::PROVIDER_YOUTUBE);
			$prweb_pr->web_video_id = $video->parse_video_id($video_url);

			$m_pb_pr->web_video_provider = Video::PROVIDER_YOUTUBE;
			$m_pb_pr->web_video_id = $prweb_pr->web_video_id;
			$m_pb_pr->save();
		}

		$prweb_pr->save();

		$this->inspect("Old date was => {$prweb_comp->date_last_pr_submitted}");
		$this->recheck_for_prn_sop($prweb_comp);
	}

	public function recheck_for_prn_sop($prweb_comp)
	{
		$this->inspect("UPDATING LATEST PR DATE for PRWEB Comp = {$prweb_comp->id}");

		$this->update_latest_pr_date($prweb_comp);
		$this->update_prweb_prn_valid($prweb_comp);

		if ($nw_ca_comp = Model_Newswire_CA_Company::find('name', $prweb_comp->name))
		{
			$this->inspect("UPDATING NEWSWIRE_CA COMPANY VALID FOR Comp = {$nw_ca_comp->id}");
			$this->update_newswire_ca_prn_valid($nw_ca_comp);
		}

	}

	public function update_latest_pr_dates()
	{
		$cnt = 1;
		
		$sql = "SELECT * 
				FROM ac_nr_prweb_company
				WHERE is_last_pr_date_updated = 0
				ORDER BY id DESC
				LIMIT 20";

		while (1)
		{
			$results = Model_PRWeb_Company::from_sql_all($sql);
			if (!count($results)) break;

			foreach ($results as $prweb_comp)
			{
				$this->console($prweb_comp->id);
				$this->update_latest_pr_date($prweb_comp);
			}
		}
	}

	protected function update_latest_pr_date($prweb_comp)
	{
		$sql = "SELECT c.date_publish
				FROM nr_pb_prweb_pr pb
				INNER JOIN nr_content c
				ON pb.content_id = c.id
				WHERE pb.prweb_company_id = ?
				ORDER BY c.date_publish DESC
				LIMIT 1";

		$date_publish = null;
		if ($content = Model::from_sql($sql, array($prweb_comp->id)))
			$date_publish = $content->date_publish;

		$prweb_comp->date_last_pr_submitted = value_or_null($date_publish);
		$prweb_comp->is_last_pr_date_updated = 1;
		$prweb_comp->is_last_pr_date_migrated = 0;
		$prweb_comp->save();
	}


	public function check_prn_valid_leads()
	{
		$cnt = 1;

		$sql = "SELECT c.*
				FROM ac_nr_prweb_company c
				LEFT JOIN ac_nr_prn_valid_company pvc
				ON pvc.source_company_id = c.id
				AND pvc.source = ?
				WHERE pvc.source_company_id IS NULL
				AND c.is_last_pr_date_updated = 1
				ORDER BY c.id
				LIMIT 20";

		while (1)
		{
			$results = Model_PRWeb_Company::from_sql_all($sql, array(Model_PRN_Valid_Company::SOURCE_PRWEB));
			if (!count($results)) break;

			foreach ($results as $prweb_comp)
			{
				$this->console($prweb_comp->id);
				$this->update_prweb_prn_valid($prweb_comp);			
			}
		}
	}	
}

?>
