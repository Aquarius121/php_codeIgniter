<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/list/main');

class Customer_Controller extends Main_Controller {

	const LISTING_CHUNK_SIZE = 20;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Lists';
	}
	
	public function index($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/contact/list/customer/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);

		$feedback = new Feedback('warning');
		$feedback->set_title('Note:');
		$feedback->set_text('This page does not include 
			system or list builder contact lists.');
		$this->use_feedback($feedback);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			// out of bounds so redirect to first
			$url = 'admin/contact/list/customer';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}
	
	public function edit($list_id)
	{
		$list = Model_Contact_List::find($list_id);
		if (!$list) $this->redirect('admin/contact/list');
		$url = "manage/contact/list/edit/{$list_id}";
		$this->admin_mode_from_company($list->company_id, $url);
	}
	
	public function delete($list_id)
	{
		$list = Model_Contact_List::find($list_id);
		if (!$list) $this->redirect('admin/contact/list');
		$url = "manage/contact/list/delete/{$list_id}";
		$this->admin_mode_from_company($list->company_id, $url);
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
			$search_fields = array('c.name');
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
			nr_contact_list c {$additional_tables}
			WHERE {$filter} AND c.company_id != 0
			ORDER BY c.id DESC {$limit_str}";
			
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
		$sql = "SELECT c.*, clc.count,
 			cm.name AS o_company_name,
			cm.id AS o_company_id,
			u.email AS o_user_email,
			u.id AS o_user_id,
			{$u_prefixes}
			FROM nr_contact_list c
			LEFT JOIN nr_company cm
			ON c.company_id = cm.id
			LEFT JOIN nr_user u 
			ON cm.user_id = u.id
			LEFT JOIN (
				SELECT x.contact_list_id, count(*) AS count 
				FROM nr_contact_list_x_contact x 
				GROUP BY x.contact_list_id
			) AS clc ON c.id = clc.contact_list_id
			WHERE c.id IN ({$id_str})
			ORDER BY c.id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Contact_List::from_db_all($query);
		
		return $results;
	}

	public function transfer_to_company()
	{
		$company_id = $this->input->post('company');
		$contact_list_id = $this->input->post('contact_list_id');

		$contact_list = Model_Contact_List::find($contact_list_id);
		$company = Model_Company::find($company_id);

		if (!$contact_list) return;
		if (!$company) return;

		$sql = "SELECT c.*
				FROM nr_contact c
				INNER JOIN nr_contact_list_x_contact clc
				ON clc.contact_id = c.id
				WHERE clc.contact_list_id = ?";

		$contacts = Model_Contact::from_sql_all($sql, array($contact_list_id));

		if (is_array($contacts) && count($contacts))
		{
			$contact_list->remove_all_contacts();

			foreach ($contacts as $contact)
			{
				if ($contact->is_media_db_contact)
					$contact_list->add_contact($contact);
				else
				{
					$dup_contact = $contact->create_duplicate($company_id);

					if ($dup_contact && $dup_contact instanceof Model_Contact)
						$dup_contact->add_lists(array($contact_list));
				}
			}
		}

		$contact_list->company_id = $company->id;
		$contact_list->save();

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The content list has been transferred.');
		$this->add_feedback($feedback);
		$this->json(array('reload' => true));
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$transfer_modal = new Modal();
		$transfer_modal->set_title('Transfer Contact List');
		$modal_view = 'admin/partials/transfer_to_company_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_content($modal_content);
		$modal_view = 'admin/partials/transfer_to_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_footer($modal_content);
		$this->add_eob($transfer_modal->render(600, 500));
		$this->vd->transfer_modal_id = $transfer_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/contact/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/contact/list/customer');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
}

?>