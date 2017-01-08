<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/pitch/main');

class Assign_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Assign';
	}
	
	public function index()
	{
		$this->redirect('admin/writing/pitch/assign/all');
	}

	public function all($chunk = 1, $filter = 1)
	{
		$post = $this->input->post();
		if ($post['pitch_order_id'])
		{
			$pitch_order_id = $post['pitch_order_id'];
			$writer_id = $post['writer_id'];
			
			if ($pitch_order_id && $writer_id)
			{
				$this->assign_writing_task($pitch_order_id, $writer_id);
				$feedback = new Feedback('success');
				$feedback->set_title('Success!');
				$feedback->set_text('Pitch writing task assigned successfully.');
				$this->add_feedback($feedback);
				$this->redirect(gstring('admin/writing/pitch/assign/all'));		
			}
		}	

		$redirect_url = 'admin/writing/pitch/assign/all';				
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/writing/pitch/assign/all/-chunk-');
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
				po.delivery, po.order_type
				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN nr_content c 
				ON ca.content_id = c.id
				{$additional_tables}
				WHERE status = ?
				AND {$filter}	
				AND po.is_archived = 0			
				ORDER BY  po.delivery DESC,
				po.date_of_last_status DESC 
				{$limit_str}";
		
		$db_result = $this->db->query($sql, array(Model_Pitch_ORDER::STATUS_NOT_ASSIGNED));
		$results = Model_Content::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		
		$chunkination->set_total($total_results);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/writing/pitch/assign/all';
			$this->redirect(gstring($url));
		}
		
		$this->add_order_detail_modal();
		$this->vd->writers = Model_MOT_Writer::find_all();
		$view_name = "admin/writing/pitch/assign";
		$this->render($chunkination, $results, $view_name);
	}
	
	protected function assign_writing_task($pitch_order_id, $writer_id)
	{
		$m_p_order = Model_Pitch_Order::find($pitch_order_id);
		$m_p_order->writer_id = $writer_id;
		$m_p_order->status = Model_Pitch_Order::STATUS_ASSIGNED_TO_WRITER;		
		$m_p_order->save();		
		
		Model_Pitch_Writing_Process::create_and_save($pitch_order_id, 
			Model_Pitch_Writing_Process::PROCESS_ASSIGNED_TO_WRITER);
		
		$writer = Model_MOT_Writer::find($writer_id);
		$pw_mailer = new Pitch_Wizard_Mailer();
		$pw_mailer->assign_writing_task_to_writer($writer);		
	}
}

?>