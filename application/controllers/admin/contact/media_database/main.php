<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');
load_controller('shared/media_database_controller_trait');
load_controller('shared/media_database_filters_trait');
load_controller('shared/media_database_profile_trait');
load_controller('shared/contact_to_tags_trait');
load_controller('shared/fetch_tweets_trait');

class Main_Controller extends Admin_Base {	
	
	public $title = 'Media Database';
	
	use Fetch_Tweets_Trait;
	use Contact_To_Tags_Trait;
	use Media_Database_Profile_Trait;
	use Media_Database_Filters_Trait;
	use Media_Database_Controller_Trait {
		process_response as _process_response;
		process_results as _process_results;
		execute_id_list as _execute_id_list;
		execute as _execute;
	}
	
	protected function process_renderer($response)
	{
		$lists = Model_Contact_Builder::find_all(null, array('id', 'desc'));
		$this->vd->lists = $lists;

		$this->vd->results = $response->results;
		$this->vd->chunkination = $response->chunkination;

		$results_view = 'admin/contact/media_database/list';
		$response->results_html = $this->load->view($results_view, null, true);
		unset($response->results);
		return $response;
	}
	
	protected function process_results($results)
	{
		$results = $this->_process_results($results);
		return $results;
	}
	
	protected function process_response($response)
	{
		$response = $this->_process_response($response);
		$response = $this->process_renderer($response);
		return $response;
	}
	
	public function execute()
	{
		$options = $this->input->post('options');
		$options = json_decode($options);
		$this->_execute($options);
	}
	
	public function index($chunk = 1)
	{
		// url to this controller that must expose the execute method
		$this->vd->database_url = 'admin/contact/media_database';
		
		$this->add_create_list_modal();
		$this->add_filter_modal();
		$this->add_options_modal();
		$this->add_profile_modal();

		$selected = $this->input->post('selected');

		if (is_array($selected))
			foreach ($selected as $contact_id => $v)
				Model_Contact_MDB_Approval::find($contact_id)->delete();

		if ($this->input->post('confirm'))
		{
			foreach ($selected as $contact_id => $v)
			{
				$contact = Model_Contact::find($contact_id);
				$contact->is_media_db_contact = 1;
				$contact->save();

				$approved = new Model_Contact_MDB_Approved($contact_id);
				$approved->save();

				$tags = $this->generate_tags($contact);
				$contact->add_tags($tags);
			}
		}

		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(50);
		$limit_str = $chunkination->limit_str();
		$sql = "SELECT SQL_CALC_FOUND_ROWS contact_id AS id
			FROM nr_contact_mdb_approval
			{$limit_str}";

		$id_list = array();
		$db_result = $this->db->query($sql);
		foreach ($db_result->result() as $row)
			$id_list[] = $row->id;
				
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		// check for out of bounds
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds()) 
			$this->redirect($chunkination->url(1));
		
		$m_contacts = $this->__execute_internal_load($id_list);
		$m_contacts = $this->process_results($m_contacts);	
		$this->vd->results = $m_contacts;
		$this->vd->chunkination = $chunkination;

		if (!$chunkination->total())
		{
			$sql = "SELECT COUNT(1) AS count FROM nr_contact 
				WHERE is_media_db_contact = 1";
			$this->vd->total_contacts = 
				$this->db->query($sql)->row()->count;
		}

		$this->load->view('admin/header');
		$this->load->view('admin/contact/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/contact/media_database/index');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function add_to_list()
	{
		$form_data = array();
		$form_data_str = $this->input->post('form_data');
		parse_str($form_data_str, $form_data);
		$selected_contact_count = 0;
		
		if ($this->input->post('create'))	
		{
			$user_id = Auth::user()->id;
			$contact_builder = new Model_Contact_Builder();
			$contact_builder->date_created = Date::$now->format(Date::FORMAT_MYSQL);
			$contact_builder->user_id = $user_id;
			$contact_builder->name = trim($this->input->post('name'));
			$contact_builder->save();
		}
		else
		{
			$company_id = $this->newsroom->company_id;
			$contact_builder_id = $form_data['contact_builder_id'];
			$contact_builder = Model_Contact_Builder::find($contact_builder_id);
			if (!$contact_builder) $this->denied();
		}
		
		$pre_contact_count = $contact_builder->count_contacts();
		$options = json_decode($this->input->post('options'));
		
		if ($options->has_select_all)
		{
			$id_list = $this->_execute_id_list($options);
			$contact_builder->add_all_contacts($id_list);
			$selected_contact_count = count($id_list);
		}
		else
		{
			foreach ($form_data['selected'] as $contact_id => $val)
			{
				$contact = Model_Contact::find($contact_id);
				if (!$contact) continue;
				if (!$contact->is_media_db_contact) continue;
				$contact_builder->add_contact($contact);
				$selected_contact_count++;
			}
		}
		
		$post_contact_count = $contact_builder->count_contacts();
		$contact_count_diff = $post_contact_count - $pre_contact_count;
		$duplicates_count = $selected_contact_count - $contact_count_diff;
		$response = new stdClass();
		$response->added_count = $contact_count_diff;
		$response->duplicates_count = $duplicates_count;
		$this->json($response);
	}
	
	protected function add_create_list_modal()
	{
		$create_list_modal = new Modal();
		$create_list_modal->set_title('Create List');
		$modal_view = 'admin/contact/media_database/create_list_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$create_list_modal->set_content($modal_content);
		$this->add_eob($create_list_modal->render(400, 44));
		$this->vd->create_list_modal_id = $create_list_modal->id;
	}
	
}

?>