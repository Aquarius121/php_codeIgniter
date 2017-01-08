<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

lib_autoload('linkedin');

class Social_Wire {

	const UPDATE_AUTO = 'auto';
	const UPDATE_MANUAL = 'manual';

	public static $company_id;
	
	public static function update($company_id, $update_method = null, $force_update = 0)
	{		
		lib_autoload('detect_language');

		static::$company_id = $company_id;

		$m_types = array();
		$nr_profile = Model_Company_Profile::find($company_id);
		
		if (!$nr_profile || 
			 !$nr_profile->is_enable_social_wire)
			return false;

		$m_types = $nr_profile->get_social_wire_media();
		if (!count($m_types))
			return false;

		$media_types = sql_in_list($m_types);

		$ci =& get_instance();
		$soc_post_cache_time = $ci->conf('social_post_cache_time'); 
		$date_cache_time_ago = Date::minutes(-1 * $soc_post_cache_time)->format(Date::FORMAT_MYSQL);

		// checking if social media entries already exist in db		
		$sql = "SELECT DISTINCT(media_type) AS media_type
				FROM nr_content c
				INNER JOIN nr_pb_social s
				ON s.content_id = c.id
				WHERE c.company_id = ?
				AND s.media_type IN ({$media_types})";

		$query = $ci->db->query($sql, array($company_id));
		$results = Model_PB_Social::from_db_all($query);
		$m_types_already_in_db = array();
		$status = false;

		foreach ($results as $result)
			$m_types_already_in_db[] = $result->media_type;
		$m_types_to_update = array();
		
		// If social media entries already in db, 
		// check which ones need updating
		if (count($m_types_already_in_db))
		{		
			$sql = "SELECT DISTINCT(media_type) AS media_type
					FROM nr_content c
					INNER JOIN nr_pb_social s
					ON s.content_id = c.id
					WHERE c.company_id = ?
					AND s.media_type IN ({$media_types})
					AND date_updated < '{$date_cache_time_ago}'";
					
			$query = $ci->db->query($sql, array($company_id));
			$results = Model_PB_Social::from_db_all($query);

			foreach ($results as $result)
				$m_types_to_update[] = $result->media_type;
		}

		if ($nr_profile->is_inc_facebook_in_soc_wire() && 
			($force_update || in_array(Model_PB_Social::TYPE_FACEBOOK, $m_types_to_update)
			|| ! in_array(Model_PB_Social::TYPE_FACEBOOK, $m_types_already_in_db)))
		{
			$facebook_feeds = static::fetch_facebook_feeds($nr_profile);
			static::update_feeds_in_db($company_id, $facebook_feeds, Model_PB_Social::TYPE_FACEBOOK);
			$status = true;
		}
		
		if ($nr_profile->is_inc_twitter_in_soc_wire() && 
			($force_update || ! in_array(Model_PB_Social::TYPE_TWITTER, $m_types_already_in_db)
			|| in_array(Model_PB_Social::TYPE_TWITTER, $m_types_to_update)))
		{
			$twitter_feeds = static::fetch_twitter_feeds($nr_profile);
			static::update_feeds_in_db($company_id, $twitter_feeds, Model_PB_Social::TYPE_TWITTER);
			$status = true;
		}

		if ($nr_profile->is_inc_gplus_in_soc_wire() && 
			($force_update || ! in_array(Model_PB_Social::TYPE_GPLUS, $m_types_already_in_db)
			|| in_array(Model_PB_Social::TYPE_GPLUS, $m_types_to_update)))
		{
			$gplus_feeds = static::fetch_gplus_feeds($nr_profile->soc_gplus);
			static::update_feeds_in_db($company_id, $gplus_feeds, Model_PB_Social::TYPE_GPLUS);
			$status = true;
		}
		
		if ( $nr_profile->is_inc_pinterest_in_soc_wire() && 
			($force_update || ! in_array(Model_PB_Social::TYPE_PINTEREST, $m_types_already_in_db)
			|| in_array(Model_PB_Social::TYPE_PINTEREST, $m_types_to_update)))		
		{
			$pinterest_feeds = static::fetch_pinterest_feeds($nr_profile->soc_pinterest);
			static::update_feeds_in_db($company_id, $pinterest_feeds, Model_PB_Social::TYPE_PINTEREST);
			$status = true;
		}

		if ($nr_profile->is_inc_youtube_in_soc_wire() && 
			($force_update || ! in_array(Model_PB_Social::TYPE_YOUTUBE, $m_types_already_in_db)
			|| in_array(Model_PB_Social::TYPE_YOUTUBE, $m_types_to_update)))
		{
			$youtube_feeds = static::fetch_youtube_feeds($nr_profile->soc_youtube);
			static::update_feeds_in_db($company_id, $youtube_feeds, Model_PB_Social::TYPE_YOUTUBE);
			$status = true;
		}

		if ($nr_profile->is_inc_vimeo_in_soc_wire() && 
			($force_update || ! in_array(Model_PB_Social::TYPE_VIMEO, $m_types_already_in_db)
			|| in_array(Model_PB_Social::TYPE_VIMEO, $m_types_to_update)))
		{
			$vimeo_feeds = static::fetch_vimeo_feeds($nr_profile->soc_vimeo);
			static::update_feeds_in_db($company_id, $vimeo_feeds, Model_PB_Social::TYPE_VIMEO);
			$status = true;
		}

		if ($nr_profile->is_inc_instagram_in_soc_wire()
			&& ($force_update || ! in_array(Model_PB_Social::TYPE_INSTAGRAM, $m_types_already_in_db)
				 || in_array(Model_PB_Social::TYPE_INSTAGRAM, $m_types_to_update)))
		{
			$instagram_feeds = static::fetch_instagram_feeds($nr_profile->soc_vimeo);
			static::update_feeds_in_db($company_id, $instagram_feeds, Model_PB_Social::TYPE_INSTAGRAM);
			$status = true;
		}

		if ($nr_profile->soc_linkedin
			&& (! in_array(Model_PB_Social::TYPE_LINKEDIN, $m_types_already_in_db)
				 || in_array(Model_PB_Social::TYPE_LINKEDIN, $m_types_to_update)))
		{
			$linkedin_feeds = static::fetch_linkedin_feeds($nr_profile->soc_linkedin);
			static::update_feeds_in_db($company_id, $linkedin_feeds, Model_PB_Social::TYPE_LINKEDIN);
			$status = true;
		}		

		static::log_update($company_id, $update_method);
		return $status;
	}

