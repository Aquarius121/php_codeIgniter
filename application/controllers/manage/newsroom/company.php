<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Company_Controller extends Manage_Base {

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Company Newsroom';
		$this->vd->title[] = 'Company Profile';
	}
	
	public function index()
	{		
		$vd = array();
		$order = array('name', 'asc');
		$criteria = array('is_common', 1);
		$vd['common_countries'] = Model_Country::find_all($criteria, $order);
		$vd['countries'] = Model_Country::find_all(null, $order);
		
		$company_id = $this->newsroom->company_id;
		$beats = Model_Beat::list_all_beats_by_group();
		$this->config->load('timezones', false);
		$this->vd->common_timezones = $this->config->item('common_timezones');
		$this->vd->timezones = DateTimeZone::listIdentifiers();
		$this->vd->beats = $beats;
		
		$this->vd->name = $this->newsroom->company_name;
		$profile = Model_Company_Profile::find($company_id);
		$this->vd->profile = $profile;
		if ($nr_custom = Model_Newsroom_Custom::find($this->newsroom->company_id))
			$this->vd->nr_custom = $nr_custom;

		$twitter_auth = Social_Twitter_Auth::find($this->newsroom->company_id);
		if ($twitter_auth && $twitter_auth->is_valid())
			$this->vd->is_twitter_auth = 1;
		else
			$this->vd->is_twitter_auth = 0;

		$info_modal = new Modal();
		$this->add_eob($info_modal->render(700, 480));
		$this->vd->info_modal_id = $info_modal->id;
		
		$this->load->view('manage/header');
		$this->load->view('manage/newsroom/company', $vd);
		$this->load->view('manage/footer');
	}

	public function save()
	{
		$post = $this->input->post();
		$company_id = $this->newsroom->company_id;
		$post['email'] = strtolower($post['email']);
		$post['description'] = $this->vd->pure($post['description']);
		
		foreach ($post as &$data)
			$data = value_or_null($data);
		
		$this->newsroom->company_name = $post['company_name'];
		$timezone = $post['timezone'];
		$timezones = DateTimeZone::listIdentifiers();
		if (in_array($timezone, $timezones))
			$this->newsroom->timezone = $timezone;
		
		$profile = Model_Company_Profile::find($company_id);
		if (!$profile) $profile = new Model_Company_Profile();
		$profile->company_id = $company_id;
		$profile->values($post);
		$profile->clean_soc();

		$profile->is_enable_blog_posts = $this->input->post('is_enable_blog_posts');
		$profile->is_enable_rss_news = $this->input->post('is_enable_rss_news');

		$profile->soc_rss = value_or_null($this->input->post('soc_rss'));
		$profile->rss_news_url = value_or_null($this->input->post('rss_news_url'));
		
		if ($this->input->post('is_preview'))
		{
			Detached_Session::write('nr_profile', $profile);
			$preview_url = Detached_Session::save($this->newsroom);
			$this->redirect($preview_url, false);
		}
		else
		{
			
			$user = Auth::user();
			$criteria = array();
			$criteria[] = array('user_id', $user->id);
			$criteria[] = array('company_name', $post['company_name']);
			$criteria[] = array('company_id !=', $company_id);		

			if ($newsroom = Model_Newsroom::find($criteria))
			{
				$feedback = new Feedback('error');
				$feedback->set_title('Error!');
				$feedback->set_text("Another company with the same name already exists in your account.");
				$this->add_feedback($feedback);
				$this->redirect('manage/newsroom/company');
			}		

			// update the dashboard progress bar 
			Model_Bar::done('dashboard', 'company-details');
			
			// load feedback message for the user
			$feedback = new Feedback('success', 'Saved!', 'The information has been saved.');
			$this->add_feedback($feedback);
			
			$this->newsroom->save();

			$del_blog_posts = 1;
			if ($profile->soc_rss /* assumed to be enabled if has value */)
			{
				$rss_reader = new RSS_Reader();
				$http = new HTTP_Request($profile->soc_rss);
				$response = $http->get();

				if (!$response || !$rss_reader->is_valid_string($response->data))
				{					
					$feedback_text = 'We were unable to validate the RSS URL: ' . $profile->soc_rss;
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$profile->soc_rss = null;
				}
				else
				{
					$blog_feed_reader = new Feed_Reader_Blog($profile);
					$blog_feed_reader->update(true);
					$del_blog_posts = 0;
				}
			}

			if ($del_blog_posts)
			{
				$blog_feed_reader = new Feed_Reader_Blog($profile);
				$blog_feed_reader->delete_older_blog_posts();
			}


			if ($profile->rss_news_url
				&& $profile->is_enable_rss_news)
			{
				$rss_reader = new RSS_Reader();
				if (!$rss_reader->is_valid_file($profile->rss_news_url))
				{
					$feedback_text = 'We were unable to validate the RSS URL: ' . $profile->rss_news_url;
					$feedback = new Feedback('warning', 'Warning!', $feedback_text);
					$this->add_feedback($feedback);
					$profile->rss_news_url = null;
				}
				else
				{
					$news_feed_reader = new Feed_Reader_News($profile);
					$news_feed_reader->update(true);
				}
			}

			$profile->save();
		}
		
		// redirect back to the company details
		$redirect_url = 'manage/newsroom/company';
		$this->set_redirect($redirect_url);
	}
	
}

?>