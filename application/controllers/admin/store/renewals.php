<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Renewals_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;
	const GRACE_PERIOD = 7;

	public $title = 'Renewals';

	public function index($chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/store/renewals/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);
		
		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = 'admin/store/renewals';
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
			$search_fields = array('i.name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
		}
		
		if ($filter_user = (int) $this->input->get('filter_user'))
		{
			$this->create_filter_user($filter_user);
			// restrict search results to this user
			$filter = "{$filter} AND cs.user_id = {$filter_user}";
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			ci.id AS id FROM co_component_item ci
			INNER JOIN co_item i ON ci.item_id = i.id
			INNER JOIN co_component_set cs ON ci.component_set_id = cs.id
			AND (ci.is_auto_renew_enabled = 1 OR ci.is_renewable = 1)
			AND ci.date_termination > ?
			WHERE {$filter}
			ORDER BY ci.date_expires ASC
			{$limit_str}";
		
		$date_cut = Date::days(-static::GRACE_PERIOD);	
		$query = $this->db->query($sql, array($date_cut));
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
		$sql = "SELECT ci.*, 
			cs.is_legacy,
			i.name AS item_name,
			i.type AS item_type,
			o.id AS order_id, 
			o.user_id AS user_id,
			u.email AS o_user_email,
			u.id AS o_user_id,
			{$u_prefixes}
			FROM co_component_item ci
			INNER JOIN co_component_set cs 
			ON ci.component_set_id = cs.id 
			INNER JOIN co_item i ON 
			ci.item_id = i.id
			LEFT JOIN co_order o 
			ON o.component_set_id = cs.id
			LEFT JOIN nr_user u 
			ON cs.user_id = u.id
			WHERE ci.id IN ({$id_str})
			ORDER BY date_expires ASC";
			
		$query = $this->db->query($sql);
		$results = Model_Component_Item::from_db_all($query);		
		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/store/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/store/renewals/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
}

?>