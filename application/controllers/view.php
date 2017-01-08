<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class View_Controller extends Browse_Base {

	protected $raw_secret = '482f767d755a2c5b96b14285c04ac9c21d0857fe';

	public function index($slug)
	{
		if ($slug === null)
			$this->redirect('browse');
		$m_content = Model_Content::find_slug($slug);
		if (!$m_content) Model_Content_Slug_Redirect::find_and_redirect($slug);
		if (!$m_content) show_404();
		$this->redirect($m_content->url());
	}
	
	public function content($slug = null, $m_content = null)
	{
		$has_internal_caller = (bool) $m_content;
		if (!$m_content && $slug === null)
			$this->redirect('browse');
		
		if (!$m_content && $this->is_detached_host)
			$m_content = Detached_Session::read('m_content');
		
		if (!$m_content) $m_content = Model_Content::find_slug($slug);
		if (!$m_content) Model_Content_Slug_Redirect::find_and_redirect($slug);
		if (!$m_content) show_404();
		
		// url rewrite has not happened
		// => direct access to view/content
		if (!has_url_rewrite('view_content')
			&& !$this->is_detached_host
			&& !$has_internal_caller)
			$this->redirect(gstring($m_content->url()));
		
		if ($this->is_common_host)
		{
			$real_newsroom = Model_Newsroom::find($m_content->company_id);
			if (!$real_newsroom) show_404();
			
			// simulate user as admin
			if (Auth::is_admin_online() 
			    && Auth::user()->id != $real_newsroom->user_id)
				Auth::admo($real_newsroom->user_id);
				
			// is active => redirect to it
			if ($real_newsroom->is_active) 
			{
				if ((new Social_Facebook_Bot_Detection())->is_bot($this->env))
				{
					// pretend that the newsroom 
					// is not active so that we 
					// can load from conanical url
					$real_newsroom->is_active = 0;
				}
				else 
				{
					$relative_url = $this->env['requested_uri'];
					$url = $real_newsroom->url($relative_url, true);
					$url = gstring($url);
					$this->redirect($url, false);
				}
			}
			
			// allowed to view inactive
			if (Auth::is_user_online()
			    && $real_newsroom->user_id == Auth::user()->id
			    && $m_content->type != Model_Content::TYPE_PR)
			{
				$relative_url = $this->env['requested_uri'];
				$url = $real_newsroom->url($relative_url, true);
				$url = gstring($url);
				$this->redirect($url, false);
			}
			
			// inactive newsroom and not a PR => 404
			if ($m_content->type != Model_Content::TYPE_PR)
				show_404();
			
			$this->newsroom = $real_newsroom;
			$this->vd->nr_custom = $this->newsroom->custom();
			$this->vd->nr_profile = $this->newsroom->profile();
			$this->vd->nr_contact = $this->newsroom->contact();
			$company_id = $m_content->company_id;
		}
		
		$company_id = (int) $this->newsroom->company_id;
		if ((int) $m_content->company_id !== $company_id)
			show_404();
		
		$this->vd->m_content = $m_content;
		$this->title = $m_content->title;
		
		$m_content->load_content_data();
		$m_content->load_local_data();

		if (!$m_content->is_published)
		{
			if (!Auth::is_user_online() &&
			     // admo above => never false for admin
			     Auth::user()->id != $this->newsroom->user_id)
				$this->denied();

			// reasons for rejection (if available)
			if (($m_content->is_rejected 
			 || ($m_content->is_under_review && Auth::is_admin_online()))
			 && $reason = Model_Rejection_Data::find($m_content->id))
				$this->vd->rejection_data = $reason->raw_data();

			// find any hold comments (if available)
			if ($m_content->is_under_review && Auth::is_admin_online()
			 && $hold = Model_Hold_Data::find($m_content->id))
				$this->vd->hold_comments = $hold->raw_data()->comments;

			if ($m_content->is_under_review && Auth::is_admin_online())
			{
				$hold_content = new Modal();
				$hold_content->set_title('Hold Content');
				$modal_view = 'admin/partials/hold_content_modal';
				$modal_content = $this->load->view($modal_view, null, true);
				$hold_content->set_content($modal_content);
				$modal_view = 'admin/partials/hold_content_modal_footer';
				$modal_content = $this->load->view($modal_view, null, true);
				$hold_content->set_footer($modal_content);
				$this->add_eob($hold_content->render(500, 300));
				$this->vd->hold_content_modal_id = $hold_content->id;

				// find duplicates based on the hashes found for this content
				$this->vd->duplicates_found = Model_Content_Hash::
					find_duplicates_for_content($m_content->id);
			}
			
			// load feedback message for the user
			$feedback_view = 'browse/view/partials/not_published_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			
			if ($m_content->is_under_writing &&
			    $m_wr_session = Model_Writing_Session::find('content_id', $m_content->id))
			{
				$m_wr_order = Model_Writing_Order::find($m_wr_session->writing_order_id);
				$this->vd->writing_session = $m_wr_session;
				$this->vd->writing_order = $m_wr_order;
				
				if ($m_wr_order->status === Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)
				{
					$comments = Model_Writing_Process::get_latest_message_to_customer($m_wr_order->id);
					$this->vd->editor_comments = $comments;
					
					// load feedback message for the user
					$feedback_view = 'browse/view/partials/under_writing_feedback';
					$feedback = $this->load->view($feedback_view, null, true);
					$this->use_feedback($feedback);
				}
			}
		}
		
		// load additional data (if function exists)
		$view_method = "pre_render_{$m_content->type}";
		if (method_exists($this, $view_method))
			call_user_func(array($this, $view_method), $m_content);
		
		// old content cached for 1 day
		// new content cached for 5 minutes
		$last_modified = $m_content->date_updated
			? $m_content->date_updated
			: $m_content->date_created;
		$dt_last_modified = Date::utc($last_modified);
		$is_old_content = $dt_last_modified < Date::days(-30);
		$this->cache_duration = $is_old_content 
			? 86400 : 300;

		// not on newsroom? 
			// switch columns
		if ($this->is_common_host)
			$this->vd->switched_cols = true;

		if ($m_content->is_scraped_content)
			if ($m_pb_scraped_c = Model_PB_Scraped_Content::find($m_content->id))
				$this->vd->scraped_content_url = $m_pb_scraped_c->source_url;

		$this->load->view('browse/header');
		$this->load->view('browse/view');
		$this->load->view('browse/footer');
	}
	
	public function internal($id)
	{
		if (!$this->_is_internal_redirect())
			show_404();
		
		$m_content = Model_Content::find($id);
		if ($m_content) return $this->content(null, $m_content);
		$this->redirect_301(null);
	}
	
	public function id($id)
	{
		$m_content = Model_Content::find($id);
		if (!$m_content) show_404();
		$this->redirect(gstring($m_content->url()));
	}
	
	public function preview($id)
	{
		$m_content = Model_Content::find($id);
		if (!$m_content) show_404();
		if ($m_content->is_published)
			$this->redirect(gstring($m_content->url()));
		$this->content(null, $m_content);
	}
	
	protected function render_raw($m_content)
	{
		$real_newsroom = Model_Newsroom::find($m_content->company_id);
		if (!$real_newsroom) show_404();
		
		$this->newsroom = $real_newsroom;
		$this->vd->nr_custom = $this->newsroom->custom();
		$this->vd->nr_profile = $this->newsroom->profile();
		$this->vd->nr_contact = $this->newsroom->contact();
		$this->vd->m_content = $m_content;
		$this->title = $m_content->title;
		$m_content->load_content_data();
		$m_content->load_local_data();
		
		$this->load->view('browse/raw');
	}

	public function raw($id)
	{
		if (!$this->is_common_host)
		{
			$url = $this->uri->uri_string;
			$url = $this->common()->url($url);
			$this->redirect($url, false);
		}

		$m_content = Model_Content::find($id);
		if (!$m_content) show_404();
		if (!Auth::is_admin_online() &&
			(!$m_content->is_published ||
		     $m_content->type !== Model_Content::TYPE_PR))
			show_404();
		
		$this->render_raw($m_content);
	}

	public function raw_read($id, $token1, $token2)
	{
		$m_content = Model_Content::find($id);
		if (!$m_content) show_404();

		if (strlen($token1) != 32) return;
		if (strlen($token2) != 32) return;
		
		if (Data_Cache_LT::read($token1) != $token2)
		{
			throw new Exception('incorrect token');
			return;
		}

		$this->render_raw($m_content);
	}
	
	public function pdf($id)
	{
		$token1 = md5(UUID::create());
		$token2 = md5(UUID::create());
		Data_Cache_LT::write($token1, $token2, 60);
		
		$url = sprintf('view/raw/read/%d/%s/%s', 
			$id, $token1, $token2);
		$url = $this->common()->url($url);

		$report = new PDF_Generator($url);
		$report->generate();
		$report->deliver('content.pdf');
	}
	
	protected function pre_render_pr($m_content)
	{
		if (!empty($m_content->source_url))
			$this->redirect($m_content->source_url, false);
	}

	protected function pre_render_news($m_content)
	{
		if (!empty($m_content->source_url))
			$this->redirect($m_content->source_url, false);
	}

	protected function pre_render_blog($m_content)
	{
		if (!empty($m_content->source_url))
			$this->redirect($m_content->source_url, false);
	}

}

?>