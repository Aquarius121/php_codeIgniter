<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/overview/base');
load_controller('shared/common_pw_orders_trait');

class Dashboard_Controller extends Overview_Base {
	
	use Common_PW_Orders_Trait;
	
	public $title = 'Overview Dashboard';
	public $use_level_colors = true;
	
	public function index()
	{
		$sql = "SELECT c.*, cm.newsroom FROM nr_content c 
			INNER JOIN nr_company cm ON c.company_id = cm.id
			WHERE cm.user_id = ? AND c.type = ?
			AND c.is_under_writing = 0
			ORDER BY c.id DESC LIMIT 5";
		
		$query = $this->db->query($sql, 
			array(Auth::user()->id, Model_Content::TYPE_PR));
		$this->vd->prs = Model_Content::from_db_all($query);
		
		foreach ($this->vd->prs as $pr)
		{
			$pr->mock_nr = new Model_Newsroom();
			$pr->mock_nr->name = $pr->newsroom;
		}
		
		$sql = "SELECT c.*,
			po.id AS pitch_order_id,
			po.status AS pitch_status,
			co.title AS content_title,
			co.type AS content_type,
			cm.newsroom
			FROM nr_campaign c 
			INNER JOIN nr_company cm
			ON c.company_id = cm.id
			LEFT JOIN pw_pitch_order po
			ON po.campaign_id = c.id
			LEFT JOIN nr_content co 
			ON c.content_id = co.id
			WHERE cm.user_id = ?
			ORDER BY c.id DESC LIMIT 5";
		
		$query = $this->db->query($sql, array(Auth::user()->id));
		$emails = $this->vd->emails = Model_Campaign::from_db_all($query);
		$email_notification_count = 0;
		
		foreach ($emails as $email)
		{
			$email->mock_nr = new Model_Newsroom();
			$email->mock_nr->name = $email->newsroom;
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
			ws.*, wo.status, wo.writer_id,
			c.title, c.is_premium, 
			cm.newsroom
			FROM nr_writing_session ws
			LEFT JOIN rw_writing_order wo
			ON ws.writing_order_id = wo.id
			INNER JOIN nr_company cm 
			ON ws.company_id = cm.id
			LEFT JOIN nr_content c
			ON ws.content_id = c.id
			WHERE cm.user_id = ? 
			AND ws.is_archived = 0
			ORDER BY ws.date_created
			DESC LIMIT 5";
		
		$query = $this->db->query($sql, array(Auth::user()->id));
		$this->vd->wr_sessions = Model_Writing_Session::from_db_all($query);
		$this->vd->wr_sessions_notification_count = 0;
		
		foreach ($this->vd->wr_sessions as $wr_session)
		{
			$wr_session->mock_nr = new Model_Newsroom();
			$wr_session->mock_nr->name = $wr_session->newsroom;
			if (Model_Writing_Session::is_customer_action_required($wr_session->status))
				$this->vd->wr_sessions_notification_count++;
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS n.*, nc.logo_image_id
			FROM nr_newsroom n LEFT JOIN nr_newsroom_custom nc
			ON n.company_id = nc.company_id WHERE 
			n.user_id = ? AND n.is_archived = 0
			ORDER BY n.order_default DESC, 
			n.company_name ASC LIMIT 5";
		
		$dbr = $this->db->query($sql, array(Auth::user()->id));
		$this->vd->companies = Model_Newsroom::from_db_all($dbr);
		
		$stats_query = new Stats_Query();
		$stats_hash = new Stats_Hash();
		$stats_hash->user = Auth::user()->id;
		$context = $stats_hash->context();		

		$this->vd->nr_hits_week  = $stats_query->hits_over_period_summation($context, Date::days(-7));
		$this->vd->nr_hits_month = $stats_query->hits_over_period_summation($context, Date::days(-30));

		$stats_hash->type = Model_Content::TYPE_PR;
		$context = $stats_hash->context();
		$this->vd->pr_hits_week  = $stats_query->hits_over_period_summation($context, Date::days(-7));
		$this->vd->pr_hits_month = $stats_query->hits_over_period_summation($context, Date::days(-30));
		
		$this->vd->pr_credits_basic = Auth::user()->pr_credits_basic_stat();
		$this->vd->pr_credits_premium = Auth::user()->pr_credits_premium_stat();
		$this->vd->email_credits = Auth::user()->email_credits_stat();
		$this->vd->writing_credits = Auth::user()->writing_credits_stat();
		
		if (count($this->vd->user_newsrooms) > 10)
		{
			$feedback = new Feedback('success');
			$feedback->set_title('Congratulations!');
			$feedback->set_text('You have over 10 active companies!');
			$feedback->add_text('Did you know that you can archive companies to make it easier to manage the important ones?', true);
			$feedback->add_text('Press releases from archived companies are still accessible', true);
			$feedback->add_text('and you can restore the company at any time.');
			$this->use_feedback($feedback);
		}

		$this->vd->is_overview = 1;
		
		$this->load->view('manage/header');
		$this->load->view('manage/overview/dashboard/index');
		$this->load->view('manage/footer');
	}
	
	public function chart()
	{
		$chart_data = array();
		$stats_hash = new Stats_Hash();
		$stats_hash->user = Auth::user()->id;
		$context = $stats_hash->context();
		$stats_query = new Stats_Query();
		$data = $stats_query->hits_daily_summation($context, 
			Date::days(-30, Date::local()), null);

		foreach ($data as $date => $sum)
		{
			$dt = Date::utc($date);
			$chart_data[] = $cd = new stdClass();
			$cd->label = $dt->format('M j');
			$cd->value = $sum;
		}
		
		$chart = new Line_Chart($chart_data, 460, 100);
		$chart->colors->font = array(60, 60, 60, 0);
		$chart->colors->line = array(95, 147, 199, 0);
		$chart->colors->fill = array(95, 147, 199, 90);
		$chart->point_size = 0;
		$chart->expires = 300;
		$chart->render();
	}

}

?>