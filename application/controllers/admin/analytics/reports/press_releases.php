<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Press_Releases_Controller extends Admin_Base {

	protected function __index($dt)
	{
		$content_types = sql_in_list(array(
			Model_Content::TYPE_PR,
		));

		$start_of_month = $dt->format('Y-m-01 00:00:00');
		$end_of_month = $dt->format('Y-m-t 23:59:59');
		$this->vd->selected_month = $dt->format('Ym');

		$sql = "SELECT cu.count, u.email, u.id from (
			  select * from (
			    select count(c.id) as count, cm.user_id from nr_content c 
			    inner join nr_company cm on cm.id = c.company_id and cm.user_id > 1
			    where c.type IN ({$content_types}) and c.is_premium = ? 
			    and c.date_publish >= '{$start_of_month}'
			    and c.date_publish <= '{$end_of_month}'
			    and c.is_published = 1
			    group by cm.user_id 
			  ) cc
			  order by count desc
			  limit 500
			) cu inner join nr_user_base u 
			on cu.user_id = u.id";

		$this->vd->active_users_premium = Model::from_sql_all($sql, array(1));
		$this->vd->active_users_basic = Model::from_sql_all($sql, array(0));

		$sql = "SELECT count(c.id) as count from nr_content c 
			  inner join nr_company cm on cm.id = c.company_id and cm.user_id > 1
			  where c.type IN ({$content_types}) and c.is_premium = ? 
			  and c.date_publish >= '{$start_of_month}'
			  and c.date_publish <= '{$end_of_month}'
			  and c.is_published = 1";

		$this->vd->total_premium = Model::from_sql($sql, array(1))->count;
		$this->vd->total_basic = Model::from_sql($sql, array(0))->count;

		$sql = "SELECT count(distinct(cm.user_id)) as count from nr_content c 
			  inner join nr_company cm on cm.id = c.company_id and cm.user_id > 1
			  where c.type IN ({$content_types}) and c.is_premium = ?
			  and c.date_publish >= '{$start_of_month}'
			  and c.date_publish <= '{$end_of_month}'
			  and c.is_published = 1";

		$this->vd->total_users_premium = Model::from_sql($sql, array(1))->count;
		$this->vd->total_users_basic = Model::from_sql($sql, array(0))->count;

		$this->load->view('admin/header');
		$this->load->view('admin/analytics/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/analytics/reports/press_releases');
		$this->load->view('admin/post-content'); 
		$this->load->view('admin/footer');
	}

	public function index($months_ago = 0)
	{
		$months_ago = (int) $months_ago;
		if ($months_ago > 0) 
		     $this->__index(Date::months(-((int) $months_ago)));
		else $this->__index(Date::utc());
	}

}