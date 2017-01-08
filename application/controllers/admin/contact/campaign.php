<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');
load_controller('shared/common_pw_orders_trait');

class Campaign_Controller extends Admin_Base {

	use Common_PW_Orders_Trait;
	const LISTING_CHUNK_SIZE = 20;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Campaigns';
	}

	public function index($status = null, $chunk = 1)
	{
		if ($status === null) 
			$this->redirect(gstring('admin/contact/campaign/all'));
		if (!$this->is_allowed_status($status)) show_404();
		
		$filters = array(
			'all' => null,
			'draft' => 'c.is_sent = 0 AND c.is_draft = 1',
			'scheduled' => 'c.is_sent = 0 AND c.is_draft = 0',
			'sent' => 'c.is_sent = 1',
		);
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring("admin/contact/campaign/{$status}/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, $filters[$status]);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			// out of bounds so redirect to first
			$url = "admin/contact/campaign/{$status}";
			$this->redirect(gstring($url));
		}
		
		$this->vd->status = $status;		
		$this->render_list($chunkination, $results);
	}
	
	public function edit($campaign_id)
	{
		$campaign = Model_Campaign::find($campaign_id);
		if (!$campaign) $this->redirect('admin/contact/campaign');
		$url = "manage/contact/campaign/edit/{$campaign_id}";
		$this->admin_mode_from_company($campaign->company_id, $url);
	}
	
	public function delete($campaign_id)
	{
		$campaign = Model_Campaign::find($campaign_id);
		if (!$campaign) $this->redirect('admin/contact/campaign');
		$url = "manage/contact/campaign/delete/{$campaign_id}";
		$this->admin_mode_from_company($campaign->company_id, $url);
	}
	
	public function stats($campaign_id)
	{
		$campaign = Model_Campaign::find($campaign_id);
		if (!$campaign) $this->redirect('admin/contact/campaign');
		$url = "manage/analyze/email/view/{$campaign_id}";
		$this->admin_mode_from_company($campaign->company_id, $url);
	}
	
	protected function fetch_results($chunkination, $filter = null)
	{
		if (!$filter) $filter = 1;
		$limit_str = $chunkination->limit_str();
		$use_additional_tables = false;
		$additional_tables = null;
		$this->vd->filters = array();	
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);			
			// restrict search results to these terms
			$search_fields = array('c.name', 'c.subject');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}

		if (($filter_user = $this->input->get('filter_user')) !== false)
		{
			$filter_user = (int) $filter_user;
			$this->create_filter_user($filter_user);	
			// restrict search results to this user
			$filter = "{$filter} AND u.id = {$filter_user}";
			$use_additional_tables = true;
		}

		if (($filter_site = $this->input->get('filter_site')) !== false)
		{
			$filter_site = (int) $filter_site;
			$this->create_filter_site($filter_site);
			if ($filter_site === -1)
			     $filter = "{$filter} AND IFNULL(u.virtual_source_id, 0) = 0";
			else $filter = "{$filter} AND u.virtual_source_id = {$filter_site}";
			$use_additional_tables = true;
		}
		
		if (($filter_company = $this->input->get('filter_company')) !== false)
		{
			$filter_company = (int) $filter_company;
			$this->create_filter_company($filter_company);	
			// restrict search results to this user
			$filter = "{$filter} AND cm.id = {$filter_company}";
			$use_additional_tables = true;
		}
		
		// add sql for connecting in additional tables
		if ($use_additional_tables) $additional_tables = 
			"INNER JOIN nr_company cm ON c.company_id = cm.id
			 INNER JOIN nr_user u ON cm.user_id = u.id";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.id FROM 
			nr_campaign c {$additional_tables}
			LEFT JOIN pw_pitch_order po
			ON po.campaign_id = c.id
			WHERE {$filter} ORDER BY c.id 
			DESC {$limit_str}";
			
		$query = $this->db->query($sql);
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = (int) $row->id;
		
		// no results found so exit
		if (!$id_list) return array();
				
		$id_str = sql_in_list($id_list);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		$u_prefixes = Model_User::__prefixes('u');
		$sql = "SELECT c.*,
			cm.name AS o_company_name,
			cm.id AS o_company_id,
			u.email AS o_user_email,
			u.id AS o_user_id,
			po.id as pitch_order_id,
			co.title as content_title,
			{$u_prefixes}
			FROM nr_campaign c
			LEFT JOIN pw_pitch_order po
			ON po.campaign_id = c.id
			LEFT JOIN nr_content co 
			ON c.content_id = co.id
			LEFT JOIN nr_company cm
			ON c.company_id = cm.id
			LEFT JOIN nr_user u 
			ON cm.user_id = u.id
			WHERE c.id IN ({$id_str}) 
			ORDER BY c.id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Campaign::from_db_all($query);		
		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->add_order_detail_modal();
		
		$this->load->view('admin/header');
		$this->load->view('admin/contact/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/contact/campaign/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	protected function is_allowed_status($status)
	{
		if ($status === 'all') return true;
		if ($status === 'sent') return true;
		if ($status === 'scheduled') return true;
		if ($status === 'draft') return true;
		return false;
	}

	public function spam_report($id)
	{
		$this->allow_cors();
		$campaign = Model_Campaign::find($id);
		if (!$campaign) return;
		$report = $campaign->spam_report();
		$message = preg_replace(
			'#.*analysis details:[^\r\n]+#is',
 			 null, $report->message);
		echo sprintf('<pre>%s</pre>', trim($message));
	}

}
