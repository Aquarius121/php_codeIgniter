<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/pitch/main');

class PW_Order_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'PW Orders';
	}

	public function all($chunk = 1, $filter = 1)
	{		
		$redirect_url = 'admin/writing/pitch/pw_order/all';
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/writing/pitch/pw_order/all/-chunk-');
		$chunkination->set_url_format($url_format);
		
		$results = $this->all_pw_orders($chunkination, $filter);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/writing/pitch/pw_order/all';
			$this->redirect(gstring($url));
		}	
		
		$this->add_order_detail_modal();
		$view_name = "admin/writing/pitch/pw_order";
		$this->render($chunkination, $results, $view_name);
	}	
	
	public function archive($chunk = 1, $filter = 1)
	{		
		$redirect_url = 'admin/writing/pitch/pw_order/archive';
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/writing/pitch/pw_order/archive/-chunk-');
		$chunkination->set_url_format($url_format);		
		
		$results = $this->all_pw_orders($chunkination, $filter, 1);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/writing/pitch/pw_order/archive';
			$this->redirect(gstring($url));
		}	
		
		$this->add_order_detail_modal();
		$this->vd->is_archive = 1;
		$view_name = "admin/writing/pitch/pw_order";
		$this->render($chunkination, $results, $view_name);
	}
	
	public function mark_archived($pitch_order_id)
	{
		if (empty($pitch_order_id))
			$this->denied;
		
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->is_writing_archived = 1;
		$m_pw_order->save();
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Pitch wizard writing task archived successfully.');
		$this->add_feedback($feedback);
		$this->redirect(gstring('admin/writing/pitch/pw_order/all'));
	}
	
	protected function all_pw_orders($chunkination, $filter = 1, $is_archive = 0)
	{
		$limit_str = $chunkination->limit_str();
		$this->vd->filters = array();
		
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];
				
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				c.id, c.title, c.type, 
				c.slug,	po.status, 
				po.max_status,
				po.id as order_id,			
				po.status, po.writer_id, 
				po.city, po.writer_id,
				po.keyword,	po.delivery, 				
				ca.content_id,
				po.order_type
				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN nr_content c 
				ON ca.content_id = c.id
				{$additional_tables}";
			
		if ($is_archive)
			$sql .= " WHERE po.is_writing_archived = 1";
		else	
			$sql .= " WHERE po.is_writing_archived = 0";
			
		$sql.="	AND {$filter}
				ORDER BY po.delivery DESC, 
				po.date_of_last_status DESC 
				{$limit_str}";

		$db_result = $this->db->query($sql);
		$results = Model_Content::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		foreach ($results as $result)
		{
			if ( ! empty($result->writer_id))
			{
				$writer = Model_MOT_Writer::find($result->writer_id);
				if ( ! empty($writer))
					$result->writer_name = $writer->name();
			}
		}	

			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
			
		return $results;
	}
}

?>