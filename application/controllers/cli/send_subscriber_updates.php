<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Send_Subscriber_Updates_Controller extends CLI_Base {

	// index is for sending daily updates
	public function index()
	{	
		$dt_12_hrs_ago = Date::hours(-12)->format(Date::FORMAT_MYSQL);
		$now = Date::$now->format(DATE::FORMAT_MYSQL);

		$mutex = new Flock_Mutex('newsroom_subscriptions');
		$never = Model_Subscription::NOTIFY_NEVER;

		while(true)
		{
			set_time_limit(300);

			$sql = "SELECT s.*, c.email as sub_email,
				cp.*,
				sh.hash
				FROM nr_subscription s
				INNER JOIN nr_contact c
				ON s.contact_id = c.id
				INNER JOIN nr_subscription_hash sh
				ON sh.subscription_id = s.id
				LEFT JOIN nr_company_profile cp
				ON cp.company_id = c.company_id	
				WHERE c.is_nr_subscriber = 1
				AND c.is_unsubscribed = 0
				AND s.is_update_active = 0 
				AND (
					s.notify_pr <> '{$never}'  OR s.notify_news <> '{$never}' OR 
					s.notify_event <> '{$never}' OR s.notify_blog <> '{$never}' OR 
					s.notify_facebook <> '{$never}'  OR s.notify_twitter <> '{$never}'
					)

				AND s.id NOT IN 
					(SELECT subscription_id
						FROM nr_subscription_daily_update_sent 
						GROUP BY subscription_id HAVING 
						max(date_sent) > '{$dt_12_hrs_ago}')
				LIMIT 0, 1";

			$mutex->lock();

			$sub = Model_Company_Profile::from_sql($sql);

			if (!$sub)
			{
				$mutex->unlock();
				break;
			}

			$q_active = "UPDATE nr_subscription
						SET is_update_active = 1
						WHERE id = {$sub->id}";

			$this->db->query($q_active);
			$mutex->unlock();

			$content_types = array();
			
			if ($sub->notify_pr !== Model_Subscription::NOTIFY_NEVER)
				$content_types[] = Model_Content::TYPE_PR;

			if ($sub->notify_news !== Model_Subscription::NOTIFY_NEVER)
				$content_types[] = Model_Content::TYPE_NEWS;

			if ($sub->notify_event !== Model_Subscription::NOTIFY_NEVER)
				$content_types[] = Model_Content::TYPE_EVENT;
			
			if ($sub->is_enable_blog_posts && $sub->soc_rss && 
				$sub->notify_blog !== Model_Subscription::NOTIFY_NEVER)
				$content_types[] = Model_Content::TYPE_BLOG;

			$social_types = array();
			if ($sub->is_enable_social_wire)
				$social_types = $this->refresh_social_content($sub);

			if (count($social_types))
				$content_types[] = Model_Content::TYPE_SOCIAL;

			$results = $this->get_latest_content($content_types, $sub, $social_types);
			
			if (is_array($results) && count($results))
			{
				$prev_results = $this->get_previous_content($content_types, $sub, $social_types);

				if ($dt = $this->get_latest_content_date($results))
				{
					$this->vd->latest_content_date = date("M d, H:i T", $dt);
					$this->send_email($results, $prev_results, $sub);
				}
			}

			$update = new Model_Subscription_Daily_Update_Sent();
			$update->subscription_id = $sub->id;
			$update->date_sent = Date::utc()->format(Date::FORMAT_MYSQL);
			$update->save();
		
			$q_deactive = "UPDATE nr_subscription
						SET is_update_active = 0
						WHERE id = {$sub->id}";

			$this->db->query($q_deactive);
		}
	}

	// This cron is supposed to run every
	// few minutes to send instant updates

	/* 
	public function instant_updates()
	{
		$dt_4_mins_ago = Date::minutes(-4)->format(Date::FORMAT_MYSQL);
		$now = Date::$now->format(DATE::FORMAT_MYSQL);
		
		$mutex = new Flock_Mutex('newsroom_subscriptions_instant');
		$instant = Model_Subscription::NOTIFY_INSTANT;
		while(true)
		{
			set_time_limit(300);

			$sql = "SELECT s.*, c.email as sub_email, 
					c.company_id, sh.hash
					FROM nr_subscription s
					INNER JOIN nr_contact c
					ON s.contact_id = c.id
					INNER JOIN nr_subscription_hash sh
					ON sh.subscription_id = s.id
					WHERE c.is_nr_subscriber = 1
					AND c.is_unsubscribed = 0
					AND s.is_update_active = 0 
					AND (
						s.notify_pr = '{$instant}'  OR 
						s.notify_news = '{$instant}' OR 
						s.notify_event = '{$instant}' OR 
						s.notify_blog = '{$instant}'
						)
					AND s.id NOT IN 
						(SELECT subscription_id
							FROM nr_subscription_instant_update_sent 
							GROUP BY subscription_id HAVING 
							max(date_sent) >= '{$dt_4_mins_ago}')
					LIMIT 0, 1";

			$mutex->lock();

			if (!($sub = $this->db->query($sql)->row())) 
			{
				$mutex->unlock();
				break;
			}

			$q_active = "UPDATE nr_subscription
						SET is_update_active = 1
						WHERE id = {$sub->id}";

			$this->db->query($q_active);
			$mutex->unlock();

			$content_types = array();
			$i_content_types = array(); // instant content types
			
			if ($sub->notify_pr !== Model_Subscription::NOTIFY_NEVER)
			{
				$content_types[] = Model_Content::TYPE_PR;
				if ($sub->notify_pr === Model_Subscription::NOTIFY_INSTANT)
					$i_content_types[] = Model_Content::TYPE_PR;
			}


			if ($sub->notify_news !== Model_Subscription::NOTIFY_NEVER)
			{
				$content_types[] = Model_Content::TYPE_NEWS;
				if ($sub->notify_news === Model_Subscription::NOTIFY_INSTANT)
					$i_content_types[] = Model_Content::TYPE_NEWS;
			}

			if ($sub->notify_event !== Model_Subscription::NOTIFY_NEVER)
			{
				$content_types[] = Model_Content::TYPE_EVENT;
				if ($sub->notify_event === Model_Subscription::NOTIFY_INSTANT)
					$i_content_types[] = Model_Content::TYPE_EVENT;
			}

			$social_m_types = array();
			$is_instant = 1;
			$results = $this->get_latest_content($i_content_types, $sub, $social_m_types, $is_instant);			

			if (is_array($results) && count($results))
			{
				$prev_results = $this->get_previous_content($content_types, $sub, $social_m_types, $is_instant);
				
				$dt = $this->get_latest_content_date($results);
				$this->vd->latest_content_date = date("M d, H:i T", $dt);
				$this->send_email($results, $prev_results, $sub);
			}

			$update = new Model_Subscription_Instant_Update_Sent();
			$update->subscription_id = $sub->id;
			$update->date_sent = Date::$now->format(Date::FORMAT_MYSQL);
			$update->save();

			foreach ($results as $result)
			{
				$update = new Model_Subscription_Content_Update_Sent();
				$update->subscription_id = $sub->id;
				$update->content_id = $result->id;
				$update->date_sent = Date::$now->format(Date::FORMAT_MYSQL);
				$update->save();
			} 

			$q_deactive = "UPDATE nr_subscription
						SET is_update_active = 0
						WHERE id = {$sub->id}";

			$this->db->query($q_deactive);
		}
	}
	*/

	protected function get_latest_content_date($results)
	{
		$dates = array();

		foreach ($results as $result)
			$dates[] = strtotime($result->date_publish);

		if (!count($dates))
			return array();

		return max($dates);

	}

	protected function refresh_social_content($sub)
	{
		// Social media update is required 
		// only if social content wasn't 
		// refreshed in the past hour

		$is_update_required = 1;
		$date_1_hr_ago = Date::hours(-1)->format(Date::FORMAT_MYSQL);

		$sql = "SELECT company_id
				FROM nr_social_wire_update
				WHERE company_id = ?
				AND date_last_manual_request > '{$date_1_hr_ago}'";
		
		if ($row = Model_Social_Wire_Update::from_sql($sql, array($sub->company_id)))
			$is_update_required = 0;

		if ($is_update_required)
		{
			$force_update = 1;
			Social_Wire::update($sub->company_id, Social_Wire::UPDATE_MANUAL, $force_update);
		}

		$social_types = array();
		if ($sub->is_facebook_feed_valid() && $sub->notify_facebook !== Model_Subscription::NOTIFY_NEVER)
			$social_types[] = Model_PB_Social::TYPE_FACEBOOK;
		
		if ($sub->is_twitter_feed_valid() && $sub->notify_twitter !== Model_Subscription::NOTIFY_NEVER)
			$social_types[] = Model_PB_Social::TYPE_TWITTER;	
			
		return $social_types;
	}

	protected function get_latest_content($content_types, $sub, $social_types, $is_instant = 0)
	{
		if (!count($content_types))
			return array();

		$c_types = sql_in_list($content_types);

		$s_types_filter = 1;
		if (is_array($social_types) && count($social_types))
		{
			$s_types = sql_in_list($social_types);
			$s_types_filter = "s.media_type IN ({$s_types})";
		}
		
		if ($is_instant)
			$dt_interval_ago = Date::hours(-1)->format(Date::FORMAT_MYSQL);
		else
			$dt_interval_ago = Date::hours(-24)->format(Date::FORMAT_MYSQL);

		$now = Date::$now->format(DATE::FORMAT_MYSQL);

		$pr = Model_Content::TYPE_PR;
		$news = Model_Content::TYPE_NEWS;
		$event = Model_Content::TYPE_EVENT;
		$blog = Model_Content::TYPE_BLOG;
		$social = Model_Content::TYPE_SOCIAL;

		$sql = "SELECT c.id, c.company_id, c.type,
				c.title AS title, c.slug, c.date_publish,
				cd.summary AS summary, b.source_url,
				s.content_id AS m_pb_social__content_id,
				s.media_type AS m_pb_social__media_type,
				s.raw_data AS m_pb_social__raw_data
				FROM nr_content c
				LEFT JOIN nr_content_data cd
				ON cd.content_id = c.id
				LEFT JOIN nr_pb_blog b
				ON b.content_id = c.id
				LEFT JOIN nr_pb_social s
				ON s.content_id = c.id
				WHERE c.company_id = ?
				AND c.type IN ({$c_types})
				AND c.is_published = 1
				AND c.date_publish BETWEEN
				'{$dt_interval_ago}' AND '{$now}'";
		
		if ($is_instant)
			$sql = "{$sql} AND c.id NOT IN (
						SELECT content_id 
						FROM nr_subscription_content_update_sent
						WHERE subscription_id = {$sub->id})";

		$sql = "{$sql} AND (s.media_type IS NULL OR {$s_types_filter})
				ORDER BY FIELD(c.type, '{$pr}', '{$news}', '{$event}', '{$blog}', '{$social}'), s.media_type";

		$query = $this->db->query($sql, array($sub->company_id));
		$results = Model_Content::from_db_all($query, array('m_pb_social' => 'Model_PB_Social'));

		return $results;
		
	}

	protected function get_previous_content($content_types, $sub, $social_types, $is_instant = 0)		
	{
		if (!count($content_types))
			return array();

		$c_types = sql_in_list($content_types);

		$s_types_filter = 1;
		if (is_array($social_types) && count($social_types))
		{
			$s_types = sql_in_list($social_types);
			$s_types_filter = "s.media_type IN ({$s_types})";
		}

		$dt_24_hrs_ago = Date::hours(-24)->format(Date::FORMAT_MYSQL);
		$dt_1_hrs_ago = Date::hours(-1)->format(Date::FORMAT_MYSQL);

		$pr = Model_Content::TYPE_PR;
		$news = Model_Content::TYPE_NEWS;
		$event = Model_Content::TYPE_EVENT;
		$blog = Model_Content::TYPE_BLOG;
		$social = Model_Content::TYPE_SOCIAL;

		$sql = "SELECT c.company_id, c.type,
				c.title AS title, c.slug, c.date_publish,
				cd.summary AS summary, b.source_url,
				s.content_id AS m_pb_social__content_id,
				s.media_type AS m_pb_social__media_type,
				s.raw_data AS m_pb_social__raw_data
				FROM nr_content c
				LEFT JOIN nr_content_data cd
				ON cd.content_id = c.id
				LEFT JOIN nr_pb_blog b
				ON b.content_id = c.id
				LEFT JOIN nr_pb_social s
				ON s.content_id = c.id
				WHERE c.company_id = ?
				AND	c.type IN ({$c_types})
				AND c.is_published = 1";
		if ($is_instant)
			$sql = "{$sql} AND c.date_publish < '{$dt_1_hrs_ago}' ";
		else
			$sql = "{$sql} AND c.date_publish < '{$dt_24_hrs_ago}' ";
		
		$sql = "{$sql} AND (s.media_type IS NULL OR {$s_types_filter} )				
				ORDER BY date_publish DESC
				LIMIT 0, 5";

		$query = $this->db->query($sql, array($sub->company_id));
		$results = Model_Content::from_db_all($query, array('m_pb_social' => 'Model_PB_Social'));

		return $results;
		
	}

	protected function send_email($results, $prev_results, $sub)
	{
		$ci =& get_instance();
		$newsroom = Model_Newsroom::find_company_id($sub->company_id);		
		$this->vd->nr_custom = Model_Newsroom_Custom::find($sub->company_id);
		$this->vd->newsroom = $newsroom;
		$this->vd->latest_content = $this->prepare_content($results, $sub);
		$this->vd->previous_content = $this->prepare_prev_content($prev_results, $sub);

		$url = "browse/subscribe/edit/{$sub->hash}";
		$this->vd->manage_subscr_link = $newsroom->url($url);

		$url = "browse/subscribe/unsubscribe_all/{$sub->hash}";
		$this->vd->unsub_link = $newsroom->url($url);
	
		$message_view = 'browse/subscribe/emails/daily-update';
		$message = $this->load->view($message_view, null, true);

		$subject = "{$newsroom->company_name} Newsroom Updates";

		$mail = new Email();
		$mail->set_to_email($sub->sub_email);
		$mail->set_from_email($ci->conf('email_address'));
		$mail->set_from_name('Newswire Notification');
		$mail->set_subject($subject);
		$mail->set_message($message);
		$mail->enable_html();
		Mailer::queue($mail, true, Mailer::POOL_MARKETING);
	}

	protected function prepare_content($results, $sub)
	{
		$ci =& get_instance();
		$basic_types = array(Model_Content::TYPE_PR, Model_Content::TYPE_NEWS, Model_Content::TYPE_EVENT);
		$prs = $news = $events = $blog = $fb = $twitter = array();
		foreach ($results as $result)
		{
			$content = new stdClass();
			if (in_array($result->type, $basic_types))
			{
				$content->title = $result->title;
				$content->summary = $result->summary;
				$content->url = $ci->website_url($result->url());
			}

			if ($result->type == Model_Content::TYPE_PR)
				$prs[] = $content;

			if ($result->type == Model_Content::TYPE_NEWS)
				$news[] = $content;

			if ($result->type == Model_Content::TYPE_EVENT)
				$events[] = $content;

			if ($result->type == Model_Content::TYPE_BLOG)
			{
				$content->title = $result->title;
				$content->summary = $result->summary;
				$content->url = $result->source_url;
				$blog[] = $content;
			}

			if ($result->type == Model_Content::TYPE_SOCIAL)
			{
				$raw_data = $result->m_pb_social->raw_data();
				
				if ($result->m_pb_social->media_type == Model_PB_Social::TYPE_FACEBOOK && 
						!empty($raw_data->post_message))
				{
					
					$content->title = $raw_data->post_message;
					$content->summary = $raw_data->post_message;
					$content->url = "http://www.facebook.com/{$sub->soc_facebook}/posts/{$raw_data->post_id}";
					$fb[] = $content;
					
				}

				if ($result->m_pb_social->media_type == Model_PB_Social::TYPE_TWITTER &&
						!empty($raw_data->text))
				{
					$content->title = '';
					$content->summary = $raw_data->text;
					$content->url = "https://twitter.com/{$raw_data->user->screen_name}/status/{$raw_data->id}";
					$twitter[] = $content;
				}
			}
		}

		$content = new stdClass();
		$content->prs = $prs;
		$content->events = $events;
		$content->news = $news;
		$content->blog = $blog;
		$content->fb = $fb;
		$content->twitter = $twitter;
		
		return $content;
	}


	protected function prepare_prev_content($results, $sub)
	{
		$ci =& get_instance();
		$basic_types = array(Model_Content::TYPE_PR, Model_Content::TYPE_NEWS, Model_Content::TYPE_EVENT);

		$contents = new stdClass();
		
		foreach ($results as $result)
		{
			$content = new stdClass();
			if (in_array($result->type, $basic_types))
			{
				$content->title = $result->title;
				$content->url = $ci->website_url($result->url());
			}

			if ($result->type == Model_Content::TYPE_BLOG)
			{
				$content->title = $result->title;
				$content->url = $result->source_url;
			}

			if ($result->type == Model_Content::TYPE_SOCIAL)
			{
				$raw_data = $result->m_pb_social->raw_data();
				
				if ($result->m_pb_social->media_type == Model_PB_Social::TYPE_FACEBOOK && 
						!empty($raw_data->post_message))
				{
					
					$content->title = $result->title;
					$content->url = "http://www.facebook.com/{$sub->soc_facebook}/posts/{$raw_data->post_id}";
					
				}

				if ($result->m_pb_social->media_type == Model_PB_Social::TYPE_TWITTER &&
						!empty($raw_data->text))
				{
					$content->title = $raw_data->text;
					$content->url = "https://twitter.com/{$raw_data->user->screen_name}/status/{$raw_data->id}";
				}
			}

			$content->title = strip_tags(@$content->title);

			$contents->content[] = $content;

		}
		
		return $contents;
	}
	
}

?>