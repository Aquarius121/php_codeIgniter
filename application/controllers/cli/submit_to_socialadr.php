<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
lib_autoload('social_adr');
lib_autoload('simple_html_dom');

class Submit_To_SocialAdr_Controller extends CLI_Base {
	
	protected $social_adr;
	const MAX_TITLE_LENGTH = 200;
	const MIN_DESCRIPTION_LENGTH = 70;
	const MAX_DESCRIPTION_LENGTH = 700;
	const EXPIRE_TOKEN_DESCRIPTION = "The access token provided has expired";

	public function __construct()
	{
		parent::__construct();
		
		$sadr_config = $this->conf('socialadr_app');
		$callback_url = $sadr_config['base_url'] . "/callback";		
		$this->social_adr = new SocialAdrAPI($sadr_config['clientId'], $sadr_config['secret'], 
			$sadr_config['appId'], $callback_url);

		$token = Model_SocialAdr_Auth::find('1');
		$this->social_adr->setAccessToken($token->access_token);
	}	

	public function index()
	{
		$date_start = Date::hours(-48)->format(Date::FORMAT_MYSQL);
		$date_end = Date::hours(-3)->format(Date::FORMAT_MYSQL);
		
		$nr_prefixes = Model_Newsroom::__prefixes('nr');

		$sql = "SELECT c.*, cd.content, {$nr_prefixes}
				FROM nr_content c 
				LEFT JOIN sa_socialadr_submission ss 
				ON c.id = ss.content_id 
				LEFT JOIN nr_content_data cd
				ON cd.content_id = c.id
				INNER JOIN nr_newsroom nr 
				ON nr.company_id = c.company_id
				INNER JOIN nr_pb_pr pb
				ON pb.content_id = c.id
				WHERE c.type = ?
				AND pb.is_distribution_disabled = 0
				AND c.is_premium = 1 
				AND c.is_published = 1
				AND c.date_publish > '{$date_start}'
				AND c.date_publish < '{$date_end}'
				AND ss.content_id IS NULL
				LIMIT 1 ";

		while (true)
		{
			set_time_limit(300);
			
			$dbr = $this->db->query($sql, Model_Content::TYPE_PR);
			$m_content = Model_Content::from_db($dbr, array('newsroom' => 'Model_Newsroom'));

			if (!$m_content) break;
			
			$content = $this->prepare_content($m_content);
			
			$this->submit($content);

			sleep(2);
		}
	}

	protected function prepare_content($m_content)
	{
		$content = new stdClass();
		$content->id = $m_content->id;

		if ($m_content->newsroom->is_active)
			$content->url = $m_content->newsroom->url($m_content->url(), true);
		else
			$content->url = $this->website_url($m_content->url());

		$tags = $m_content->get_tags();
		$combinations = $this->combinations($tags);
		$keywords = array_merge($tags, $combinations);

		$m_content->title = str_replace("|", "", $m_content->title);
		$m_content->content = str_replace("|", "", $m_content->content);

		foreach ($keywords as $i => $keyword)
		{
			$title = "{$m_content->title} - {$keyword}";

			if (strlen($title) <= static::MAX_TITLE_LENGTH)
				$keywords[$i] = $title;
		}

		$keywords[] = $m_content->title;

		$content->title = implode(" | ", $keywords);

		$content->title = "{{$content->title}}";

		$description = $this->get_description($m_content->content);

		$content->description = $description;

		$beat_group_id = 0;
		$beats = $m_content->get_beats();

		foreach ($beats as $beat)
		{
			$beat_group_id = $beat->beat_group_id;

			if ($beat_group_id != 0)
				break;
		}

		if ($sa_cat = Model_SocialAdr_Beat_Map::find($beat_group_id))
			$content->category = $sa_cat->social_adr_category_code;

		if (empty($content->category))
			$content->category = 'busi';

		$tags_string = $m_content->get_tags_string();
		if (!empty($tags_string))
			$content->tags = $tags_string;
		else
			$content->tags = $m_content->title;

		return $content;

	}

	protected function get_description($content)
	{
		$html = str_get_html($content);

		$plain_desc = "";
		$descriptions = array();

		$total_length = 0;
		foreach ($html->find('p') as $p)
		{
			$text = $p->plaintext;
			$text = HTML2Text::plain($text);

			// remove non-utf8 chars
			$text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);
			
			$desc = $this->suitable_description($text);

			if ($desc && strlen($desc) >= static::MIN_DESCRIPTION_LENGTH)
			{
				$descriptions[] = $desc;
				$total_length += strlen($desc);
			}

			$plain_desc = "{$plain_desc} {$text}";
		}

		if (count($descriptions))
			$total_length += strlen($descriptions[0]);

		if (strlen($plain_desc) >= 1000)
		{
			while (true)
			{
				$desc = $this->get_random_description($plain_desc);
				if ($desc && strlen($desc) >= static::MIN_DESCRIPTION_LENGTH)
					if (!in_array($desc, $descriptions))
					{
						$descriptions[] = $desc;
						$total_length += strlen($desc);
					}

				if (count($descriptions) >= 100 || $total_length > 18000)
					break;
			}
		}

		if (!count($descriptions))
			die();
		
		if (count($descriptions))
			$description = implode(' | ', $descriptions);
		
		if (empty($description))
			die();		
		
		$description = "{{$description}}";