	public static function log_update($company_id, $update_method)
	{
		if (! $log_rec = Model_Social_Wire_Update::find($company_id))
		{
			$log_rec = new Model_Social_Wire_Update();
			$log_rec->company_id = $company_id;
		}

		if ($update_method == static::UPDATE_AUTO)
			$log_rec->date_last_auto_update = Date::$now->format(DATE::FORMAT_MYSQL);
		else
			$log_rec->date_last_manual_request = Date::$now->format(DATE::FORMAT_MYSQL);

		$log_rec->is_updating = 0;
		$log_rec->save();
	}

		
	public static function update_feeds_in_db($company_id, $feeds, $media_type)
	{
		$social_content_ids = array();
		$ci =& get_instance();

		$sql = "SELECT id 
				FROM nr_content
				WHERE company_id = ?
				AND type = ?";

		$query = $ci->db->query($sql, array($company_id, Model_Content::TYPE_SOCIAL));
		$results = Model_Content::from_db_all($query);
		$ids = array(0);
		foreach ($results as $result)
			$ids[] = $result->id;
		
		$ids = sql_in_list($ids);

		foreach ($feeds as $feed)
		{			
			$criteria = array();
			$criteria[] = array('media_type', $media_type);
			$criteria[] = array('post_id', (string) $feed->post_id);
			$criteria[] = array("content_id IN ({$ids})");			
			$m_pb_social = null;

			if ($m_pb_social = Model_PB_Social::find($criteria))
			{
				$m_content = Model_Content::find($m_pb_social->content_id);
			}
			else
			{
				$m_content = new Model_Content();
				$m_content->date_created = Date::$now->format(DATE::FORMAT_MYSQL);					
			}

			$m_content->company_id = $company_id;
			$m_content->type = Model_Content::TYPE_SOCIAL;
						
			$m_content->title = $feed->post_title;

			$m_content->date_publish = date(DATE::FORMAT_MYSQL, strtotime($feed->post_date_publish));
			$m_content->date_updated = Date::$now->format(DATE::FORMAT_MYSQL);;
			
			$m_content->is_published = 1;
			$m_content->is_draft = 0;
			$m_content->is_legacy = 0;
			$m_content->is_under_review = 0;
			$m_content->is_approved = 1;
			$m_content->is_credit_locked = 1;
			$m_content->save();

			if (!empty($m_pb_social))
				$m_content_data = Model_Content_Data::find('content_id', $m_pb_social->content_id);
			else
				$m_content_data = new Model_Content_Data();

			$m_content_data->content_id = $m_content->id;
			

			$m_content_data->content = ($feed->post_content);
			$m_content_data->summary = $feed->post_summary;
			

			$m_content_data->save();

			if (empty($m_pb_social))
				$m_pb_social = new Model_PB_Social();
			$m_pb_social->post_id = trim($feed->post_id);
			$m_pb_social->content_id = $m_content->id;
			$m_pb_social->media_type = $media_type;
			
			// hack to preserve manual edits
			if (!$m_pb_social->id)
				$m_pb_social->raw_data($feed);

			$m_pb_social->save();
			
			$social_content_ids[] = $m_content->id;
		}

		// removing the old entries now
		$sql = "SELECT c.id AS id, type
				FROM nr_content c
				INNER JOIN nr_pb_social s
				ON s.content_id = c.id
				WHERE c.company_id = ?
				AND s.media_type = ?";

		$query = $ci->db->query($sql, array($company_id, $media_type));
		$results = Model_Content::from_db_all($query);			
		
		foreach ($results as $result)
		{
			if (!in_array($result->id, $social_content_ids))
				$result->delete();
		}		

	}

