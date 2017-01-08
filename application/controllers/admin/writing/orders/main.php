<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Main_Controller extends Admin_Base {

	public $title = 'Writing Orders';

	const DEFAULT_BITS = 5;
	const DEFAULT_TAB = 'all';
	const LISTING_CHUNK_SIZE = 20;
	
	protected $is_admin_editor_visible = false;
	protected $is_reseller_editor_visible = false;
	protected $is_internal_order_visible = false;
	protected $visible_bits_to_vars = array();
	protected $is_archive = false;
	protected $visible_bits = null;
	protected $tab = null;
	protected $filter = 1;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->visible_bits_to_vars[] = 'is_admin_editor_visible';
		$this->visible_bits_to_vars[] = 'is_reseller_editor_visible';
		$this->visible_bits_to_vars[] = 'is_internal_order_visible';
		$this->visible_bits_to_vars[] = 'is_archive';
	}
	
	public function var_to_visible_bit($var)
	{
		$index = array_search($var, $this->visible_bits_to_vars);
		if ($index === false) return 0;
		return pow(2, $index);
	}
	
	protected function redirect_to_default()
	{
		$url = gstring('admin/writing/orders/%s/%d');
		$url = sprintf($url, static::DEFAULT_TAB,
			static::DEFAULT_BITS);
		$this->redirect($url);
	}
	
	public function index($chunk = 1)
	{
		$this->index_sub($chunk);
	}

	protected function index_sub($chunk = 1)
	{
		if ($this->visible_bits === null || $this->tab === null)
			$this->redirect_to_default();
		
		if (!($this->set_visible_vars($this->visible_bits)))
			$this->redirect_to_default();
		
		$filter = $this->filter;
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring("admin/writing/orders/{$this->tab}/{$this->visible_bits}/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination, $filter);
		
		if ($chunkination->is_out_of_bounds()) 
		{
			// out of bounds so redirect to first
			$url = "admin/writing/orders/{$this->tab}/{$this->visible_bits}";
			$this->redirect(gstring($url));
		}
		
		$this->vd->tab = $this->tab;
		$this->vd->visible_bits = $this->visible_bits;
		foreach ($this->visible_bits_to_vars as $name)
			$this->vd->{$name} = $this->{$name};
		
		$this->add_tab_counters();
		$this->process_results($results);		
		$this->render_list($chunkination, $results);
	}
	
	protected function add_tab_counters()
	{
		// result count for the pending tab		
		$status_list = sql_in_list(array(
			Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION,
			Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS));		
		$filter = "wo.status IN ({$status_list})";
		$this->vd->tab_count_pending = 
			$this->count_results_for_filter($filter);
		
		// result count for the assign tab
		$status = Model_Writing_Order::STATUS_NOT_ASSIGNED;
		$filter = "wo.status = '{$status}'";
		$this->vd->tab_count_assign = 
			$this->count_results_for_filter($filter);
		
		// result count for the review tab
		$status = Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER;
		$filter = "wo.status = '{$status}'";
		$this->vd->tab_count_review = 
			$this->count_results_for_filter($filter);
		
		// result count for the rejected tab
		$status = Model_Writing_Order::STATUS_CUSTOMER_REJECTED;
		$filter = "wo.status = '{$status}'";
		$this->vd->tab_count_rejected = 
			$this->count_results_for_filter($filter);
	}
	
	protected function count_results_for_filter($filter)
	{
		$filter = $this->add_permissions_filter($filter);
		$is_archive = (int) $this->is_archive;
		$sql = "SELECT COUNT(*) AS count
			FROM rw_writing_order_code woc 
			LEFT JOIN rw_writing_order wo ON wo.writing_order_code_id = woc.id
			LEFT JOIN rw_reseller_details rd ON woc.reseller_id = rd.user_id
			WHERE {$filter} AND woc.is_archived = {$is_archive}";
		return $this->db->query($sql)->row()->count;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/orders/list');	
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	protected function process_results($results)
	{
		$writers = Model_MOT_Writer::find_all();	
		
		foreach ($results as $result)
		{
			if (!isset($result->writing_order->writer_id)) continue;
			$writer_id = $result->writing_order->writer_id;
			$result->writer = Model_MOT_Writer::find($writer_id);
		}
		
		return $results;
	}
	
	protected function add_permissions_filter($filter)
	{
		if (!$this->is_admin_editor_visible)
		{
			// admin editor is not visible so exclude that privilege
			$permission = Model_Reseller_Details::PRIV_ADMIN_EDITOR;
			$filter = "{$filter} AND (woc.reseller_id IS NULL 
				OR rd.editing_privilege != '{$permission}')";
			// directly queue draft is not visible so exclude that privilege
			$permission = Model_Reseller_Details::PRIV_DIRECTLY_QUEUE_DRAFT;
			$filter = "{$filter} AND (woc.reseller_id IS NULL
				OR rd.editing_privilege != '{$permission}')";
		}
		
		if (!$this->is_reseller_editor_visible)
		{
			// reseller editor is not visible so exclude the privilege
			$permission = Model_Reseller_Details::PRIV_RESELLER_EDITOR;
			$filter = "{$filter} AND (woc.reseller_id IS NULL 
				OR rd.editing_privilege != '{$permission}')";
		}
		
		if (!$this->is_internal_order_visible)
		{
			// exclude orders who are not through a reseller
			$filter = "{$filter} AND woc.reseller_id IS NOT NULL";
		}
		
		return $filter;
	}
	
	protected function fetch_results($chunkination, $filter = 1)
	{
		$is_archive = (int) $this->is_archive;
		$limit_str = $chunkination->limit_str();
		$use_additional_tables = false;
		$additional_tables = null;
		$this->vd->filters = array();	
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			// restrict search results to these terms
			$search_fields = array('c.title', 'woc.writing_order_code',
				'wo.primary_keyword', 'woc.customer_name', 'woc.customer_email',
				'cm.name', 'user.first_name', 'user.last_name');
			$terms_filter = sql_search_terms($search_fields, $filter_search);
			$filter = "{$filter} AND {$terms_filter}";
			$use_additional_tables = true;
		}
		
		if (($filter_user = $this->input->get('filter_user')) !== false)
		{
			$filter_user = (int) $filter_user;
			$this->create_filter_user($filter_user);	
			// restrict search results to this user
			$filter = "{$filter} AND u.id = {$filter_user}";
			$use_additional_tables = true;
		}
		
		if (($filter_company = $this->input->get('filter_company')) !== false)
		{
			$filter_company = (int) $filter_company;
			$this->create_filter_company($filter_company);	
			// restrict search results to this user
			$filter = "{$filter} AND cm.id = {$filter_company}";
			$use_additional_tables = true;
		}
		
		// add permissions filter admin/reseller/internal
		$filter = $this->add_permissions_filter($filter);
		
		// add sql for connecting in additional tables
		if ($use_additional_tables) $additional_tables = 
			"LEFT JOIN nr_user reseller ON reseller.id = woc.reseller_id
			 LEFT JOIN nr_writing_session ws ON ws.writing_order_code_id = woc.id
			 LEFT JOIN nr_content c ON wo.content_id = c.id
			 LEFT JOIN nr_company cm ON c.company_id = cm.id
			 LEFT JOIN nr_user u ON cm.user_id = u.id";
		 
		$sql = "SELECT SQL_CALC_FOUND_ROWS woc.id 
			FROM rw_writing_order_code woc 
			LEFT JOIN rw_writing_order wo ON wo.writing_order_code_id = woc.id
			LEFT JOIN rw_reseller_details rd ON woc.reseller_id = rd.user_id
			{$additional_tables}
			WHERE {$filter} AND woc.is_archived = {$is_archive}
			ORDER BY woc.id DESC
			{$limit_str}";
			
		$query = $this->db->query($sql);
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = (int) $row->id;
		
		// no results found so exit
		if (!$id_list) return array();
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();
		
		$id_str = sql_in_list($id_list);		
		$status_assigned = Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER;
		$status_written = Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER;
		$status_sent_to_customer = Model_Writing_Order::STATUS_SENT_TO_CUSTOMER;
		$status_customer_accepted = Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED;
		$status_customer_rejected = Model_Writing_Order::STATUS_CUSTOMER_REJECTED;
			
		$sql = "SELECT woc.id AS writing_order_code__id,
			woc.writing_order_code AS writing_order_code__writing_order_code,
			woc.customer_name AS writing_order_code__customer_name,
			woc.customer_email AS writing_order_code__customer_email,
			woc.date_ordered AS writing_order_code__date_ordered,
			woc.date_last_reminder_sent AS writing_order_code__date_last_reminder_sent,
			wo.id AS writing_order__id,
			wo.primary_keyword AS writing_order__primary_keyword,
			wo.status AS writing_order__status,
			wo.writer_id AS writing_order__writer_id,
			wo.date_ordered AS writing_order__date_ordered,
			rd.company_name AS reseller_details__company_name,
			rd.website AS reseller_details__website,
			reseller.id AS reseller__id,
			reseller.first_name AS reseller__first_name,
			reseller.last_name AS reseller__last_name,
			reseller.email AS reseller__email,
			user.id AS user__id,
			user.first_name AS user__first_name,
			user.last_name AS user__last_name,
			user.email AS user__email,
			c.title AS content__title,
			c.id AS content__id,
			c.slug AS content__slug,
			c.is_published AS content__is_published,
			cm.id AS company__id,
			cm.name AS company__name,
			cm.newsroom AS company__newsroom,
			ws.id AS writing_session__id,
			
			IF (woc.reseller_id IS NULL, rsd.date_sent, rs.date_sent) AS writing_order__date_report_sent,
			a_date.date_assigned_to_writer AS writing_order__date_assigned_to_writer,
			w_date.date_submitted_by_writer AS writing_order__date_submitted_by_writer,
			s_date.date_sent_to_customer AS writing_order__date_sent_to_customer,
			r_date.date_customer_rejected AS writing_order__date_customer_rejected,
			ap_date.date_customer_approved AS writing_order__date_customer_approved,
			p_max.max_status_index AS writing_order__max_status_index
			
			FROM rw_writing_order_code woc
			LEFT JOIN rw_writing_order wo ON wo.writing_order_code_id = woc.id
			LEFT JOIN rw_reseller_details rd ON woc.reseller_id = rd.user_id
			LEFT JOIN nr_user reseller ON reseller.id = woc.reseller_id
			LEFT JOIN nr_writing_session ws ON ws.writing_order_code_id = woc.id
			LEFT JOIN nr_content c ON wo.content_id = c.id
			LEFT JOIN nr_company cm ON c.company_id = cm.id
			LEFT JOIN nr_user user ON cm.user_id = user.id
			LEFT JOIN nr_report_sent_dist rsd ON rsd.content_id = c.id
			LEFT JOIN rw_report_sent rs ON rs.content_id = c.id
			
			LEFT JOIN (
				SELECT writing_order_id,
				MAX(process+0) AS max_status_index
				FROM rw_writing_process
				GROUP BY writing_order_id
			) AS p_max ON p_max.writing_order_id = wo.id

			LEFT JOIN (
				SELECT writing_order_id, 
				MAX(process_date) AS date_sent_to_customer
				FROM rw_writing_process
				WHERE process = '{$status_sent_to_customer}'
				GROUP BY writing_order_id
			) AS s_date ON s_date.writing_order_id = wo.id

			LEFT JOIN (
				SELECT writing_order_id, 
				MAX(process_date) AS date_customer_rejected
				FROM rw_writing_process
				WHERE process = '{$status_customer_rejected}'
				GROUP BY writing_order_id
			) AS r_date ON r_date.writing_order_id = wo.id

			LEFT JOIN (
				SELECT writing_order_id, 
				MAX(process_date) AS date_customer_approved
				FROM rw_writing_process
				WHERE process = '{$status_customer_accepted}'
				GROUP BY writing_order_id
			) AS ap_date ON ap_date.writing_order_id = wo.id
						
			LEFT JOIN (
				SELECT writing_order_id, 
				MAX(process_date) AS date_assigned_to_writer
				FROM rw_writing_process
				WHERE process = '{$status_assigned}'
				GROUP BY writing_order_id
			) AS a_date ON a_date.writing_order_id = wo.id
			
			LEFT JOIN (
				SELECT writing_order_id,
				MAX(process_date) AS date_submitted_by_writer
				FROM rw_writing_process
				WHERE process = '{$status_written}'
				GROUP BY writing_order_id
			) AS w_date ON w_date.writing_order_id = wo.id
		
			WHERE woc.id IN ({$id_str}) 
			ORDER BY woc.id DESC";
			
		$query = $this->db->query($sql);
		$results = Model_Base::from_db_all($query, array(
			'company' => 'model_company',
			'content' => 'model_content',
			'reseller' => 'model_user',
			'user' => 'model_user',
			'writing_order' => 'model_writing_order',
			'writing_order_code' => 'model_writing_order_code',
			'writing_session' => 'model_writing_session',
			'reseller_details' => 'model_reseller_details',
		));
		
		return $results;
	}
	
	public function archive($writing_order_code_id)
	{
		$wr_code = Model_Writing_Order_Code::find($writing_order_code_id);
		if (!$wr_code) return;	
		$wr_code->is_archived = 1;
		$wr_code->save();
	
		if ($wr_order = Model_Writing_Order::find_code($wr_code->id))
		{
			$wr_order->is_archived = 1;
			$wr_order->save();
		}
		
		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('The order has been archived');
		$this->add_feedback($feedback);
		
		// redirect back to the last location
		$url = value_or_null($_SERVER['HTTP_REFERER']);
		$this->redirect($url, false);
	}
	
	protected function set_visible_vars($bits)
	{
		$bits = (int) $bits;
		$vbtv = $this->visible_bits_to_vars;
		for ($idx = count($vbtv) - 1; $idx >= 0; $idx--)
		{
			if ($bits >= ($bit = pow(2, $idx)))
			{
				$this->{$vbtv[$idx]} = true;
				$bits -= $bit;
			}
		}
		
		return $bits === 0;
	}

}

?>