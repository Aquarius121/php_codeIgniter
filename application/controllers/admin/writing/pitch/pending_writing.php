<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/pitch/main');

class Pending_Writing_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Pending Writing';
	}
	
	public function index()
	{
		$this->redirect('admin/writing/pitch/pending_writing/all');
	}

	public function all($chunk = 1, $filter = 1)
	{
		if ($this->input->post('bt_send_to_writer'))
		{
			$this->send_to_writer();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Message sent to writer successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/writing/pitch/pending_writing/all'));
		}
		
		if ($this->input->post('bt_send_to_customer'))
		{
			$this->send_to_customer_to_revise_details();
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Message sent to customer successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/writing/pitch/pending_writing/all'));		
		}
		
		
		$redirect_url = 'admin/writing/pitch/pending_writing/all';				
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/writing/pitch/pending_writing/all/-chunk-');
		$chunkination->set_url_format($url_format);		
		
		$this->vd->filters = array();
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];		
		$limit_str = $chunkination->limit_str();		
		
		$pending_status_list = 
					sql_in_list(array(Model_Pitch_Order::STATUS_ASSIGNED_TO_WRITER, 
						Model_Pitch_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION, 
						Model_Pitch_Order::STATUS_SENT_BACK_TO_WRITER, 
						Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE, 
						Model_Pitch_Order::STATUS_CUSTOMER_REVISE_DETAILS));
						
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				c.id, c.title, c.type, 
				c.slug,	po.status, 
				po.id as order_id,			
				po.status, po.city, 
				po.keyword, po.date_created,
				po.writer_id, po.delivery, po.order_type
				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN nr_content c 
				ON ca.content_id = c.id
				{$additional_tables}
				WHERE status IN ({$pending_status_list})
				AND {$filter}				
				AND po.is_archived = 0
				ORDER BY po.date_of_last_status DESC 
				{$limit_str}";
		
		$db_result = $this->db->query($sql);
		$results = Model_Content::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		
		$chunkination->set_total($total_results);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/writing/pitch/pending_writing/all';
			$this->redirect(gstring($url));
		}
		
		foreach ($results as $result)
			$result->writer = Model_MOT_Writer::find($result->writer_id);
		
		$pending_log_modal = new Modal();
		$pending_log_modal->set_title('Pitch Writing Comments');
		$this->add_eob($pending_log_modal->render(420, 350));
		$this->vd->pending_log_modal_id = $pending_log_modal->id;		
		
		$this->add_order_detail_modal();
		$view_name = "admin/writing/pitch/pending_writing";
		$this->render($chunkination, $results, $view_name);
	}
	
	public function load_pending_modal($pitch_order_id)
	{
		$this->vd->history = Model_Pitch_Writing_Process::get_pre_writing_conversation($pitch_order_id);
		$this->vd->pitch_order_id = $pitch_order_id;
		$this->load->view('admin/writing/pitch/partials/pre_writing_log_modal_box');
	}
	
	protected function send_to_writer()
	{
		$post = $this->input->post();
		$pitch_order_id = $post['pitch_order_id'];
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_SENT_BACK_TO_WRITER;
		$m_pw_order->save();
		
		Model_Pitch_Writing_Process::create_and_save($pitch_order_id, 
								Model_Pitch_Writing_Process::PROCESS_SENT_BACK_TO_WRITER, $post['comments']);
		
		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->send_message_to_writer($pitch_order_id, $post['comments']);
	}
	
	protected function send_to_customer_to_revise_details()
	{
		$post = $this->input->post();
		$pitch_order_id = $post['pitch_order_id'];
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE;
		$m_pw_order->save();
		
		Model_Pitch_Writing_Process::create_and_save($pitch_order_id, 
								Model_Pitch_Writing_Process::PROCESS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE, $post['comments']);
		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->send_to_customer_to_revise_details($pitch_order_id, $post['comments']);
	}
	
}

?>