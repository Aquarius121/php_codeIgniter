<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/pitch_wizard_order/main');

class All_List_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->vd->title[] = 'All Lists';
	}

	public function index($chunk = 1, $filter = 1)
	{		
		$redirect_url = 'admin/contact/pitch_wizard_order/all_list';
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/contact/pitch_wizard_order/all_list/-chunk-');
		$chunkination->set_url_format($url_format);
		$limit_str = $chunkination->limit_str();
		$this->vd->filters = array();
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			$terms_filter = sql_search_terms(array('po.keyword', 'po.city',	'c.title', 'st.abbr', 
											'st.name'), $filter_search);			
			$filter = "{$filter} AND {$terms_filter}";
		}
		
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];		
		
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			c.*, po.id as order_id,
			po.keyword, po.city, 
			po.date_created, po.delivery,
			pl.status as pw_list_status,
			pl.date_of_last_status,
			ca.content_id, ca.date_send,
			st.abbr as state_abbr,
			pl.id as list_id,
			ub.first_name AS user__first_name,
			ub.last_name AS user__last_name,
			po.order_type
			FROM nr_campaign ca
			INNER JOIN pw_pitch_order po 
			ON po.campaign_id = ca.id
			LEFT JOIN pw_pitch_list pl			
			ON pl.pitch_order_id = po.id
			LEFT JOIN nr_content c 
			ON ca.content_id = c.id
			LEFT JOIN nr_state st
			ON po.state_id = st.id			
			LEFT JOIN nr_user ub
			ON pl.list_builder_user_id = ub.id	
			{$additional_tables}
			WHERE {$filter}
			AND pl.is_archived = 0
			AND po.order_type = ?
			ORDER BY pl.date_of_last_status DESC
			{$limit_str}";
			
		$db_result = $this->db->query($sql, array(Model_Pitch_Order::ORDER_TYPE_OUTREACH));
		$results = Model_Content::from_db_all($db_result,
					array('user' => 'Model_User'));
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
		{
			$url = 'admin/contact/pitch_wizard_order/all_list';
			$this->redirect(gstring($url));
		}
		
		$this->add_order_detail_modal();
		$this->vd->admin_list = Model_User::find_all(array('is_admin', 1));
		$view_name = "admin/contact/pitch_wizard_order/all_list";
		$this->render($chunkination, $results, $view_name);
	}
}

?>