<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Browse_Base extends CIL_Controller {

	protected static $allowed_on_common_host = array(

		'#^$#i', 									/* browse base */
		'#^browse$#i', 							/* browse base */
		'#^browse/all$#i', 						/* browse all content */
		'#^browse/search$#i', 					/* browse search */
		'#^browse/cat#i', 						/* browse categories */
		'#^browse/beat#i', 						/* browse beats */
		'#^browse/rss#i', 						/* browse rss feed */
		'#^browse/tag#i', 						/* browse tags */
		'#^view/[a-z0-9\-]+$#i', 		 		/* view individual content */
		'#^view/content/[a-z0-9\-]+$#i', 	/* view individual content */
		'#^view/internal/[0-9]+$#i', 			/* view individual content (legacy) */
		'#^view/id/[0-9]+$#i', 					/* view individual content (perma) */
		'#^view/preview/[0-9]+$#i', 			/* view individual content (no-slug) */
		'#^view/pdf/[0-9]+$#i', 				/* view individual content (pdf) */
		'#^view/raw/[0-9]+$#i', 				/* view individual content (raw) */
		'#^view/collab/#i', 	               /* view collaborative content */
		'#^view/raw/read/#i', 					/* view individual content (raw read) */
		'#^shared/log(in|out)$#i', 			/* authentication */
		
	);
	
	public function __construct()
	{		
		parent::__construct();

		$this->vd->full_width = false;
		$this->vd->switched_cols = true;
		$this->vd->is_auto_built_unclaimed_nr = false;
		$this->vd->is_browse = true;

		if ($this->is_common_host)
		{
			if (!$this->is_allowed_on_common_host())
				$this->redirect('manage');
			
			if (!$this->is_website_host && 
			    !$this->is_detached_host &&
			    !$this->input->post())
			{
				$url = $this->website_url($this->env['requested_uri']);
				$this->redirect(gstring($url), false);
			}
		}
		else
		{
			if (!$this->newsroom->is_active)
			{
				$scraped_default_user_id = $this->conf('scraped_nr_default_user');

				// if this is a preview (with token)
				// of an auto created newsroom
				if ($this->is_ac_nr_private_preview())
				{
					$this->vd->is_from_private_link = 1;
				}

				elseif ($this->input->get('preview'))
				{
					$criteria = array();
					$criteria[] = array('company_id', $this->newsroom->company_id);
					$criteria[] = array('access_token', $this->input->get('preview'));
					$criteria[] = array('date_expires > ', Date::$now->format(DATE::FORMAT_MYSQL));
					if ($m_pr_token = Model_Newsroom_Preview_Token::find($criteria))
						$this->vd->is_private_preview = 1;
				}

				elseif (Auth::is_user_online() && 
					$this->newsroom->user_id == Auth::user()->id)
				{
					// load feedback message for the user
					$feedback_view = 'browse/partials/newsroom_inactive_feedback';
					$feedback = $this->load->view($feedback_view, null, true);
					$this->use_feedback($feedback);
				}

				// if this is an auto created 
				// inactive newsroom
				elseif ($this->newsroom->is_scraped() && $this->newsroom->user_id == $scraped_default_user_id)
				{
					$this->redirect($this->website_url());
				}
				
				else
				{
					$relative_url = $this->env['requested_uri'];
					$url = $this->website_url($relative_url, true);
					$this->redirect(gstring($url), false);
				}
			}
			
			$this->vd->wide_view = false;
			$this->vd->nr_custom = $this->newsroom->custom();
			$this->vd->nr_profile = $this->newsroom->profile();
			$this->vd->nr_contact = $this->newsroom->contact();
			$this->vd->nr_listed_types = $this->listed_types();
			$this->vd->nr_listed_archives = $this->listed_archives();
			$this->auto_create_nr_claim();

			if ($this->vd->nr_custom)
			     $this->vd->content_type_labels = $this->vd->nr_custom->content_type_labels();
			else $this->vd->content_type_labels = null;
		}
		
		if ($this->is_detached_host)
		{
			if ($nr_custom = Detached_Session::read('nr_custom'))
				$this->vd->nr_custom = $nr_custom;
			if ($nr_profile = Detached_Session::read('nr_profile'))
				$this->vd->nr_profile = $nr_profile;
			if ($nr_contact = Detached_Session::read('nr_contact'))
				$this->vd->nr_contact = $nr_contact;
		}

		$this->add_follow_modal();
	}

	protected function auto_create_nr_claim()
	{
		$statuses = array(Model_Newsroom_Claim::STATUS_CLAIMED, Model_Newsroom_Claim::STATUS_CONFIRMED);
		$status_q = sql_in_list($statuses);

		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array("status IN ({$status_q})");

		if ($this->newsroom->is_scraped())
			if ( ! $m_claim = Model_Newsroom_Claim::find($criteria))
				$this->vd->is_auto_built_unclaimed_nr = 1;
			else
				$this->vd->is_paid_claimed_nr = 1;
	}
	
	protected function is_allowed_on_common_host()
	{
		$uri = $this->uri->uri_string;
		foreach (static::$allowed_on_common_host as $pattern)
			if (preg_match($pattern, $uri)) return true;
		return false;
	}
	
	protected function listed_types()
	{
		$listed_types = new stdClass();
		foreach (Model_Content::allowed_types() as $type)
			$listed_types->{$type} = false;
		
		$sql = "SELECT c.type FROM nr_content c WHERE 
			c.company_id = {$this->newsroom->company_id} 
			AND c.is_published = 1
			GROUP BY c.type";
		
		$results = Model_Base::from_db_all($this->db->query($sql));
		foreach ($results as $result)
			$listed_types->{$result->type} = true;
		
		$listed_types->contact = (bool) 
			(Model_Company_Contact::count_all(array('company_id', 
			$this->newsroom->company_id)) > 1);
			
		foreach ($listed_types as $listed)
			if ($listed) return $listed_types;
		return false;
	}
	
	protected function listed_archives()
	{
		$listed_archives = array();
		$dt_ranges[] = Date::in(Date::$now->format('Y-M-01 00:00:00'));
		$dt_ranges[] = Date::months(-1, end($dt_ranges));
		$dt_ranges[] = Date::months(-1, end($dt_ranges));
		$dt_ranges[] = Date::months(-1, end($dt_ranges));
		$dt_ranges[] = Date::months(-1, end($dt_ranges));
		
		for ($i = count($dt_ranges) - 1; $i > 0; $i--)
		{
			$date_start = $dt_ranges[$i]->format(Date::FORMAT_MYSQL);
			$date_end = $dt_ranges[$i-1]->format(Date::FORMAT_MYSQL);
			
			$sql = "SELECT 1 FROM nr_content c WHERE 
			 	c.company_id = {$this->newsroom->company_id} 
			 	AND c.is_published = 1 AND c.date_publish >= ? 
			 	AND c.date_publish <= ? LIMIT 1";
			 	
			$dbr = $this->db->query($sql, array($date_start, $date_end));
			if ($dbr->result()) $listed_archives[] = $dt_ranges[$i];
		}
		
		return array_reverse($listed_archives);
	}

	protected function add_follow_modal()
	{
		$modal = new Modal();
		$modal->set_content_view('browse/partials/follow_content');
		$modal->set_header_view('browse/partials/follow_header');
		$modal->set_id('follow-modal');
		$this->vd->follow_modal_id = $modal->id;
		$this->add_eob($modal->render(540, 580));
	}

	protected function is_ac_nr_private_preview()
	{
		if (!empty($this->ac_nr_preview_token))
		{
			$criteria = array();
			$criteria[] = array('company_id', $this->newsroom->company_id);
			$criteria[] = array('token', $this->ac_nr_preview_token);
			if ($token = Model_Newsroom_Claim_Token::find($criteria))
				$this->session->set('ac_nr_tokened_visit_nr_id', $this->newsroom->company_id);
		}

		if ($sess_tokened_visit_nr_id = $this->session->get('ac_nr_tokened_visit_nr_id'))
		{
			if ($sess_tokened_visit_nr_id == $this->newsroom->company_id)
				return true;

		}

		return false;	
	}
}