<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Order_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;
	public $title = 'Orders';

	public function index($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/order/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/store/order';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}
	
	protected function fetch_results($chunkination, $filter = null)
	{
		if (!$filter) $filter = 1;
		$limit_str = $chunkination->limit_str();
		$this->vd->filters = array();	
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('o.id');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}
		
		if ($filter_user = (int) $this->input->get('filter_user'))
		{
			$this->create_filter_user($filter_user);
			// restrict search results to this user
			$filter = "{$filter} AND o.user_id = {$filter_user}";
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			o.id AS id FROM 
			co_order o 
			WHERE {$filter} ORDER BY 
			o.date_created DESC {$limit_str}";
			
		$query = $this->db->query($sql);
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = $row->id;
		
		// no results found so exit
		if (!$id_list) return array();
				
		$id_str = sql_in_list($id_list);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		$u_prefixes = Model_User::__prefixes('u');
		$sql = "SELECT o.*,
			u.email AS o_user_email,
			u.id AS o_user_id,
			{$u_prefixes}
			FROM co_order o
			LEFT JOIN nr_user u 
			ON o.user_id = u.id
			WHERE o.id IN ({$id_str})
			ORDER BY o.date_created DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Order::from_db_all($query);		
		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/store/order/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
}

?>