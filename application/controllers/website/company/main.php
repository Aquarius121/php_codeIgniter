<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/news-center/base');

class Main_Controller extends News_Center_Base {
	
	protected static $allowed_on_common_host = array('//');
	protected $limit = 20;
	
	public function __construct()
	{
		parent::__construct();
		
		if (!$this->is_website_host)
		{
			$url = $this->website_url($this->uri->uri_string);
			$this->redirect(gstring($url), false);
		}
		
		$this->vd->title[] = 'Company Directory';
	}
	
	public function _remap($method, $params = array())
	{
		$chunk = 1;
		$url = $this->uri->uri_string;
		$this->vd->news_center_params = $params;
		
		if (count($params) > 1)
		{
			$last_b1 = $params[count($params) - 2];
			$last_b0 = $params[count($params) - 1];
			
			if ($last_b1 === 'page' && is_numeric($last_b0))
			{
				$url_params = array_slice($this->uri->segments, 0, -2);
				$this->vd->news_center_params = $params = array_slice($url_params, 1);
				$url = implode('/', $url_params);
				if ($last_b0 == 1) $this->redirect(gstring($url));
				$chunk = $last_b0;
			}
		}

		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size($this->limit);
		$url_format = gstring("{$url}/page/-chunk-");
		$chunkination->set_url_format($url_format);
		$this->chunkination = $chunkination;
		$this->vd->chunkination = $chunkination;
		$this->offset = $chunkination->offset();		
		
		if ($method === null)
			$method = array_shift($params);
		return parent::_remap($method, $params);
	}

	protected function __on_execution_start()
	{
		$this->cache_duration = 2592000;
	}

	public function search()
	{
		$this->title = 'Search Results';
		$terms = $this->input->get('terms');
		if (!$terms) return $this->render_list_view(array());
		$fields = array('n.name');
		$terms_sql = sql_search_terms($fields, $terms);
		
		$terms_str = $this->vd->esc($terms);
		$terms_str = preg_replace('#(\W|^)-(\w+)#s', 
			'$1<span class="status-false">$2</span>', $terms_str);
		$this->vd->ln_header_html = "<span class=\"muted\">
			Search:</span> {$terms_str}";

		$sql = "SELECT SQL_CALC_FOUND_ROWS
			n.company_id
			FROM nr_newsroom n
			LEFT JOIN nr_company_profile cp
			ON cp.company_id = n .company_id
			WHERE n.is_active = 1			
			AND {$terms_sql}
			AND n.company_id NOT IN (
				SELECT id FROM nr_company 
				WHERE newsroom REGEXP '^.*[0-9]{3}$'
				AND date_created > '2015-03-15' )
			ORDER BY cp.company_id DESC
			LIMIT {$this->offset}, {$this->limit}";

		$results = $this->find_results($sql);
		$this->render_list_view($results);
	}
	
	public function industries()
	{
		$sql = "SELECT DISTINCT(beat_id) AS beat_id
			FROM nr_newsroom n
			INNER JOIN nr_company_profile cp
			ON cp.company_id = n .company_id
			WHERE n.is_active = 1
			AND cp.beat_id IS NOT NULL";

		$beat_ids = array();
		$query = $this->db->query($sql);
		$results = Model_Beat::from_db_all($query);
		foreach ($results as $result)
			$beat_ids[] = $result->beat_id;
		
		$groups = Model_Beat::list_all_beats_by_group($beat_ids);
		$this->vd->beat_groups = $groups;

		$lines_count = 0;		
		foreach ($groups as $group)
			foreach ($group->beats as $beat)
				$lines_count++;

		$this->vd->title[] = 'Industries';
		$this->vd->lines_count = $lines_count;
		$this->load->view('website/header');
		$this->load->view('website/company/industries');
		$this->load->view('website/footer');
	}

	public function industry($slug)
	{
		$criteria = array('slug', $slug);
		$beat = Model_Beat::find($criteria);
		if (!$beat) $this->redirect('company');
		
		$this->title = $beat->name;
		$beat_name = $this->vd->esc($beat->name);
		$this->vd->ln_header_html = "<span class=\"muted\">
			Industry:</span> {$beat_name}";
		
		$beat_id_list = array();
		
		$sql = "SELECT id FROM nr_beat
			WHERE id = {$beat->id} 
			OR beat_group_id = {$beat->id}";
		
		$dbr = $this->db->query($sql);
		$beat_id_list = Model_Beat::values_from_db($dbr, 'id');

		$beat_in_id_list = sql_in_list($beat_id_list);
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS
			n.company_id
			FROM nr_newsroom n
			INNER JOIN nr_company_profile cp
			ON cp.company_id = n .company_id
			WHERE n.is_active = 1
			AND cp.beat_id IN ({$beat_in_id_list})
			ORDER BY cp.company_id DESC
			LIMIT {$this->offset}, {$this->limit}";
		
		$results = $this->find_results($sql);
		$this->render_list_view($results);
	}
	
	public function all()
	{
		$this->basic();
	}
		
	public function index($filter = null)
	{
		// filter can be null or industry
		if ($filter === null) return $this->basic();
		$this->industry($filter);
	}

	protected function basic()
	{	
		$basic_filter = 1;
		$sql = "SELECT SQL_CALC_FOUND_ROWS
			n.company_id
			FROM nr_newsroom n
			LEFT JOIN nr_company_profile cp
			ON cp.company_id = n .company_id
			WHERE n.is_active = 1
			AND {$basic_filter}
			ORDER BY cp.company_id DESC
			LIMIT {$this->offset}, {$this->limit}";
		
		$results = $this->find_results($sql);
		$this->render_list_view($results);
	}
	
	protected function render_list_view($results)
	{
		$this->vd->results = $results;
		
		if ($this->chunkination->is_out_of_bounds()) 
		{
			// redirect to the first chunk as out of bounds
			$params = array_slice($this->uri->segments, 0, -2);
			$url = gstring(implode('/', $params));
			$this->redirect($url);
		}
		
		$this->load->view('website/header');
		$this->load->view('website/company/listing');
		$this->load->view('website/footer');
	}

	protected function find_results($sql, $params = null)
	{	
		$results = array();
		$id_filter = array();
		$query = $this->db->query($sql, $params);
		$ids = Model_Company::values_from_db($query, 'company_id');

		if (!is_array($ids) || !count($ids))
			return array();

		$ids_list = sql_in_list($ids);

		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
		$this->chunkination->set_total($total_results);
		if ($this->chunkination->is_out_of_bounds())
			return array();

		$cp_prefixes = Model_Company_Profile::__prefixes('cp', 'profile');
		//$nrc_prefixes = Model_Newsroom_Custom::__prefixes('nrc', 'custom');

		$sql = "SELECT nr.*, 
				{$cp_prefixes},
    			nrc.*,
				b.name as beat_name
				FROM nr_newsroom nr
				LEFT JOIN nr_newsroom_custom nrc
				ON nrc.company_id = nr.company_id
				LEFT JOIN nr_company_profile cp
				ON cp.company_id = nr.company_id
				LEFT JOIN nr_beat b
				ON cp.beat_id = b.id
				WHERE nr.company_id IN ({$ids_list})
				ORDER BY nr.company_id DESC";

		$results = Model_Newsroom::from_sql_all($sql, array(), array(
			'profile' => 'Model_Company_Profile',
		));

		$class = get_class($this);
		$method = 'combined_sort';
		$callable = array($class, $method);
		usort($results, $callable);	

		return $results;
	}

}

?>
