<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/common_pw_orders_trait');
load_controller('shared/process_results_release_plus_trait');
load_controller('shared/process_results_distribution_bundle_trait');

class Dashboard_Controller extends Manage_Base {
	
	use Common_PW_Orders_Trait;
	use Process_Results_Release_Plus_Trait;
	use Process_Results_Distribution_Bundle_Trait;
	
	public function __construct()
	{
		parent::__construct();	
		$name = $this->newsroom->company_name;
		$this->vd->title[] = "{$name} Dashboard";
	}
	
	public function index()
	{		
		$sql = "SELECT c.* FROM nr_content c 
			WHERE c.company_id = ? AND c.type = ?
			AND c.is_under_writing = 0
			ORDER BY c.id DESC LIMIT 5";
		
		$query = $this->db->query($sql, 
			array($this->newsroom->company_id, Model_Content::TYPE_PR));

		$results = Model_Content::from_db_all($query);
		$results = $this->process_results_release_plus($results);
		$results = $this->process_results_distribution_bundle($results);

		$this->vd->prs = $results;
		foreach ($this->vd->prs as $pr)
			$pr->mock_nr = $this->newsroom;
		
		$sql = "SELECT c.*,
			po.id as pitch_order_id,
			po.status as pitch_status,
			co.title as content_title
			FROM nr_campaign c 
			LEFT JOIN pw_pitch_order po
			ON po.campaign_id = c.id
			LEFT JOIN nr_content co 
			ON c.content_id = co.id
			WHERE c.company_id = ? 
			ORDER BY c.id DESC LIMIT 5";
		
		$query = $this->db->query($sql, array($this->newsroom->company_id));
		$emails = $this->vd->emails = Model_Campaign::from_db_all($query);
		$email_notification_count = 0;
		
		foreach ($emails as $email)
		{
			$email->mock_nr = $this->newsroom;
			if (!empty($email->pitch_order_id) && 
			  (($email->pitch_status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER ||
				 $email->pitch_status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED)
			 && $email->is_draft))
			{
				$email->requires_action = 1;
				$email_notification_count++;
			}
		}
		
		$this->vd->email_notification_count = $email_notification_count;
		$this->add_order_detail_modal();
		
		$sql = "SELECT 
			ws.*, wo.status, wo.writer_id, c.title
			FROM nr_writing_session ws
			LEFT JOIN rw_writing_order wo
			ON ws.writing_order_id = wo.id
			LEFT JOIN nr_content c
			ON ws.content_id = c.id
			WHERE ws.company_id = ?
			AND ws.is_archived = 0
			ORDER BY ws.date_created
			DESC LIMIT 5";
		
		$query = $this->db->query($sql, 
			array($this->newsroom->company_id));
		$this->vd->wr_sessions = Model_Writing_Session::from_db_all($query);
		$this->vd->wr_sessions_notification_count = 0;
		
		foreach ($this->vd->wr_sessions as $wr_session)
		{
			$wr_session->mock_nr = $this->newsroom;
			if (Model_Writing_Session::is_customer_action_required($wr_session->status))
				$this->vd->wr_sessions_notification_count++;
		}
		
		$stats_query = new Stats_Query();
		$stats_hash = new Stats_Hash();
		$stats_hash->company = $this->newsroom->company_id;
		$context = $stats_hash->context();
		
		$this->vd->nr_hits_week  = $stats_query->hits_over_period_summation($context, Date::days(-7, Date::local()));
		$this->vd->nr_hits_month = $stats_query->hits_over_period_summation($context, Date::days(-30, Date::local()));
		
		$stats_hash->type = Model_Content::TYPE_PR;
		$context = $stats_hash->context();

		$this->vd->pr_hits_week  = $stats_query->hits_over_period_summation($context, Date::days(-7, Date::local()));
		$this->vd->pr_hits_month = $stats_query->hits_over_period_summation($context, Date::days(-30, Date::local()));

		if (!$this->newsroom->is_active)
			$this->vd->is_stat_muted = 1;
			
		$this->vd->pr_credits_basic = Auth::user()->pr_credits_basic_stat();
		$this->vd->pr_credits_premium = Auth::user()->pr_credits_premium_stat();
		$this->vd->email_credits = Auth::user()->email_credits_stat();
		$this->vd->writing_credits = Auth::user()->writing_credits_stat();
	
		if ($this->newsroom->is_active)
			$this->vd->chart = $this->chart();
		
		// add resources specific to overview
		$view = 'manage/overview/partials/eob';
		$eob = $this->load->view($view, null, true);
		$this->add_eob($eob);
			
		$this->load->view('manage/header');
		$this->load->view('manage/dashboard/index');
		$this->load->view('manage/footer');
	}
	
	public function chart()
	{
		$stats_hash = new Stats_Hash();
		$stats_hash->company = $this->newsroom->company_id;
		$context = $stats_hash->context();
		$stats_query = new Stats_Query();
		$data = $stats_query->hits_daily_summation($context, 
			Date::days(-7, Date::local()), null);

		$nr_stats = array();
		foreach ($data as $date => $sum)
		{
			$dt = Date::utc($date);
			$nr_stats[] = $cd = new stdClass();
			$cd->label = $dt->format('m/j');
			$cd->value = $sum;
		}

		$line = new stdClass();
		$line->label = "Newsroom Views";
		$line->points = $nr_stats;
		$colors = new stdClass();
		$colors->line = array(19, 87, 168, 0);
		$colors->fill = array(19, 87, 168, 115);
		$colors->point = array(19, 87, 168, 0);
		$colors->highlight = array(42, 125, 223, 1);
		$line->color = $colors;
		
		$lines[] = $line;

		$stats_hash->type = Model_Content::TYPE_PR;
		$context = $stats_hash->context();

		$data = $stats_query->hits_daily_summation($context, 
			Date::days(-7, Date::local()), null);

		$pr_stats = array();
		foreach ($data as $date => $sum)
		{
			$dt = Date::utc($date);
			$pr_stats[] = $cd = new stdClass();
			$cd->label = $dt->format('m/j');
			$cd->value = $sum;
		}

		$line = new stdClass();
		$line->label = "Press Release Views";
		$line->points = $pr_stats;
		$colors = new stdClass();
		$colors->line = array(81, 208, 211, 0);
		$colors->fill = array(81, 208, 211, 115);
		$colors->point = array(81, 208, 211, 0);
		$colors->highlight = array(90, 231, 235, 1);
		$line->color = $colors;

		$lines[] = $line;

		$chart = new Canvas_Line_Chart($lines, 460, 100);
		$chart->point_size = 0;
		$chart->expires = 300;
		return $chart->render();
	}
}

?>