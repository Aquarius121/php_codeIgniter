<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Email_Controller extends Manage_Base {
	
	protected $show_all_contacts = false;
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Analytics';
		$this->vd->title[] = 'Email Stats';
	}
	
	public function index($chunk = 1)
	{
		$terms_sql = 1;
		$company_id = $this->newsroom->company_id;
		$terms = $this->input->get('terms');
		$terms_sql = sql_search_terms(array('ca.name', 
			'ca.subject', 'co.title'), $terms);
				
		$this->load->view('manage/header');
		
		$chunkination = new Chunkination($chunk);
		$limit_str = $chunkination->limit_str();
		
		$order = ($terms ? "ca.name ASC" : "ca.id DESC");		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ca.*,
			co.type AS content_type
			FROM nr_campaign ca 
			LEFT JOIN nr_content co ON ca.content_id = co.id
			WHERE ca.company_id = ? AND ca.is_sent = 1
			AND {$terms_sql} ORDER BY {$order} {$limit_str}";
		
		$query = $this->db->query($sql, 
			array($company_id));
		
		$results = array();
		foreach ($query->result() as $result)
			$results[] = $result;
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$url_format = gstring("manage/analyze/email/-chunk-");
		$chunkination->set_url_format($url_format);
		$chunkination->set_total($total_results);
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$stats_query = new Stats_Query();
		$view_hashes = array();
		$click_hashes = array();

		foreach ($results as $result)
		{
			$stats_hash = new Stats_Hash();
			$stats_hash->action = 'view';
			$stats_hash->campaign = $result->id;
			$view_hashes[] = $stats_hash->hash();

			$stats_hash = new Stats_Hash();
			$stats_hash->action = 'click';
			$stats_hash->campaign = $result->id;
			$click_hashes[] = $stats_hash->hash();
		}

		$click_contexts = array_values(Stats_Hash::__context_batch($click_hashes));
		foreach ($click_contexts as $k => $context_set)
			$results[$k]->clicks = $stats_query->activated_set_summation($context_set);

		$view_contexts = array_values(Stats_Hash::__context_batch($view_hashes));
		foreach ($view_contexts as $k => $context_set)
			$results[$k]->views = max($results[$k]->clicks, 
				$stats_query->activated_set_summation($context_set));
		
		$this->load->view('manage/analyze/email');
		$this->load->view('manage/footer');
	}

	protected function load_results($campaign, $chunkination = null, $filter = 1)
	{
		$sort = $this->input->get('sort');
		$reverse = (bool) $this->input->get('reverse');
		$this->vd->sort = $sort;
		$this->vd->reverse = $reverse;
		$this->load_general_stats($campaign);
		$this->vd->campaign = $campaign;
		$campaign->load_content_data();

		$data_contacts = @unserialize($campaign->contacts);
		if (!$data_contacts) $data_contacts = array();
		foreach ($data_contacts as $k => $data_contact)
			$data_contacts[$k] = (int) $data_contact;

		$activated_view_contexts = $this->load_contact_stats($campaign, 'view');
		$activated_click_contexts = $this->load_contact_stats($campaign, 'click');

		$view_hashes = array();
		$click_hashes = array();

		foreach ($data_contacts as $k => $contact_id)
		{
			$stats_hash = new Stats_Hash();
			$stats_hash->action = 'view';
			$stats_hash->contact = $contact_id;
			$stats_hash->campaign = $campaign->id;
			$view_hashes[] = $stats_hash->hash();

			$stats_hash = new Stats_Hash();
			$stats_hash->action = 'click';
			$stats_hash->contact = $contact_id;
			$stats_hash->campaign = $campaign->id;
			$click_hashes[] = $stats_hash->hash();
		}

		$click_contexts = array_values(Stats_Hash::__context_batch($click_hashes));
		$view_contexts = array_values(Stats_Hash::__context_batch($view_hashes));
		$mock_contacts = array();

		foreach ($data_contacts as $k => $contact_id)
		{
			$click_context = $click_contexts[$k];
			$view_context = $view_contexts[$k];
			$mock_contacts[$contact_id] = new Model_Contact();
			$mock_contacts[$contact_id]->id = $contact_id;
			$mock_contacts[$contact_id]->clicked = in_array($click_context, $activated_click_contexts);
			$mock_contacts[$contact_id]->viewed = in_array($view_context, $activated_view_contexts);
		}

		if ($sort === 'viewed')
		{
			usort($data_contacts, function($a, $b) use ($mock_contacts, $reverse) {
				if ($reverse) var_swap($a, $b);
				// we assume it was viewed if it has been clicked
				$a_viewed = $mock_contacts[$a]->viewed || $mock_contacts[$a]->clicked;
				$b_viewed = $mock_contacts[$b]->viewed || $mock_contacts[$b]->clicked;
				if ($a_viewed && $b_viewed) return 0;
				if ($a_viewed && !$b_viewed) return -1;
				if (!$a_viewed && $b_viewed) return 1;
			});
		}

		if ($sort === 'clicked')
		{
			usort($data_contacts, function($a, $b) use ($mock_contacts, $reverse) {
				if ($reverse) var_swap($a, $b);
				if ($mock_contacts[$a]->clicked && $mock_contacts[$b]->clicked) return 0;
				if ($mock_contacts[$a]->clicked && !$mock_contacts[$b]->clicked) return -1;
				if (!$mock_contacts[$a]->clicked && $mock_contacts[$b]->clicked) return 1;
			});
		}

		if ($chunkination === null)
		{
			$chunkination = new Chunkination(1);
			$chunkination->set_chunk_size(count($data_contacts));
		}
			
		$results = array();
		$total_results = 0;
		
		if (count($data_contacts))
		{
			$limit_str = $chunkination->limit_str();
			$in_data_contacts = sql_in_list($data_contacts);

			if ($sort)
			{
				$sql = "SELECT SQL_CALC_FOUND_ROWS 
					c.id, c.first_name, c.last_name, 
					c.email, c.company_name, c.company_id
					FROM nr_contact c WHERE c.id IN ({$in_data_contacts})
					AND {$filter}
					ORDER BY FIELD (c.id, {$in_data_contacts})
					{$limit_str}";
			}
			else
			{
				$sql = "SELECT SQL_CALC_FOUND_ROWS 
					c.id, c.first_name, c.last_name, 
					c.email, c.company_name, c.company_id
					FROM nr_contact c WHERE c.id IN ({$in_data_contacts})
					AND {$filter}
					ORDER BY c.first_name ASC, c.last_name ASC
					{$limit_str}";
			}
					
			$query = $this->db->query($sql);
			$results = Model_Contact::from_db_all($query);
			$total_results = $this->db
				->query("SELECT FOUND_ROWS() AS count")
				->row()->count;
		}
				
		$chunkination->set_total($total_results);
		$obfuscator = Media_Database_Contact_Access::email_obfuscator();
		
		foreach ($results as $result)
		{
			if ($result->company_id == 0)
				$result->email = $obfuscator
					->obfuscate_parts($result->email);

			$mock_contact = $mock_contacts[(int) $result->id];
			$result->viewed = $mock_contact->viewed;
			$result->clicked = $mock_contact->clicked;
		}
		
		$this->vd->total_results = $total_results;
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		return $results;
	}
	
	public function view($campaign_id, $chunk = 1)
	{
		$company_id = $this->newsroom->company_id;
		if (!$campaign = Model_Campaign::find($campaign_id)) return;
		if ($campaign->company_id != $company_id)
			$this->denied();
		
		$this->title = $campaign->name;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(100);
		$url_format = gstring("manage/analyze/email/view/{$campaign_id}/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->load_results($campaign, $chunkination);
		
		$this->load->view('manage/header');
		$this->load->view('manage/analyze/email-view');
		$this->load->view('manage/footer');
	}

	public function view_search($chunk = 1)
	{
		$campaign_id = $this->input->get('campaign_id');
		$terms = $this->input->get('terms');

		$company_id = $this->newsroom->company_id;
		if (!$campaign = Model_Campaign::find($campaign_id)) return;
		if ($campaign->company_id != $company_id)
			$this->denied();

		$match = array(
			array('c.email', SQL_SEARCH_TERMS_EQUALS),
			array('c.first_name', SQL_SEARCH_TERMS_LIKE_INDEXED),
			array('c.last_name', SQL_SEARCH_TERMS_LIKE_INDEXED),
			array('c.company_name', SQL_SEARCH_TERMS_LIKE_INDEXED)
		);

		$terms_sql = sql_search_terms($match, $terms); 

		$this->title = $campaign->name;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(100);
		$url_format = gstring("manage/analyze/email/view/search/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->load_results($campaign, $chunkination, $terms_sql);

		$this->vd->is_search_result = true;
		$this->load->view('manage/header');
		$this->load->view('manage/analyze/email-view-search');
		$this->load->view('manage/footer');
	}
	
	public function report($id)
	{
		$generate_url = "manage/analyze/email/report_generate/{$id}";
		$generate_url = gstring($generate_url);
		$this->vd->generate_url = $generate_url;
		
		$return_url = "manage/analyze/email/view/{$id}";
		$return_url = gstring($return_url);
		$this->vd->return_url = $return_url;
		
		$this->load->view('manage/header');
		$this->load->view('manage/analyze/report-generate');
		$this->load->view('manage/footer');
	}
	
	public function report_generate($id)
	{
		$url = "manage/analyze/email/report_index/{$id}";
		$url = $this->newsroom->url($url);
		$report = new PDF_Generator($url);
		$report->generate();
		
		if ($this->input->post('indirect'))
			  $this->vd->download_url = $report->indirect();
		else $report->deliver();
		
		// indirect => load feedback (and download) message for the user
		$feedback_view = 'manage/partials/report-generated-feedback';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
	}
	
	public function report_index($campaign_id)
	{
		$company_id = $this->newsroom->company_id;
		if (!$campaign = Model_Campaign::find($campaign_id)) return;
		if ($campaign->company_id != $company_id)
			$this->denied();
		
		$this->title = $campaign->name;
		$results = $this->load_results($campaign);
		$this->vd->results = $results;
		
		$this->load->view('manage/header');
		$this->load->view('manage/analyze/report/email');
		$this->load->view('manage/footer');
	}
	
	protected function load_contact_stats($campaign, $action)
	{
		$stats_hash = new Stats_Hash();
		$stats_hash->action = $action;
		$stats_hash->campaign = $campaign->id;
		$context_set = $stats_hash->context();
		$stats_query = new Stats_Query();
		$activations = $stats_query->activated_set($context_set);
		$context_list = array();
		foreach ($activations as $activation)
			$context_list[] = $activation->context;
		return $context_list;
	}

	protected function load_general_stats($campaign)
	{
		$stats_hash = new Stats_Hash();
		$stats_hash->action = 'view';
		$stats_hash->campaign = $campaign->id;
		$view_context_set = $stats_hash->context();

		$stats_hash = new Stats_Hash();
		$stats_hash->action = 'click';
		$stats_hash->campaign = $campaign->id;
		$click_context_set = $stats_hash->context();

		$stats_query = new Stats_Query();
		$clicks = $stats_query->activated_set_summation($click_context_set);
		$views = $stats_query->activated_set_summation($view_context_set);
		
		$this->vd->clicks = $clicks;
		$this->vd->views = max($clicks, $views);
	}

	public function save_viewed($id)
	{
		$this->save_contacts($id, 'view');
	}

	public function save_clicked($id)
	{
		$this->save_contacts($id, 'click');
	}

	protected function save_contacts($campaign_id, $action)
	{
		$company_id = $this->newsroom->company_id;
		if (!$campaign = Model_Campaign::find($campaign_id)) return;
		if ($campaign->company_id != $company_id)
			$this->denied();
		
		$action_hashes = array();
		$activated_contacts = array();
		$activated_contexts = $this->load_contact_stats($campaign, $action);		
		$campaign->load_content_data();

		// decode list of sent contacts and make sure its
		// a uniform array for alignment with hashes
		$sent_contacts = @unserialize($campaign->contacts);
		if (!$sent_contacts) $sent_contacts = array();
		$sent_contacts = array_values($sent_contacts);

		// generate hashes for each contact
		foreach ($sent_contacts as $k => $contact_id)
		{
			$stats_hash = new Stats_Hash();
			$stats_hash->action = $action;
			$stats_hash->contact = $contact_id;
			$stats_hash->campaign = $campaign->id;
			$action_hashes[] = $stats_hash->hash();
		}

		// convert all hashes to contexts in batch
		$action_contexts = array_values(Stats_Hash::__context_batch($action_hashes));

		// look for context in list of activated contexts
		// and build up a list of activated contacts
		foreach ($sent_contacts as $k => $contact_id)
		{
			$action_context = $action_contexts[$k];
			if (in_array($action_context, $activated_contexts))
				$activated_contacts[] = (int) $contact_id;
		}

		$list = new Model_Contact_List();
		$list->name = Date::$now->__toString();
		$list->date_created = Date::$now;
		$list->company_id = $company_id;
		$list->save();
		$list->add_all_contacts($activated_contacts);

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The contact list has been created.');
		$this->add_feedback($feedback);
		$this->redirect(sprintf('manage/contact/list/edit/%d', $list->id));
	}
	
}

?>