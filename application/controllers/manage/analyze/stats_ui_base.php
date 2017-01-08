<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stats_UI_Base {

	protected $context;
	protected $_this;
	
	public function __construct($_this, $context)
	{
		$this->_this = $_this;
		$this->context = $context;
	}

	public function index($view)
	{
		$_this = $this->_this;		
		$stats_query = new Stats_Query();
		$date_range = $this->set_date_range();
		$_this->vd->hits = $stats_query
			->hits_summation($this->context);

		$this->views_chart();
		$this->hours_chart();

		$_this->load->view('manage/header');
		$_this->load->view($view);
		$_this->load->view('manage/footer');
	}

	protected function set_date_range()
	{
		$_this = $this->_this;

		$date_range = static::calc_date_range($_this);
		$_this->vd->dt_min = $date_range[0];
		$_this->vd->dt_max = $date_range[1];

		return $date_range;
	}

	public static function calc_date_range($_this)
	{
		$dt_min = Date::days(-14, Date::local());
		$dt_max = Date::local();
		
		if (($date_start = $_this->input->get_post('date_start')))
			$dt_min = Date::local($date_start);
		if (($date_end = $_this->input->get_post('date_end')))
			$dt_max = Date::local($date_end);
		if ($dt_min > Date::days(-3, $dt_max))
			$dt_max = Date::days(3, $dt_min);
		if (Date::years(1, $dt_min) < $dt_max)
			$dt_max = Date::years(1, $dt_min);
		
		$dt_min->setTime(0, 0, 0);
		$dt_max->setTime(23, 59, 59);

		return array($dt_min, $dt_max);
	}
	
	public function views_chart()
	{
		$_this = $this->_this;
		$stats_query = new Stats_Query();
		$date_range = $this->set_date_range();
		$date_span = $date_range[0]->diff($date_range[1])->days;
		$data = $stats_query->hits_daily_summation($this->context, 
			$date_range[0], $date_range[1]);

		$max_data_points = 34;
		$each_point = 1;
		$data_points = 0;

		for ($i = 35; $i >= 18; $i--)
		{
			if ($date_span % $i === 0 && $i > $data_points)
			{
				$each_point = $date_span / $i;
				$data_points = $i;
			}
		}

		if ($data_points === 0) 
		{
			$each_point = intval($date_span / 30) + 1;
			$data_points = 30;
		}

		$chunk = array_chunk($data, $each_point, true);
		
		foreach ($chunk as $data) 
		{	
			$sum = array_sum($data);
			$dates = array_keys($data);
			$dt = Date::utc($dates[intval(count($dates) / 2)]);
			$chart_data[] = $cd = new stdClass();
			$cd->label = $dt->format('M j');
			$cd->value = $sum;
		}
	
		$colors = new stdClass();
		$colors->line = array(19, 87, 168, 0);
		$colors->fill = array(19, 87, 168, 115);
		$colors->point = array(19, 87, 168, 0);
		$colors->highlight = array(42, 125, 223, 1);

		if ($this->_this->rdata->chart_color_line)
			$colors->line = $this->_this->rdata->chart_color_line;
		if ($this->_this->rdata->chart_color_fill)
			$colors->fill = $this->_this->rdata->chart_color_fill;

		$lines = array();
		$line = new stdClass();
		$line->points = $chart_data;
		$line->color = $colors;
		$lines[] = $line;

		$chart = new Canvas_Line_Chart($lines, 460, 100);
		$chart->is_hide_legend = 1;
		$_this->vd->views_chart = $chart->render();
	}

	public function hours_chart()
	{
		$_this = $this->_this;
		$stats_query = new Stats_Query();
		$date_range = $this->set_date_range();
		$data = $stats_query->hits_hour_window_summation($this->context);
		$chart_data = array();

		foreach ($data as $hour => $sum)
		{
			$chart_data[] = $cd = new stdClass();
			$cd->label = sprintf('%02d:00', $hour);
			$cd->value = $sum;
		}

		$colors = new stdClass();
		$colors->line = array(19, 87, 168, 0);
		$colors->fill = array(19, 87, 168, 115);

		$lines = array();
		$line = new stdClass();
		$line->points = $chart_data;
		$line->color = $colors;
		$lines[] = $line;

		$chart = new Canvas_Bar_Chart($lines, 460, 75);
		$chart->is_hide_legend = 1;
		$_this->vd->hours_chart = $chart->render();
	}

	public function geolocation($view = null, $countries = 5, $regions = 5, $regions_per_country = PHP_INT_MAX) 
	{
		$default_view = 'manage/analyze/partials/geolocation';
		$countries = (int) $countries;
		$regions = (int) $regions;

		$_this = $this->_this;
		$context = $this->context;
		$stats_query = new Stats_Query();
		$bucket = Stats_Engine::hits_bucket($context);

		// count hits for each country and select largest
		$sql = "SELECT geo.*, ld.country_name from 
			(select count(1) as count, geo_country
			from {$bucket} where context = ? group by geo_country) geo 
			inner join (select distinct geo_country, country_name
				from location_data) ld
			on geo.geo_country = ld.geo_country
			order by count desc
			limit {$countries}";

		$params = array($context);
		$countries = $stats_query->query($sql, $params);
		$_this->vd->countries = $countries;
		$indexed_countries = array();
		$countries_in_list = array(null);

		foreach ($countries as $k => $country)
		{
			$country->flag = $this->find_flag($country->geo_country);
			$countries_in_list[] = $country->geo_country;
			$country->regions = array();
			$indexed_countries[$country->geo_country]
				= $country;
		}

		// count hits for each region and select largest
		$countries_in_list = sql_in_list($countries_in_list);
		$sql = "SELECT geo.*, ld.sub_name as region_name from 
			(select count(1) as count, geo_country, geo_sub	
			from {$bucket} where context = ? 
			and geo_country in ({$countries_in_list})
			and length(geo_sub) > 0
			group by geo_country, geo_sub) geo 
			left join location_data ld
			on geo.geo_country = ld.geo_country
			and geo.geo_sub = ld.geo_sub
			order by count desc
			limit {$regions}";

		$params = array($context);
		$regions = $stats_query->query($sql, $params);

		foreach ($regions as $region)
		{
			$country = $indexed_countries[$region->geo_country];
			if (count($country->regions) < $regions_per_country)
				$country->regions[] = $region;
		}
		
		if ($view === null) 
			$view = $default_view;
		$render = $_this->load->view($view);
		$_this->expires(300);
		return $render;
	}

	public function world_map($view = null, $width = '100%', $height = '300px') 
	{
		$default_view = 'manage/analyze/partials/world-map';
		$_this = $this->_this;
		$_this->vd->width = $width;
		$_this->vd->height = $height;
		$context = $this->context;
		$stats_query = new Stats_Query();
		$bucket = Stats_Engine::hits_bucket($context);

		// count hits for each country
		$sql = "SELECT count(1) as count, geo_country
			from {$bucket} where context = ? group by geo_country";

		$params = array($context);
		$countries = $stats_query->query($sql, $params);

		$chart_data = array();
		foreach ($countries as $country)
			$chart_data[strtolower($country->geo_country)]
				= $country->count;

		$_this->vd->chart_data = $chart_data;

		if ($view === null) 
			$view = $default_view;
		$render = $_this->load->view($view);
		$_this->expires(300);
		return $render;
	}

	public function us_states_map($view = null, $width = '100%', $height = '300px')
	{
		$default_view = 'manage/analyze/partials/us-states-map';
		$_this = $this->_this;
		$_this->vd->width = $width;
		$_this->vd->height = $height;
		$context = $this->context;
		$stats_query = new Stats_Query();
		$bucket = Stats_Engine::hits_bucket($context);

		// count hits for each country
		$sql = "SELECT count(1) as count, geo_sub
			from {$bucket} where context = ? and geo_country = 'US'
			group by geo_sub";

		$params = array($context);
		$subs = $stats_query->query($sql, $params);

		$chart_data = array();
		foreach ($subs as $sub)
			$chart_data[strtolower($sub->geo_sub)]
				= $sub->count;

		$_this->vd->chart_data = $chart_data;
		if ($view === null) 
			$view = $default_view;
		$render = $_this->load->view($view);
		$_this->expires(300);
		return $render;
	}

	protected function find_flag($geo_country)
	{
		$base = $this->_this->conf('assets_base_dir');
		$relative = sprintf('im/flags/%s.png', $geo_country);
		if (!is_file(sprintf('%s/%s', $base, $relative)))
		     return 'im/flags/default.png';
		else return $relative;
	}
	
}

?>