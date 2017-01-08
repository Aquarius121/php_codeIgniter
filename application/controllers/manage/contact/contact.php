<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/contact/listing');

class Contact_Controller extends Listing_Base { 

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Contacts';
	}
	
	public function email_check()
	{
		$company_id = $this->newsroom->company_id;
		$email = $this->input->post('email');
		$email = strtolower(trim($email));
		$contact = Model_Contact::find_match($company_id, $email);
		$this->json(array('available' => (!$contact || 
			$contact->id == $this->input->post('contact_id'))));
	}
	
	public function index($chunk = 1)
	{
		if ($this->process_selected()) return;
		
		$this->load->view('manage/header');
		
		$company_id = $this->newsroom->company_id;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(20);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.*, 
			b1.name AS beat_1_name, 
			b2.name AS beat_2_name,
			cn.id AS country__id,
			cn.name AS country__name
			FROM nr_contact c 
			LEFT JOIN nr_beat b1 ON c.beat_1_id = b1.id
			LEFT JOIN nr_beat b2 ON c.beat_2_id = b2.id
			LEFT JOIN nr_country cn ON c.country_id = cn.id
			WHERE c.company_id = ?
			ORDER BY c.first_name ASC, 
			c.last_name ASC
			{$limit_str}";

		$constructs = array();
		$constructs['country'] = 'Model_Country';
		
		$query = $this->db->query($sql, array($company_id));
		$results = Model_Contact::from_db_all($query, $constructs);

		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$url_format   = 'manage/contact/contact/-chunk-';
		$listing_view = 'manage/contact/contact';
		
		$chunkination->set_url_format($url_format);
		$chunkination->set_total($total_results);
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array('is_pitch_wizard_list', 0);
		$criteria[] = array('is_nr_subscriber_list', 0);
			
		$lists = Model_Contact_List::find_all($criteria, array('name', 'asc'));
		$this->vd->lists = $lists;
		
		$this->load->view($listing_view);
		$this->load->view('manage/footer');
	}
	
	public function edit_from($list_id)
	{
		$company_id = $this->newsroom->company_id;
		$list = Model_Contact_List::find($list_id);
		if ($list && $list->company_id == $company_id)
			$this->vd->from_m_contact_list = $list;
		$this->edit();
	}
	
	public function edit($contact_id = null)
	{
		if ($contact_id)
			  $this->vd->title[] = 'Edit Contact';
		else $this->vd->title[] = 'New Contact';
		
		$contact = Model_Contact::find($contact_id);
		$company_id = $this->newsroom->company_id;
		$this->vd->contact = $contact;

		if ($contact && $contact->company_id != $company_id)
			$this->denied();
		
		$vd = array();
		$vd['lists_allow_create'] = true;
		$vd['beats'] = Model_Beat::list_all_beats_by_group();		
		
		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array('is_pitch_wizard_list', 0);
		$criteria[] = array('is_nr_subscriber_list', 0);
			
		$vd['lists'] = Model_Contact_List::find_all($criteria, 
			array('name', 'asc'));
			
		$order = array('name', 'asc');
		$criteria = array('is_common', 1);
		$vd['common_countries'] = Model_Country::find_all($criteria, $order);
		$vd['countries'] = Model_Country::find_all(null, $order);
		
		$this->vd->related_lists = $contact ? 
			$contact->get_lists() : array();
		
		$this->load->view('manage/header');
		$this->load->view('manage/contact/contact-edit', $vd);
		$this->load->view('manage/footer');
	}
	
	public function edit_save()
	{
		$post = $this->input->post();
		$company_id = $this->newsroom->company_id;
		$contact_id = value_or_null($post['contact_id']);
		$post['email'] = strtolower(trim($post['email']));
		
		foreach ($post as &$data)
			$data = value_or_null($data);
		
		$contact = Model_Contact::find($contact_id);
		if ($contact && $contact->company_id != $company_id)
			$this->denied();
		
		if (!$contact) $contact = new Model_Contact();
		$contact->values($post);		
		$contact->company_id = $company_id;
		$contact->save();		
		$lists = array();		
		
		foreach ((array) @$post['lists'] as $contact_list_id)
		{
			if (!$contact_list_id) continue;
			if (!($list = Model_Contact_List::find($contact_list_id))) continue;
			if ($list->company_id != $company_id) continue;
			$lists[] = $list;
		}
		
		foreach ((array) @$post['create_lists'] as $name)
		{
			if (!($name = trim($name))) continue;
			$list = new Model_Contact_List();
			$list->company_id = $company_id;
			$list->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
			$list->name = $name;
			$list->save();			
			$lists[] = $list->id;
		}
		
		$contact->set_lists($lists);
		
		// load feedback message for the user
		$feedback_view = 'manage/contact/partials/contact_save_feedback';
		$feedback = $this->load->view($feedback_view, array('contact' => $contact), true);
		$this->add_feedback($feedback);
		
		// redirect back to the contacts list
		$redirect_url = 'manage/contact/contact';
		$this->set_redirect($redirect_url);
	}
	
	public function search($chunk = 1)
	{
		if ($this->process_selected()) return;
		
		$terms = $this->input->get('terms');

		$match = array(
			array('c.email', SQL_SEARCH_TERMS_EQUALS),
			array('c.first_name', SQL_SEARCH_TERMS_LIKE_INDEXED),
			array('c.last_name', SQL_SEARCH_TERMS_LIKE_INDEXED),
			array('c.company_name', SQL_SEARCH_TERMS_LIKE_INDEXED)
		);

		$terms_sql = sql_search_terms($match, $terms); 		
		$this->load->view('manage/header');
		
		$company_id = $this->newsroom->company_id;
		$chunkination = new Chunkination($chunk);
		$limit_str = $chunkination->limit_str();
		
		$contact_list_join = null;
		$contact_list_terms = 1;

		if ($contact_list_id = $this->input->get('contact_list_id'))
		{
			$contact_list_join = "INNER JOIN nr_contact_list_x_contact x
						ON x.contact_id = c.id";
			
			$contact_list_terms = 	"x.contact_list_id = {$contact_list_id}";
			$this->vd->contact_list = Model_Contact_List::find($contact_list_id);
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS c.*,
			b1.name AS beat_1_name, 
			b2.name AS beat_2_name, 
			b3.name AS beat_3_name
			FROM nr_contact c 
			{$contact_list_join}
			LEFT JOIN nr_beat b1 ON c.beat_1_id = b1.id
			LEFT JOIN nr_beat b2 ON c.beat_2_id = b2.id
			LEFT JOIN nr_beat b3 ON c.beat_3_id = b3.id
			WHERE c.company_id = ? AND {$terms_sql}
			AND {$contact_list_terms}
			ORDER BY c.first_name ASC,
			c.last_name ASC
			{$limit_str}";
		
		$query = $this->db->query($sql, 
			array($company_id));
		
		$results = array();
		foreach ($query->result() as $result)
			$results[] = $result;
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$url_format   = 'manage/contact/contact/search/-chunk-';
		$listing_view = 'manage/contact/contact-search';
		$url_format   = gstring($url_format);
		
		$chunkination->set_url_format($url_format);
		$chunkination->set_total($total_results);
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array('is_pitch_wizard_list', 0);
		$criteria[] = array('is_nr_subscriber_list', 0);
				
		$lists = Model_Contact_List::find_all($criteria, array('name', 'asc'));
		$this->vd->lists = $lists;
		
		$this->load->view($listing_view);
		$this->load->view('manage/footer');
	}
	
	public function delete($contact_id)
	{
		if (!$contact_id) return;
		$contact = Model_Contact::find($contact_id);
		$company_id = $this->newsroom->company_id;

		if ($contact->is_nr_subscriber)
		{
			$criteria = array();
			$criteria[] = array('email', $contact->email);
			$criteria[] = array('company_id', $this->newsroom->company_id);
			if ($subscription = Model_Subscription::find($criteria))
				$contact->company_id = $this->newsroom->company_id;			
		}
		
		if ($contact && $contact->company_id != $company_id)
			$this->denied();

		if ($contact->is_nr_subscriber)
		{
			$this->delete_subscriber($contact_id);
			return;
		}
		
		if ($this->input->post('confirm'))
		{
			$contact->delete();
			
			// load feedback message
			$feedback = new Feedback('success');
			$feedback->set_title('Deleted!');
			$feedback->set_text('The contact has been removed.');
			$this->add_feedback($feedback);
			
			// redirect back to type specific listing
			$redirect_url = 'manage/contact/contact/';
			$this->set_redirect($redirect_url);
		}
		else
		{
			// load confirmation feedback 
			$this->vd->contact_id = $contact_id;
			$feedback_view = 'manage/contact/partials/contact_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($contact_id);
		}
	}

	protected function delete_subscriber($contact_id)
	{
		$contact = Model_Contact::find($contact_id);
		$company_id = $this->newsroom->company_id;
		
		if ($this->input->post('confirm'))
		{
			$contact->delete();
						
			// load feedback message
			$feedback = new Feedback('success');
			$feedback->set_title('Deleted!');
			$feedback->set_text('The contact has been removed.');
			$this->add_feedback($feedback);

			// redirect back to type specific listing
			$redirect_url = 'manage/contact/list/';
			$this->set_redirect($redirect_url);
		}
		else
		{
			// load confirmation feedback 
			$this->vd->contact_id = $contact_id;
			$feedback_view = 'manage/contact/partials/subscriber_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($contact_id);
		}
	}
	
	public function download()
	{
		$csv = new CSV_Writer('php://memory');
		$sql = "SELECT co.email, co.first_name, co.last_name, 
			co.company_name, co.title, co.twitter, co.phone
			FROM nr_contact co WHERE co.company_id = ?";

		$row = array();
		$row[] = 'Email';
		$row[] = 'First Name';
		$row[] = 'Last Name';
		$row[] = 'Company Name';
		$row[] = 'Job Title';
		$row[] = 'Twitter';
		$row[] = 'Phone';
		$csv->write($row);
				
		$query = $this->db->query($sql, array($this->newsroom->company_id));
		foreach ($query->result_array() as $row)
			$csv->write($row);
		
		$handle = $csv->handle();
		rewind($handle);
		
		$this->load->helper('download');
		force_download('contacts.csv', stream_get_contents($handle));
		return;
	}
	
}

?>