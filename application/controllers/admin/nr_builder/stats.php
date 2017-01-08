<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Stats_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;
	public $title = 'Stats | Newsroom Builder';
	public $sources = array();

	public function index($chunk = 1)
	{	
		$this->nr_sources();

		if ($this->input->post('bt_stats_report'))
			$this->stats_report();

		if ($this->input->post('bt_single_stats'))
			$this->single_stats();

		if (!$this->input->post('bt_single_stats') && !$this->input->post('bt_stats_report'))
			$this->single_stats(false);

		$this->load->view('admin/header');
		$this->load->view('admin/companies/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/stats/main');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	protected function nr_sources()
	{
		$sources = array();
		
		$sources = Model_Company::scraping_sources();
		/*
		$sources[0]['name'] = Model_Company::SOURCE_CRUNCHBASE;
		$sources[0]['title'] = Model_Company::full_source(Model_Company::SOURCE_CRUNCHBASE);
		
		$sources[1]['name'] = Model_Company::SOURCE_PRWEB;
		$sources[1]['title'] = Model_Company::full_source(Model_Company::SOURCE_PRWEB);

		$sources[2]['name'] = Model_Company::SOURCE_MARKETWIRED;
		$sources[2]['title'] = Model_Company::full_source(Model_Company::SOURCE_MARKETWIRED);

		$sources[3]['name'] = Model_Company::SOURCE_BUSINESSWIRE;
		$sources[3]['title'] = Model_Company::full_source(Model_Company::SOURCE_BUSINESSWIRE);

		$sources[4]['name'] = Model_Company::SOURCE_MYNEWSDESK;
		$sources[4]['title'] = Model_Company::full_source(Model_Company::SOURCE_MYNEWSDESK);

		$sources[5]['name'] = Model_Company::SOURCE_NEWSWIRE_CA;
		$sources[5]['title'] = Model_Company::full_source(Model_Company::SOURCE_NEWSWIRE_CA);

		$sources[6]['name'] = Model_Company::SOURCE_OWLER;
		$sources[6]['title'] = Model_Company::full_source(Model_Company::SOURCE_OWLER);

		$sources[7]['name'] = Model_Company::SOURCE_PR_CO;
		$sources[7]['title'] = Model_Company::full_source(Model_Company::SOURCE_PR_CO);
		*/

		$this->sources = $sources;
		$this->vd->sources = $sources;
	}

	protected function stats_report()
	{
		$sources = array();
		$posts = $this->input->post();

		$this->add_sales_agents();

		$duration = $posts['duration'];
		$date_start = $posts['date_start'];
		$date_end = $posts['date_end'];
		$sales_agent_id = $posts['sales_agent_id'];

		$this->vd->duration = $duration;
		$this->vd->date_start = $date_start;
		$this->vd->date_end = $date_end;
		$this->vd->sales_agent_id = $sales_agent_id;

		$dates = $this->get_start_end_date($duration);

		if (is_array($dates))
		{
			$date_start = $dates['date_start'];
			$date_end = $dates['date_end'];
		}
		
		foreach ($this->sources as $source)
			if (!empty($posts[$source]))
				$sources[] = $source;

		$source_filter = 1;
		if (count($sources))
		{
			$sources_list = sql_in_list($sources);
			$source_filter = "nr.source IN ({$sources_list})";
		}

		$this->vd->sources_selected = $sources;

		$sales_agent_filter = 1;
		if ($sales_agent_id)
			$sales_agent_filter = "ne.sales_agent_id = {$sales_agent_id}";

		$sql = "SELECT DATE_FORMAT(t.date_created,'%a %m/%d') AS date_created, 
				DATE_FORMAT(t.date_created,'%Y-%m-%d') AS date_created_ymd,
				nr.source, COUNT(t.price) AS counter, 
				SUM(ROUND(t.price, 2)) as total_price
				FROM nr_newsroom nr
				INNER JOIN co_transaction t
				ON nr.user_id = t.user_id

				LEFT JOIN ac_nr_auto_built_nr_export_x_company xc
				ON xc.company_id = nr.company_id
				LEFT JOIN ac_nr_auto_built_nr_export ne
				ON xc.auto_built_nr_export_id = ne.id
				LEFT JOIN nr_sales_agent sa 
				ON ne.sales_agent_id = sa.id

				WHERE {$source_filter}
				AND t.date_created between '{$date_start}' AND '{$date_end}'
				AND {$sales_agent_filter}
				GROUP BY DATE(t.date_created), nr.source
				ORDER BY t.date_created";

		$query = $this->db->query($sql);

		$results = Model_Newsroom::from_db_all($query);

		$rows = array();
		$prev_date = -1;
		$sums = array();	
		
		foreach ($results as $result)
		{
			if ($result->date_created != $prev_date)
			{
				if (isset($row) && is_array($row) && count($row))
				{
					$row['date'] = $prev_date;
					$row['date_ymd'] = $prev_date_ymd;
					$rows[] = $row;
				}

				$row = array();
				$prev_date = $result->date_created;
				$prev_date_ymd = $result->date_created_ymd;
			}

			$row[$result->source]['total_price'] = round($result->total_price, 2);
			$row[$result->source]['count'] = $result->counter;

			if (!isset($sums[$result->source]['price']))
				$sums[$result->source]['price'] = 0;

			$sums[$result->source]['price'] = $sums[$result->source]['price'] + round($result->total_price, 2);

			if (!isset($sums[$result->source]['count']))
				$sums[$result->source]['count'] = 0;

			$sums[$result->source]['count'] = $sums[$result->source]['count'] + $result->counter;
		}

		if (isset($row) && is_array($row) && count($row))
		{
			$row['date'] = $prev_date;
			$row['date_ymd'] = $prev_date_ymd;
			$rows[] = $row;
		}

		$this->vd->is_posted = 1;

		$this->vd->results = $rows;
		$this->vd->sums = $sums;
		$this->vd->load_stats_table = 1;

		$t_modal = new Modal();
		$t_modal->set_title("Transaction Detail");
		$this->add_eob($t_modal->render(500, 400));
		$this->vd->t_modal_id = $t_modal->id;
	}

	protected function single_stats($is_posted = true)
	{
		$sources = array();

		$this->add_sales_agents();

		if ($is_posted)
		{
			$posts = $this->input->post();

			$duration = $posts['duration'];
			$date_start = $posts['date_start'];
			$date_end = $posts['date_end'];
			$sales_agent_id = $posts['sales_agent_id'];

			$this->vd->date_start = $date_start;
			$this->vd->date_end = $date_end;
			$this->vd->sales_agent_id = $sales_agent_id;

			foreach ($this->sources as $source)
				if (!empty($posts[$source]))
					$sources[] = $source;

			if (count($sources))
				$sources_list = sql_in_list($sources);
		}
		else
		{
			$duration = 'this_month';
			$sources = array(Model_Company::SOURCE_MYNEWSDESK, Model_Company::SOURCE_OWLER);
		}

		$dates = $this->get_start_end_date($duration);

		if (is_array($dates))
		{
			$date_start = $dates['date_start'];
			$date_end = $dates['date_end'];
		}

		$this->vd->sources_selected = $sources;
		$this->vd->duration = $duration;

		$sales_agent_filter = 1;
		if (!empty($sales_agent_id))
			$sales_agent_filter = "ne.sales_agent_id = {$sales_agent_id}";

		$vd_results = array();

		foreach ($sources as $source)
		{
			$tbl_prefix = Model_Company::scraping_source_tbl_prefix($source);
			
			$sql = "SELECT DATE_FORMAT(t.date_created,'%m/%d') AS date_created, 
					DATE_FORMAT(c.date_first_exported_to_csv,'%m/%d') AS date_exported,
					ROUND(t.price, 2) AS price, 
					t.id, t.virtual_cart,
					nr.company_id, nr.company_name, nr.name,
					sa.first_name AS agent_first_name,
					sa.last_name AS agent_last_name
					FROM {$tbl_prefix}company c
					INNER JOIN nr_newsroom nr
					ON c.company_id = nr.company_id
					INNER JOIN co_transaction t
					ON nr.user_id = t.user_id

					LEFT JOIN ac_nr_auto_built_nr_export_x_company xc
					ON xc.company_id = nr.company_id
					LEFT JOIN ac_nr_auto_built_nr_export ne
					ON xc.auto_built_nr_export_id = ne.id
					LEFT JOIN nr_sales_agent sa 
					ON ne.sales_agent_id = sa.id

					WHERE nr.source = '{$source}'
					AND t.date_created between '{$date_start}' AND '{$date_end}'
					AND {$sales_agent_filter}
					ORDER BY t.date_created";

			$query = $this->db->query($sql);

			$results = Model_Newsroom::from_db_all($query);

			$total = 0;

			foreach ($results as $result)
			{
				$virtual_cart = json_decode($result->virtual_cart);
				$items = array();
				foreach ($virtual_cart->items as $item)
				{
					$m_item = Model_Item::find($item->item_id);
					$i = new stdClass();
					$i->name = $m_item->name;
					$i->quantity = $item->quantity;
					$items[] = $i;
				}

				$total = $total + $result->price;

				$result->items = $items;
			}

			$total_title = "{$source}_total";

			$vd_results[$total_title] = $total;
			$vd_results[$source] = $results;
		}

		$this->vd->results = $vd_results;

		$this->vd->is_posted = 1;
		$this->vd->load_single_stats = 1;
		
	}

	protected function add_sales_agents()
	{
		$criteria = array();
		$criteria[] = array('is_deleted', 0);
		$criteria[] = array('is_active', 1);
		$sort_order = array('first_name', 'asc');
		$sales_agents = Model_Sales_Agent::find_all($criteria, $sort_order);
		$this->vd->sales_agents = $sales_agents;
	}

	protected function get_start_end_date($duration)
	{
		$posts = $this->input->post();

		$date_start = $posts['date_start'];
		$date_end = $posts['date_end'];

		$timezone = new DateTimeZone('America/New_York');

		if ($duration == "today")
		{
			$date_end = Date::hours(0);
			$date_end->setTimezone($timezone);
			$date_end->setTime(23, 59, 59);
			$date_end->setTimezone(Date::$utc);
			
			$date_start = Date::days(-1, $date_end);
		}

		elseif ($duration == "this_week")
		{
			$d_start = new DateTimeExtended();
			$d_start->setTimezone($timezone);
			$d_start->modify('this week');
			$d_start->setTime(0, 0, 1);
			$d_start->setTimezone(Date::$utc);
			$date_start = $d_start->format(Date::FORMAT_MYSQL);
			
			$d_end = new DateTimeExtended();
			$d_end->setTimezone($timezone);
			$d_end->modify('this week +6 days');
			$d_end->setTime(23, 59, 59);
			$d_end->setTimezone(Date::$utc);
			$date_end = $d_end->format(Date::FORMAT_MYSQL);
		}

		elseif ($duration == "last_month")
		{
			$d_start = new DateTimeExtended();
			$d_start->setTimezone($timezone);
			$d_start->modify('first day of last month');
			$d_start->setTime(0, 0, 1);
			$d_start->setTimezone(Date::$utc);
			$date_start = $d_start->format(Date::FORMAT_MYSQL);
			
			$d_end = new DateTimeExtended();
			$d_end->setTimezone($timezone);
			$d_end->modify('last day of last month');
			$d_end->setTime(23, 59, 59);
			$d_end->setTimezone(Date::$utc);
			$date_end = $d_end->format(Date::FORMAT_MYSQL);
		}

		elseif ($duration == "this_month")
		{
			$d_start = new DateTimeExtended();
			$d_start->setTimezone($timezone);
			$d_start->modify('first day of this month');
			$d_start->setTime(0, 0, 1);
			$d_start->setTimezone(Date::$utc);
			$date_start = $d_start->format(Date::FORMAT_MYSQL);
			
			$d_end = new DateTimeExtended();
			$d_end->setTimezone($timezone);
			$d_end->modify('last day of this month');
			$d_end->setTime(23, 59, 59);
			$d_end->setTimezone(Date::$utc);
			$date_end = $d_end->format(Date::FORMAT_MYSQL);
		}

		elseif ($duration == "this_year")
		{
			$current_year = date('Y');
			$d_start = new DateTimeExtended();
			$d_start->setTimezone($timezone);
			$d_start->setDate($current_year, 1, 1);
			$d_start->setTime(0, 0, 1);
			$d_start->setTimezone(Date::$utc);
			$date_start = $d_start->format(Date::FORMAT_MYSQL);
			
			$d_end = new DateTimeExtended();
			$d_end->setTimezone($timezone);
			$d_end->setDate($current_year, 12, 31);
			$d_end->setTime(23, 59, 59);
			$d_end->setTimezone(Date::$utc);
			$date_end = $d_end->format(Date::FORMAT_MYSQL);
		}

		return array('date_start' => $date_start, 'date_end' => $date_end);
	}

	public function transaction_detail($source, $t_date)
	{
		$tbl_prefix = Model_Company::scraping_source_tbl_prefix($source);

		if (empty($t_date))
			return;

		$date_start = "{$t_date} 00:00:01";
		$date_end = "{$t_date} 23:59:59";
		
		$sql = "SELECT DATE_FORMAT(t.date_created,'%m/%d') AS date_created, 
				DATE_FORMAT(c.date_first_exported_to_csv,'%m/%d') AS date_exported,
				ROUND(t.price, 2) AS price, 
				t.id, t.virtual_cart, t.is_renewal,
				nr.company_id, nr.company_name, nr.name,
				u.first_name, u.last_name, t.user_id,
				sa.first_name AS agent_first_name,
				sa.last_name AS agent_last_name
				FROM {$tbl_prefix}company c
				INNER JOIN nr_newsroom nr
				ON c.company_id = nr.company_id
				INNER JOIN co_transaction t
				ON nr.user_id = t.user_id
				INNER JOIN nr_user u
				ON t.user_id = u.id
				
				LEFT JOIN ac_nr_auto_built_nr_export_x_company xc
				ON xc.company_id = nr.company_id
				LEFT JOIN ac_nr_auto_built_nr_export ne
				ON xc.auto_built_nr_export_id = ne.id
				LEFT JOIN nr_sales_agent sa 
				ON ne.sales_agent_id = sa.id


				WHERE nr.source = '{$source}'
				AND t.date_created between '{$date_start}' AND '{$date_end}'
				ORDER BY t.date_created";

		$query = $this->db->query($sql);

		$results = Model_Newsroom::from_db_all($query);

		foreach ($results as $result)
		{
			$virtual_cart = json_decode($result->virtual_cart);
			$items = array();

			foreach ($virtual_cart->items as $item)
			{
				$m_item = Model_Item::find($item->item_id);
				$i = new stdClass();
				$i->name = $m_item->name;
				$i->quantity = $item->quantity;
				$items[] = $i;
			}

			$date_created = $result->date_created;
			
			$result->items = $items;
		}

		$this->vd->results = $results;
		$this->vd->source_title = Model_Company::full_source($source);
		$this->vd->date_created = $date_created;
		$this->load->view('admin/nr_builder/stats/transaction_detail_modal');
	}
	
}

?>