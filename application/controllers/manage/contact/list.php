<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/contact/listing');
load_controller('shared/common_pw_orders_trait');
load_controller('shared/media_database_profile_trait');
load_controller('shared/contact_list_history_trait');

class List_Controller extends Listing_Base { 

	use Common_PW_Orders_Trait;
	use Media_Database_Profile_Trait;
	use Contact_List_History_Trait;
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Lists';
	}
	
	public function index($chunk = 1)
	{
		$this->load->view('manage/header');
		
		$company_id = $this->newsroom->company_id;
		$chunkination = new Chunkination($chunk);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS cl.*, 
			ct.count_contacts, 
			ca.name AS last_campaign_name,
			po.id AS pitch_order_id,
			po.city, po.keyword,
			po.date_created AS po_date_created,
			ca.is_sent, 
			c.title AS content_title,			
			st.abbr AS state_abbr,
			cla.count_actions
			FROM nr_contact_list cl
			LEFT JOIN nr_campaign ca 
			ON cl.last_campaign_id = ca.id
			LEFT JOIN nr_content c 
			ON ca.content_id = c.id
			LEFT JOIN pw_pitch_list pl
			ON pl.contact_list_id = cl.id
			LEFT JOIN pw_pitch_order po
			ON pl.pitch_order_id = po.id
			LEFT JOIN nr_state st
			ON po.state_id = st.id
			LEFT JOIN (
				SELECT COUNT(*) as count_contacts, x.contact_list_id 
				FROM nr_contact_list_x_contact x
				GROUP BY contact_list_id
			) ct ON cl.id = ct.contact_list_id

			LEFT JOIN (
				SELECT COUNT(*) AS count_actions, cl.contact_list_id 
				FROM nr_contact_list_action cl
				GROUP BY contact_list_id
			) cla ON cl.id = cla.contact_list_id

			WHERE cl.company_id = ?
			ORDER BY cl.id DESC
			{$limit_str}";

		$query = $this->db->query($sql, 
			array($company_id));
		
		$results = array();
		foreach ($query->result() as $result)
			$results[] = $result;
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$url_format   = 'manage/contact/list/-chunk-';
		$listing_view = 'manage/contact/list';
		
		$chunkination->set_url_format($url_format);
		$chunkination->set_total($total_results);
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->add_order_detail_modal();
		$this->add_history_modal();
		
		$this->load->view($listing_view);
		$this->load->view('manage/footer');
	}

	public function search($chunk = 1)
	{
		if ($this->process_selected()) return;
		
		$terms = $this->input->get('terms');
		$terms_sql = sql_search_terms(array('cl.name'), $terms);
		
		$this->load->view('manage/header');
		
		$company_id = $this->newsroom->company_id;
		$chunkination = new Chunkination($chunk);
		$limit_str = $chunkination->limit_str();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS cl.*, 
			ct.count_contacts, 
			ca.name AS last_campaign_name,
			po.id AS pitch_order_id,
			po.city, po.keyword,
			po.date_created AS po_date_created,
			ca.is_sent, 
			c.title AS content_title,			
			st.abbr AS state_abbr,
			cla.count_actions
			FROM nr_contact_list cl
			LEFT JOIN nr_campaign ca 
			ON cl.last_campaign_id = ca.id
			LEFT JOIN nr_content c 
			ON ca.content_id = c.id
			LEFT JOIN pw_pitch_list pl
			ON pl.contact_list_id = cl.id
			LEFT JOIN pw_pitch_order po
			ON pl.pitch_order_id = po.id
			LEFT JOIN nr_state st
			ON po.state_id = st.id
			LEFT JOIN (
				SELECT COUNT(*) as count_contacts, x.contact_list_id 
				FROM nr_contact_list_x_contact x
				GROUP BY contact_list_id
			) ct ON cl.id = ct.contact_list_id

			LEFT JOIN (
				SELECT COUNT(*) AS count_actions, cl.contact_list_id 
				FROM nr_contact_list_action cl
				GROUP BY contact_list_id
			) cla ON cl.id = cla.contact_list_id

			WHERE cl.company_id = ?
			AND {$terms_sql}
			ORDER BY cl.id DESC
			{$limit_str}";
		
		$query = $this->db->query($sql, 
			array($company_id));
		
		$results = array();
		foreach ($query->result() as $result)
			$results[] = $result;
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$url_format   = 'manage/contact/list/search/-chunk-';
		$listing_view = 'manage/contact/list-search';
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

		$this->add_history_modal();
		
		$this->load->view($listing_view);
		$this->load->view('manage/footer');
	}

	public function duplicate($contact_list_id)
	{
		$list = Model_Contact_List::find($contact_list_id);
		if (!$list) $this->redirect('manage/contact/list');

		$dup_contact_list = new Model_Contact_List();
		$dup_contact_list->company_id = $this->newsroom->company_id;
		$dup_contact_list->name = "{$list->name} (COPY)";
		$dup_contact_list->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
		$dup_contact_list->save();

		$sql = "INSERT IGNORE 
				INTO nr_contact_list_x_contact (contact_list_id, contact_id)
  				SELECT ?, contact_id 
  				FROM nr_contact_list_x_contact 
  				WHERE contact_list_id = ?";

  		$this->db->query($sql, array($dup_contact_list->id, $list->id));

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The contact list has been duplicated.');
		$this->add_feedback($feedback);

		$this->redirect("manage/contact/list");
	}
	
	public function edit($contact_list_id, $chunk = 1, $chunk_size = 20)
	{
		$list = Model_Contact_List::find($contact_list_id);
		if (!$list) $this->redirect('manage/contact/list');
		$contact_list_id = (int) $contact_list_id;
		$company_id = $this->newsroom->company_id;
		$this->vd->list = $list;
		$this->title = $list->name;
		
		if ($list)
		{
			if ($list->company_id != $company_id)
				$this->denied();
			
			if ($this->process_selected($list)) return;
			
			$chunkination = new Chunkination($chunk);
			$chunkination->set_chunk_size($chunk_size);
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
				INNER JOIN nr_contact_list_x_contact x
				ON c.id = x.contact_id AND x.contact_list_id = ?
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

			$query = $this->db->query($sql, array($contact_list_id));
			$results = Model_Contact::from_db_all($query, $constructs);

			$total_results = $this->db
				->query("SELECT FOUND_ROWS() AS count")
				->row()->count;

			$sql = "SELECT COUNT(1) AS count FROM (
				SELECT 1 FROM nr_contact c 
				INNER JOIN nr_contact_list_x_contact x
				ON c.id = x.contact_id AND x.contact_list_id = ?
				GROUP BY c.email
			) AS c";

			$query = $this->db->query($sql, array($contact_list_id));
			$this->vd->unique_contact_count = $query->row()->count;
			
			$url_format = "manage/contact/list/edit/{$contact_list_id}/-chunk-/{$chunk_size}";
			$chunkination->set_url_format($url_format);
			$chunkination->set_total($total_results);
			$this->vd->chunk_size = $chunk_size;
			$this->vd->chunkination = $chunkination;
			$this->vd->results = $results;
			
			$criteria = array();
			$criteria[] = array('company_id', $this->newsroom->company_id);
			$criteria[] = array('is_pitch_wizard_list', 0);
			$criteria[] = array('is_nr_subscriber_list', 0);
				
			$lists = Model_Contact_List::find_all($criteria, array('name', 'asc'));
			$this->vd->lists = $lists;
			
			if ($m_pw_list = Model_Pitch_List::find('contact_list_id', $contact_list_id))
			{
				$m_pw_order = Model_Pitch_Order::find($m_pw_list->pitch_order_id);
				$m_state = Model_State::find($m_pw_order->state_id);
				$this->vd->pw_order = $m_pw_order;
				$this->vd->state_abbr = $m_state->abbr;
			}
		}

		$this->add_profile_modal();

		if ($cl_action = Model_Contact_List_Action::find('contact_list_id', $contact_list_id))
		{
			$this->add_history_modal();
			$this->vd->has_list_history = 1;
		}
		
		$this->load->view('manage/header');
		$this->load->view('manage/contact/list-edit');
		$this->load->view('manage/footer');
	}
	
	public function rename($contact_list_id)
	{
		if (!$contact_list_id) return;
		$list = Model_Contact_List::find($contact_list_id);
		$company_id = $this->newsroom->company_id;
		if (!$list || $list->company_id != $company_id)
			$this->denied();
		
		$list->name = $this->input->post('name');
		$list->save();
		$this->json(true);
	}
	
	public function edit_save()
	{
		$post = $this->input->post();
		$company_id = $this->newsroom->company_id;
		if (isset($post['contact_list_id']))
		     $contact_list_id = value_or_null($post['contact_list_id']);
		else $contact_list_id = null;
		
		foreach ($post as &$data)
			$data = value_or_null($data);
		
		$list = Model_Contact_List::find($contact_list_id);
		if ($list && $list->company_id != $company_id)
			$this->denied();
		
		if (!$list && !$this->input->post('name'))
			$this->redirect('manage/contact/list');
		
		if (!$list) $list = new Model_Contact_List();
		$list->values($post);		
		$list->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
		$list->company_id = $company_id;
		$list->save();
		
		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The contact list has been saved.');
		$this->add_feedback($feedback);
		
		// redirect back to the list of lists
		$redirect_url = "manage/contact/list/edit/{$list->id}";
		$this->set_redirect($redirect_url);
	}
	
	public function delete($contact_list_id)
	{
		if (!$contact_list_id) return;
		$list = Model_Contact_List::find($contact_list_id);
		$company_id = $this->newsroom->company_id;
		
		if ($list && $list->company_id != $company_id)
			$this->denied();
		
		if ($this->input->post('confirm'))
		{
			$list->delete();
			
			// load feedback message 
			$feedback = new Feedback('success', 'Deleted!', 'The contact list has been removed.');
			$this->add_feedback($feedback);
			
			// redirect back to type specific listing
			$redirect_url = 'manage/contact/list/';
			$this->set_redirect($redirect_url);
		}
		else
		{
			// load confirmation feedback 
			$this->vd->contact_list_id = $contact_list_id;
			$this->vd->compact_list = true;
			$feedback_view = 'manage/contact/partials/list_delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($contact_list_id);
		}
	}
	
	public function download($contact_list_id = null)
	{
		$list = Model_Contact_List::find($contact_list_id);
		if ($list->is_pitch_wizard_list) $this->denied();
		$company_id = $this->newsroom->company_id;
		if (!$list || $list->company_id != $company_id)
			$this->denied();
		
		$email_obfuscator = Media_Database_Contact_Access::email_obfuscator();
		$phone_obfuscator = Media_Database_Contact_Access::phone_obfuscator();

		$csv = new CSV_Writer('php://memory');
		$sql = "SELECT co.*, cp.raw_data,
			cn.name AS country_name, 
			re.name AS region_name, 
			lo.name AS locality_name,
			b1.name as beat_1_name,
			b2.name as beat_2_name,
			b3.name as beat_3_name
			FROM nr_contact_list_x_contact x 
			INNER JOIN nr_contact co ON x.contact_id = co.id
			LEFT JOIN nr_contact_x_contact_profile cxcp ON 
			cxcp.contact_id = co.id LEFT JOIN nr_contact_profile cp
			ON cp.id = cxcp.contact_profile_id
			LEFT JOIN nr_country cn ON co.country_id = cn.id
			LEFT JOIN nr_region re ON co.region_id = re.id
			LEFT JOIN nr_locality lo ON co.locality_id = lo.id
			LEFT JOIN nr_beat b1 ON b1.id = co.beat_1_id
			LEFT JOIN nr_beat b2 ON b2.id = co.beat_2_id
			LEFT JOIN nr_beat b3 ON b3.id = co.beat_3_id
			WHERE x.contact_list_id = ?
			/* fix for multiple contact profile 
			   causing contacts to multiple times */
			GROUP BY co.id";
		
		$query = $this->db->query($sql, array($list->id));

		$row = array();
		$row[] = 'Email';
		$row[] = 'First Name';
		$row[] = 'Last Name';
		$row[] = 'Company Name';
		$row[] = 'Job Title';
		$row[] = 'Twitter';
		$row[] = 'Phone';
		$row[] = 'Country';
		$row[] = 'Region';
		$row[] = 'City';
		$row[] = 'Beat 1';
		$row[] = 'Beat 2';
		$row[] = 'Beat 3';
		$csv->write($row);

		foreach ($query->result() as $record)
		{
			$contact = Model_Contact::from_object($record);
			$raw_data = $contact->raw_data();

			// use phone info from the contact profile
			if ($raw_data && !empty($raw_data->phone))
				$contact->phone = $raw_data->phone;

			if ($contact->is_media_db_contact)
			{
				$contact->phone = $phone_obfuscator->obfuscate($contact->phone);
				$contact->email = $email_obfuscator->obfuscate($contact->email, '*');
			}
			
			$row = array();
			$row[] = $contact->email;
			$row[] = $contact->first_name;
			$row[] = $contact->last_name;
			$row[] = $contact->company_name;
			$row[] = $contact->title;
			$row[] = $contact->twitter;
			$row[] = (new Phone_Number($contact->phone))->formatted();			
			$row[] = $record->country_name;
			$row[] = $record->region_name;
			$row[] = $record->locality_name;
			$row[] = $record->beat_1_name;
			$row[] = $record->beat_2_name;
			$row[] = $record->beat_3_name;
			$csv->write($row);
		}
		
		$handle = $csv->handle();
		rewind($handle);
		
		$this->load->helper('download');
		force_download('contacts.csv', stream_get_contents($handle));
		return;
	}

	public function remove_duplicates($id)
	{
		$list = Model_Contact_List::find($id);
		$company_id = $this->newsroom->company_id;
		if (!$list || $list->company_id != $company_id)
			$this->denied();

		$sql = "DELETE FROM nr_contact_list_x_contact
			WHERE contact_list_id = ? AND contact_id 
			NOT IN (SELECT * FROM (SELECT co.id FROM nr_contact_list_x_contact x 
				INNER JOIN nr_contact co ON x.contact_id = co.id
				WHERE x.contact_list_id = ?
				GROUP BY co.email) AS new)";

		$this->db->query($sql, array($id, $id));

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Removed duplicate email addresses.');
		$this->add_feedback($feedback);

		// redirect back to the list of lists
		$redirect_url = "manage/contact/list/edit/{$list->id}";
		$this->redirect($redirect_url);
	}

	public function load_history_modal($list_id)
	{
		$this->render_history_modal($list_id);
	}
	
}