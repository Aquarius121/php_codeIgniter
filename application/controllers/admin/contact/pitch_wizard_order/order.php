<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/pitch_wizard_order/main');

class Order_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Orders';
	}

	public function all($chunk = 1, $filter = 1)
	{		
		$redirect_url = 'admin/contact/pitch_wizard_order/order/all';
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/contact/pitch_wizard_order/order/all/-chunk-');
		$chunkination->set_url_format($url_format);
		
		$results = $this->all_pw_orders($chunkination, $filter);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/contact/pitch_wizard_order/order/all';
			$this->redirect(gstring($url));
		}	
		$this->add_order_detail_modal();
		$view_name = "admin/contact/pitch_wizard_order/order";
		$this->render($chunkination, $results, $view_name);
	}	
	
	// public function archive($chunk = 1, $filter = 1)
	// {		
	// 	$redirect_url = 'admin/contact/pitch_wizard_order/order/archive';
	// 	$chunkination = new Chunkination($chunk);
	// 	$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
	// 	$url_format = gstring('admin/contact/pitch_wizard_order/order/archive/-chunk-');
	// 	$chunkination->set_url_format($url_format);		
		
	// 	$results = $this->all_pw_orders($chunkination, $filter, 1);
		
	// 	if ($chunkination->is_out_of_bounds())
	// 	{
	// 		// out of bounds so redirect to first
	// 		$url = 'admin/contact/pitch_wizard_order/order/archive';
	// 		$this->redirect(gstring($url));
	// 	}	
	// 	$this->add_order_detail_modal();
	// 	$this->vd->is_archive = 1;
	// 	$view_name = "admin/contact/pitch_wizard_order/order";
	// 	$this->render($chunkination, $results, $view_name);
	// }
	
	public function mark_archived($pitch_order_id)
	{
		if (empty($pitch_order_id))
			$this->denied;
		
		$m_pw_order = Model_Pitch_Order::find($pitch_order_id);
		$m_pw_order->is_writing_archived = 1;
		$m_pw_order->is_archived = 1;
		$m_pw_order->save();
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Pitch wizard order archived successfully.');
		$this->add_feedback($feedback);
		$this->redirect(gstring('admin/contact/pitch_wizard_order/order/all'));
	}
	
	protected function all_pw_orders($chunkination, $filter = 1, $is_archived = 0)
	{
		$limit_str = $chunkination->limit_str();
		$this->vd->filters = array();
		
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			$terms_filter = sql_search_terms(array('po.keyword', 'po.city',	'c.title'), $filter_search);			
			$filter = "{$filter} AND {$terms_filter}";
		}
		
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];
		
			
		$is_archived = (int) ((bool) $is_archived);		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			po.id as order_id,
			po.status, po.writer_id, c.*,
			po.city, po.writer_id, 
			po.keyword, po.delivery,
			ub.first_name AS user__first_name, 
			ub.last_name AS user__last_name,
			pl.status as pw_list_status,
			ca.content_id, ca.date_send,
			ca.id as campaign_id,
			pl.id as list_id,
			po.order_type
			FROM nr_campaign ca
			INNER JOIN pw_pitch_order po 
			ON po.campaign_id = ca.id
			LEFT JOIN pw_pitch_list pl
			ON pl.pitch_order_id = po.id
			LEFT JOIN nr_content c 
			ON ca.content_id = c.id
			LEFT JOIN nr_user ub
			ON pl.list_builder_user_id = ub.id 
			{$additional_tables} 
			WHERE po.is_writing_archived = {$is_archived}
			AND {$filter}
			ORDER BY po.date_of_last_status DESC
			{$limit_str}";				

		$db_result = $this->db->query($sql);
		$results = Model_Content::from_db_all($db_result,
					array('user' => 'Model_User'));
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		foreach ($results as $result)
		{
			if ( ! empty($result->writer_id))
			{
				if ($writer = Model_MOT_Writer::find($result->writer_id))
					$result->writer = $writer;
			}
		}	

			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
			
		return $results;
	}
}

?>