	public static function fetch_instagram_feeds($instagram_id)
	{
		$ci =& get_instance();
		$num_feeds = $ci->conf('num_social_media_feeds_per_type'); 
		$feeds = Social_Instagram_Feed::get($instagram_id, $num_feeds);

		if (count ($feeds))
		{
			foreach ($feeds as $cnt => $feed)
			{	
				$feed->post_id = $feed->id;
				$feed->post_date_publish = @date(DATE::FORMAT_MYSQL, $feed->caption->created_time);
				$feed->post_title = @html_entity_decode(strip_tags(@$feed->caption->text));
				$feed->post_content = @html_entity_decode(strip_tags(@$feed->caption->text));
				$feed->post_summary = @html_entity_decode(strip_tags(@$feed->caption->text));
			}
		}

		return $feeds;

	}

	public static function fetch_vimeo_feeds($vimeo_id)
	{
		$vimeo_feed = new Social_Vimeo_Feed();
		$ci =& get_instance();
		$num_feeds = $ci->conf('num_social_media_feeds_per_type'); 
		$feeds = $vimeo_feed->get($vimeo_id, $num_feeds);

		if (count ($feeds))
		{
			foreach ($feeds as $cnt => $feed)
			{	
				$feed->post_id = $feed->uri;
				$feed->post_date_publish = @date(DATE::FORMAT_MYSQL, strtotime($feed->created_time));
				$feed->post_title = HTML2Text::plain($feed->name);
				$feed->post_content = HTML2Text::plain($feed->description);
				$feed->post_summary = HTML2Text::plain($feed->description);
			}
		}

		return $feeds;

	}

