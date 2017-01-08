<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');
load_controller('shared/common_pw_orders_trait');

class Main_Controller extends Admin_Base {

	use Common_PW_Orders_Trait;
	
	const LISTING_CHUNK_SIZE = 20;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->vd->title[] = 'Media Outreach';
		$this->vd->title[] = 'Pitch Wizard Orders';
	}

	public function index($chunk = 1)
	{
		$this->redirect(gstring('admin/contact/pitch_wizard_order/order/all'));
	}
	
	public function load_upload_modal($pitch_order_id, $is_reupload = 0)
	{
		$this->vd->pitch_order_id = $pitch_order_id;
		$this->vd->is_reupload = $is_reupload;
		$this->load->view('admin/contact/pitch_wizard_order/partials/upload_modal');
	}	
	
	
	public function load_rejection_log_modal($pitch_list_id)
	{
		$this->vd->rejections = Model_Pitch_List_Process::get_rejection_conversation($pitch_list_id);
		$this->vd->pitch_list_id = $pitch_list_id;
		$this->load->view('admin/contact/pitch_wizard_order/partials/rejection_log_modal_box');
	}
	
	public function load_order_detail_modal($pitch_order_id)
	{
		$this->order_detail_modal($pitch_order_id);
	}
	
	public function store_csv()
	{
		$company_id = $this->newsroom->company_id;
		$file = Stored_File::from_uploaded_file('csv');
		if (!$file->exists()) $this->redirect(gstring('manage/contact/import'));
		$file->move();
		
		$stored_file_id = $file->save_to_db();		
		$csv = new CSV_Reader($file->destination);
		$limit = 5000;
		$contacts = array();
		
		while ($row = $csv->read())
		{
			$contact = Model_Contact::create_from_csv_row(0, $row);
			if (!$contact) continue;
			$contacts[] = $contact;
			if (count($contacts) === $limit)
				break;
		}
		
		$csv->close();
		
		$this->vd->results = $contacts;
		$view = 'admin/contact/pitch_wizard_order/partials/upload-preview';
		$preview = $this->load->view($view, null, true);
		
		
		$response = array(
			'filename' => $file->filename,
			'stored_file_id' => $stored_file_id,
			'preview' => $preview,
		);
		
		return $this->json($response);
	}

	public function find_builder_list()
	{
		$builder_name = $this->input->post('builder_name');
		$builder_name = sql_loose_term($builder_name);

		$sql = "SELECT cb.* FROM nr_contact_builder cb
			WHERE cb.name LIKE '%{$builder_name}%'
			LIMIT 10";

		$dbr = $this->db->query($sql);
		$this->vd->lists = Model_Contact_Builder::from_db_all($dbr);
		$this->load->view('admin/contact/pitch_wizard_order/partials/find_builder_list');
	}
	
	public function save_list()
	{
		$post = $this->input->post();
		if ($this->input->post('is_reupload'))
			$this->del_list($post['pitch_order_id']);
			
		$pitch_order_id = $post['pitch_order_id'];
		$m_pitch_order = Model_Pitch_Order::find($pitch_order_id);
		$m_campaign = Model_Campaign::find($m_pitch_order->campaign_id);
		$m_content = Model_Content::find($m_campaign->content_id);
		$m_contact_builder = null;

		if (!empty($post['builder_select']))
		{
			$m_contact_builder = Model_Contact_Builder::find($post['builder_select']);
			if (!$m_contact_builder) return;
		}
		else
		{
			$stored_file_id = $this->input->post('stored_file_id');
			if ( ! $stored_file_id) $this->redirect(gstring('admin/contact/pitch_wizard_order/upload_list'));
			
			$file = Stored_File::from_db($stored_file_id);
			if ( ! $file) $this->redirect(gstring('admin/contact/pitch_wizard_order/upload_list'));
			if ($file->filename != $this->input->post('filename'))
				$this->denied();
		}
		
		$lists = array();
		
		$name = "Pitch: {$m_content->title}";
		$list = new Model_Contact_List();
		$list->date_created = Date::$now->format(DATE::FORMAT_MYSQL);
		$list->name = $name;
		$list->last_campaign_id = $m_pitch_order->campaign_id;
		$list->is_pitch_wizard_list = 1;
		$list->save();
		
		$m_pitch_list = Model_Pitch_List::find('pitch_order_id', $pitch_order_id);
		$m_pitch_list->contact_list_id = $list->id;
		$m_pitch_list->status = Model_Pitch_List::STATUS_SENT_TO_ADMIN;
		$m_pitch_list->date_list_submitted = Date::$now->format(Date::FORMAT_MYSQL);
		$m_pitch_list->save();
		
		Model_Pitch_List_Process::create_and_save($m_pitch_list->id, Model_Pitch_List_Process::PROCESS_SENT_TO_ADMIN);
		
		if ($m_contact_builder)
		{
			$contacts_id_list = $m_contact_builder->contacts_id_list();
			$count = count($contacts_id_list);
			$list->add_all_contacts($contacts_id_list);
		}
		else
		{
			$this->session->write('import_csv_count', 0);
			$this->session->commit();
			$csv = new CSV_Reader($file->source);
			$count = 0;
			
			while ($row = $csv->read())
			{
				$contact = Model_Contact::create_from_csv_row(0, $row);
				if ( ! $contact) continue;
				$contact->save();
				$count++;
				
				$list->add_contact($contact);		
				
				if ($count % 100 == 0)
				{
					$this->session->write('import_csv_count', $count);
					$this->session->commit();
				}
			}
			
			$csv->close();
		}
		
		// load feedback message for the user
		$feedback_view = 'manage/contact/partials/contact_import_feedback';
		$feedback = $this->load->view($feedback_view, array('count' => $count), true);
		$this->add_feedback($feedback);
		
		if ($post['is_reupload'])
			$redirect_url = 'admin/contact/pitch_wizard_order/rejected_list';
		else
			$redirect_url = 'admin/contact/pitch_wizard_order/upload_list';
		$this->set_redirect(gstring($redirect_url));
		
	}
	
	protected function render($chunkination, $results, $view_name)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		$this->add_counters();
		$this->load->view('admin/header');
		$this->load->view('admin/contact/menu');
		$this->load->view('admin/pre-content');
		$this->load->view($view_name);
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	protected function add_rejection_modal()
	{
		$rejection_modal = new Modal();
		$rejection_modal->set_title('Rejection Log');
		$this->add_eob($rejection_modal->render(420, 350));
		$this->vd->rejection_modal_id = $rejection_modal->id;
	}
	
	protected function add_upload_modal()
	{
		$upload_modal = new Modal();
		$upload_modal->set_title('Upload List');
		$this->add_eob($upload_modal->render(700, 400));
		$this->vd->upload_modal_id = $upload_modal->id;

		$list_builder_modal = new Modal();
		$list_builder_modal->set_title('Select from MDB List Builder');
		$modal_view = 'admin/contact/pitch_wizard_order/partials/list_builder_modal';
		$modal_content = $this->load->view($modal_view, null, true);
		$list_builder_modal->set_content($modal_content);
		$modal_view = 'admin/contact/pitch_wizard_order/partials/list_builder_modal_footer';
		$modal_content = $this->load->view($modal_view, null, true);
		$list_builder_modal->set_footer($modal_content);
		$this->add_eob($list_builder_modal->render(400, 400));
		$this->vd->list_builder_modal_id = $list_builder_modal->id;
	}
	
	protected function add_counters()
	{
		$this->vd->assign_count = Model_Pitch_List::count_all(array(array('is_archived', 0), array('status', Model_Pitch_List::STATUS_NOT_ASSIGNED)));
		$this->vd->pending_count = Model_Pitch_List::count_all(array(array('is_archived', 0), array('status', 
									Model_Pitch_List::STATUS_ASSIGNED_TO_LIST_BUILDER)));
		$this->vd->review_count = Model_Pitch_List::count_all(array(array('is_archived', 0), array('status', Model_Pitch_List::STATUS_SENT_TO_ADMIN)));
		$this->vd->rejected_count = Model_Pitch_List::count_all(array(array('is_archived', 0), array('status', Model_Pitch_List::STATUS_ADMIN_REJECTED)));
	}
	
	
	protected function fetch_pending_list($chunkination, $filter = 1)
	{
		$limit_str = $chunkination->limit_str();
		
		$this->vd->filters = array();
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			po.id as order_id,
			po.keyword, po.city, po.date_created,
			po.delivery,
			pl.status as pw_list_status,
			pl.date_of_last_status,
			ca.content_id, ca.date_send,
			st.abbr as state_abbr,
			pl.id as list_id,
			ub.first_name as user__first_name,
			ub.last_name as user__last_name,
			plp.process_date as date_assigned
			FROM nr_campaign ca
			INNER JOIN pw_pitch_order po 
			ON po.campaign_id = ca.id
			LEFT JOIN pw_pitch_list pl
			ON pl.pitch_order_id = po.id
			LEFT JOIN nr_state st
			ON po.state_id = st.id	
			LEFT JOIN nr_content c 
			ON ca.content_id = c.id	
			LEFT JOIN nr_user ub
			ON pl.list_builder_user_id = ub.id
			LEFT JOIN pw_pitch_list_process plp
			ON plp.pitch_list_id = pl.id
			AND plp.process = ?
			{$additional_tables}
			WHERE pl.status = ?
			AND {$filter}
			ORDER BY pl.date_of_last_status DESC
			{$limit_str}";
		
		$db_result = $this->db->query($sql, array(
			Model_Pitch_List_Process::PROCESS_ASSIGNED_TO_LIST_BUILDER, 
			Model_Pitch_List::STATUS_ASSIGNED_TO_LIST_BUILDER));
		$results = Model_Pitch_Order::from_db_all($db_result, array(
			'user' => 'Model_User',
		));
		
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
			
		return $results;
	}	
	
	protected function del_list($pitch_order_id)
	{
		$m_pitch_list = Model_Pitch_List::find('pitch_order_id', $pitch_order_id);
		$m_contact_list = Model_Contact_List::find($m_pitch_list->contact_list_id);
		$m_contact_list->delete();
	}
	
	protected function add_user_company_search_filter($filter = 1)
	{	
		$use_additional_tables = false;
		$additional_tables = '';
		
		if (($filter_user = $this->input->get('filter_user')) !== false)
		{
			$filter_user = (int) $filter_user;
			$this->create_filter_user($filter_user);	
			// restrict search results to this user
			$filter = "{$filter} AND u.id = {$filter_user}";
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
			 
		return array('filter' => $filter, 'additional_tables' => $additional_tables);
	}
	
	protected function send_to_list_builder($post)
	{
		$pitch_list_id = $post['pitch_list_id'];
		$m_pitch_list = Model_Pitch_List::find($pitch_list_id);
		$m_pitch_list->status = Model_Pitch_List::STATUS_ADMIN_REJECTED;
		$m_pitch_list->save();
		
		Model_Pitch_List_Process::create_and_save($pitch_list_id,
			Model_Pitch_List_Process::PROCESS_ADMIN_REJECTED,
			$post['comments']); 
		
		$pwm = new Pitch_Wizard_Mailer();
		$pwm->send_rejection_to_list_builder($pitch_list_id, $post['comments']);
	}
	
}

?>