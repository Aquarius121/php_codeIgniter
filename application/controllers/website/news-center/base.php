<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/news-center/listing');

class News_Center_Base extends News_Center_Listing {
	
	protected $basic_types;

	use Search_Util_Trait;

	public function __construct()
	{
		parent::__construct();
	}
	
	public function _remap($method, $params = array())
	{
		// detect rss request with params
		// and enable rss mode for normal request
		if ($method == 'rss' && count($params))
		{
			$method = $params[0];
			$params = array_slice($params, 1);
			$this->limit = $this->rss_limit;
			$this->rss_enabled = true;
			return $this->_remap($method, $params);
		}

		return parent::_remap($method, $params);
	}	
	
	public function search()
	{
		$this->title = 'Search Results';
		$terms_str = $this->input->get('terms');		

		$terms_str = $this->vd->esc($terms_str);
		$terms_str = preg_replace('#(\W|^)-(\w+)#s', 
			'$1<span class="status-false">$2</span>', $terms_str);
		$this->vd->ln_header_html = "<span class=\"muted\">
			Search:</span> {$terms_str}";		

		$results = $this->search_news_center($terms_str);
		$this->render_list_view($results);
	}

	public function cat($slug)
	{
		$this->redirect_301('browse');
	}

	public function beat($slug)
	{
		$beat = Model_Beat::find_slug($slug);
		if (!$beat) $this->redirect('browse');
		
		$this->title = $beat->name;
		$beat_name = $this->vd->esc($beat->name);
		$this->vd->ln_header_html = "<span class=\"muted\">
			Category:</span> {$beat_name}";
		
		$beat_id_list = array();
		$sql = "SELECT id FROM nr_beat
			WHERE id = {$beat->id} 
			OR beat_group_id = {$beat->id}";
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $result)
			$beat_id_list[] = $result->id;
		$beat_in_id_list = sql_in_list($beat_id_list);

		$nc_types = $this->news_center_types();
		$nc_types_filter = 1;
		if (is_array($nc_types) && count($nc_types))
		{
			$nc_types_str = sql_in_list($nc_types);
			$nc_types_filter = "c.type IN ({$nc_types_str})";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
			c.id, c.type FROM nr_beat_x_content bxc
			INNER JOIN nr_content c ON 
			bxc.beat_id IN ({$beat_in_id_list})
			AND bxc.content_id = c.id
			INNER JOIN nr_newsroom nr ON 
			c.company_id = nr.company_id AND
			(c.type = ? OR nr.is_active = 1)
			WHERE {$nc_types_filter}
			AND c.is_published = 1
			AND is_excluded_from_news_center = 0
			GROUP BY c.id
			ORDER BY c.date_publish DESC 
			LIMIT {$this->offset}, {$this->limit}";
		
		$results = $this->find_results($sql, array(Model_Content::TYPE_PR));
		$this->render_list_view($results);
	}

