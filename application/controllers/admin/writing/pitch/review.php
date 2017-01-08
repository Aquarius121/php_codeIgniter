<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/pitch/main');

class Review_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Review';
	}
	
	public function index()
	{
		$this->redirect('admin/writing/pitch/review/all');
	}

	public function all($chunk = 1, $filter = 1)
	{
		$redirect_url = 'admin/writing/pitch/review/all';				
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/writing/pitch/review/all/-chunk-');
		$chunkination->set_url_format($url_format);		
		
		$this->vd->filters = array();
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];		
		$limit_str = $chunkination->limit_str();		
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				c.id, c.title, c.type, 
				c.slug,	po.status, 
				po.id as order_id,			
				po.status, po.city, 
				po.keyword, po.date_created,
				po.writer_id, po.delivery,
				pc.date_written, po.order_type
				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN pw_pitch_content pc
				ON pc.pitch_order_id = po.id
				LEFT JOIN nr_content c 
				ON ca.content_id = c.id
				{$additional_tables}
				WHERE status = ?
				AND {$filter}				
				AND po.is_archived = 0
				ORDER BY po.date_of_last_status DESC 
				{$limit_str}";
		
		$db_result = $this->db->query($sql, array(Model_Pitch_ORDER::STATUS_WRITTEN_SENT_TO_ADMIN));
		$results = Model_Content::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		
		$chunkination->set_total($total_results);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/writing/pitch/review/all';
			$this->redirect(gstring($url));
		}
		
		foreach ($results as $result)
			$result->writer = Model_MOT_Writer::find($result->writer_id);
		
		$this->add_order_detail_modal();
		$view_name = "admin/writing/pitch/review";
		$this->render($chunkination, $results, $view_name);
	}
	
	protected function send_to_customer_for_review()
	{
		$post = $this->input->post();
		$pitch_order_id = $post['pitch_order_id'];
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->status = Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER;
		$m_pw_order->save();
		
		$m_pw_content = Model_Pitch_Content::find($pitch_order_id);
		$m_pw_content->subject = $post['subject'];
		$m_pw_content->pitch_text = $post['pitch_text'];
		$m_pw_content->save();
		
		Model_Pitch_Writing_Process::create_and_save($pitch_order_id, 
			Model_Pitch_Writing_Process::PROCESS_SENT_TO_CUSTOMER);
	}
	
	
	
}

?>