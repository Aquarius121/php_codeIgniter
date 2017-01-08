<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Import_Controller extends Manage_Base {
	
	const CSV_PREVIEW_COUNT = 5;
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Import Contacts';
	}
	
	public function index()
	{
		$company_id = $this->newsroom->company_id;
		
		$vd = array();
		$vd['lists_allow_create'] = true;
		
		$criteria = array();
		$criteria[] = array('company_id', $this->newsroom->company_id);
		$criteria[] = array('is_pitch_wizard_list', 0);
		$criteria[] = array('is_nr_subscriber_list', 0);
			
		$vd['lists'] = Model_Contact_List::find_all(
			$criteria, array('name', 'asc'));
		
		$this->load->view('manage/header');
		$this->load->view('manage/contact/import', $vd);
		$this->load->view('manage/footer');
	}
	
	public function store_csv()
	{
		$company_id = $this->newsroom->company_id;
		$file = Stored_File::from_uploaded_file('csv');
		if (!$file->exists()) $this->redirect('manage/contact/import');
		$file->move();
		
		$stored_file_id = $file->save_to_db();		
		$csv = new CSV_Reader($file->destination);
		$limit = static::CSV_PREVIEW_COUNT;
		$contacts = array();
		
		while ($row = $csv->read())
		{
			$contact = Model_Contact::create_from_csv_row($company_id, $row);
			if (!$contact) continue;
			$contacts[] = $contact;
			if (count($contacts) === $limit)
				break;
		}
		
		$csv->close();
		
		$this->vd->results = $contacts;
		$view = 'manage/contact/import-preview';
		$preview = $this->load->view($view, null, true);
		
		$response = array(
			'filename' => $file->filename,
			'stored_file_id' => $stored_file_id,
			'preview' => $preview,
		);
		
		return $this->json($response);
	}
	
	public function progress()
	{
		$count = $this->session->read('import_csv_count');
		return $this->json($count);
	}
	
	public function save()
	{
		$post = $this->input->post();
		$company_id = $this->newsroom->company_id;
		$stored_file_id = $this->input->post('stored_file_id');
		if (!$stored_file_id) $this->redirect('manage/contact/import');

		$file = Stored_File::from_db($stored_file_id);
		if (!$file) $this->redirect('manage/contact/import');
		if ($file->filename != $this->input->post('filename'))
			$this->denied();
		
		$list_name = sprintf('Imported Contacts %s',
			Date::out()->format('Y-m-d'));

		$criteria = array();
		$criteria[] = array('name', $list_name);
		$criteria[] = array('company_id', $this->newsroom->company_id);

		if (!$list = Model_Contact_List::find($criteria))
		{
			$list = new Model_Contact_List();
			$list->name = $list_name;
			$list->company_id = $this->newsroom->company_id;
			$list->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
			$list->save();
		}

		$lists = array($list);		
		
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
			$list->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
			$list->company_id = $company_id;
			$list->name = $name;
			$list->save();			
			$lists[] = $list->id;
		}
		
		$this->session->write('import_csv_count', 0);
		$this->session->commit();
		$csv = new CSV_Reader($file->source);
		$count = 0;
		
		while ($row = $csv->read())
		{
			$contact = Model_Contact::create_from_csv_row($company_id, $row);
			if (!$contact) continue;
			$contact->save();
			$count++;
			
			if ($contact->id) 
			{
				$contact->add_lists($lists);
			}
			
			if ($count % 100 == 0)
			{
				$this->session->write('import_csv_count', $count);
				$this->session->commit();
			}
		}

		// log list action
		if (!$import_file = Model_Contact_List_Action_Import_File::find($stored_file_id))
		{
			$import_file = new Model_Contact_List_Action_Import_File();
			$import_file->stored_file_id = $stored_file_id;
			$import_file->name = $post['fake_file_name'];
			$import_file->save();
		}

		foreach ($lists as $list)
		{
			if (!$list->id)
				continue;

			$cl_action = Model_Contact_List_Action::log_import_from_csv($list->id);

			Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
				Model_Contact_List_Action_Detail::DETAIL_CSV_STORED_FILE_ID, $stored_file_id);

			Model_Contact_List_Action_Detail::create_and_save($cl_action->id, 
				Model_Contact_List_Action_Detail::DETAIL_CSV_NUM_CONTACTS_IMPORTED, $count);
			
		}
		
		$csv->close();
		
		// load feedback message for the user
		$feedback = new Feedback('success');
		$feedback->set_title('Done!');
		$feedback->set_text("{$count} contacts were added or updated.");
		$this->add_feedback($feedback);
		
		// redirect back to the contacts list
		$redirect_url = 'manage/contact/contact';
		$this->set_redirect($redirect_url);
	}
	
}

?>