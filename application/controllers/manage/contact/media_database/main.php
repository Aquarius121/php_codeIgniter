<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/media_database_controller_trait');
load_controller('shared/media_database_filters_trait');
load_controller('shared/media_database_profile_trait');
load_controller('shared/fetch_tweets_trait');

class Main_Controller extends Manage_Base {
	
	public $title = 'Media Database';
	
	use Fetch_Tweets_Trait;
	use Media_Database_Profile_Trait;
	use Media_Database_Filters_Trait;
	use Media_Database_Controller_Trait {
		process_response as _process_response;
		process_results as _process_results;
		execute_id_list as _execute_id_list;
		execute as _execute;
	}
	
	protected function process_email_obfuscation($results)
	{
		$obfuscator = Media_Database_Contact_Access::email_obfuscator();
		foreach ($results as $result)
			$result->email = $obfuscator->obfuscate_parts($result->email);
		return $results;
	}
	
	protected function process_renderer($response)
	{
		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array('is_pitch_wizard_list', 0);
		$criteria[] = array('is_nr_subscriber_list', 0);
		$lists = Model_Contact_List::find_all($criteria, array('name', 'asc'));
		$this->vd->lists = $lists;
		
		$this->vd->results = $response->results;
		$this->vd->chunkination = $response->chunkination;
		
		$results_view = 'manage/contact/media_database/list';
		$response->results_html = $this->load->view($results_view, null, true);
		unset($response->results);
		return $response;
	}
	
	protected function process_results($results)
	{
		$results = $this->_process_results($results);
		$results = $this->process_email_obfuscation($results);
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
	
	public function index()
	{
		// url to this controller that must expose the execute method
		$this->vd->database_url = 'manage/contact/media_database';
		
		$this->add_create_list_modal();
		$this->add_filter_modal();
		$this->add_options_modal();
		$this->add_profile_modal();
		
		$sql = "SELECT COUNT(1) AS count FROM nr_contact 
			WHERE is_media_db_contact = 1";
		$this->vd->total_contacts = 
			$this->db->query($sql)->row()->count;
				
		$this->load->view('manage/header');
		$this->load->view('manage/contact/media_database/index');
		$this->load->view('manage/footer');
	}
	
	public function add_to_list()
	{
		$form_data = array();
		$form_data_str = $this->input->post('form_data');
		parse_str($form_data_str, $form_data);
		$selected_contact_count = 0;
		$is_new_list_created = false;

		if ($this->input->post('create'))	
		{
			$company_id = $this->newsroom->company_id;
			$contact_list = new Model_Contact_List();
			$contact_list->date_created = Date::$now->format(Date::FORMAT_MYSQL);
			$contact_list->company_id = $company_id;
			$contact_list->name = trim($this->input->post('name'));
			$contact_list->save();

			$is_new_list_created = true;
		}
		else
		{
			$company_id = $this->newsroom->company_id;
			$contact_list_id = $form_data['contact_list_id'];
			$contact_list = Model_Contact_List::find($contact_list_id);
			if (!$contact_list || $contact_list->company_id != $company_id)
				$this->denied();			
		}

		$pre_contact_count = $contact_list->count_contacts();
		$options = json_decode($this->input->post('options'));
		
		if ($options->has_select_all)
		{
			$id_list = $this->_execute_id_list($options);
			$contact_list->add_all_contacts($id_list);
			$selected_contact_count = count($id_list);
		}
		else
		{
			foreach ($form_data['selected'] as $contact_id => $val)
			{
				$contact = Model_Contact::find($contact_id);
				if (!$contact) continue;
				if (!$contact->is_media_db_contact) continue;
				$contact_list->add_contact($contact);
				$selected_contact_count++;
			}
		}
		
		$post_contact_count = $contact_list->count_contacts();
		$contact_count_diff = $post_contact_count - $pre_contact_count;
		$duplicates_count = $selected_contact_count - $contact_count_diff;
		$response = new stdClass();
		$response->added_count = $contact_count_diff;
		$response->duplicates_count = $duplicates_count;

		// log list actions
		if ($contact_count_diff)
		{
			if ($is_new_list_created)			
				$cl_action = Model_Contact_List_Action::log_create_list_from_mdb($contact_list->id);
			else
				$cl_action = Model_Contact_List_Action::log_add_contacts_from_mdb($contact_list_id);

			// logging number of contacts added
			Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
				Model_Contact_List_Action_Detail::DETAIL_MDB_NUM_CONTACTS_ADDED, $contact_count_diff);

			// logging search text
			if (!empty($options->search))
			{
				$mdb_search_text = new Model_Contact_List_Action_MDB_Search_Text();
				$mdb_search_text->text = $options->search;
				$mdb_search_text->save();
				Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
					Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_SEARCH_TEXT_ID, $mdb_search_text->id);
			}

			// logging beats
			if (isset($options->filters->beats) && count($options->filters->beats))
			{
				foreach ($options->filters->beats as $beat)
					Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
						Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_BEAT_ID, $beat);
			}

			// logging media types
			if (isset($options->filters->media_types) && count($options->filters->media_types))
			{
				foreach ($options->filters->media_types as $media_type)
					Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
						Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_MEDIA_TYPE_ID, $media_type);
			}

			// logging roles
			if (isset($options->filters->roles) && count($options->filters->roles))
			{
				foreach ($options->filters->roles as $role)
					Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
						Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_ROLE_ID, $role);
			}

			// logging coverage
			if (isset($options->filters->coverages) && count($options->filters->coverages))
			{
				foreach ($options->filters->coverages as $coverage)
					Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
						Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_COVERAGE_ID, $coverage);
			}

			// logging countries
			if (isset($options->filters->countries) && count($options->filters->countries))
			{
				foreach ($options->filters->countries as $country)
					Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
						Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_COUNTRY_ID, $country);
			}

			// logging regions
			if (isset($options->filters->regions) && count($options->filters->regions))
			{
				foreach ($options->filters->regions as $region)
					Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
						Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_REGION_ID, $region);
			}

			// logging localities
			if (isset($options->filters->localities) && count($options->filters->localities))
			{
				foreach ($options->filters->localities as $locality)
					Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
						Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_LOCALITY_ID, $locality);
			}

			// logging unique email addresses only
			if (isset($options->unique_only) && $options->unique_only)
			{
				Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
					Model_Contact_List_Action_Detail::DETAIL_MDB_OPTION_UNIQUE_EMAILS, 1);
			}

			// logging contacts with pictures only
			if (isset($options->pictures_only) && $options->pictures_only)
			{
				Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
					Model_Contact_List_Action_Detail::DETAIL_MDB_OPTION_CONTACT_WITH_PICS, 1);
			}
		}


		$this->json($response);
	}
	
	protected function add_create_list_modal()
	{
		$create_list_modal = new Modal();
		$create_list_modal->set_title('Create List');
		$modal_view = 'manage/contact/media_database/create_list_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$create_list_modal->set_content($modal_content);
		$this->add_eob($create_list_modal->render(400, 44));
		$this->vd->create_list_modal_id = $create_list_modal->id;
	}
	
}

?>