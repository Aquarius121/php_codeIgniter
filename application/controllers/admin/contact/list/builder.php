<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/list/main');

class Builder_Controller extends Main_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Builder Lists';
	}

	public function index($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/contact/list/builder/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			// out of bounds so redirect to first
			$url = 'admin/contact/list/builder';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}
	
	protected function fetch_results($chunkination, $filter = null)
	{
		if (!$filter) $filter = 1;
		$limit_str = $chunkination->limit_str();
		$this->vd->filters = array();
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('c.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}
		
		if ($filter_user = (int) $this->input->get('filter_user'))
		{
			$this->create_filter_user($filter_user);	
			// restrict search results to this user
			$filter = "{$filter} AND c.user_id = {$filter_user}";
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.id FROM 
			nr_contact_builder c WHERE {$filter} 
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
			u.email AS o_user_email,
			u.id AS o_user_id,
			{$u_prefixes}
			FROM nr_contact_builder c		
			LEFT JOIN nr_user u 
			ON c.user_id = u.id
			LEFT JOIN (
				SELECT x.contact_builder_id, count(*) AS count 
				FROM nr_contact_builder_x_contact x 
				GROUP BY x.contact_builder_id
			) AS clc ON c.id = clc.contact_builder_id
			WHERE c.id IN ({$id_str})
			ORDER BY c.id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Contact_Builder::from_db_all($query);
		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$feedback = new Feedback('info');
		$feedback->set_text('Once a customer has approved a list');
		$feedback->add_text('you can safely remove the builder list from here.');
		$this->use_feedback($feedback);

		$this->load->view('admin/header');
		$this->load->view('admin/contact/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/contact/list/builder');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function transfer()
	{
		$builder_list = Model_Contact_Builder::find($this->input->post('list'));
		$company = Model_Company::find($this->input->post('company'));
		if (!$builder_list) return;
		if (!$company) return;

		$new_list = new Model_Contact_List();
		$new_list->company_id = $company->id;
		$new_list->name = $builder_list->name;
		$new_list->date_created = $builder_list->date_created;
		$new_list->save();

		$contacts_id_list = $builder_list->contacts_id_list();
		$new_list->add_all_contacts($contacts_id_list);
		$builder_list->delete();

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The contact list has been transferred.');
		$this->add_feedback($feedback);

		$url = "manage/contact/list/edit/{$new_list->id}";
		$url = $company->newsroom()->url($url);
		$this->json(array('redirect' => $url));
	}		

	public function edit($contact_builder_id, $chunk = 1)
	{
		$list = Model_Contact_Builder::find($contact_builder_id);
		if (!$list) $this->redirect('admin/contact/list');
		$contact_builder_id = (int) $contact_builder_id;
		$this->vd->list = $list;
		$this->title = $list->name;
		$this->vd->is_list_builder = true;
		
		if ($list)
		{			
			// handles bulk operations
			if ($this->process_selected($list)) return;
			
			$chunkination = new Chunkination($chunk);
			$chunkination->set_chunk_size(20);
			$limit_str = $chunkination->limit_str();
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS c.*, 
				b1.name AS beat_1_name, 
				b2.name AS beat_2_name,
				/* media database extras */
				re.id AS region__id,
				re.name AS region__name,
				re.abbr AS region__abbr,
				lo.id AS locality__id,
				lo.name AS locality__name,
				cn.id AS country__id,
				cn.name AS country__name,
				cr.id AS contact_role__id,
				cr.role AS contact_role__role
				FROM nr_contact c 
				INNER JOIN nr_contact_builder_x_contact x
				ON c.id = x.contact_id AND x.contact_builder_id = ?
				LEFT JOIN nr_beat b1 ON c.beat_1_id = b1.id
				LEFT JOIN nr_beat b2 ON c.beat_2_id = b2.id
				LEFT JOIN nr_region re ON c.region_id = re.id
				LEFT JOIN nr_locality lo ON c.locality_id = lo.id
				LEFT JOIN nr_country cn ON c.country_id = cn.id
				LEFT JOIN nr_contact_role cr ON c.contact_role_id = cr.id
				ORDER BY c.first_name ASC, 
				c.last_name ASC
				{$limit_str}";
			
			$constructs = array();
			$constructs['region'] = 'Model_Region';
			$constructs['locality'] = 'Model_Locality';
			$constructs['country'] = 'Model_Country';
			$constructs['contact_role'] = 'Model_Contact_Role';

			$query = $this->db->query($sql, array($contact_builder_id));
			$results = Model_Contact::from_db_all($query, $constructs);
			
			$total_results = $this->db
				->query("SELECT FOUND_ROWS() AS count")
				->row()->count;
			
			$url_format = "admin/contact/list/builder/edit/{$contact_builder_id}/-chunk-";						
			$chunkination->set_url_format($url_format);
			$chunkination->set_total($total_results);
			$this->vd->chunkination = $chunkination;
			$this->vd->results = $results;	
		}

		$transfer_modal = new Modal();
		$transfer_modal->set_title('Transfer List');
		$modal_view = 'admin/contact/list/builder/transfer_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_content($modal_content);
		$modal_view = 'admin/partials/transfer_to_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$transfer_modal->set_footer($modal_content);
		$this->add_eob($transfer_modal->render(600, 500));
		$this->vd->transfer_modal_id = $transfer_modal->id;

		$this->vd->lists = Model_Contact_Builder::find_all();		
		$this->load->view('admin/header');
		$this->load->view('admin/contact/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/contact/list/builder-edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function rename($contact_builder_id)
	{
		if (!$contact_builder_id) return;
		$list = Model_Contact_Builder::find($contact_builder_id);
		
		$list->name = $this->input->post('name');
		$list->save();
		$this->json(true);
	}
	
	public function edit_save()
	{
		$post = $this->input->post();
		if (isset($post['contact_builder_id']))
		     $contact_builder_id = value_or_null($post['contact_builder_id']);
		else $contact_builder_id = null;
		
		foreach ($post as &$data)
			$data = value_or_null($data);
		
		$builder = Model_Contact_Builder::find($contact_builder_id);		
		if (!$builder && !$this->input->post('name'))
			$this->redirect('admin/contact/list/builder');
		
		if (!$builder) $builder = new Model_Contact_Builder();
		$builder->values($post);
		$builder->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
		$builder->user_id = Auth::user()->id;
		$builder->save();
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The list has been saved.');
		$this->add_feedback($feedback);

		$redirect_url = "admin/contact/list/builder/edit/{$builder->id}";
		$this->set_redirect($redirect_url);
	}
	
	public function delete($contact_builder_id)
	{
		if (!$contact_builder_id) return;
		$builder = Model_Contact_Builder::find($contact_builder_id);
		
		if ($this->input->post('confirm'))
		{
			$builder->delete();
			
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('The list has been removed.');
			$this->add_feedback($feedback);
			
			// redirect back to type specific listing
			$redirect_url = 'admin/contact/list/builder';
			$this->set_redirect($redirect_url);
		}
		else
		{
			// load confirmation feedback 
			$this->vd->contact_builder_id = $contact_builder_id;
			$this->vd->compact_list = true;
			$feedback_view = 'admin/contact/list/builder/delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($contact_builder_id);
		}
	}
	
	public function download($contact_builder_id = null)
	{
		$builder = Model_Contact_Builder::find($contact_builder_id);
		if (!$builder) return;
		
		$csv = new CSV_Writer('php://memory');
		$sql = "SELECT co.*, cn.name AS country_name, 
			re.name AS region_name, lo.name AS locality_name
			FROM nr_contact_builder_x_contact x 
			INNER JOIN nr_contact co ON x.contact_id = co.id
			LEFT JOIN nr_country cn ON co.country_id = cn.id
			LEFT JOIN nr_region re ON co.region_id = re.id
			LEFT JOIN nr_locality lo ON co.locality_id = lo.id
			WHERE x.contact_builder_id = ?";

		$query = $this->db->query($sql, array($builder->id));
		foreach ($query->result() as $record)
		{
			$row = array();
			$row[] = $record->email;
			$row[] = $record->first_name;
			$row[] = $record->last_name;
			$row[] = $record->company_name;
			$row[] = $record->country_name;
			$row[] = $record->region_name;
			$row[] = $record->locality_name;
			$csv->write($row);
		}
		
		$handle = $csv->handle();
		rewind($handle);
		
		$this->load->helper('download');
		force_download('contacts.csv', stream_get_contents($handle));
		return;
	}

	protected function process_selected($context_list = null)
	{
		$selected = $this->input->post('selected');
		if (!is_array($selected)) return false;
		$this->vd->selected = $selected;
		
		if ($this->input->post('add_to_list'))
			if ($this->add_to_list_selected(array_keys($selected)))
				return true;
		
		if ($this->input->post('remove_from_list') && $context_list)
			if ($this->remove_from_list_selected($context_list, array_keys($selected)))
				return true;
		
		if ($this->input->post('delete'))
			if ($this->delete_selected(array_keys($selected)))
				return true;
		
		return false;
	}
	
	protected function remove_from_list_selected($list, $selected)
	{
		foreach ($selected as $contact_id)
		{
			$contact = Model_Contact::find($contact_id);
			if (!$contact) continue;
			$list->remove_contact($contact);
		}
		
		// load feedback message 
		$feedback = new Feedback('success');
		$feedback->set_title('Removed!');
		$feedback->set_text('The contacts have been removed from the list.');
		$this->use_feedback($feedback);
	}
	
	protected function add_to_list_selected($selected)
	{
		$contact_list_id = $this->input->post('contact_list_id');
		$list = Model_Contact_Builder::find($contact_list_id);
		if (!$list) return;
		
		foreach ($selected as $contact_id)
		{
			$contact = Model_Contact::find($contact_id);
			if (!$contact) continue;
			$list->add_contact($contact);
		}
		
		// load feedback message 
		$feedback = new Feedback('success');
		$feedback->set_title('Added!');
		$feedback->set_text('The contacts have been added to the list.');
		$this->use_feedback($feedback);
	}
	
	protected function delete_selected($selected)
	{		
		if ($this->input->post('confirm'))
		{
			foreach ($selected as $contact_id)
			{
				$contact = Model_Contact::find($contact_id);
				if (!$contact) continue;				
				$contact->delete();
			}
			
			// load feedback message 
			$feedback = new Feedback('success');
			$feedback->set_title('Deleted!');
			$feedback->set_text('The contacts have been deleted.');
			$this->use_feedback($feedback);
		}
		else
		{
			// load confirmation feedback 
			$feedback_view = 'admin/contact/list/builder/contact_multi_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);

			$this->vd->lists = Model_Contact_Builder::find_all();		
			$this->load->view('admin/header');
			$this->load->view('admin/contact/menu');
			$this->load->view('admin/pre-content');
			
			foreach ($selected as &$contact_id)
				$contact_id = (int) $contact_id;
			$selected_str = implode(',', $selected);
		
			$sql = "SELECT SQL_CALC_FOUND_ROWS c.* 
				FROM nr_contact c 
				WHERE c.id IN ({$selected_str})
				ORDER BY c.first_name ASC, 
				c.last_name ASC";
		
			$query = $this->db->query($sql);
			
			$results = array();
			foreach ($query->result() as $result)
				$results[] = $result;
		
			$chunkination = new Chunkination(1);
			$chunkination->set_total(count($results));
			$chunkination->set_chunk_size(count($results));
			$this->vd->chunkination = $chunkination;
			$this->vd->results = $results;
			$this->vd->compact_list = true;

			$this->load->view('manage/contact/partials/contact_listing');
			$this->load->view('admin/post-content');
			$this->load->view('admin/footer');
			return true;
		}
	}

}

?>