<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/pitch_wizard_order/main');

class Rejected_List_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Rejected Lists';
	}

	public function index($chunk = 1, $filter = 1)
	{
			
		$post = $this->input->post();
		if (isset($post['bt_send_to_list_builder']))
		{
			$this->send_to_list_builder($post);
			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Sent to list builder successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/contact/pitch_wizard_order/rejected_list'));
		}
			
		$redirect_url = 'admin/contact/pitch_wizard_order/rejected_list';				
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/contact/pitch_wizard_order/rejected_list/-chunk-');
		$chunkination->set_url_format($url_format);
		//$results = $this->fetch_rejected_list($chunkination);
		
		
		$limit_str = $chunkination->limit_str();
		
		$this->vd->filters = array();
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.*, 
			po.id as order_id,
			po.keyword, po.city,
			po.delivery,
			pl.status as pw_list_status,
			pl.date_of_last_status,
			ca.content_id, ca.date_send,
			st.abbr as state_abbr,
			pl.id as list_id,
			ub.first_name as user__first_name, 
			ub.last_name as user__last_name,
			plp.process_date as date_assigned,
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
			LEFT JOIN pw_pitch_list_process plp
			ON plp.pitch_list_id = pl.id
			AND plp.process = ?
			{$additional_tables}
			WHERE pl.status = ?
			AND pl.is_archived = 0
			AND {$filter}
			ORDER BY pl.date_of_last_status DESC
			{$limit_str}";
		
		$db_result = $this->db->query($sql, array(Model_Pitch_List_Process::PROCESS_ASSIGNED_TO_LIST_BUILDER,
										Model_Pitch_List::STATUS_ADMIN_REJECTED));

		$results = Model_Content::from_db_all($db_result, array('user' => 'Model_User'));
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		$chunkination->set_total($total_results);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/contact/pitch_wizard_order/rejected_list';
			$this->redirect(gstring($url));
		}
			
		$this->add_order_detail_modal();
		$this->add_rejection_modal();
		$this->add_upload_modal();		
		
		$view_name = "admin/contact/pitch_wizard_order/rejected_list";
		$this->render($chunkination, $results, $view_name);
		
	}
}

?>