	public static function fetch_twitter_feeds($nr_profile)
	{
		lib_autoload('detect_language');

		$twitter_name = $nr_profile->soc_twitter;
		$is_twitter_english_feeds = $nr_profile->is_twitter_english_feeds;

		$tw_feed = new Social_Twitter_Feed();
		$tw_feed->set_screen_name($twitter_name);

		$twitter_auth = Social_Twitter_Auth::find($nr_profile->company_id);
		if ($twitter_auth && $twitter_auth->is_valid())
			$tw_feed->set_auth($twitter_auth);

		$ci =& get_instance();
		$num_feeds = $ci->conf('num_social_media_feeds_per_type'); 
		$feeds = $tw_feed->get_for_social_wire($num_feeds);

		if (count ($feeds))
		{
			foreach ($feeds as $cnt => $feed)
			{			
				// checking the language
				if ($is_twitter_english_feeds && !empty($feed->text))
				{
					$language_code = Detect_Language::detect($feed->text);
					if ($language_code != "en")
						unset($feeds[$cnt]);
				}

				if ($cnt >= $num_feeds)
					break;
				$feed->post_id = $feed->id;
				$feed->post_date_publish = @date(DATE::FORMAT_MYSQL, strtotime($feed->created_at));
				$feed->post_title = HTML2Text::plain($feed->text);
				$feed->post_content = HTML2Text::plain($feed->text);
				$feed->post_summary = HTML2Text::plain($feed->text);
			}
		}

		return $feeds;
	}

	public static function fetch_facebook_feeds($nr_profile)
	{
		$ci = & get_instance();
		$fb_name = $nr_profile->soc_facebook;
		$num_feeds = $ci->conf('num_social_media_feeds_per_type');
		$fb_feed = new Social_Facebook_Feed();
		$fb_auth = Social_Facebook_Auth::find($nr_profile->company_id);
		if ($fb_auth && $fb_auth->is_valid())
			$fb_feed->set_auth($fb_auth);

		$data = $fb_feed->get($fb_name);
		$feeds = @$data['data'];
		$fb_feeds = array();
		if (count($feeds))
		{
			foreach ($feeds as $feed)
			{
				if (count($fb_feeds) >= $num_feeds)
					break;

				$feed = (object) $feed;
				
				$post_id = explode("_", $feed->id);
				$post_id = $post_id[1];

				$feed->post_id = $post_id;
				$feed->post_date_publish = $feed->created_time;			
				$feed->post_content = HTML2Text::plain(@$feed->description);
				$feed->post_summary = HTML2Text::plain(@$feed->description);
				$feed->post_message = HTML2Text::plain(@$feed->message);
				$feed->post_message = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', 
						'', $feed->post_message);
				$feed->post_title = @$feed->post_message;	

				// Do not include stories in which a user commented on something or liked something				
				if (!empty($feed->post_title) || !empty($feed->post_content) || !empty($feed->post_summary) 
					|| !empty($feed->post_message) || !empty($feed->picture))			
				{
					if (empty($feed->post_title) && !empty($feed->story))
						$feed->post_message = $feed->post_title = $feed->story;

					if (!preg_match("/ commented on /i", $feed->post_title) && 
						 !preg_match("/ likes a /i", $feed->post_title)) 
						$fb_feeds[] = $feed;
				}
			}
		}
		
