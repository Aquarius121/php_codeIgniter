<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Companies_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;	
	protected $use_archived = false;
	public $title = 'Companies';

	public function index($status = null, $chunk = 1)
	{
		if ($status === 'all') $filter = 1;
		else if ($status === 'basic')
			$filter = 'n.is_active = 0 && n.is_archived = 0';
		else if ($status === 'newsroom')
			$filter = 'n.is_active = 1 && n.is_archived = 0';
		else if ($status === 'archived')
			$filter = 'n.is_archived = 1';
		else $this->redirect(gstring('admin/companies/all'));
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring("admin/companies/{$status}/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, $filter);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/companies/{$status}";
			$this->redirect(gstring($url));
		}
		
		$this->vd->status = $status;
		$this->render_list($chunkination, $results);
	}
	
	public function view($company_id)
	{
		$newsroom = Model_Newsroom::find($company_id);
		if (!$newsroom) $this->redirect('admin/companies');
		$this->admin_mode_from_company($company_id, 'manage');
	}

	public function transfer_to_user()
	{
		$company_id = $this->input->post('company');
		$user_id = $this->input->post('user');

		$user = Model_User::find($user_id);
		$company = Model_Company::find($company_id);

		if (!$user) return;
		if (!$company) return;

		$company->user_id = $user->id;
		$company->save();

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The company has been transferred.');
		$this->add_feedback($feedback);
		$this->json(array('reload' => true));
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
			$search_fields = array('n.company_name', 'n.name');
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
		
		// add sql for connecting in additional tables
		if ($use_additional_tables) $additional_tables = 
			"INNER JOIN nr_user u ON n.user_id = u.id";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			n.company_id AS id FROM 
			nr_newsroom n {$additional_tables}
			WHERE is_deleted = 0
			AND {$filter} ORDER BY 
			n.company_id DESC {$limit_str}";
			
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
		$now = Date::$now->format(DATE::FORMAT_MYSQL);
		$sql = "SELECT n.*,
			n.company_id AS id,			
			u.email AS o_user_email,
			u.id AS o_user_id,
			pt.access_token,
			pt.date_expires,
			{$u_prefixes}
			FROM nr_newsroom n
			LEFT JOIN nr_user u 
			ON n.user_id = u.id
			LEFT JOIN nr_newsroom_preview_token pt
			ON n.company_id = pt.company_id
			AND pt.date_expires > '{$now}'
			WHERE n.company_id IN ({$id_str})
			ORDER BY n.company_id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Newsroom::from_db_all($query);
		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$transfer_modal = new Modal();
		$transfer_modal->set_title('Transfer Company');
		$modal_view = 'admin/partials/transfer_to_user_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_content($modal_content);
		$modal_view = 'admin/partials/transfer_to_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_footer($modal_content);
		$this->add_eob($transfer_modal->render(500, 300));
		$this->vd->transfer_modal_id = $transfer_modal->id;

		$delete_modal = new Modal();
		$delete_modal->set_title('Delete Newsroom');
		$this->add_eob($delete_modal->render(400, 200));
		$this->vd->delete_modal_id = $delete_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/companies/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/companies/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function private_preview_link($company_id)
	{
		if (!$m_pr_token = Model_Newsroom_Preview_Token::find($company_id))
		{
			$m_pr_token = new Model_Newsroom_Preview_Token();
			$m_pr_token->company_id = $company_id;
		}

		$m_pr_token->generate();
		$m_pr_token->save();
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Private preview link generated successfully.');		
		$this->add_feedback($feedback);
		$this->redirect(gstring('admin/companies/all'));
	}

	public function delete_modal($company_id)
	{
		$this->vd->nr = Model_Newsroom::find($company_id);
		$this->load->view('admin/companies/delete_nr_modal');
	}

	public function delete_newsroom()
	{
		$company_id = $this->input->post('company_id');
		if (!$company_id)
			return;

		if (!$this->input->post('delete_nr'))
			return;

		$nr = Model_Newsroom::find($company_id);
		$nr->name = Newsroom_Assist::random_name();
		$nr->user_id = Model_User::DEFAULT_ACCOUNT_ID;
		$nr->is_active = 0;
		$nr->is_archived = 1;
		$nr->is_deleted = 1;
		$nr->save();

		if ($this->input->post('email'))
		{
			// now sending email
			$ci =& get_instance();
			$em = new Email();
			$em->set_to_email($this->input->post('email'));
			$em->set_from_name($ci->conf('email_name'));
			$em->set_from_email($ci->conf('email_address'));
			$em->set_subject('Company Newsroom Removed');
			$message = $this->load->view_return('admin/companies/nr_deletion_email');
			$em->set_message($message);
			$em->enable_html();
			Mailer::queue($em, false, Mailer::POOL_TRANSACTIONAL);
		}
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Newsroom has been soft deleted.');
		$this->add_feedback($feedback);
		$this->redirect(gstring('admin/companies/all'));
	}
	
}

?>