	public function tag($slug)
	{
		if ($this->rss_enabled)
			show_404();

		$this->title = $slug;
		$slug = $this->vd->esc($slug);
		$this->vd->ln_header_html = "<span class=\"muted\">
			Tagged:</span> {$slug}";
		
		$nc_types = $this->news_center_types();
		$nc_types_filter = 1;
		if (is_array($nc_types) && count($nc_types))
		{
			$nc_types_str = sql_in_list($nc_types);
			$nc_types_filter = "c.type IN ({$nc_types_str})";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
			c.id, c.type FROM nr_content c 
			INNER JOIN nr_content_tag ct ON 
			c.id = ct.content_id
			INNER JOIN nr_newsroom nr ON 
			c.company_id = nr.company_id AND
			(c.type = ? OR nr.is_active = 1)
			WHERE {$nc_types_filter}
			AND is_excluded_from_news_center = 0
			AND c.is_published = 1 AND ct.uniform = ?
			LIMIT {$this->offset}, {$this->limit}";
		
		$results = $this->find_results($sql, array(Model_Content::TYPE_PR, $slug));
		$this->render_list_view($results);
	}
	
	protected function basic($types = null, $basic_filter = 1)
	{
		if (!$types) $types = $this->basic_types;
		if (!$types) $types = Model_Content::allowed_types();
		
		$types_quoted = array();
		foreach ($types as $type)
		{
			if (!Model_Content::is_allowed_type($type)) continue;
			$types_quoted[] = $this->db->escape($type);
		}
		
		if (count($types) === 1)
		{
			$type = $types[0];

			$this->title = Model_Content::full_type_plural($type);
			$content_type_labels = $this->vd->content_type_labels;
			if (!empty($content_type_labels->{$type}->plural))
				$this->title = $content_type_labels->{$type}->plural;
		}

		$types_str = implode(',', $types_quoted);
		if (!$types_str) return show_404();

		$nc_types = $this->news_center_types();
		$nc_types_filter = 1;
		if (is_array($nc_types) && count($nc_types))
		{
			$nc_types_str = sql_in_list($nc_types);
			$nc_types_filter = "c.type IN ({$nc_types_str})";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
			c.type, c.id
			FROM nr_content c 
			INNER JOIN nr_newsroom nr ON 
			c.company_id = nr.company_id AND
			(c.type = ? OR nr.is_active = 1)
			WHERE {$nc_types_filter}
			AND c.is_excluded_from_news_center = 0
			AND c.type IN ({$types_str}) 
			AND c.is_published = 1
			AND {$basic_filter}
			ORDER BY c.date_publish DESC
			LIMIT {$this->offset}, {$this->limit}";

		$results = $this->find_results($sql, array(Model_Content::TYPE_PR));
		$this->render_list_view($results);
	}
	
	public function rss()
	{
		$nc_types = $this->news_center_types();
		$nc_types_filter = 1;
		if (is_array($nc_types) && count($nc_types))
		{
			$nc_types_str = sql_in_list($nc_types);
			$nc_types_filter = "c.type IN ({$nc_types_str})";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
			c.type, c.id FROM nr_content c 
			INNER JOIN nr_newsroom nr ON 
			c.company_id = nr.company_id AND
			(c.type = ? OR nr.is_active = 1)
			WHERE {$nc_types_filter}
			AND is_excluded_from_news_center = 0
			AND c.is_published = 1
			ORDER BY c.date_publish DESC
			LIMIT 0, {$this->rss_limit}";

		$results = $this->find_results($sql, array(Model_Content::TYPE_PR));
		$this->vd->results = $results;
		
		$this->output->set_content_type('application/rss+xml');
		$this->load->view('browse/rss');
	}

	protected function news_center_types()
	{
		return array(
			Model_Content::TYPE_PR,
			Model_Content::TYPE_NEWS,
			Model_Content::TYPE_IMAGE,
			Model_Content::TYPE_AUDIO,
			Model_Content::TYPE_VIDEO,
			Model_Content::TYPE_EVENT,
		);
	}

	protected function search_news_center($terms = null)
	{
		if (!$terms) return array();
		
		$sqwc = new Search_Query_With_Content($this->db);
		$sqwc->query($terms);
		$tf_table = $sqwc->table();

		$nc_types = $this->news_center_types();
		$nc_types_in_list = sql_in_list($nc_types);
		$type_PR = escape_and_quote(Model_Content::TYPE_PR);
				
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			c.type, c.id, (tf.quality - (1 * 
				  DATEDIFF(UTC_TIMESTAMP(), c.date_publish))) 
				AS orderQuality
			 FROM nr_content c 
			INNER JOIN {$tf_table} tf
			ON tf.content_id = c.id
			INNER JOIN nr_newsroom nr ON 
			c.company_id = nr.company_id AND
				(c.type = {$type_PR} OR nr.is_active = 1)
			WHERE c.type IN ({$nc_types_in_list})
			AND c.is_excluded_from_news_center = 0
			AND c.is_published = 1
			ORDER BY orderQuality DESC, c.id DESC
			LIMIT {$this->offset},
				{$this->limit}";

		$results = $this->find_results($sql);
		return $results;
	}

}