		return $description;
	}

	protected function update_bookmark($guid)
	{
		if (!$submission = Model_SocialAdr_Submission::find('response', $guid))
			return false;

		if (!$m_content_data = Model_Content_Data::find($submission->content_id))
			return false;

		$description = $this->get_description($m_content_data->content);
		

		$myURL = new SocialAdrURL();
		$myURL->guid = $guid;
		$myURL->descr = $description;
		$result = $this->social_adr->urlUpdate($myURL);
		
	}

	public function update_missing_description()
	{
		$this->refresh_socialadr_token();
		$results = $this->social_adr->urlList(100, 100);

		if (!$results->success)
		{
			echo "nothing found";
			die();
		}

		foreach ($results->response as $result)
			if (empty($result->description))
				$this->update_bookmark($result->guid);
	}

	// this updates the url for the 
	// PRs for which nr is inactive
	public function update_bookmark_url()
	{
		$this->refresh_socialadr_token();
		$results = $this->social_adr->urlList(100, 500);

		if (!$results->success)
		{
			echo "nothing found";
			die();
		}

		$guids = array();

		foreach ($results->response as $result)
		{
			$guid = $result->guid;
			$guids[] = $guid;
		}

		if (!is_array($guids) || !count($guids))
			return false;

		$guid_str = sql_in_list($guids);

		$sql = "SELECT ss.response, c.*
				FROM nr_content c
				INNER JOIN nr_newsroom nr
				ON c.company_id = nr.company_id
				INNER JOIN sa_socialadr_submission ss
				ON ss.content_id = c.id
				WHERE ss.response IN ({$guid_str})
				AND nr.is_active = 0";

		$query = $this->db->query($sql);
		
		$results = Model_Content::from_db_all($query);

		foreach ($results as $i => $result)
		{
			$guid = $result->response;
			$url = $this->website_url($result->url());			

			$myURL = new SocialAdrURL();
			$myURL->guid = $guid;
			$myURL->url = $url;
			$rs = $this->social_adr->urlUpdate($myURL);
		}
	}

	protected function submit($content)
	{
		$myURL = new SocialAdrURL();
		$myURL->url = $content->url;
		$myURL->title = $content->title;
		$myURL->keywords = $content->title;
		$myURL->descr = $content->description;
		$myURL->tags = $content->tags;
		$myURL->category = $content->category;
		$myURL->microblog = $content->title;
		$myURL->submitRate = 'fast';
		$myURL->submitLimit = 250;
		$result = $this->social_adr->urlAdd($myURL);

		if (!empty($result->error))
			$this->refresh_socialadr_token();

		if (@$result->success)
		{
			$sa_submission = new Model_SocialAdr_Submission();
			$sa_submission->content_id = $content->id;
			$sa_submission->response = $result->response;
			$sa_submission->date_submitted = Date::$now->format(Date::FORMAT_MYSQL);
			$sa_submission->save();
		}
	}


	protected function refresh_socialadr_token()
	{
		// Just in case the refresh token doesnt work
		// We will need to get a new token using the 
		// controllers/common/socialadr_auth_request
		// manually and then the refresh cycle will 
		// continue.

		if (!$token = Model_SocialAdr_Auth::find('1'))
		{
			$this->notify_token_refresh_failure();
			return false;
		}

		$result = $this->social_adr->refresh($token->refresh_token);
		
		if (!empty($result->error))
		{
			$this->notify_token_refresh_failure();
			die();
		}

		if ($result->access_token)
		{
			$token->access_token = $result->access_token;
			$token->refresh_token = $result->refresh_token;
			$token->expires_in = $result->expires_in;
			$token->date_renewed = Date::$now->format(Date::FORMAT_MYSQL);
			$token->save();
			
			$this->social_adr->setAccessToken($result->access_token);
			return true;
		}
	}

	protected function notify_token_refresh_failure()
	{
		$alert = new Critical_Alert();
		$alert->set_subject('Social Adr Token Refresh Failed');
		$text = 'Social Adr token refresh failed.';
		$text .= ' Please take corrective action';
		$alert->set_content($text);
		$alert->send();
	}

	protected function get_proper_sentences($content)
	{
		$dot = ".";

		$first_dot_pos = stripos ($content, $dot);
		
		if ($first_dot_pos) 
		{ 
			$start = $first_dot_pos + 1;
			$part = substr($content, $start);
			$last_dot_pos = strrpos($part, $dot);

			if ($last_dot_pos)
			{
				$text = substr($part, 0, $last_dot_pos);
				$text = "{$text}{$dot}";
			}
			else
				$text = $part;

			return $text;
		}

		else //if there are no dots
			return $content;

	}

	protected function get_random_description($text)
	{
		$length = strlen($text);

		if (!$length)
			return false;

		$start_pt_low = 0;
		$start_pt_upper = $length - 600;
		$start_pt = rand($start_pt_low, $start_pt_upper);
		
		$rand_len = rand(300, 600);
		
		$descr = substr($text, $start_pt, $rand_len);
		$descr = $this->get_proper_sentences($descr);
		if ($descr = $this->suitable_description($descr))
			return $descr;

		return false;
		
	}

	protected function suitable_description($desc)
	{
		$length = strlen($desc);


		if ($length < static::MIN_DESCRIPTION_LENGTH)
			return false;

		if ($length >= static::MIN_DESCRIPTION_LENGTH && $length <= static::MAX_DESCRIPTION_LENGTH)
			return $desc;

		$desc = substr($desc, 0, static::MAX_DESCRIPTION_LENGTH - 20);
		return $desc;
	}

	protected function combinations($arr)
	{
		if (!is_array($arr) || !count($arr))
			return false;

		if (count($arr) == 1)
			return $arr[0];

		$results = array();
		for ($i =0; $i < count($arr); $i++)
			for ($j = 0; $j < count($arr); $j++)
				if ($i !== $j)
					$results[] = $arr[$i] . " " . $arr[$j];

		return $results;
	}

		
}

?>