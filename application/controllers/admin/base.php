<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Base extends CIL_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->vd->is_admin = true;
		
		if (!$this->is_common_host &&
			 !$this->input->post())
		{
			if (Auth::is_admin_mode())
			{
				$url = $this->uri->uri_string;
				$url = Admo::url($url);
				$url = gstring($url);
				$this->redirect($url, false);
			}
			else
			{
				$url = $this->uri->uri_string;
				$url = $this->website_url($url);
				$url = gstring($url);
				$this->redirect($url, false);
			}
		}

		if (!Auth::is_admin_online()) 
			$this->denied();

		if ($this->is_fallback_server() && Auth::is_admin_online())
		{
			$feedback = new Feedback('alternative');
			$feedback->set_text('Fallback server active. ');
			$feedback->add_text('Please record all changes.');
			$this->use_feedback($feedback);
		}
		
		$this->calculate_menu_totals();
		$this->vd->is_admin_panel = true;
		$this->vd->title[] = 'Admin';
		$this->add_list_filter_modal();

		// visit to admin panel => reset demo mode
		$this->session->set('admo_demo_mode', false);		
	}
	
	protected function admin_mode_from_content($content_id, $url)
	{
		$content = Model_Content::find($content_id);
		$this->admin_mode_from_company($content->company_id);
	}
	
	protected function admin_mode_from_company($company_id, $url)
	{
		// we force an integer to allow 
		// the special 0 value which
		// would usually be discarded
		$company_id = (int) $company_id;
		$newsroom = Model_Newsroom::find($company_id);
		$this->redirect($newsroom->url($url), false);
	}
	
	protected function admin_mode_from_user($user_id, $url)
	{
		$url = Admo::url($url, $user_id);
		$this->redirect($url, false);
	}

	protected function create_filter_search($filter_search, $label = null)
	{
		$list_filter = new stdClass();
		$list_filter->name = value_or($label, 'search');
		$list_filter->value = $filter_search;
		$gstring = array('filter_search' => $filter_search);
		$list_filter->gstring = http_build_query($gstring);
		if (!isset($this->vd->filters))
			$this->vd->filters = array();
		array_push($this->vd->filters, $list_filter);
	}
	
	protected function create_filter_user($filter_user)
	{
		$user = Model_User::find($filter_user);
		Admo::save_recent_user($user->id);
		$list_filter = new stdClass();
		$list_filter->name = 'user';
		$list_filter->value = $user->email;
		if (!$list_filter->value)
			$list_filter->value = $user->id;
		$gstring = array('filter_user' => $filter_user);
		$list_filter->gstring = http_build_query($gstring);
		if (!isset($this->vd->filters))
			$this->vd->filters = array();
		array_push($this->vd->filters, $list_filter);
	}
	
	protected function create_filter_company($filter_company)
	{
		$company = Model_Company::find($filter_company);
		Admo::save_recent_newsroom($company->id);
		$list_filter = new stdClass();
		$list_filter->name = 'company';
		$list_filter->value = $company->name;
		if (!$list_filter->value)
			$list_filter->value = $company->id;
		$gstring = array('filter_company' => $filter_company);
		$list_filter->gstring = http_build_query($gstring);
		if (!isset($this->vd->filters))
			$this->vd->filters = array();
		array_push($this->vd->filters, $list_filter);
	}

	protected function create_filter_site($filter_site)
	{
		$site = Model_Virtual_Source::find($filter_site);
		$list_filter = new stdClass();
		$list_filter->name = 'site';
		$list_filter->value = $site->name;
		if (!$list_filter->value)
			$list_filter->value = $company->id;
		$gstring = array('filter_site' => $filter_site);
		$list_filter->gstring = http_build_query($gstring);
		if (!isset($this->vd->filters))
			$this->vd->filters = array();
		array_push($this->vd->filters, $list_filter);
	}
	
	protected function calculate_menu_total_publish()
	{
		$sql = "SELECT COUNT(*) AS count FROM 
			nr_content c WHERE c.type = ?
			AND c.is_under_review = 1";
		$dbr = $this->db->query($sql, array(Model_Content::TYPE_PR));
		$this->vd->menu_count_publish = $dbr->row()->count;
	}
	
	protected function calculate_menu_totals()
	{
		$this->calculate_menu_total_publish();
		$this->calculate_menu_total_writing();
	}
	
	protected function calculate_menu_total_writing()
	{
		$this->vd->menu_count_writing = 0;
		
		// ----------------------------
		// calculate for content orders
		// ----------------------------
		
		$statuses = array(
			Model_Writing_Order::STATUS_NOT_ASSIGNED,
			Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION,
			Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS,
			Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER,
			Model_Writing_Order::STATUS_CUSTOMER_REJECTED,
		);
		
		$privileges = array(
			Model_Reseller_Details::PRIV_ADMIN_EDITOR,
			Model_Reseller_Details::PRIV_DIRECTLY_QUEUE_DRAFT,
		);
		
		$statuses_str = sql_in_list($statuses);
		$privileges_str = sql_in_list($privileges);
		
		$sql = "SELECT COUNT(*) AS count FROM 
			rw_writing_order_code woc INNER JOIN
			rw_writing_order wo ON woc.id = wo.writing_order_code_id
			LEFT JOIN rw_reseller_details rd ON rd.user_id = woc.reseller_id
			WHERE wo.status IN ({$statuses_str})
			AND (woc.reseller_id IS NULL 
			OR rd.editing_privilege IN ({$privileges_str}))
			AND wo.is_archived = 0
			AND woc.is_archived = 0";
			
		$dbr = $this->db->query($sql);
		$this->vd->menu_count_writing += $dbr->row()->count;
		
		// --------------------------
		// calculate for pitch orders
		// --------------------------
		
		$statuses_str = sql_in_list(array(
			Model_Pitch_Order::STATUS_NOT_ASSIGNED,
			Model_Pitch_ORDER::STATUS_WRITER_REQUEST_DETAILS_REVISION, 
			Model_Pitch_ORDER::STATUS_CUSTOMER_REVISE_DETAILS,
			Model_Pitch_Order::STATUS_WRITTEN_SENT_TO_ADMIN,
			Model_Pitch_Order::STATUS_CUSTOMER_REJECTED,
		));
		
		$sql = "SELECT COUNT(*) AS count 
			FROM pw_pitch_order po
			WHERE po.status IN ({$statuses_str})
			AND po.is_writing_archived = 0";
			
		$dbr = $this->db->query($sql);
		$this->vd->menu_count_writing += $dbr->row()->count;
	}

	protected function __on_execution_end()
	{
		parent::__on_execution_end();
		$this->set_return_url();
	}

	protected function set_return_url()
	{
		// allow return back to this url after any editing
		// * this shouldn't be set if we just did a redirect
		// * this shouldn't be set if we returned json
		// * this shouldn't be set if requested over ajax
		
		if ($this->is_ajax_request())
			return;

		$headers = headers_list();
		$headers_ci = $this->output->get_headers();
		foreach ($headers_ci as $header_ci)
			$headers[] = $header_ci[0];
			
		foreach ($headers as $header)
			if (preg_match('#^content-type:(.*)$#i', $header, $ex))
				if (!str_contains($ex[1], 'text/html'))
					return;

		$this->session->set('admin_return_url', 
			$this->uri->uri_string);
	}

	protected function add_list_filter_modal()
	{
		$view_content = 'admin/partials/filter_modal';
		$view_footer = 'admin/partials/filter_modal_footer';
		$modal_content = $this->load->view_return($view_content);
		$modal_footer = $this->load->view_return($view_footer);

		$modal = new Modal();		
		$modal->set_title('Add Filter');
		$modal->set_id('admin-add-filter-modal');
		$modal->set_content($modal_content);
		$modal->set_footer($modal_footer);
		$this->vd->admin_add_filter_modal = $modal;

		$html_view = 'admin/partials/add_filter';
		$html_content = $this->load->view_return($html_view);
		$this->add_eob($html_content);
	}

}