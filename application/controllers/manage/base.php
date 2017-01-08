<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manage_Base extends CIL_Controller {
	
	protected $inherited_access = false;
	protected $set_allowed_on_all_host = false;
	protected $set_required_common_host = false;
	
	protected static $allowed_on_all_host = array(
	
		'#^manage/?$#i', 							/* base */ 
		'#^manage/account#i', 					/* account */
		'#^manage/image#i', 						/* image upload */
		'#^manage/order#i', 						/* order system */
		'#^manage/upgrade#i', 					/* upgrade account */
		'#^manage/insights#i',					/* competitor insights feature */
		'#^manage/tutorial#i',					/* tutorial videos */
		
	);
	
	protected static $requires_common_host = array(
		
		'#^manage/companies#i', 			/* companies */
		'#^manage/overview#i', 				/* overview */
		
	);
	
	protected static $allowed_as_free_user = array(
	   
		'#^manage/?$#i', 								/* base */
		'#^manage/account#i', 						/* account settings */
		'#^manage/analyze/?$#i', 					/* analyze base */
		'#^manage/analyze/content#i', 			/* analyze content */
		'#^manage/analyze/overall#i', 			/* analyze active newsroom */
		'#^manage/analyze/email#i', 				/* analyze email */
		'#^manage/analyze/settings#i', 			/* analyze settings */
		'#^manage/companies#i', 					/* companies */
		'#^manage/contact#i',	 					/* contact all */
		'#^manage/dashboard#i', 					/* dashboard */
		'#^manage/image#i', 							/* image upload */
		'#^manage/newsroom/?$#i',					/* newsroom base */
		'#^manage/newsroom/company#i',			/* newsroom company details */
		'#^manage/newsroom/contact#i',			/* newsroom contacts */
		'#^manage/newsroom/customize#i',			/* newsroom customization */
		'#^manage/newsroom/social#i',				/* newsroom social auth */
		'#^manage/order#i', 							/* order system */
		'#^manage/overview/dashboard#i', 		/* overview dashboard */
		'#^manage/publish/?$#i', 					/* ipublish base */
		'#^manage/publish/audio#i', 				/* ipublish audio */
		'#^manage/publish/event#i', 				/* ipublish events */
		'#^manage/publish/image#i', 				/* ipublish images */
		'#^manage/publish/news#i', 				/* ipublish news releases */
		'#^manage/publish/pr#i', 					/* ipublish press releases */
		'#^manage/publish/search#i', 				/* ipublish search */
		'#^manage/publish/video#i', 				/* ipublish videos */
		'#^manage/publish/common#i', 				/* ipublish common */
		'#^manage/publish/collab#i', 				/* ipublish collaboration */
		'#^manage/upgrade#i', 						/* upgrade account */
		'#^manage/writing#i', 						/* pr writing */
		
	);

	// a list of url patterns that are allowed 
	// by the default company (for use by admin)
	protected static $allowed_as_default_company = array(

		'#^manage/publish/[a-z0-9_]+/edit#i', 		/* edit content */
		'#^manage/publish/[a-z0-9_]+/delete#i', 	/* delete content */
		'#^manage/contact/contact/edit#i', 			/* edit contact */
		'#^manage/contact/contact/delete#i', 		/* delete contact */
		'#^manage/image#i', 								/* image upload */
		
	);

	protected function admo_or_common_url($url)
	{
		if (Auth::is_admin_online())
		     return Admo::url($url);
		else return $this->common()->url($url);
	}
	
	public function __construct()
	{
		parent::__construct();

		$this->vd->is_manage = true;
		$is_common_host = $this->is_common_host;
		$use_common_host = $this->requires_common_host();

		if ($this->is_fallback_server() && Auth::is_admin_online())
		{
			$feedback = new Feedback('alternative');
			$feedback->set_text('Fallback server active. ');
			$feedback->add_text('Please record all changes.');
			$this->use_feedback($feedback);
		}
		
		if (!$this->allowed_on_all_host())
		{
			if (!$is_common_host && $use_common_host)
			{
				$url = $this->env['requested_uri'];
				$url = $this->admo_or_common_url($url);
				$url = gstring($url);
				$this->redirect($url, false);
			}
			
			if ($is_common_host && !$use_common_host)
			{
				// find a newsroom => create newsroom on fail?
				$newsroom = Auth::user()->default_newsroom();
				$this->redirect(gstring($newsroom->url($this->env['requested_uri'])), false);
			}
		}
		
		// if on common host then we should use website host
		// but don't redirect if there is post data or if 
		// we are viewing a detached host

		if ($is_common_host && !$this->is_website_host && 
		    !$this->is_detached_host && !$this->is_admo_host &&
		    !$this->input->post())
		{
			$url = $this->website_url($this->env['requested_uri']);
			$this->redirect(gstring($url), false);
		}
		
		if (Auth::user()->is_free_user() && !$this->inherited_access)
		{
			if (!$this->is_allowed_as_free_user())
				$this->redirect('manage/upgrade/premium');
		}
		
		if ($this->newsroom->is_archived && 
		    !Auth::is_from_secret() && 
		    !Auth::is_admin_online() &&
		    !Auth::user()->is_reseller)
		{
			$url = $this->admo_or_common_url('manage/dashboard');
			$this->redirect(gstring($url), false);
		}
		
		$this->check_for_offline_conversions();
		
		$this->vd->is_user_panel = true;
		$this->vd->bar = new Model_Bar('dashboard', $this->newsroom);
		$this->vd->user_newsrooms = array();
		
		if (((int) Auth::user()->id) !== Model_User::DEFAULT_ACCOUNT_ID)
		{
			$uid = (int) Auth::user()->id;
			$sql = "SELECT nc.* FROM (
				  SELECT n.*, cc.color FROM nr_newsroom n
				  LEFT JOIN nr_company_color cc 
				  ON n.company_id = cc.company_id
				  WHERE n.user_id = {$uid}
				  AND n.is_archived = 0
				  AND n.is_deleted = 0
				  ORDER BY n.order_default DESC
				  LIMIT 250 
				) nc 
				ORDER BY nc.company_name ASC
				LIMIT 250";

			$dbr = $this->db->cached->query($sql, array(), 60);
			$nrs = Model_Newsroom::from_db_all($dbr);
			$this->vd->user_newsrooms = $nrs;
		}

		$new_company_modal = new Modal();
		$new_company_modal->set_title('Add Company');
		$modal_view = 'manage/partials/new_company_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$new_company_modal->set_content($modal_content);
		$this->add_eob($new_company_modal->render(400, 44));
		$this->vd->new_company_modal_id = $new_company_modal->id;
	}
	
	protected function is_allowed_as_free_user()
	{
		$uri = $this->uri->uri_string;
		foreach (static::$allowed_as_free_user as $pattern)
			if (preg_match($pattern, $uri)) return true;
		return false;
	}
	
	protected function requires_common_host()
	{
		if ($this->set_required_common_host) return true;
		$uri = $this->uri->uri_string;
		foreach (static::$requires_common_host as $pattern)
			if (preg_match($pattern, $uri)) return true;
		return false;
	}
	
	protected function allowed_on_all_host()
	{
		if ($this->set_allowed_on_all_host) return true;
		$uri = $this->uri->uri_string;
		foreach (static::$allowed_on_all_host as $pattern)
			if (preg_match($pattern, $uri)) return true;
		return false;
	}

	protected function allowed_as_default_company()
	{
		$uri = $this->uri->uri_string;
		foreach (static::$allowed_as_default_company as $pattern)
			if (preg_match($pattern, $uri)) return true;
		return false;
	}

	protected function __on_execution_start()
	{
		// this is the default company so we trigger the injects
		if (!$this->newsroom->company_id && !$this->is_common_host)
		{
			if (!$this->allowed_as_default_company())
			{
				if ($url = $this->session->get('admin_return_url'))
					$this->redirect($this->website_url($url), false);
				$this->redirect($this->website_url('admin'), false);
			}

			// inject some javascript that removes the non-essential UI elements
			$this->add_eoh($this->load->view('admin/partials/default-inject', null, true));

			// inject any custom view
			$segments = $this->uri->segment_array();
			$segments = array_slice($segments, 0, -count($this->params));
			$uri = implode('_', $segments);
			$uri = preg_replace('#[^a-z0-9_]#i', '_', $uri);
			$inject_view = "admin/partials/default-inject/{$uri}";
			if ($this->load->view_test($inject_view))
				$this->add_eoh($this->load->view($inject_view, null, true));
		}
	}
	
	protected function check_for_offline_conversions()
	{
		if (Auth::is_admin_mode())
			return;
		
		$locc_session_key = 'last_offline_conversion_check';
		$locc_session_value = $this->session->get($locc_session_key);
		
		// check for offline conversions at most every 2 minutes
		if (!$locc_session_value || Date::utc($locc_session_value) < Date::seconds(-120))
		{
			$this->session->set($locc_session_key, (string) Date::$now);
			$mocs = Model_Offline_Conversion::find_for_conversion(Auth::user());
			
			foreach ($mocs as $moc)
			{
				$this->vd->user = $user = Auth::user();
				$this->vd->transaction = $transaction = Model_Transaction::find($moc->transaction_id);
				$this->vd->order = $order = Model_Order::find($transaction->order_id);
				$this->vd->cart = Virtual_Cart::create_from_transaction($transaction);
		
				// add order tracking feedback for thanks page
				$feedback = new Feedback_View('partials/track-order');
				$this->schedule_use_feedback($feedback, null);
				
				$moc->is_converted = 1;
				$moc->save();
			}
		}
	}

	protected function __on_execution_end()
	{
		if (isset($this->env['headers']['x-ax-elements']))
		{
			$axes = $this->env['headers']['x-ax-elements'];
			$html = $this->output->get_output();
			$axes = comma_explode($axes);
			$parser = html5qp($html);
			$elements = array();

			foreach ($axes as $selector)
			{
				$pelement = $parser->find($selector);
				$elements[$selector] = $pelement->innerHTML();
			}
			
			$res = new stdClass();
			$res->elements = $elements;
			$this->json($res);
		}
	}
	
}

?>