<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Custom_RSS_Controller extends CIL_Controller {
	
	public function __construct()
	{
		parent::__construct();		
		
		if (!$this->is_website_host)
			show_404();		
	}
	
	public function index($slug)
	{
		$this->custom_rss($slug);		
	}
	
	public function custom_rss($slug)
	{		
		$condition = 1;
		$criteria = array();
		$criteria[] = array('slug', $slug);
		$criteria[] = array('is_enabled', 1);
		$rss_feed = Model_RSS_Feed::find($criteria);
				
		if ( ! $rss_feed) $this->redirect('browse');
		if ( ! $rss_feed->is_include_prs && ! $rss_feed->is_include_news) 
			$this->redirect('browse');
		
		$this->title = $rss_feed->title;
		$feed_title = $this->vd->esc($rss_feed->title);
		$this->vd->ln_header_html = "<span class=\"muted\">
			RSS:</span> {$feed_title}";
					
		$sql_news = "SELECT c.id, c.type, c.date_publish 
			FROM nr_content c
			WHERE c.type = 'news'
			AND c.is_published = 1 
			AND {$condition}";
		
		$is_premium = (int) $rss_feed->is_all_premium;
		$sql_pr = "SELECT c.id, c.type, c.date_publish 
			FROM nr_content c
			WHERE c.type = 'pr'
			AND c.is_premium >= {$is_premium}
			AND c.is_published = 1 
		   AND {$condition} ";
		
		$queries = array();
		if ($rss_feed->is_include_prs)
			$queries[] = $sql_pr;			
		if ($rss_feed->is_include_news)
			$queries[] = $sql_news;		
		
		// union all between the queries for 
		// each of the different content types
		$sql_union = implode(" UNION ALL ", $queries);

		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			c.id, c.type FROM (
			/* ------------------ */			
			{$sql_union}
			/* ------------------ */
			) AS c ORDER BY c.date_publish DESC 
			LIMIT 0, {$rss_feed->item_count}";
				
		$results = $this->find_results($sql);
				
		foreach ($results as $result)
		{
			if ($rss_feed->is_tracking_enabled)
			{
				$builder = new Stats_URI_Builder();
				$builder->add_content_view($result->newsroom, $result);
				$builder->add_remote_content_view($result, $this->uri->uri_string);
				$result->tracking_url = $builder->build(Stats_URI_Builder::MEDIA_IMAGE);
			}

			if (!empty($rss_feed->footer_text))
			{
				$footer_text = $rss_feed->footer_text;
				$footer_text = strip_tags($footer_text);
				$footer_text = HTML2Text::plain($footer_text);

				$params = array();
				$params['url'] = $this->website_url($result->url());
				$params['title'] = $result->title;
				$params['company_name'] = $result->company_name;
				$params['newsroom_url'] = $result->newsroom->url();
				$params['company_website'] = $result->website;
				$footer_text = Model_RSS_Feed::generate_content(
					$params, $footer_text);

				if ($rss_feed->is_spin_footer_text)
				{
					$spintax = new Spintax();
					$footer_text = $spintax->process($footer_text);
				}

				$result->footer_text = $footer_text;
			}
		}
	
		$this->render_list_view($results, $rss_feed);
	}	
	
	protected function find_results($sql, $params = null)
	{		
		$results = array();
		$id_filter = array();
		$query = $this->db->query($sql, $params);
		foreach ($query->result() as $result)
		{
			if (!isset($id_filter[$result->type]))
				$id_filter[$result->type] = array();
			$id_filter[$result->type][] = $result->id;
		}	
		
		foreach ($id_filter as $type => $ids)
		{
			$ids = sql_in_list($ids);
			$sql = "SELECT c.*, cd.*,
			 	nr.name AS newsroom__name,
			 	nr.company_id AS newsroom__company_id,
			 	nr.user_id AS newsroom__user_id,
				nr.company_name,
				nrc.logo_image_id AS newsroom_custom__logo_image_id,
				cp.website,
				cm.name as c_name, 
				CONCAT(cc.first_name, ' ', cc.last_name) as c_contact_name,
				cc.phone as c_contact_phone, cp.website as c_website, 
				cp.address_street as c_address_street, 
				cp.address_apt_suite as c_address_apt_suite, 
				cp.address_zip as c_address_zip,
				cp.phone as c_phone, ct.name as c_address_country,
				cp.address_state as c_address_state, 
				cp.address_city as c_address_city,
				UNIX_TIMESTAMP(c.date_publish) as ts
				FROM nr_content c
				LEFT JOIN nr_content_data cd 
				ON c.id = cd.content_id
				LEFT JOIN nr_pb_{$type} tl
				ON c.id = tl.content_id
				LEFT JOIN nr_company cm
				ON c.company_id = cm.id
				LEFT JOIN nr_company_profile cp
				ON c.company_id = cp.company_id	
				LEFT JOIN nr_country ct
				ON ct.id = cp.address_country_id	
				LEFT JOIN nr_company_contact cc
				ON cm.company_contact_id = cc.id
				LEFT JOIN nr_newsroom nr
				ON c.company_id = nr.company_id
				LEFT JOIN nr_newsroom_custom nrc
				ON c.company_id = nrc.company_id
				WHERE c.id IN ({$ids})
				ORDER BY c.date_publish DESC";
				
			$query = $this->db->query($sql);
			$results_list = Model_Content::from_db_all($query, array(
				'newsroom' => 'Model_Newsroom',
				'newsroom_custom' => 'Model_Newsroom_Custom'
			));
			
			foreach ($results_list as $result)
				$results[] = $result;			
		}
		
		$class = get_class($this);
		$method = 'combined_sort';
		$callable = array($class, $method);
		usort($results, $callable);
		
		return $results;
	}
	
	protected function render_list_view($results, $rss_feed)
	{
		$this->vd->results = $results;
		$this->vd->rss_feed = $rss_feed;
		
		$this->output->set_content_type('application/rss+xml');
		return $this->load->view('custom_rss');				
	}
	
	public static function combined_sort($a, $b)
	{
		if ($a->ts > $b->ts) return -1;
		if ($a->ts < $b->ts) return +1;
		return 0;
	}

}

?>