<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/listing');

class Browse_Controller extends Listing_Base {
	
	protected $basic_types;
	protected $is_include_social_feed;

	use Search_Util_Trait;

	public function __construct()
	{
		parent::__construct();
		
		if ($this->is_common_host)
		{
			$url = implode('/', array_splice($this->uri->segments, 1));
			$url = $this->website_url("newsroom/{$url}");
			$this->redirect(gstring($url), false);
		}
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
	
	public function all()
	{
		$this->limit *= 2;
		$this->basic();
	}
	
	public function index($type = null)
	{
		// redirect browse to root of host
		if ($type === null && $this->uri->segment(1))
			$this->redirect(null);

		// common doesn't filter on type
		if ($this->is_common_host)
			$this->redirect('browse/all');

		// prevent displaying of the "default" newsroom
		if ((int) $this->newsroom->company_id === 0)
			$this->redirect($this->website_url(), false);
		
		$nr_profile = Model_Company_Profile::find($this->newsroom->company_id);
		
		if (!empty($nr_profile) && $nr_profile->has_any_valid_social_feed() && 
			$nr_profile->is_enable_social_wire)
			$this->is_include_social_feed = 1;

		if ($type)
		{
			$types = array($type);
			$type_str = Model_Content::full_type_plural($type);
			$type_str = $this->vd->esc($type_str);
			$content_type_labels = $this->vd->content_type_labels;

			if ($type == Model_Content::TYPE_SOCIAL)
			{
				if (!empty($content_type_labels->social->plural))
				     $this->vd->ln_header_html = $this->vd->esc($content_type_labels->social->plural);
				else $this->vd->ln_header_html = "<span class=\"muted\">Social</span> Wire";
			}
			else
			{
				if (!empty($content_type_labels->{$type}->plural))
					$type_str = $this->vd->esc($content_type_labels->{$type}->plural);
				$this->vd->ln_header_html = "<span class=\"muted\">Latest</span> {$type_str}";
			}

			if ($this->is_include_social_feed)
			{
				$criteria = array();
				$criteria[] = array('company_id', $this->newsroom->company_id);
				$criteria[] = array('type', Model_Content::TYPE_SOCIAL);
				if (! $soc = Model_Content::find($criteria)) // no social content already exists
					Social_Wire::update($this->newsroom->company_id, Social_Wire::UPDATE_MANUAL);
			}
		}
		else
		{
			$this->vd->newsroom_main_page = 1;
			$this->vd->ln_header_html = "<span class=\"muted\">Latest</span> News Feed";
			
			$sess_var_name = "show_video_guide_for_{$this->newsroom->company_id}";
			if ($this->session->get('ac_nr_tokened_visit_nr_id') && $this->newsroom->company_id
				&& $this->session->get('ac_nr_tokened_visit_nr_id') == $this->newsroom->company_id
				&& $this->session->get($sess_var_name) == 1)
			{
				$this->vd->show_intro_video_for_ac_nr = 1;

				$video_modal = new Modal();
				$video_modal->set_title('Control Panel Overview');
				$this->add_eob($video_modal->render(853, 480));
				$this->vd->video_modal_id = $video_modal->id;
				$this->vd->external_video_id = Model_Setting::value('overview_video');
				$this->session->set($sess_var_name, 0);
			}

			$types = array(
				Model_Content::TYPE_PR, 
				Model_Content::TYPE_NEWS,
				Model_Content::TYPE_EVENT,
				Model_Content::TYPE_AUDIO,
				Model_Content::TYPE_VIDEO,
				Model_Content::TYPE_IMAGE,
			);
		
			if ($nr_profile = Model_Company_Profile::find($this->newsroom->company_id))
			{
				if ($nr_profile->soc_rss && $nr_profile->is_enable_blog_posts)
					$types[] = Model_Content::TYPE_BLOG;

				if ($this->is_include_social_feed)
				{
					$types[] = Model_Content::TYPE_SOCIAL;
					
					$criteria = array();
					$criteria[] = array('company_id', $this->newsroom->company_id);
					$criteria[] = array('type', Model_Content::TYPE_SOCIAL);
					if (! $soc = Model_Content::find($criteria)) // no social content already exists
						Social_Wire::update($this->newsroom->company_id, Social_Wire::UPDATE_MANUAL);
				}
			}
		}

		if (!empty($this->vd->newsroom_main_page) && 
			$this->vd->newsroom_main_page && 
			$this->is_include_social_feed)
			$this->limit *= 5;

		$this->vd->type = $type;
		$this->basic($types);
	}

	public function search()
	{
		$this->title = 'Search Results';
		$terms_str = $this->input->get('terms');
		$terms = $this->extract_terms($terms_str);
		if (!$terms) return $this->render_list_view(array());
		$token = md5(microtime(true));

		// create a temporary table for matches
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS
			nr_search_ct_{$token} (
				content_id INT(11),
				PRIMARY KEY (content_id)
			) ENGINE=MEMORY";
		$this->db->query($sql);
		
		foreach ($terms as $index => $term)
		{
			if ($index === 0)
			{
				// insert content that has this term
				$sql = "INSERT IGNORE INTO nr_search_ct_{$token}
					SELECT si.content_id FROM nr_search_index_content si
					INNER JOIN nr_search_term st 
					ON st.id = si.search_term_id
					WHERE term = ?";

				$this->db->query($sql, array($term));
			}
			else
			{
				// remove all content without this term
				$sql = "DELETE sct FROM nr_search_ct_{$token} sct
					LEFT JOIN (
						SELECT si.content_id FROM nr_search_index_content si
						INNER JOIN nr_search_term st 
						ON st.id = si.search_term_id
						WHERE term = ?
					) si2 ON si2.content_id = sct.content_id
					WHERE si2.content_id IS NULL";

				$this->db->query($sql, array($term));
			}
		}
		
		$types = array(
			Model_Content::TYPE_PR,
			Model_Content::TYPE_NEWS,
			Model_Content::TYPE_VIDEO,
			Model_Content::TYPE_IMAGE,
			Model_Content::TYPE_EVENT,
			Model_Content::TYPE_AUDIO,
			Model_Content::TYPE_SOCIAL,
		);

		$types_sql = sql_in_list($types);
		$terms_str = $this->vd->esc($terms_str);
		$terms_str = preg_replace('#(\W|^)-(\w+)#s', 
			'$1<span class="status-false">$2</span>', $terms_str);
		$this->vd->ln_header_html = "<span class=\"muted\">
			Search:</span> {$terms_str}";
				
		$sql = "SELECT {$this->calculate_total_insert}
			  c.type, c.id FROM nr_content c 
			  INNER JOIN nr_search_ct_{$token} sct
			  ON sct.content_id = c.id
			  WHERE c.company_id = {$this->newsroom->company_id}
			  AND c.is_published = 1
			  AND c.type IN ({$types_sql})
			  ORDER BY c.id DESC
			  LIMIT {$this->offset}, {$this->limit}";

		$results = $this->find_results($sql);
		$this->render_list_view($results);

		// clean up temporary table
		$sql = "DROP TABLE IF EXISTS nr_search_ct_{$token}";
		$this->db->query($sql);
	}

	public function month($year, $month)
	{
		$year = (int) $year;
		$month = (int) $month;
		
		$ln_date = new DateTime("{$year}-{$month}-01");
		$ln_date = $ln_date->format('F Y');
		$this->vd->ln_header_html = "{$ln_date} 
			<span class=\"muted\">Archive</span>";
		
		$dt_0 = Date::in("{$year}-{$month}-01 00:00:00");
		$dt_1 = Date::months(1, $dt_0);
		
		$dt_0_str = $dt_0->format(Date::FORMAT_MYSQL);
		$dt_1_str = $dt_1->format(Date::FORMAT_MYSQL);
		$basic_filter = "c.date_publish >= '{$dt_0_str}'
			AND c.date_publish < '{$dt_1_str}'";
		
		$this->title = 'Archive';
		$this->basic(null, $basic_filter);
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

		$sql = "SELECT {$this->calculate_total_insert}
			c.id, c.type FROM nr_beat_x_content bxc
			INNER JOIN nr_content c ON 
			bxc.beat_id IN ({$beat_in_id_list})
			AND bxc.content_id = c.id
			WHERE c.is_published = 1
			AND c.company_id = {$this->newsroom->company_id}
			GROUP BY c.id
			ORDER BY c.date_publish DESC 
			LIMIT {$this->offset}, {$this->limit}";
		
		$results = $this->find_results($sql);
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
		
		$sql = "SELECT {$this->calculate_total_insert}
			  c.id, c.type FROM nr_content c 
			  INNER JOIN nr_content_tag ct ON 
			  c.id = ct.content_id
			  WHERE c.company_id = {$this->newsroom->company_id}
			  AND c.is_published = 1 AND ct.uniform = ?
			  LIMIT {$this->offset}, {$this->limit}";
		
		$results = $this->find_results($sql, array($slug));
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

			if ($type == Model_Content::TYPE_SOCIAL)
				$this->limit *= 10;
		}

		$types_str = implode(',', $types_quoted);
		if (!$types_str) return show_404();

		$sql = "SELECT {$this->calculate_total_insert}
			c.type, c.id, pc.content_id
			FROM nr_content c 
			LEFT JOIN nr_pinned_content pc
			ON pc.content_id = c.id
			WHERE c.company_id = {$this->newsroom->company_id}
			AND c.type IN ({$types_str}) 
			AND c.is_published = 1
			AND {$basic_filter}
			ORDER BY pc.priority DESC, c.date_publish DESC
			LIMIT {$this->offset}, {$this->limit}";

		$results = $this->find_results($sql);
		$this->render_list_view($results);
	}

