<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
lib_autoload('linkedin');

class Social_Controller extends Manage_Base {

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Company Newsroom';
		$this->vd->title[] = 'Social Settings';
	}

	public function index()
	{
		$company_id = $this->newsroom->company_id;
		$profile = Model_Company_Profile::find($company_id);

		$social_wire_settings = new stdClass();

		if (!$profile)
			$profile = new Model_Company_Profile();

		$social_wire_settings = $profile->raw_data_object('social_wire_settings');
		$this->vd->social_wire_settings = $social_wire_settings;
		$this->vd->profile = $profile;
		
		if ($nr_custom = Model_Newsroom_Custom::find($this->newsroom->company_id))
			$this->vd->nr_custom = $nr_custom;

		$this->vd->is_twitter_auth = 0;
		$twitter_auth = Social_Twitter_Auth::find($this->newsroom->company_id);
		if ($twitter_auth && $twitter_auth->is_valid())
		{
			$this->vd->is_twitter_auth = 1;
			$this->vd->twitter_name = $twitter_auth->username;
		}

		$this->vd->is_linkedin_auth = 0;
		$linkedin_auth = Social_Linkedin_Auth::find($this->newsroom->company_id);
		if ($profile->soc_linkedin && $linkedin_auth && $linkedin_auth->is_valid())
			$this->vd->is_linkedin_auth = 1;

		if ($facebook_auth = Social_Facebook_Auth::find($this->newsroom->company_id))
			$this->vd->facebook_name = Social_Facebook_Profile::name($facebook_auth);

		$this->vd->facebook_auth = $facebook_auth;

		if ($facebook_auth && $facebook_auth->is_valid())
		{
			$pages = $facebook_auth->page_list();
			$this->vd->facebook_pages = $pages;
			$this->vd->is_facebook_auth = 1;
		}
		else
		{
			$this->vd->is_facebook_auth = 0;
		}

		$info_modal = new Modal();
		$this->add_eob($info_modal->render(600, 460));
		$this->vd->info_modal_id = $info_modal->id;

		$settings_modal = new Modal();
		$this->add_eob($settings_modal->render(450, 480));
		$this->vd->settings_modal_id = $settings_modal->id;

		$this->load->view('manage/header');
		$this->load->view('manage/newsroom/social');
		$this->load->view('manage/footer');
	}


	public function save()
	{
		$post = $this->input->post();
		$company_id = $this->newsroom->company_id;
		
		foreach ($post as &$data)
			$data = value_or_null($data);
		
		$social_wire_settings = new stdClass();
		$profile = Model_Company_Profile::find($company_id);
		if (!$profile) $profile = new Model_Company_Profile();
		$social_wire_settings = $profile->raw_data_object('social_wire_settings');

		$profile->company_id = $company_id;
		$profile->values($post);
		$profile->clean_soc();

		$profile->is_enable_social_wire = $this->input->post('is_enable_social_wire');
		$profile->is_twitter_english_feeds = $this->input->post('is_twitter_english_feeds');

		if ($this->input->post('is_preview'))
		{
			Detached_Session::write('nr_profile', $profile);
			$preview_url = Detached_Session::save($this->newsroom);
			$this->redirect($preview_url, false);
		}
		else
		{
			// load feedback message for the user
			$feedback = new Feedback('success', 'Saved!', 'The information has been saved.');
			$this->add_feedback($feedback);
			
			$this->newsroom->save();
			$remove_soc_med = array();

			if (($profile->has_modified_value('soc_facebook') || !$social_wire_settings->soc_facebook_is_feed_valid) && $profile->soc_facebook)
			{
				if (Social_Facebook_Feed::is_valid($profile->soc_facebook))
				{
					$social_wire_settings->soc_facebook_is_feed_valid = 1;

					// Updating the fb feeds for the nr
					$facebook_feeds = Social_Wire::fetch_facebook_feeds($profile);
					Social_Wire::update_feeds_in_db($company_id, $facebook_feeds, Model_PB_Social::TYPE_FACEBOOK);
				}
				else
				{
					$feedback_text = "We were unable to validate the following social media account: 
						{$profile->soc_facebook} (Facebook)";
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$social_wire_settings->soc_facebook_is_feed_valid = 0;
					$remove_soc_med[] = Model_PB_Social::TYPE_FACEBOOK;
				}			
			}

			if (($profile->has_modified_value('soc_twitter') || !$social_wire_settings->soc_twitter_is_feed_valid) && $profile->soc_twitter)
			{
				if (Social_Twitter_Feed::is_valid($profile->soc_twitter))
				{
					$social_wire_settings->soc_twitter_is_feed_valid = 1;
					$twitter_feeds = Social_Wire::fetch_twitter_feeds($profile);
					Social_Wire::update_feeds_in_db($company_id, $twitter_feeds, Model_PB_Social::TYPE_TWITTER);
				}
				else				
				{
					$feedback_text = "We were unable to validate the following social media account: 
						{$profile->soc_twitter} (Twitter)";
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$social_wire_settings->soc_twitter_is_feed_valid = 0;
					$remove_soc_med[] = Model_PB_Social::TYPE_TWITTER;
				}	
			}			

			if (($profile->has_modified_value('soc_gplus') || !$social_wire_settings->soc_gplus_is_feed_valid) && $profile->soc_gplus)
			{
				if (Social_GPlus_Feeds::is_valid($profile->soc_gplus))
				{
					$social_wire_settings->soc_gplus_is_feed_valid = 1;
					$gplus_feeds = Social_Wire::fetch_gplus_feeds($profile->soc_gplus);
					Social_Wire::update_feeds_in_db($company_id, $gplus_feeds, Model_PB_Social::TYPE_GPLUS);
				}
				else	
				{
					$feedback_text = "We were unable to validate the following social media account: 
						{$profile->soc_gplus} (Google Plus)";
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$social_wire_settings->soc_gplus_is_feed_valid = 0;
					$remove_soc_med[] = Model_PB_Social::TYPE_GPLUS;
				}
			}

			if (($profile->has_modified_value('soc_youtube') || !$social_wire_settings->soc_youtube_is_feed_valid) && $profile->soc_youtube)
			{
				if (Social_Youtube_Feed::is_valid($profile->soc_youtube))
				{
					$social_wire_settings->soc_youtube_is_feed_valid = 1;
					$youtube_feeds = Social_Wire::fetch_youtube_feeds($profile->soc_youtube);
					Social_Wire::update_feeds_in_db($company_id, $youtube_feeds, Model_PB_Social::TYPE_YOUTUBE);
					$pattern = '#^(https?://|)(www\.|)([a-z\-\.]+\.)?youtube\.com/(channel/?[a-z0-9\_\-]+)#is';
					if (preg_match($pattern, $this->input->post('soc_youtube'), $match))
					     $profile->soc_youtube_is_channel = 1;
					else $profile->soc_youtube_is_channel = 0;
				}
				else
				{
					$feedback_text = "We were unable to validate the following social media account: 
						{$profile->soc_youtube} (Youtube)";
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$social_wire_settings->soc_youtube_is_feed_valid = 0;
					$remove_soc_med[] = Model_PB_Social::TYPE_YOUTUBE;
				}	
			}

			if (($profile->has_modified_value('soc_pinterest') || !$social_wire_settings->soc_pinterest_is_feed_valid) && $profile->soc_pinterest)
			{
				if (Social_Pinterest_Feed::is_valid($profile->soc_pinterest))
				{
					$social_wire_settings->soc_pinterest_is_feed_valid = 1;
					$pinterest_feeds = Social_Wire::fetch_pinterest_feeds($profile->soc_pinterest);
					Social_Wire::update_feeds_in_db($company_id, $pinterest_feeds, 
							Model_PB_Social::TYPE_PINTEREST);
				}
				else
				{
					$feedback_text = "We were unable to validate the following social media account: 
						{$profile->soc_pinterest} (Pinterest)";
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$social_wire_settings->soc_pinterest_is_feed_valid = 0;
					$remove_soc_med[] = Model_PB_Social::TYPE_PINTEREST;
				}
			}

			if (($profile->has_modified_value('soc_vimeo') || !$social_wire_settings->soc_vimeo_is_feed_valid) && $profile->soc_vimeo)
			{
				if (Social_Vimeo_Feed::is_valid($profile->soc_vimeo))
				{
					$social_wire_settings->soc_vimeo_is_feed_valid = 1;
					$soc_vimeo_feeds = Social_Wire::fetch_vimeo_feeds($profile->soc_vimeo);
					Social_Wire::update_feeds_in_db($company_id, $soc_vimeo_feeds, 
							Model_PB_Social::TYPE_VIMEO);
				}
				else
				{
					$feedback_text = "We were unable to validate the following social media account: 
						{$profile->soc_vimeo} (Vimeo)";
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$social_wire_settings->soc_vimeo_is_feed_valid = 0;
					$remove_soc_med[] = Model_PB_Social::TYPE_VIMEO;
				}
			}

			if (($profile->has_modified_value('soc_instagram') || !$social_wire_settings->soc_instagram_is_feed_valid) && $profile->soc_instagram)
			{
				if (Social_Instagram_Feed::is_valid($profile->soc_instagram))
				{
					$social_wire_settings->soc_instagram_is_feed_valid = 1;
					$soc_instagram_feeds = Social_Wire::fetch_instagram_feeds($profile->soc_instagram);
					Social_Wire::update_feeds_in_db($company_id, $soc_instagram_feeds, 
							Model_PB_Social::TYPE_INSTAGRAM);
				}					
				else
				{
					$feedback_text = "We were unable to validate the following social media account: 
						{$profile->soc_instagram} (Instagram)";
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$social_wire_settings->soc_instagram_is_feed_valid = 0;
					$remove_soc_med[] = Model_PB_Social::TYPE_INSTAGRAM;
				}
			}

			if ($profile->has_modified_value('soc_linkedin') && $profile->soc_linkedin)
			{
				$linkedin_feeds = Social_Wire::fetch_linkedin_feeds($profile->soc_linkedin);
				Social_Wire::update_feeds_in_db($company_id, $linkedin_feeds, Model_PB_Social::TYPE_LINKEDIN);
			}

			$social_wire_settings->is_inc_twitter_in_soc_wire = (int) ($this->input->post('is_inc_twitter_in_soc_wire')
				&& $profile->soc_twitter && $social_wire_settings->soc_twitter_is_feed_valid);

			$social_wire_settings->is_inc_facebook_in_soc_wire = (int) ($this->input->post('is_inc_facebook_in_soc_wire') 
				&& $profile->soc_facebook && $social_wire_settings->soc_facebook_is_feed_valid);

			$social_wire_settings->is_inc_gplus_in_soc_wire = (int) ($this->input->post('is_inc_gplus_in_soc_wire') 
				&& $profile->soc_gplus && $social_wire_settings->soc_gplus_is_feed_valid);

			$social_wire_settings->is_inc_youtube_in_soc_wire = (int) ($this->input->post('is_inc_youtube_in_soc_wire') 
				&& $profile->soc_youtube && $social_wire_settings->soc_youtube_is_feed_valid);

			$social_wire_settings->is_inc_pinterest_in_soc_wire = (int) ($this->input->post('is_inc_pinterest_in_soc_wire') 
				&& $profile->soc_pinterest && $social_wire_settings->soc_pinterest_is_feed_valid);

			$social_wire_settings->is_inc_vimeo_in_soc_wire = (int) ($this->input->post('is_inc_vimeo_in_soc_wire') 
				&& $profile->soc_vimeo && $social_wire_settings->soc_vimeo_is_feed_valid);

			$social_wire_settings->is_inc_instagram_in_soc_wire = (int) ($this->input->post('is_inc_instagram_in_soc_wire') 
				&& $profile->soc_instagram && $social_wire_settings->soc_instagram_is_feed_valid);

			$social_wire_settings->is_inc_linkedin_in_soc_wire = (int) ($this->input->post('is_inc_linkedin_in_soc_wire') 
				&& $profile->soc_linkedin);

			$profile->raw_data_write('social_wire_settings', $social_wire_settings);
			$profile->save();

			$soc_media = Model_PB_Social::social_media();
			
			foreach ($soc_media as $soc_med)
			{
				$soc_field = "soc_{$soc_med}";
				if (empty($profile->{$soc_field}))
					$remove_soc_med[] = $soc_med;
			}

			if (is_array($remove_soc_med) && count($remove_soc_med))
			{
				$remove_med_str = sql_in_list($remove_soc_med);
				$sql = "SELECT c.* 
						FROM nr_content c
						INNER JOIN nr_pb_social pb
						ON pb.content_id = c.id
						WHERE c.company_id = ?
						AND pb.media_type IN ({$remove_med_str})";

				$results = Model_Content::from_sql_all($sql, array($this->newsroom->company_id));
				foreach ($results as $result)
					$result->delete();
			}
		}
		
		$redirect_url = 'manage/newsroom/social';
		$this->set_redirect($redirect_url);
	}

	public function auth_poll($media)
	{
		if (empty($media))
			return;

		if ($media == Model_PB_Social::TYPE_TWITTER)
			return $this->twitter_auth_poll();

		if ($media == Model_PB_Social::TYPE_FACEBOOK)
			return $this->facebook_auth_poll();

		if ($media == Model_PB_Social::TYPE_LINKEDIN)
			return $this->linkedin_auth_poll();
	}

	public function settings_modal($media)
	{
		if (empty($media))
			return;

		if ($media == Model_PB_Social::TYPE_TWITTER)
			return $this->twitter_settings_modal();

		if ($media == Model_PB_Social::TYPE_FACEBOOK)
			return $this->facebook_settings_modal();

		if ($media == Model_PB_Social::TYPE_LINKEDIN)
			return $this->linkedin_settings_modal();
	}

	public function start_auth($media)
	{
		if (empty($media))
			return;

		if ($media == Model_PB_Social::TYPE_TWITTER)
			$this->twitter_start();

		if ($media == Model_PB_Social::TYPE_FACEBOOK)
			$this->facebook_start();

		if ($media == Model_PB_Social::TYPE_LINKEDIN)
			$this->linkedin_start();
	}

	public function delete_auth($media)
	{
		if (empty($media))
			return;

		if ($media == Model_PB_Social::TYPE_TWITTER)
			return $this->twitter_delete();

		if ($media == Model_PB_Social::TYPE_FACEBOOK)
			return $this->facebook_delete();

		if ($media == Model_PB_Social::TYPE_LINKEDIN)
			return $this->linkedin_delete();
	}
	

	public function twitter_auth_poll()
	{
		$response = new stdClass();
		$twitter_auth = Social_Twitter_Auth::find($this->newsroom->company_id);
		if ($twitter_auth && $twitter_auth->is_valid())
		{
			$response->is_auth = 1;
			$response->social_name = $twitter_auth->username;
		}

		return $this->json($response);
	}

	public function facebook_auth_poll()
	{
		$response = new stdClass();
		$facebook_auth = Social_Facebook_Auth::find($this->newsroom->company_id);
		if ($facebook_auth && $facebook_auth->is_valid())
		{
			$response->is_auth = 1;
			$response->social_name = Social_Facebook_Profile::name($facebook_auth);
		}

		return $this->json($response);
	}

	public function linkedin_auth_poll()
	{
		$response = new stdClass();
		$linkedin_auth = Social_Linkedin_Auth::find($this->newsroom->company_id);
		if ($linkedin_auth && $linkedin_auth->is_valid())
		{
			$response->is_auth = 1;

			// Now updating the db
			if ($linkedin_company_id = $this->session->get('linkedin_company_id'))
			{
				$nr = Model_Company_Profile::find($this->newsroom->company_id);
				$nr->soc_linkedin = $linkedin_company_id;
				$nr->save();

				$linkedin_feeds = Social_Wire::fetch_linkedin_feeds($soc_linkedin);
				Social_Wire::update_feeds_in_db($this->newsroom->company_id, $linkedin_feeds, Model_PB_Social::TYPE_LINKEDIN);
			}
		}

		return $this->json($response);
	}

	public function twitter_settings_modal()
	{
		$twitter_auth = Social_Twitter_Auth::find($this->newsroom->company_id);
		$this->vd->twitter_auth = $twitter_auth;
		if ($twitter_auth) $twitter_auth->test();
		$this->vd->twitter_name = @$twitter_auth->username;

		return $this->load->view('manage/newsroom/partials/social_twitter_settings');
	}

	public function facebook_settings_modal()
	{
		$facebook_auth = Social_Facebook_Auth::find($this->newsroom->company_id);
		$this->vd->facebook_auth = $facebook_auth;
		if ($facebook_auth) $facebook_auth->test();

		$this->vd->facebook_name = Social_Facebook_Profile::name($facebook_auth);
		$this->vd->facebook_pages = array();
		
		if ($facebook_auth && $facebook_auth->is_valid())
		{
			$pages = $facebook_auth->page_list();
			usort($pages, function($a, $b) {
				if ($a->name < $b->name) return -1;
				if ($a->name > $b->name) return 1;
				return 0;
			});

			$this->vd->facebook_pages = $pages;
		}

		return $this->load->view('manage/newsroom/partials/social_facebook_settings');
	}

	public function linkedin_settings_modal()
	{
		$linkedin_auth = Social_Linkedin_Auth::find($this->newsroom->company_id);
		$c_profile = Model_Company_Profile::find($this->newsroom->company_id);
		$this->vd->c_profile = $c_profile;
		
		$this->vd->linkedin_auth = $linkedin_auth;

		$ci =& get_instance();
		$linkedin_config = $ci->conf('linkedin_app');
		
		$linkedin = new Linkedin($linkedin_config['clientId'], $linkedin_config['secret']);

		$linkedin->set_access_token($linkedin_auth->access_token);
		$linkedin_companies = $linkedin->get_user_companies();

		$this->vd->linkedin_companies = $linkedin_companies->values;
		
		return $this->load->view('manage/newsroom/partials/social_linkedin_settings');
	}
	
	public function facebook_page()
	{
		$page = $this->input->post('page');
		$facebook_auth = Social_Facebook_Auth::find($this->newsroom->company_id);
		if ($facebook_auth) $facebook_auth->set_page($page);
		$this->redirect('manage/newsroom/social');
	}

	public function linkedin_company()
	{
		$linkedin_company_id = $this->input->post('linkedin_company_id');
		$linkedin_auth = Social_Linkedin_Auth::find($this->newsroom->company_id);
		if ($linkedin_auth)
		{
			$linkedin_auth->set_company($linkedin_company_id);
			
			$c_profile = Model_Company_Profile::find($this->newsroom->company_id);
			$linkedin_feeds = Social_Wire::fetch_linkedin_feeds($c_profile->soc_linkedin);
			Social_Wire::update_feeds_in_db($this->newsroom->company_id, $linkedin_feeds, Model_PB_Social::TYPE_LINKEDIN);
		}
	}
	
	public function facebook_delete()
	{
		$facebook_auth = Social_Facebook_Auth::find($this->newsroom->company_id);
		if ($facebook_auth) $facebook_auth->delete();
		$response = new stdClass();
		$response->is_deleted = 1;
		return $this->json($response);
	}
	
	public function twitter_delete()
	{
		$twitter_auth = Social_Twitter_Auth::find($this->newsroom->company_id);
		if ($twitter_auth) $twitter_auth->delete();
		$response = new stdClass();
		$response->is_deleted = 1;
		return $this->json($response);
	}

	public function linkedin_delete()
	{
		$linkedin_auth = Social_Linkedin_Auth::find($this->newsroom->company_id);
		if ($linkedin_auth) $linkedin_auth->delete();
		$response = new stdClass();
		$response->is_deleted = 1;
		return $this->json($response);
	}
	
	public function facebook_start()
	{
		$common = $this->conf('common_host');
		$params = array('newsroom' => $this->newsroom->name);
		$params = http_build_query($params);
		$prefix = "http://{$common}/common/";
		$url = "{$prefix}facebook_auth_request?{$params}";
		$this->redirect($url, false);
	}
	
	public function twitter_start()
	{
		$common = $this->conf('common_host');
		$params = array('newsroom' => $this->newsroom->name);
		$params = http_build_query($params);
		$prefix = "http://{$common}/common/";
		$url = "{$prefix}twitter_auth_request?{$params}";
		$this->redirect($url, false);
	}

	public function linkedin_start()
	{
		$linkedin_company_id = $this->input->get('social_id');
		$this->session->set('linkedin_company_id', $linkedin_company_id);

		$common = $this->conf('common_host');
		$params = array('newsroom' => $this->newsroom->name);
		$params = http_build_query($params);
		$prefix = "http://{$common}/common/";
		$url = "{$prefix}linkedin_auth_request?{$params}";
		$this->redirect($url, false);
	}

	public function linkedin_not_company_admin()
	{
		$this->load->view('manage/newsroom/partials/auth_linkedin_not_company_admin');	
	}

	public function auth_complete()
	{
		$this->load->view('manage/newsroom/partials/auth_complete');
	}
	
}

?>