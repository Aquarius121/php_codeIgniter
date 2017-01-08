<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/pitch_wizard_order/main');

class Assign_List_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Assign Lists';
	}

	public function index($chunk = 1, $filter = 1)
	{
		$post = $this->input->post();
		if ($post['list_id'])
		{
			$list_id = $post['list_id'];
			$list_builder_id = $post['list_builder_id'];
			
			if ($list_id && $list_builder_id)
			{
				$this->assign_single_list($list_id, $list_builder_id);
				$feedback = new Feedback('success');
				$feedback->set_title('Success!');
				$feedback->set_text('List assigned successfully.');
				$this->add_feedback($feedback);
				$this->redirect(gstring('admin/contact/pitch_wizard_order/assign_list'));		
			}
		}
			
		// redirect url when we are doing an action
		$redirect_url = 'admin/contact/pitch_wizard_order/assign_list';				
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/contact/pitch_wizard_order/assign_list/-chunk-');
		$chunkination->set_url_format($url_format);		
		
		$this->vd->filters = array();
		$arr = $this->add_user_company_search_filter($filter);
		$additional_tables = $arr['additional_tables'];
		$filter = $arr['filter'];		
		$limit_str = $chunkination->limit_str();		
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			po.id AS order_id, po.status, 
			po.keyword, po.city, po.delivery,
			po.writer_id, po.date_created,
			pl.status AS pw_list_status,
			ca.content_id, ca.date_send,
			st.abbr AS state_abbr,
			pl.id AS list_id,
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
			{$additional_tables}
			WHERE pl.status = ?
			AND {$filter}
			AND pl.is_archived = 0
			AND po.order_type = ?
			ORDER BY pl.date_of_last_status DESC
			{$limit_str}";
		
		$db_result = $this->db->query($sql, array(Model_Pitch_List::STATUS_NOT_ASSIGNED,
			Model_Pitch_Order::ORDER_TYPE_OUTREACH));
		$results = Model_Pitch_Order::from_db_all($db_result);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		
		
		$chunkination->set_total($total_results);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/contact/pitch_wizard_order/assign_list';
			$this->redirect(gstring($url));
		}
		
		$this->add_order_detail_modal();
		$this->vd->admin_list = Model_User::find_all(array('is_admin', 1));
		$view_name = "admin/contact/pitch_wizard_order/assign_list";
		$this->render($chunkination, $results, $view_name);
	}
	
	protected function assign_single_list($pitch_list_id, $list_builder_user_id)
	{
		$m_pitch_list = Model_Pitch_List::find($pitch_list_id);
		$m_pitch_list->list_builder_user_id = $list_builder_user_id;
		$m_pitch_list->status = Model_Pitch_List::STATUS_ASSIGNED_TO_LIST_BUILDER;		
		$m_pitch_list->save();		
		
		Model_Pitch_List_Process::create_and_save($pitch_list_id, 
													Model_Pitch_List_Process::PROCESS_ASSIGNED_TO_LIST_BUILDER);
		
		$pwm = new Pitch_Wizard_Mailer();
		$pwm->send_new_task_to_list_builder($pitch_list_id, $list_builder_user_id);
	}
}

?>