<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class Listing_Base extends Browse_Base {
	
	protected $limit = 10;
	protected $offset = 0;
	protected $rss_limit = 50;
	protected $rss_enabled = false;
	protected $calculate_total = false;
	protected $calculate_total_insert = false;
	protected $chunkination;
	protected $query_cache_ttl = 60;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->offset = (int) 
			$this->input->get('offset');
			
		if ($this->calculate_total)
		{
			$this->calculate_total_insert = 
				// calculate the total number of result
				// because this view uses chunkination
				"SQL_CALC_FOUND_ROWS";
		}
	}
	
	protected function find_results($sql, $params = null)
	{	
		$results = array();
		$id_filter = array();
		$query = $this->db->cached->query($sql, $params, $this->query_cache_ttl);

		foreach ($query->result() as $result)
		{
			if (!isset($id_filter[$result->type]))
				$id_filter[$result->type] = array();
			$id_filter[$result->type][] = $result->id;
		}
		
		if ($this->calculate_total)
		{
			$total_results = $query->found_rows();
			$this->chunkination->set_total($total_results);
			if ($this->chunkination->is_out_of_bounds())
				return array();
		}

		foreach ($id_filter as $type => $ids)
		{			
			$ids = sql_in_list($ids);
			
			if ($type == Model_Content::TYPE_SOCIAL)
			{
				if ($filter_social_type = $this->uri->segment(3))
					$this->vd->filter_social_type = $filter_social_type;

				$nr_profile = Model_Company_Profile::find($this->newsroom->company_id);
				$m_types = $nr_profile->get_social_wire_media();				
				if (!is_array($m_types) || !count($m_types))
					continue;
				
				$media_types = sql_in_list($m_types);
				$sql = "SELECT *, 
						tl.content_id AS m_pb_social__content_id,
						tl.raw_data AS m_pb_social__raw_data,
						tl.media_type AS m_pb_social__media_type,
						tl.post_id AS m_pb_social__post_id,
						cp.soc_twitter, cp.soc_facebook,
						cp.soc_gplus, cp.soc_youtube, 
						cp.soc_pinterest, c.type,
						UNIX_TIMESTAMP(c.date_publish) as ts,
						pc.priority
						FROM nr_content c 
						LEFT JOIN nr_content_data cd 
						ON c.id = cd.content_id
						LEFT JOIN nr_pb_{$type} tl
						ON c.id = tl.content_id
						LEFT JOIN nr_company_profile cp
						ON cp.company_id = c.company_id
						LEFT JOIN nr_pinned_content pc
						ON pc.content_id = c.id
						WHERE c.id IN ({$ids})
						AND tl.media_type IN ({$media_types})
						ORDER BY pc.priority DESC, c.date_publish DESC";
				
				// this cannot use from_db_all because
				// its adding to the previous loop
				$query = $this->db->query($sql);
				$prefixes = array('m_pb_social' => 'Model_PB_Social');
				foreach ($query->result() as $result)
					$results[] = Model_Content::from_db_object($result, $prefixes);
			}

			elseif ($type == Model_Content::TYPE_BLOG)
			{
				$nr_profile = Model_Company_Profile::find($this->newsroom->company_id);

				if ($nr_profile && $nr_profile->is_enable_blog_posts)
				{
					 $sql = "SELECT *, 
						tl.content_id AS m_pb_blog__content_id,
						tl.source_url AS m_pb_blog__source_url,
						UNIX_TIMESTAMP(c.date_publish) as ts,
						pc.priority
						FROM nr_content c 
						LEFT JOIN nr_content_data cd 
						ON c.id = cd.content_id
						LEFT JOIN nr_pb_{$type} tl
						ON c.id = tl.content_id
						LEFT JOIN nr_pinned_content pc
						ON pc.content_id = c.id
						WHERE c.id IN ({$ids})
						ORDER BY pc.priority DESC, c.date_publish DESC";
					
					// this cannot use from_db_all because
					// its adding to the previous loop
					$query = $this->db->query($sql);
					$prefixes = array('m_pb_blog' => 'Model_PB_Blog');
					foreach ($query->result() as $result)
						$results[] = Model_Content::from_db_object($result, $prefixes);
				}
			}
			
			else 
			{
				$sql = "SELECT *,
					UNIX_TIMESTAMP(c.date_publish) as ts,
					pc.priority
					FROM nr_content c 
					LEFT JOIN nr_content_data cd 
					ON c.id = cd.content_id
					LEFT JOIN nr_pb_{$type} tl
					ON c.id = tl.content_id
					LEFT JOIN nr_pinned_content pc
					ON pc.content_id = c.id
					WHERE c.id IN ({$ids})
					ORDER BY pc.priority DESC, c.date_publish DESC";
				
				// this cannot use from_db_all because
				// its adding to the previous loop
				$query = $this->db->query($sql);
				foreach ($query->result() as $result)
					$results[] = Model_Content::from_db_object($result);
			}
		}

		$class = get_class($this);
		$method = 'combined_sort';
		$callable = array($class, $method);
		usort($results, $callable);

		return $results;
	}

	protected function render_list_view($results)
	{
		$this->vd->results = $results;		
		
		if ($this->rss_enabled)
		{
			$this->output->set_content_type('application/rss+xml');
			return $this->load->view('browse/rss');
		}

		if ($this->conf('stats_enabled'))
		{
			$impressions_uri = new Stats_URI_Builder();
			foreach ($results as $result)
				$impressions_uri->add_content_impression($result);
			$impressions_uri_str = $impressions_uri->build();
			$this->vd->impressions_uri = $impressions_uri_str;
		}
		
		if ($this->input->get('partial')) 
		{
			if (!count($results)) return $this->json(false);
			$content = $this->load->view('browse/partial-listing', null, true);
			return $this->json(array('data' => $content, 'pixel' => $this->vd->impressions_uri));
		}
		
		$this->vd->hide_right_bar = 1;
		
		$this->load->view('browse/header');
		if ($this->is_common_host)
		     $this->load->view('browse/listing-common');
		else $this->load->view('browse/listing');
		
		$this->load->view('browse/footer');
	}
	
	public static function combined_sort($a, $b)
	{
		if ($a->is_pinned && $b->is_pinned)
		{
			if ($a->priority > $b->priority) return -1;
			if ($a->priority < $b->priority) return +1;
		}

		if ($a->is_pinned) return -1;
		if ($b->is_pinned) return +1;
		if ($a->ts > $b->ts) return -1;
		if ($a->ts < $b->ts) return +1;
		return 0;
	}
	
}