	public function pr_all()
	{ 		
		$this->redirect('browse/pr');
	}
	
	public function rss()
	{
		$sql = "SELECT {$this->calculate_total_insert}
			c.type, c.id FROM nr_content c 
			WHERE c.company_id = {$this->newsroom->company_id}
			AND c.is_published = 1
			ORDER BY c.date_publish DESC
			LIMIT 0, {$this->rss_limit}";

		$results = $this->find_results($sql);
		$this->vd->results = $results;
		
		$this->output->set_content_type('application/rss+xml');
		$this->load->view('browse/rss');
	}

	public function refresh_social_content()
	{
		if (!Social_Wire::update($this->newsroom->company_id, Social_Wire::UPDATE_MANUAL))
			return $this->json(false);

		$types = array(Model_Content::TYPE_SOCIAL);
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

			if ($type == Model_Content::TYPE_SOCIAL)
				$this->limit *= 10;
		}

		$types_str = implode(',', $types_quoted);
		if (!$types_str) return show_404();

		$sql = "SELECT {$this->calculate_total_insert}
			c.type, c.id FROM nr_content c 
			WHERE c.company_id = {$this->newsroom->company_id}
			AND c.type IN ({$types_str}) 
			AND c.is_published = 1
			ORDER BY c.date_publish DESC
			LIMIT {$this->offset}, {$this->limit}";

		$results = $this->find_results($sql);
		$this->vd->results = $results;

		if (!count($results)) return $this->json(false);
		$content = $this->load->view('browse/partial-listing', null, true);
		return $this->json(array('data' => $content));
	}

}