		return $fb_feeds;
	}

	public static function fetch_linkedin_feeds($soc_linkedin)
	{
		$ci =& get_instance();
		$linkedin_config = $ci->conf('linkedin_app');
		
		$linkedin = new Linkedin($linkedin_config['clientId'], $linkedin_config['secret']);
		$linkedin_auth = Social_Linkedin_Auth::find(static::$company_id);

		if (!$linkedin_auth)
			return array();
		
		$linkedin->set_access_token($linkedin_auth->access_token);
		$feeds = $linkedin->get_company_updates($linkedin_auth->linkedin_company_id);

		$feeds = $feeds->values;

		$num_feeds = $ci->conf('num_social_media_feeds_per_type'); 

		if (count ($feeds))
		{
			foreach ($feeds as $cnt => $feed)
			{
				if ($cnt >= $num_feeds)
					break;

				$title = @$feed->updateContent->companyStatusUpdate->share->content->description;

				if (empty($title))
					$title = $feed->updateContent->companyStatusUpdate->share->comment;


				if (!empty($title))
					$title = HTML2Text::plain($title);

				$post_id = $feed->updateContent->companyStatusUpdate->share->id;
				$feed->post_id = $post_id;
				$linux_timestamp = (int) $feed->updateContent->companyStatusUpdate->share->timestamp;
				$linux_timestamp = (int) ($linux_timestamp / 1000);
				$feed->post_date_publish = @date(DATE::FORMAT_MYSQL, $linux_timestamp);
				$feed->post_title = $title;
				$feed->post_content = $title;
				$feed->post_summary = $title;
			}
		}

		return $feeds;
	}


	public static function fetch_gplus_feeds($gplus_id)
	{
		$ci =& get_instance();
		$num_feeds = $ci->conf('num_social_media_feeds_per_type'); 
		$data = Social_GPlus_Feeds::get($gplus_id);
		$feeds = $data->items;
		$g_feeds = array();
		$cnt = 0;

		if (count($feeds))
		{
			foreach ($feeds as $feed)
			{			
				if ($cnt >= $num_feeds)
					break;

				$feed->post_id = $feed->id;
				$feed->post_date_publish = date(DATE::FORMAT_MYSQL, strtotime($feed->published));
				$feed->post_title = HTML2Text::plain($feed->title);
				$feed->post_content = HTML2Text::plain($feed->title);
				$feed->post_summary = HTML2Text::plain($feed->title);
				if (!empty($feed->post_title))
					$g_feeds[] = $feed;
				$cnt++;

			}
		}
		
		return $g_feeds;
	}


	public static function fetch_pinterest_feeds($pinterest_id)
	{
		$ci =& get_instance();
		$num_feeds = $ci->conf('num_social_media_feeds_per_type'); 
		$sxml = Social_Pinterest_Feed::get($pinterest_id);
		$feeds = array();
		if ( ! count($sxml->channel->item))
			return;

		$cnt = 0;
		$dummy = ('NOTHING');
		foreach ($sxml->channel->item as $feed)	
		{			
			if ($cnt >= $num_feeds)
				break;

			$link = $feed->link; // e.g. https://www.pinterest.com/pin/328973947755322948/
			$id = @substr($link, strpos($link, 'pin/')+4);
			$id = (int) $id;
			$feed->post_id = $id;
			$feed->post_date_publish = date(DATE::FORMAT_MYSQL, strtotime($feed->pubDate));
			$feed->post_title = null;
			$feed->post_content = $dummy;
			$feed->post_summary = $dummy;
			$feed->post_description_text = null;

			if (!empty($feed->description))
			{
				$feed->post_description_text = HTML2Text::plain($feed->description);
				$feed->post_title = HTML2Text::plain($feed->description);

				if (preg_match('@<img.*src="([^"]*)"[^>/]*/?>@Ui', $feed->description, $out))
					$feed->picture = $out[1];
			}

			$feeds[] = $feed;
			$cnt++;
		}	
		
		return $feeds;
	}


	public static function fetch_youtube_feeds($youtube_id)
	{
		$ci =& get_instance();
		$num_feeds = $ci->conf('num_social_media_feeds_per_type'); 
		$items = Social_Youtube_Feed::get($youtube_id);

		if ( ! count($items))
			return;

		$feeds = array();
		foreach ($items as $item)
		{	
			if (count($feeds) >= $num_feeds)
				break;

			if ($video_id = @$item['id']['videoId'])
			{
				$feed = new stdClass();
				$feed->post_id = $video_id;
				$feed->post_date_publish = date(DATE::FORMAT_MYSQL, strtotime(@$item['snippet']['publishedAt']));
				$feed->post_title = HTML2Text::plain($item['snippet']['title']);
				$feed->post_content = HTML2Text::plain($item['snippet']['description']);

				$feed->link = "https://youtube.com/watch?v={$video_id}";
				$feed->post_summary = $feed->post_content;
				$feed->description = $feed->post_content;
				$feed->post_description_text = $feed->post_title;

				if (!empty($feed->description))
					$feed->post_description_text = "{$feed->post_description_text} {$feed->description}";

				$feeds[] = $feed;
			}			
		}

		return $feeds;
	}
}
