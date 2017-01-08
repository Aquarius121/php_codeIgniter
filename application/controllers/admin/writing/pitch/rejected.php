<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/pitch/main');

class Rejected_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Rejected';
	}

	public function index()
	{
		$this->redirect('admin/writing/pitch/rejected/all');
	}
	
	public function all($chunk = 1, $filter = 1)
	{
		$redirect_url = 'admin/writing/pitch/rejected/all';				
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/writing/pitch/rejected/all/-chunk-');
		$chunkination->set_url_format($url_format);		
		
		$this->vd->filters = array();
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];		
		$limit_str = $chunkination->limit_str();		
		
		$rejected_status_list = sql_in_list(array(Model_Pitch_Order::STATUS_ADMIN_REJECTED, 
									Model_Pitch_Order::STATUS_CUSTOMER_REJECTED));
						
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				c.id, c.title, c.type, 
				c.slug,	po.status, 
				po.id as order_id,			
				po.status, po.city, 
				po.keyword, po.date_created,
				po.writer_id, po.delivery, 
				po.order_type
				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN nr_content c 
				ON ca.content_id = c.id
				{$additional_tables}
				WHERE status IN ({$rejected_status_list})
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
			$url = 'admin/writing/pitch/rejected';
			$this->redirect(gstring($url));
		}
		
		foreach ($results as $result)
			$result->writer = Model_MOT_Writer::find($result->writer_id);		
		
		$this->add_order_detail_modal();
		
		$view_name = "admin/writing/pitch/rejected";
		$this->render($chunkination, $results, $view_name);
	}
	
	
	
	
	
}

?>